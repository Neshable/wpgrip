<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

use App\Services\GripNotifications;

use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;

use App\Models\PerformanceScore;
use App\Jobs\Tests\PageSpeed;


class PerformanceHistory extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected string $view = 'site.single.performance_history';


    public function getHeader(): ?View
    {
        return view('site.single.header');
    }


}
