<?php

namespace App\Livewire;

use App\Models\Site;
use App\Models\Plugin;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Contracts\View\View;

use App\Jobs\Site\GetAllPlugins;
use App\Jobs\Site\SwitchSinglePlugin;
use App\Services\ActivityLogger;
use App\Jobs\Site\UpdateSinglePlugin;

class PluginsPanel extends Component
{
    public Site $site_model;

    /** @var array */
    public array $plugins = [];

    public function mount(Site $site_model): void
    {
        $this->site_model = $site_model;
        $this->loadPlugins();
    }

    private function loadPlugins(): void
    {
        $this->plugins = $this->site_model
            ->plugins()
            ->withPivot(['version', 'update_version', 'status'])
            ->get()
            ->map(fn (Plugin $p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'title'          => $p->title,
                'description'    => $p->description,
                'version'        => $p->pivot->version,
                'update_version' => $p->pivot->update_version,
                'status'         => $p->pivot->status,
                'is_vulnerable'  => (bool) $p->is_vulnerable,
            ])
            ->toArray();
    }

    public function syncPlugins(): void
    {
        GetAllPlugins::dispatch($this->site_model);
        ActivityLogger::siteAction('plugins.synced', $this->site_model);
        Notification::make()
            ->title('Plugin sync queued.')
            ->success()
            ->body('Plugin list is syncing in the background.')
            ->send();
    }

    public function activatePlugin(int $pluginId): void
    {
        $plugin = Plugin::find($pluginId);
        if ($plugin) {
            SwitchSinglePlugin::dispatch($this->site_model, $plugin);
            Notification::make()->title('Activating plugin…')->success()->body('Running in the background.')->send();
        }
    }

    public function deactivatePlugin(int $pluginId): void
    {
        $plugin = Plugin::find($pluginId);
        if ($plugin) {
            SwitchSinglePlugin::dispatch($this->site_model, $plugin, true);
            Notification::make()->title('Deactivating plugin…')->success()->body('Running in the background.')->send();
        }
    }

    public function updatePlugin(int $pluginId): void
    {
        $plugin = Plugin::find($pluginId);
        if ($plugin && $plugin->pivot?->update_version ?? false) {
            $p = $this->site_model->plugins()->withPivot(['version', 'update_version', 'status'])->find($pluginId);
            if ($p) {
                UpdateSinglePlugin::dispatch($this->site_model, $p, $p->pivot->update_version);
                Notification::make()->title('Updating plugin…')->success()->body('Update is running in the background.')->send();
            }
        }
    }

    public function deletePlugin(int $pluginId): void
    {
        $this->site_model->plugins()->detach($pluginId);
        Notification::make()->title('Plugin removed.')->success()->send();
        $this->loadPlugins();
    }

    public function render(): View
    {
        return view('livewire.plugins-panel');
    }
}
