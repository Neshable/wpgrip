@extends('site/single/pagetemplate')

@section('content')

<div
    x-data="fileExplorer()"
    class="relative"
>

    {{-- Connect screen (shown before connection) --}}
    <div x-show="!connected && !sidebarLoading" class="fe-connect-screen">
        <div class="fe-connect-card">
            <div class="fe-connect-icon">
                <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 0 0-.12-1.03l-2.268-9.64a3.375 3.375 0 0 0-3.285-2.602H7.923a3.375 3.375 0 0 0-3.285 2.602l-2.268 9.64a4.5 4.5 0 0 0-.12 1.03v.228m19.5 0a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3m19.5 0a3 3 0 0 0-3-3H5.25a3 3 0 0 0-3 3m16.5 0h.008v.008h-.008v-.008Zm-3 0h.008v.008h-.008v-.008Z" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">SSH File Manager</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-5">Connect via SFTP to browse and edit files on the remote server.</p>
            <button @click="refresh()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-600 text-white text-sm font-medium hover:bg-primary-500 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m9.86-2.54a4.5 4.5 0 0 0-1.242-7.244l4.5-4.5a4.5 4.5 0 0 1 6.364 6.364l-1.757 1.757" />
                </svg>
                Connect
            </button>
        </div>
    </div>

    {{-- Connecting spinner --}}
    <div x-show="!connected && sidebarLoading" class="fe-connect-screen">
        <div class="fe-connect-card">
            <svg class="w-8 h-8 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mt-4">Connecting…</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Establishing SFTP connection to the server.</p>
        </div>
    </div>

    {{-- Main container (shown after connection) --}}
    <div x-show="connected" x-cloak class="fe-container">

        {{-- ====== SIDEBAR ====== --}}
        <div class="fe-sidebar" :style="'width:' + sidebarWidth + 'px; min-width:' + sidebarWidth + 'px'">

            {{-- Sidebar toolbar --}}
            <div class="fe-sidebar-header">
                <button @click="goUp()" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800" title="Go up">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </button>
                <button @click="refresh()" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800" title="Refresh">
                    <svg class="w-4 h-4 text-gray-500" :class="sidebarLoading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
                <button @click="startCreate('file')" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800" title="New file">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </button>
                <button @click="startCreate('dir')" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800" title="New folder">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                </button>
            </div>

            {{-- Breadcrumbs --}}
            <div class="fe-breadcrumbs">
                <template x-for="(crumb, idx) in breadcrumbs" :key="crumb.path">
                    <span class="flex items-center gap-1">
                        <span x-show="idx > 0" class="text-gray-300 dark:text-gray-600">/</span>
                        <span
                            @click="navigate(crumb.path)"
                            class="fe-breadcrumb-link"
                            :class="idx === breadcrumbs.length - 1 ? 'font-semibold text-gray-700 dark:text-gray-200 cursor-default no-underline' : ''"
                            x-text="crumb.name"
                        ></span>
                    </span>
                </template>
            </div>

            {{-- Create new item inline --}}
            <div x-show="creating" x-cloak class="px-3 py-2 border-b border-gray-200 dark:border-white/10 bg-blue-50 dark:bg-blue-950/30">
                <div class="flex items-center gap-2">
                    <svg x-show="creating === 'dir'" class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                    </svg>
                    <svg x-show="creating === 'file'" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <input
                        data-create-input
                        x-model="createValue"
                        @keydown.enter="submitCreate()"
                        @keydown.escape="cancelCreate()"
                        class="flex-1 text-sm bg-transparent border-none outline-none text-gray-800 dark:text-gray-200 p-0"
                        :placeholder="creating === 'dir' ? 'New folder name...' : 'New file name...'"
                    >
                    <button @click="submitCreate()" class="text-blue-500 hover:text-blue-700 text-xs font-medium">OK</button>
                    <button @click="cancelCreate()" class="text-gray-400 hover:text-gray-600 text-xs">✕</button>
                </div>
            </div>

            {{-- File list --}}
            <div class="fe-file-list" x-ref="fileList">
                {{-- Loading overlay --}}
                <div x-show="sidebarLoading && items.length === 0" class="flex items-center justify-center py-12">
                    <div class="fe-spinner"></div>
                </div>

                {{-- Empty state --}}
                <div x-show="!sidebarLoading && items.length === 0" class="py-12 text-center text-sm text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    Empty directory
                </div>

                {{-- Items --}}
                <template x-for="item in items" :key="item.path">
                    <div
                        class="fe-file-item"
                        :class="openFile && openFile.path === item.path ? 'active' : ''"
                        @click="clickItem(item)"
                        @contextmenu="showContextMenu($event, item)"
                    >
                        {{-- Rename inline --}}
                        <template x-if="renaming === item.path">
                            <div class="flex items-center gap-2 w-full" @click.stop>
                                <input
                                    data-rename-input
                                    x-model="renameValue"
                                    @keydown.enter="submitRename()"
                                    @keydown.escape="cancelRename()"
                                    @blur="cancelRename()"
                                    class="flex-1 text-sm bg-white dark:bg-gray-800 border border-blue-400 rounded px-1.5 py-0.5 outline-none"
                                >
                            </div>
                        </template>

                        {{-- Normal display --}}
                        <template x-if="renaming !== item.path">
                            <div class="flex items-center gap-2 w-full min-w-0">
                                {{-- Icon --}}
                                <span :class="iconColor(fileIcon(item))">
                                    {{-- Folder --}}
                                    <svg x-show="item.type === 'dir'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                    </svg>
                                    {{-- File --}}
                                    <svg x-show="item.type !== 'dir'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <span class="fe-file-name" x-text="item.name"></span>
                                <span x-show="item.type !== 'dir'" class="fe-file-size" x-text="formatSize(item.size)"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- ====== RESIZE HANDLE ====== --}}
        <div
            class="fe-resize-handle"
            :class="resizing ? 'active' : ''"
            @mousedown="startResize($event)"
        ></div>

        {{-- ====== EDITOR PANE ====== --}}
        <div class="fe-editor">
            {{-- Editor header --}}
            <div class="fe-editor-header" x-show="openFile">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200" x-text="openFile?.name || ''"></span>
                <span x-show="dirty" class="text-xs text-amber-500 font-semibold">● Modified</span>
                <span x-show="openFile && !openFile.writable" class="text-xs text-red-400 bg-red-50 dark:bg-red-900/20 px-1.5 py-0.5 rounded">Read-only</span>
                <span class="text-xs text-gray-400" x-text="openFile ? formatSize(openFile.size) : ''"></span>

                <div class="ml-auto flex items-center gap-2">
                    <button
                        @click="save()"
                        :disabled="!dirty || saving || !openFile?.writable"
                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition
                               bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span x-text="saving ? 'Saving...' : 'Save'"></span>
                        <span class="text-[10px] opacity-70">⌘S</span>
                    </button>
                    <button
                        @click="closeFile()"
                        class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-gray-600"
                        title="Close file"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Editor body --}}
            <div class="fe-editor-body" x-ref="editorContainer">
                {{-- Loading --}}
                <template x-if="loading">
                    <div class="fe-loading">
                        <div class="fe-spinner"></div>
                    </div>
                </template>

                {{-- Empty state --}}
                <template x-if="!openFile && !loading">
                    <div class="fe-empty-editor">
                        <svg class="w-12 h-12 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        <p class="text-sm">Select a file to view or edit</p>
                        <p class="text-xs text-gray-300 dark:text-gray-600">Right-click items for more options</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ====== CONTEXT MENU ====== --}}
    <div
        x-show="contextMenu.show"
        x-cloak
        :style="'left:' + contextMenu.x + 'px; top:' + contextMenu.y + 'px'"
        class="fe-context-menu"
        @click.outside="contextMenu.show = false"
    >
        <template x-if="contextMenu.item?.type === 'dir'">
            <div>
                <div class="fe-context-item" @click="navigate(contextMenu.item.path); contextMenu.show = false">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    Open folder
                </div>
            </div>
        </template>
        <template x-if="contextMenu.item?.type !== 'dir'">
            <div>
                <div class="fe-context-item" @click="open(contextMenu.item.path); contextMenu.show = false">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Open in editor
                </div>
            </div>
        </template>
        <div class="fe-context-sep"></div>
        <div class="fe-context-item" @click="startRename(contextMenu.item)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Rename
        </div>
        <div class="fe-context-sep"></div>
        <div class="fe-context-item danger" @click="requestDelete(contextMenu.item)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete
        </div>
    </div>

    {{-- ====== DELETE CONFIRMATION ====== --}}
    <template x-if="confirmDelete">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="confirmDelete = null">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 max-w-sm w-full mx-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Are you sure you want to delete:</p>
                <p class="text-sm font-mono font-medium text-gray-700 dark:text-gray-300 mb-4 break-all" x-text="confirmDelete?.name"></p>
                <p x-show="confirmDelete?.type === 'dir'" class="text-xs text-amber-600 dark:text-amber-400 mb-4">Directory must be empty to delete.</p>
                <div class="flex items-center justify-end gap-3">
                    <button @click="confirmDelete = null" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        Cancel
                    </button>
                    <button @click="doDelete()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ====== TOAST ====== --}}
    <div x-show="toast" x-cloak x-transition class="fe-toast" :class="toast?.type || 'success'" x-text="toast?.message || ''"></div>
</div>

@endsection
