<?php

namespace App\Livewire;
 
use App\Models\Site;
use App\Models\Theme;

use Filament\Tables\Grouping\Group;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables;
use Filament\Actions\Concerns\InteractsWithActions;

use Filament\Tables\Columns\ViewColumn;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Jobs\RemoteDBBackup;
use App\Jobs\Backup\RemoteFilesBackup;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use App\Jobs\Site\GetAllThemes;

use App\Jobs\Site\SwitchSingleTheme;

use App\Jobs\Site\UpdateSinglePlugin;



use Filament\Tables\Actions\BulkAction;

 
class ListThemes extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public $site_model;

    
    public function table(Table $table): Table
    {   
        // $which = Plugins::all()->where
        // $this->site_model->themes()->withPivot( ['version', 'update_version', 'status'] )->get()->load('pivot')->toQuery()
        return $table     
            ->relationship( fn (): BelongsToMany => $this->site_model->themes() )
            ->recordClasses(fn (Theme $record) => match ($record->status) {
                'active' => 'border-s-4' . ($record->update_version ? ' bg-amber-50 dark:bg-amber-200/20 border-amber-200' : ''),
                'dropin' => 'border-s-2' . ($record->update_version ? ' bg-gray-100 dark:bg-gray-500 border-amber-200' : ''),
                'inactive' => $record->update_version ? ' bg-gray-100 border-amber-200' : '',
                default => $record->update_version ? ' bg-green-50 border-amber-200' : null,
            })
            ->deferLoading()
            ->columns([
                // ViewColumn::make('')->view('filament.tables.columns.plugin-image'),
                // ViewColumn::make('Plugin')->view('filament.tables.columns.plugin-info'),
                // ViewColumn::make('Description')->view('filament.tables.columns.plugin-description'),    
                
                TextColumn::make('title')
                    ->wrap()
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Medium)
                    ->label('Theme Name')
                    ->tooltip(fn (Theme $record): string => $record->description),
                    // ->description(fn (Plugin $record): string => $record->description),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'inactive' => 'gray',
                        'dropin' => 'warning',
                        'active' => 'success',
                    }),
                ViewColumn::make('version')->view('filament.tables.columns.plugin-version'),
                // TextColumn::make('version')
                //     ->size(TextColumn\TextColumnSize::Medium)
                //     ->weight(FontWeight::Medium)
                //     ->label('Current Version')
                //     ->color(fn (string $state): string => 'gray'),
                TextColumn::make('update_version')
                    ->badge()
                    ->color('success')
                    ->weight(FontWeight::Medium)
                    ->label('New version'),
               
                // Tables\Columns\Layout\Panel::make([
                //     Tables\Columns\Layout\Stack::make([
                //         ViewColumn::make('vulnerabilities')->view('filament.tables.columns.plugin-vulnerabilities' )
                //     ]), 
                // ])->collapsed(false),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // Action::make('update')
                //     ->requiresConfirmation()
                //     ->label('Update Plugin')
                //     ->modalHeading('Process of updating a plugin')
                //     ->modalSubmitActionLabel('Update')
                //     ->successNotificationTitle( 'Would you like to update this plugin?' )
                //     ->color('success')
                //     ->visible(fn (Plugin $record) => $record->update_version ? true : false )
                //     ->action(fn (Plugin $record) => dd($record)),
         
                ActionGroup::make([         
                    // Action::make('activate')
                    //     ->label('Activate Theme')
                    //     ->modalHeading('Activating a Theme')
                    //     ->modalSubmitActionLabel('Activate')
                    //     ->successNotificationTitle( 'Would you like to activate this theme?' )
                    //     ->color('success')
                    //     ->visible( fn (Theme $record) => $record->status == 'inactive' )
                    //     ->requiresConfirmation()
                    //     ->action( fn (Theme $record) => SwitchSingleTheme::dispatchSync( $this->site_model, $record )  ),

                    // Action::make('updatesingle')
                    //     ->color('success')
                    //     ->label('Update theme')
                    //     ->modalHeading('Process of updating a theme')
                    //     ->modalSubmitActionLabel('Update')
                    //     ->successNotificationTitle( 'Would you like to update this theme?' )
                    //     ->color('success')
                    //     ->requiresConfirmation()
                    //     ->action( fn (Theme $record) => UpdateSingleTheme::dispatchSync( $this->site_model, $record, $record->update_version )  ),

                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->headerActions([
                // Sync plugin list
                Action::make('getallthemes')
                    ->label('Sync themes')
                    ->requiresConfirmation()
                    ->color('success')
                    ->action( fn () => GetAllThemes::dispatchSync( $this->site_model )  ),
                
                ActionGroup::make([

                    // Action::make('Update')
                    // ->label('Safe update all')
                    // ->color('success')
                    // ->requiresConfirmation()
                    // ->action( fn (Theme $record) => $record->name ),

                    // Action::make('install')
                    // ->label('Add new plugin')
                    // ->color('info')
                    // ->requiresConfirmation()
                    // ->action( fn (Plugin $record) => $record->name ),

                ])->icon('heroicon-m-ellipsis-vertical'),

            
                // ActionGroup::make([
                //     Action::make('Deactivate')
                //         ->color('info'),
                //     ,
                //     DeleteAction::make(),
                // ])->icon('heroicon-m-ellipsis-horizontal')
            ])
            ->bulkActions([
                BulkAction::make('Update')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->update())
            ])
            ->heading('Manage all themes')
            ->description('Update, Add, Activate, Deactivate, and Access Changelogs - All Your Themes Needs in One Place.')
            ->emptyStateHeading('No themes found')
            ->emptyStateDescription('You haven\'t synced your website yet.')
            ->paginated(false);
    }


    public function render(): View
    {
        return view('livewire.list-themes'); 
    }
}