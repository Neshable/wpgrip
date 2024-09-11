<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions\Action;

use App\Services\GripNotifications;

use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;

use App\Models\PerformanceScore;
use App\Jobs\Tests\PageSpeed;


class Performance extends ViewRecord
{
    protected static string $resource = SiteResource::class;

    protected static string $view = 'site.single.performance';

 
    public function runLightHouseTest()
    {
      
        $last_sync = PerformanceScore::where('site_id', $this->record->id )
        ->orderBy('created_at', 'desc')
        ->first();

        if ( $last_sync ) 
        {
            // Init a carbon object out of created date.
            $carbon_created_at = Carbon::parse( $last_sync->created_at );

             // Check if its more than one hour.
            if (!Carbon::now()->subHour()->gte($carbon_created_at))
            {
                GripNotifications::notAllowedToRunTest();
                return false;
            }
        }
        
        PageSpeed::dispatchSync( $this->record, 'mobile' );
        //PageSpeed::dispatch( $this->record, 'desktop' );
        
    }

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }


}
