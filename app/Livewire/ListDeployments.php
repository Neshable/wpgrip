<?php


namespace App\Livewire;
 
use App\Models\Deployment;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;

use App\Jobs\RemoteDBBackup;
use App\Jobs\Backup\RemoteFilesBackup;
use Filament\Tables\Columns\IconColumn;

use Filament\Tables\Columns\ViewColumn;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;
use Carbon\Carbon;

use Illuminate\Support\HtmlString;

use App\Services\GripNotifications;
 
class ListDeployments extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $repository_id = 8899;
    
    public function table(Table $table): Table
    {   
        return $table
            ->query( Deployment::query()->where('repository_id', $this->repository_id )->orderBy('id', 'desc') )
            ->deferLoading()
            ->columns([
                IconColumn::make('success')
                ->label('Status')
                ->boolean(),
                TextColumn::make('site.name'),
                TextColumn::make('branch')->icon('icon-git'),
                TextColumn::make('commit')
                    ->copyable()
                    ->limit(7),
                    // ->formatStateUsing(fn (string $state): HtmlString => new HtmlString( '<a href="#">asd</a<' . $state)),
                TextColumn::make('message'),
                // TextColumn::make('pivot_id'),
                
                TextColumn::make('committer')->label('Comitter'),
                
                TextColumn::make('created_at')->dateTime()->since()->label('Date')->dateTimeTooltip(),
                
                // TextColumn::make('message'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ActionGroup::make([
                    // Action::make('restore')
                    //     ->requiresConfirmation()
                    //     ->label('Restore commit')
                    //     ->modalHeading('Process of reverting back a commit')
                    //     ->modalSubmitActionLabel('Revert')
                    //     ->successNotificationTitle('Are you sure you want to revert back to this commit?')
                    //     ->color('warning')
                    //     ->icon('heroicon-o-server-stack')
                    //     ->modalIcon('heroicon-o-server-stack')
                    //     ->action(fn (Backup $record) => 'neshtosi'),
                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->headerActions([
            ])
            ->heading('Deployments')
            ->description('History of all your deployments.')
            ->emptyStateHeading('No deployments found')
            ->emptyStateDescription('You haven\'t done any deployments yet.')
            ->bulkActions([
                BulkAction::make('delete')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete())
            ]);
    }
    
    public function render()
    {
        return view('livewire.list-deployments');
    }

    
}
