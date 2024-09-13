<?php


namespace App\Livewire;
 
use App\Models\RepoCommit;

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

use App\Services\GripNotifications;
 
class ListCommits extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $repository_id = 8899;
    
    public function table(Table $table): Table
    {   
        return $table
            ->query( RepoCommit::query()->where('repository_id', $this->repository_id )->orderBy('id', 'desc') )
            ->deferLoading()
            ->columns([
                IconColumn::make('success')
                ->label('Status')
                ->boolean(),
                TextColumn::make('commit')->description(fn (RepoCommit $record): string => $record->message),
                TextColumn::make('pivot_id'),
                // TextColumn::make('site.name')->sortable(),
                TextColumn::make('committer')->label('Author'),
                TextColumn::make('branch'),
                TextColumn::make('created_at')->dateTime()->label('Date'),
                
                // TextColumn::make('message'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('restore')
                        ->requiresConfirmation()
                        ->label('Restore commit')
                        ->modalHeading('Process of reverting back a commit')
                        ->modalSubmitActionLabel('Revert')
                        ->successNotificationTitle('Are you sure you want to revert back to this commit?')
                        ->color('warning')
                        ->icon('heroicon-o-server-stack')
                        ->modalIcon('heroicon-o-server-stack')
                        ->action(fn (Backup $record) => 'neshtosi'),
                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->headerActions([
            ])
            ->heading('Deployments')
            ->description('List of all past deployments. Rollback if needed.')
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
        return view('livewire.list-commits');
    }

    
}
