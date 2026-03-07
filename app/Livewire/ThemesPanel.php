<?php

namespace App\Livewire;

use App\Models\Site;
use App\Models\Theme;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Contracts\View\View;

use App\Jobs\Site\GetAllThemes;
use App\Services\ActivityLogger;

class ThemesPanel extends Component
{
    public Site $site_model;

    /** @var array */
    public array $themes = [];

    public function mount(Site $site_model): void
    {
        $this->site_model = $site_model;
        $this->loadThemes();
    }

    private function loadThemes(): void
    {
        $this->themes = $this->site_model
            ->themes()
            ->withPivot(['version', 'update_version', 'status', 'is_vulnerable'])
            ->get()
            ->map(fn (Theme $t) => [
                'id'             => $t->id,
                'name'           => $t->name,
                'title'          => $t->title,
                'description'    => $t->description,
                'version'        => $t->pivot->version,
                'update_version' => $t->pivot->update_version,
                'status'         => $t->pivot->status,
                'is_vulnerable'  => (bool) $t->pivot->is_vulnerable,
            ])
            ->toArray();
    }

    public function syncThemes(): void
    {
        GetAllThemes::dispatch($this->site_model);
        ActivityLogger::siteAction('themes.synced', $this->site_model);
        Notification::make()
            ->title('Theme sync queued.')
            ->success()
            ->body('Theme list is syncing in the background.')
            ->send();
    }

    public function activateTheme(int $themeId): void
    {
        $theme = Theme::find($themeId);
        if ($theme) {
            // Activate by updating the pivot status directly;
            // a dedicated SwitchSingleTheme job can replace this later.
            $this->site_model->themes()->updateExistingPivot($themeId, ['status' => 'active']);

            // Deactivate all other themes for this site
            $this->site_model->themes()
                ->wherePivot('status', 'active')
                ->where('themes.id', '!=', $themeId)
                ->each(function (Theme $t) {
                    $this->site_model->themes()->updateExistingPivot($t->id, ['status' => 'inactive']);
                });

            Notification::make()
                ->title('Theme activated.')
                ->success()
                ->body('The theme has been set as active.')
                ->send();

            $this->loadThemes();
        }
    }

    public function deleteTheme(int $themeId): void
    {
        $this->site_model->themes()->detach($themeId);
        Notification::make()->title('Theme removed.')->success()->send();
        $this->loadThemes();
    }

    public function render(): View
    {
        return view('livewire.themes-panel');
    }
}
