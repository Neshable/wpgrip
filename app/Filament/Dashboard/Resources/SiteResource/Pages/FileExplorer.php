<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Services\SFTPFileService;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class FileExplorer extends ViewRecord
{
    protected static string $resource = SiteResource::class;
    protected static string $view     = 'site.single.file-explorer';

    // -------------------------------------------------------------------------
    // Livewire state
    // -------------------------------------------------------------------------

    public string $currentPath    = '';
    public array  $directoryItems = [];
    public string $openFilePath   = '';
    public string $openFileName   = '';
    public string $fileContent    = '';
    public bool   $fileWritable   = false;
    public bool   $fileDirty      = false;
    public int    $fileSize       = 0;
    public bool   $loading        = false;
    public string $errorMessage   = '';
    public bool   $connected      = false;

    public array $breadcrumbs = [];

    // -------------------------------------------------------------------------
    // Mount
    // -------------------------------------------------------------------------

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->currentPath = rtrim($this->record->dir_path, '/');

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): HtmlString => new HtmlString($this->explorerStyles()),
            scopes: [static::class],
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SCRIPTS_AFTER,
            fn (): HtmlString => new HtmlString($this->explorerScripts()),
            scopes: [static::class],
        );
    }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    // -------------------------------------------------------------------------
    // SFTP connection helper — reuses cached connection per site
    // -------------------------------------------------------------------------

    protected function sftp(): ?SFTPFileService
    {
        try {
            $service = SFTPFileService::make($this->record);
            $this->connected = true;
            return $service;
        } catch (\Throwable $e) {
            $this->connected = false;
            $this->errorMessage = 'SSH connection failed: ' . $e->getMessage();
            Log::error('FileExplorer SSH error', [
                'site'  => $this->record->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    // -------------------------------------------------------------------------
    // Directory navigation
    // -------------------------------------------------------------------------

    public function loadDirectory(?string $path = null): array
    {
        $path = $path ?? $this->currentPath;
        $this->errorMessage = '';

        $sftp = $this->sftp();
        if (!$sftp) {
            return ['ok' => false, 'error' => $this->errorMessage];
        }

        try {
            $items = $sftp->listDirectory($path);
            $this->currentPath    = $path;
            $this->directoryItems = $items;
            $this->breadcrumbs    = $this->buildBreadcrumbs($path);

            return [
                'ok'          => true,
                'path'        => $this->currentPath,
                'items'       => $items,
                'breadcrumbs' => $this->breadcrumbs,
            ];
        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = 'Access denied: ' . $e->getMessage();
            return ['ok' => false, 'error' => $this->errorMessage];
        } catch (\Throwable $e) {
            $this->errorMessage = 'Failed to list directory: ' . $e->getMessage();
            Log::error('FileExplorer listDirectory', ['path' => $path, 'error' => $e->getMessage()]);
            return ['ok' => false, 'error' => $this->errorMessage];
        }
    }

    public function navigateTo(string $path): array
    {
        return $this->loadDirectory($path);
    }

    public function navigateUp(): array
    {
        $parent = dirname($this->currentPath);
        $root   = rtrim($this->record->dir_path, '/');

        if (strlen($parent) < strlen($root)) {
            $parent = $root;
        }

        return $this->loadDirectory($parent);
    }

    // -------------------------------------------------------------------------
    // File operations
    // -------------------------------------------------------------------------

    public function openFile(string $path): array
    {
        $this->errorMessage = '';

        $sftp = $this->sftp();
        if (!$sftp) {
            return ['ok' => false, 'error' => $this->errorMessage];
        }

        try {
            $data = $sftp->readFile($path);

            if ($this->isBinary($data['content'])) {
                return [
                    'ok'     => false,
                    'error'  => 'This file appears to be binary and cannot be edited.',
                    'binary' => true,
                ];
            }

            $this->openFilePath = $path;
            $this->openFileName = basename($path);
            $this->fileContent  = $data['content'];
            $this->fileWritable = $data['writable'];
            $this->fileSize     = $data['size'];
            $this->fileDirty    = false;

            return [
                'ok'       => true,
                'path'     => $path,
                'name'     => $this->openFileName,
                'content'  => $data['content'],
                'writable' => $data['writable'],
                'size'     => $data['size'],
                'language' => $this->detectLanguage($path),
            ];
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
            return ['ok' => false, 'error' => $this->errorMessage];
        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = 'Access denied: ' . $e->getMessage();
            return ['ok' => false, 'error' => $this->errorMessage];
        } catch (\Throwable $e) {
            $this->errorMessage = 'Failed to open file: ' . $e->getMessage();
            Log::error('FileExplorer openFile', ['path' => $path, 'error' => $e->getMessage()]);
            return ['ok' => false, 'error' => $this->errorMessage];
        }
    }

    public function saveFile(string $path, string $content): array
    {
        $this->errorMessage = '';

        $sftp = $this->sftp();
        if (!$sftp) {
            return ['ok' => false, 'error' => $this->errorMessage];
        }

        try {
            $sftp->writeFile($path, $content);
            $this->fileContent = $content;
            $this->fileDirty   = false;
            $this->fileSize    = strlen($content);

            return ['ok' => true, 'size' => $this->fileSize];
        } catch (\Throwable $e) {
            $this->errorMessage = 'Failed to save: ' . $e->getMessage();
            Log::error('FileExplorer saveFile', ['path' => $path, 'error' => $e->getMessage()]);
            return ['ok' => false, 'error' => $this->errorMessage];
        }
    }

    public function createNewFile(string $dirPath, string $name): array
    {
        $this->errorMessage = '';
        $fullPath = rtrim($dirPath, '/') . '/' . $name;

        $sftp = $this->sftp();
        if (!$sftp) {
            return ['ok' => false, 'error' => $this->errorMessage];
        }

        try {
            $sftp->createFile($fullPath);
            return $this->loadDirectory($dirPath);
        } catch (\Throwable $e) {
            $this->errorMessage = 'Failed to create file: ' . $e->getMessage();
            return ['ok' => false, 'error' => $this->errorMessage];
        }
    }

    public function createNewDirectory(string $dirPath, string $name): array
    {
        $this->errorMessage = '';
        $fullPath = rtrim($dirPath, '/') . '/' . $name;

        $sftp = $this->sftp();
        if (!$sftp) {
            return ['ok' => false, 'error' => $this->errorMessage];
        }

        try {
            $sftp->createDirectory($fullPath);
            return $this->loadDirectory($dirPath);
        } catch (\Throwable $e) {
            $this->errorMessage = 'Failed to create directory: ' . $e->getMessage();
            return ['ok' => false, 'error' => $this->errorMessage];
        }
    }

    public function deleteItem(string $path, string $type): array
    {
        $this->errorMessage = '';

        $sftp = $this->sftp();
        if (!$sftp) {
            return ['ok' => false, 'error' => $this->errorMessage];
        }

        try {
            if ($type === 'dir') {
                $sftp->deleteDirectory($path);
            } else {
                $sftp->deleteFile($path);
            }

            if ($this->openFilePath === $path) {
                $this->openFilePath = '';
                $this->openFileName = '';
                $this->fileContent  = '';
            }

            return $this->loadDirectory($this->currentPath);
        } catch (\Throwable $e) {
            $this->errorMessage = 'Failed to delete: ' . $e->getMessage();
            return ['ok' => false, 'error' => $this->errorMessage];
        }
    }

    public function renameItem(string $oldPath, string $newName): array
    {
        $this->errorMessage = '';
        $newPath = dirname($oldPath) . '/' . $newName;

        $sftp = $this->sftp();
        if (!$sftp) {
            return ['ok' => false, 'error' => $this->errorMessage];
        }

        try {
            $sftp->rename($oldPath, $newPath);

            if ($this->openFilePath === $oldPath) {
                $this->openFilePath = $newPath;
                $this->openFileName = $newName;
            }

            return $this->loadDirectory($this->currentPath);
        } catch (\Throwable $e) {
            $this->errorMessage = 'Failed to rename: ' . $e->getMessage();
            return ['ok' => false, 'error' => $this->errorMessage];
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function buildBreadcrumbs(string $path): array
    {
        $root   = rtrim($this->record->dir_path, '/');
        $crumbs = [['name' => basename($root) ?: '/', 'path' => $root]];

        if ($path === $root) {
            return $crumbs;
        }

        $relative = substr($path, strlen($root) + 1);
        $segments = explode('/', $relative);
        $current  = $root;

        foreach ($segments as $segment) {
            $current .= '/' . $segment;
            $crumbs[] = ['name' => $segment, 'path' => $current];
        }

        return $crumbs;
    }

    protected function isBinary(string $content): bool
    {
        if (empty($content)) {
            return false;
        }
        return str_contains(substr($content, 0, 8192), "\x00");
    }

    protected function detectLanguage(string $path): string
    {
        // Handle compound extensions like .blade.php
        if (str_ends_with($path, '.blade.php')) {
            return 'html';
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'php', 'phtml'              => 'php',
            'js', 'mjs', 'cjs'         => 'javascript',
            'ts', 'tsx'                 => 'typescript',
            'css'                       => 'css',
            'scss', 'sass'              => 'scss',
            'less'                      => 'less',
            'html', 'htm'              => 'html',
            'json'                      => 'json',
            'xml', 'svg'                => 'xml',
            'yaml', 'yml'              => 'yaml',
            'md', 'markdown'           => 'markdown',
            'sql'                       => 'sql',
            'sh', 'bash', 'zsh'        => 'shell',
            'py'                        => 'python',
            'rb'                        => 'ruby',
            'ini', 'conf', 'cfg', 'env' => 'ini',
            'log', 'txt'               => 'plaintext',
            'htaccess'                  => 'apache',
            'nginx'                     => 'nginx',
            'twig'                      => 'twig',
            default                     => 'plaintext',
        };
    }

    // -------------------------------------------------------------------------
    // Inline CSS
    // -------------------------------------------------------------------------

    private function explorerStyles(): string
    {
        return <<<'CSS'
<style>
.fe-connect-screen {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
}
.fe-connect-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 2.5rem;
    border-radius: 1rem;
    border: 1px dashed rgb(209 213 219);
    background: white;
    max-width: 360px;
}
.dark .fe-connect-card {
    border-color: rgba(255 255 255 / 0.1);
    background: rgba(255 255 255 / 0.03);
}
.fe-connect-icon {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 9999px;
    background: rgba(63 99 230 / 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.fe-container {
    display: flex;
    height: calc(100vh - 250px);
    min-height: 500px;
    border-radius: 0.75rem;
    overflow: hidden;
    border: 1px solid rgb(229 231 235);
}
.dark .fe-container { border-color: rgba(255 255 255 / 0.1); }

.fe-sidebar {
    display: flex;
    flex-direction: column;
    background: white;
    border-right: 1px solid rgb(229 231 235);
    overflow: hidden;
}
.dark .fe-sidebar { background: rgb(17 24 39); border-right-color: rgba(255 255 255 / 0.1); }

.fe-sidebar-header {
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid rgb(229 231 235);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}
.dark .fe-sidebar-header { border-bottom-color: rgba(255 255 255 / 0.1); }

.fe-file-list { flex: 1; overflow-y: auto; overflow-x: hidden; }

.fe-file-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.75rem;
    cursor: pointer;
    font-size: 0.8125rem;
    color: rgb(55 65 81);
    transition: background-color 0.1s;
    position: relative;
    user-select: none;
}
.dark .fe-file-item { color: rgb(209 213 219); }
.fe-file-item:hover { background: rgb(243 244 246); }
.dark .fe-file-item:hover { background: rgba(255 255 255 / 0.05); }
.fe-file-item.active { background: rgb(219 234 254); }
.dark .fe-file-item.active { background: rgba(59 130 246 / 0.2); }

.fe-file-item .fe-actions { display: none; margin-left: auto; gap: 0.25rem; }
.fe-file-item:hover .fe-actions { display: flex; }

.fe-file-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.fe-file-size { font-size: 0.7rem; color: rgb(156 163 175); flex-shrink: 0; }

.fe-editor {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: white;
    overflow: hidden;
}
.dark .fe-editor { background: rgb(17 24 39); }

.fe-editor-header {
    padding: 0.5rem 1rem;
    border-bottom: 1px solid rgb(229 231 235);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
    min-height: 2.75rem;
}
.dark .fe-editor-header { border-bottom-color: rgba(255 255 255 / 0.1); }

.fe-editor-body { flex: 1; overflow: hidden; position: relative; }
.fe-editor-body .CodeMirror { height: 100% !important; font-size: 13px; }

.fe-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: rgb(107 114 128);
    overflow-x: auto;
    white-space: nowrap;
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid rgb(229 231 235);
    flex-shrink: 0;
}
.dark .fe-breadcrumbs { border-bottom-color: rgba(255 255 255 / 0.1); color: rgb(156 163 175); }
.fe-breadcrumb-link { cursor: pointer; color: rgb(59 130 246); }
.fe-breadcrumb-link:hover { text-decoration: underline; }

.fe-empty-editor {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: rgb(156 163 175);
    gap: 0.75rem;
}

.fe-context-menu {
    position: fixed;
    background: white;
    border: 1px solid rgb(229 231 235);
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    padding: 0.25rem 0;
    z-index: 50;
    min-width: 160px;
}
.dark .fe-context-menu { background: rgb(31 41 55); border-color: rgba(255 255 255 / 0.1); }
.fe-context-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.75rem;
    font-size: 0.8125rem;
    color: rgb(55 65 81);
    cursor: pointer;
}
.dark .fe-context-item { color: rgb(209 213 219); }
.fe-context-item:hover { background: rgb(243 244 246); }
.dark .fe-context-item:hover { background: rgba(255 255 255 / 0.05); }
.fe-context-item.danger { color: rgb(239 68 68); }
.fe-context-item.danger:hover { background: rgb(254 242 242); }
.dark .fe-context-item.danger:hover { background: rgba(239 68 68 / 0.1); }
.fe-context-sep { height: 1px; background: rgb(229 231 235); margin: 0.25rem 0; }
.dark .fe-context-sep { background: rgba(255 255 255 / 0.1); }

.fe-resize-handle {
    width: 4px;
    cursor: col-resize;
    background: transparent;
    transition: background-color 0.15s;
    flex-shrink: 0;
}
.fe-resize-handle:hover, .fe-resize-handle.active { background: rgb(59 130 246); }

.fe-loading {
    position: absolute;
    inset: 0;
    background: rgba(255 255 255 / 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}
.dark .fe-loading { background: rgba(0 0 0 / 0.5); }

.fe-toast {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 500;
    z-index: 100;
    animation: fe-toast-in 0.2s ease-out;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}
.fe-toast.success { background: rgb(220 252 231); color: rgb(22 101 52); border: 1px solid rgb(187 247 208); }
.dark .fe-toast.success { background: rgba(22 101 52 / 0.3); color: rgb(187 247 208); border-color: rgba(22 101 52 / 0.5); }
.fe-toast.error { background: rgb(254 242 242); color: rgb(153 27 27); border: 1px solid rgb(254 202 202); }
.dark .fe-toast.error { background: rgba(153 27 27 / 0.3); color: rgb(254 202 202); border-color: rgba(153 27 27 / 0.5); }
@keyframes fe-toast-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fe-spin { to { transform: rotate(360deg); } }
.fe-spinner {
    width: 1.25rem; height: 1.25rem;
    border: 2px solid rgb(229 231 235);
    border-top-color: rgb(59 130 246);
    border-radius: 50%;
    animation: fe-spin 0.6s linear infinite;
}
</style>
CSS;
    }

    // -------------------------------------------------------------------------
    // Inline JS — Alpine component + CodeMirror 5 bootstrap
    // -------------------------------------------------------------------------

    private function explorerScripts(): string
    {
        return <<<'JS'
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/lib/codemirror.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/lib/codemirror.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/theme/material-darker.min.css">
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/php/php.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/css/css.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/clike/clike.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/sql/sql.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/markdown/markdown.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/yaml/yaml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/shell/shell.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/python/python.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/ruby/ruby.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/nginx/nginx.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/mode/properties/properties.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/search/search.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/search/searchcursor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/search/jump-to-line.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/dialog/dialog.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/dialog/dialog.min.css">
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/selection/active-line.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/edit/closebrackets.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/fold/foldcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/fold/foldgutter.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/fold/foldgutter.min.css">
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/fold/brace-fold.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/fold/indent-fold.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/codemirror@5.65.16/addon/fold/comment-fold.min.js"></script>

<script>
function fileExplorer() {
    return {
        items: [],
        currentPath: '',
        breadcrumbs: [],
        openFile: null,
        editor: null,
        loading: false,
        sidebarLoading: false,
        saving: false,
        dirty: false,
        connected: false,
        toast: null,
        toastTimeout: null,
        contextMenu: { show: false, x: 0, y: 0, item: null },
        renaming: null,
        renameValue: '',
        creating: null,
        createValue: '',
        confirmDelete: null,
        sidebarWidth: 320,
        resizing: false,

        init() {
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 's' && this.openFile) {
                    e.preventDefault();
                    this.save();
                }
            });
            document.addEventListener('click', () => { this.contextMenu.show = false; });
        },

        async refresh() {
            this.sidebarLoading = true;
            try {
                const r = await this.$wire.loadDirectory();
                if (r.ok) {
                    this.items = r.items;
                    this.currentPath = r.path;
                    this.breadcrumbs = r.breadcrumbs;
                    this.connected = true;
                } else {
                    this.showToast(r.error, 'error');
                }
            } catch (e) { this.showToast('Connection error', 'error'); }
            this.sidebarLoading = false;
        },

        async navigate(path) {
            this.sidebarLoading = true;
            try {
                const r = await this.$wire.navigateTo(path);
                if (r.ok) {
                    this.items = r.items;
                    this.currentPath = r.path;
                    this.breadcrumbs = r.breadcrumbs;
                } else { this.showToast(r.error, 'error'); }
            } catch (e) { this.showToast('Navigation error', 'error'); }
            this.sidebarLoading = false;
        },

        async goUp() {
            this.sidebarLoading = true;
            try {
                const r = await this.$wire.navigateUp();
                if (r.ok) {
                    this.items = r.items;
                    this.currentPath = r.path;
                    this.breadcrumbs = r.breadcrumbs;
                } else { this.showToast(r.error, 'error'); }
            } catch (e) { this.showToast('Navigation error', 'error'); }
            this.sidebarLoading = false;
        },

        async clickItem(item) {
            if (item.type === 'dir') { await this.navigate(item.path); }
            else { await this.open(item.path); }
        },

        async open(path) {
            if (this.dirty && !confirm('You have unsaved changes. Discard them?')) return;
            this.loading = true;
            try {
                const r = await this.$wire.openFile(path);
                if (r.ok) {
                    this.openFile = {
                        path: r.path, name: r.name,
                        writable: r.writable, size: r.size, language: r.language,
                    };
                    this.dirty = false;
                    this.$nextTick(() => this.initEditor(r.content, r.language));
                } else { this.showToast(r.error, 'error'); }
            } catch (e) { this.showToast('Failed to open file', 'error'); }
            this.loading = false;
        },

        initEditor(content, language) {
            const container = this.$refs.editorContainer;
            if (!container) return;
            if (this.editor) { this.editor.toTextArea(); this.editor = null; }
            container.innerHTML = '';
            const textarea = document.createElement('textarea');
            container.appendChild(textarea);

            const modeMap = {
                'php': 'application/x-httpd-php',
                'javascript': 'javascript', 'typescript': 'javascript',
                'css': 'css', 'scss': 'text/x-scss', 'less': 'text/x-less',
                'html': 'htmlmixed', 'xml': 'xml',
                'json': { name: 'javascript', json: true },
                'yaml': 'yaml', 'markdown': 'markdown', 'sql': 'sql',
                'shell': 'shell', 'python': 'python', 'ruby': 'ruby',
                'nginx': 'nginx', 'ini': 'text/x-properties',
                'apache': 'text/x-properties', 'plaintext': 'text/plain',
            };

            const isDark = document.documentElement.classList.contains('dark');
            this.editor = CodeMirror.fromTextArea(textarea, {
                mode: modeMap[language] || 'text/plain',
                theme: isDark ? 'material-darker' : 'default',
                lineNumbers: true, lineWrapping: false,
                styleActiveLine: true, matchBrackets: true,
                autoCloseBrackets: true, foldGutter: true,
                gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
                indentUnit: 4, tabSize: 4, indentWithTabs: false,
                extraKeys: {
                    'Tab': (cm) => cm.execCommand('indentMore'),
                    'Shift-Tab': (cm) => cm.execCommand('indentLess'),
                },
                readOnly: !this.openFile?.writable,
            });
            this.editor.setValue(content);
            this.editor.on('change', () => { this.dirty = true; });
            setTimeout(() => this.editor.refresh(), 50);
        },

        async save() {
            if (!this.openFile || !this.editor || this.saving) return;
            this.saving = true;
            try {
                const r = await this.$wire.saveFile(this.openFile.path, this.editor.getValue());
                if (r.ok) {
                    this.dirty = false;
                    this.openFile.size = r.size;
                    this.showToast('File saved', 'success');
                } else { this.showToast(r.error, 'error'); }
            } catch (e) { this.showToast('Failed to save', 'error'); }
            this.saving = false;
        },

        closeFile() {
            if (this.dirty && !confirm('You have unsaved changes. Discard them?')) return;
            this.openFile = null;
            this.dirty = false;
            if (this.editor) { this.editor.toTextArea(); this.editor = null; }
        },

        showContextMenu(e, item) {
            e.preventDefault();
            this.contextMenu = { show: true, x: e.clientX, y: e.clientY, item };
        },

        startRename(item) {
            this.contextMenu.show = false;
            this.renaming = item.path;
            this.renameValue = item.name;
            this.$nextTick(() => {
                const input = document.querySelector('[data-rename-input]');
                if (input) { input.focus(); input.select(); }
            });
        },

        async submitRename() {
            if (!this.renaming || !this.renameValue.trim()) return;
            this.sidebarLoading = true;
            try {
                const r = await this.$wire.renameItem(this.renaming, this.renameValue.trim());
                if (r.ok) { this.items = r.items; this.breadcrumbs = r.breadcrumbs || this.breadcrumbs; this.showToast('Renamed', 'success'); }
                else { this.showToast(r.error, 'error'); }
            } catch (e) { this.showToast('Rename failed', 'error'); }
            this.renaming = null; this.renameValue = '';
            this.sidebarLoading = false;
        },
        cancelRename() { this.renaming = null; this.renameValue = ''; },

        startCreate(type) {
            this.contextMenu.show = false;
            this.creating = type;
            this.createValue = '';
            this.$nextTick(() => {
                const input = document.querySelector('[data-create-input]');
                if (input) input.focus();
            });
        },

        async submitCreate() {
            if (!this.creating || !this.createValue.trim()) return;
            this.sidebarLoading = true;
            try {
                let r;
                if (this.creating === 'dir') {
                    r = await this.$wire.createNewDirectory(this.currentPath, this.createValue.trim());
                } else {
                    r = await this.$wire.createNewFile(this.currentPath, this.createValue.trim());
                }
                if (r.ok) { this.items = r.items; this.breadcrumbs = r.breadcrumbs || this.breadcrumbs; this.showToast((this.creating === 'dir' ? 'Directory' : 'File') + ' created', 'success'); }
                else { this.showToast(r.error, 'error'); }
            } catch (e) { this.showToast('Create failed', 'error'); }
            this.creating = null; this.createValue = '';
            this.sidebarLoading = false;
        },
        cancelCreate() { this.creating = null; this.createValue = ''; },

        requestDelete(item) { this.contextMenu.show = false; this.confirmDelete = item; },

        async doDelete() {
            if (!this.confirmDelete) return;
            this.sidebarLoading = true;
            try {
                const r = await this.$wire.deleteItem(this.confirmDelete.path, this.confirmDelete.type);
                if (r.ok) { this.items = r.items; this.breadcrumbs = r.breadcrumbs || this.breadcrumbs; this.showToast('Deleted', 'success'); }
                else { this.showToast(r.error, 'error'); }
            } catch (e) { this.showToast('Delete failed', 'error'); }
            this.confirmDelete = null;
            this.sidebarLoading = false;
        },

        startResize(e) {
            this.resizing = true;
            const startX = e.clientX;
            const startW = this.sidebarWidth;
            const move = (ev) => { this.sidebarWidth = Math.max(200, Math.min(600, startW + (ev.clientX - startX))); };
            const up = () => {
                this.resizing = false;
                document.removeEventListener('mousemove', move);
                document.removeEventListener('mouseup', up);
                if (this.editor) this.editor.refresh();
            };
            document.addEventListener('mousemove', move);
            document.addEventListener('mouseup', up);
        },

        formatSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024, sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        },
        formatDate(ts) {
            if (!ts) return '';
            const d = new Date(ts * 1000);
            return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
        fileIcon(item) {
            if (item.type === 'dir') return 'folder';
            if (item.type === 'link') return 'link';
            const ext = item.name.split('.').pop()?.toLowerCase();
            if (['php','phtml'].includes(ext)) return 'php';
            if (['js','mjs','ts','tsx'].includes(ext)) return 'js';
            if (['css','scss','sass','less'].includes(ext)) return 'css';
            if (['html','htm'].includes(ext)) return 'html';
            if (['json'].includes(ext)) return 'json';
            if (['md'].includes(ext)) return 'md';
            if (['jpg','jpeg','png','gif','svg','webp','ico'].includes(ext)) return 'img';
            if (['zip','tar','gz','rar'].includes(ext)) return 'zip';
            return 'file';
        },
        iconColor(icon) {
            return { folder:'text-amber-500', link:'text-purple-400', php:'text-indigo-500',
                js:'text-yellow-500', css:'text-blue-500', html:'text-orange-500',
                json:'text-green-500', md:'text-gray-500', img:'text-pink-500',
                zip:'text-gray-400', file:'text-gray-400' }[icon] || 'text-gray-400';
        },
        showToast(message, type = 'success') {
            this.toast = { message, type };
            if (this.toastTimeout) clearTimeout(this.toastTimeout);
            this.toastTimeout = setTimeout(() => { this.toast = null; }, 3000);
        },
    };
}
</script>
JS;
    }
}
