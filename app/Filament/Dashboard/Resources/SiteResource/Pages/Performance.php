<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

use App\Services\GripNotifications;
use Filament\Notifications\Notification;

use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;

use App\Models\PerformanceData;
use App\Jobs\Tests\PageSpeed;


class Performance extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected string $view = 'site.single.performance';

 
    public function runPageSpeedTest(): void
    {
        $last = PerformanceData::where('site_id', $this->record->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($last) {
            $carbon_created_at = Carbon::parse($last->created_at);
            if (!Carbon::now()->subHour()->gte($carbon_created_at)) {
                GripNotifications::notAllowedToRunTest();
                return;
            }
        }

        PageSpeed::dispatch($this->record, 'mobile');
        PageSpeed::dispatch($this->record, 'desktop');

        Notification::make()
            ->title('PageSpeed test queued.')
            ->success()
            ->body('The performance test is running in the background. Results will appear in a few minutes.')
            ->send();
    }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }


}
