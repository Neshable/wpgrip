<?php

namespace App\Livewire;
 
use App\Models\Site;
use App\Models\Plugin;

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

use App\Jobs\ListAllWPPlugins;
use App\Jobs\Site\GetAllPlugins;
use App\Jobs\Site\SwitchSinglePlugin;
use App\Jobs\Site\UpdateSinglePlugin;



use Filament\Tables\Actions\BulkAction;

 
class ListPlugins extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public $site_model;

    
    public function table(Table $table): Table
    {   
        // $which = Plugins::all()->where
        // $this->site_model->plugins()->withPivot( ['version', 'update_version', 'status'] )->get()->load('pivot')->toQuery()
        return $table     
            ->relationship( fn (): BelongsToMany => $this->site_model->plugins() )
            ->recordClasses(fn (Plugin $record) => match ($record->status) {
                'active' => 'border-s-4' . ($record->update_version ? ' bg-amber-50 dark:bg-amber-200/20 border-amber-200' : ''),
                'dropin' => 'border-s-2' . ($record->update_version ? ' bg-gray-100 dark:bg-gray-500 border-amber-200' : ''),
                'inactive' => $record->update_version ? ' bg-gray-100 border-amber-200' : '',
                default => $record->update_version ? ' bg-green-50 border-amber-200' : null,
            })
            ->deferLoading()
            ->columns([
                ViewColumn::make('')->view('filament.tables.columns.plugin-image'),
                // ViewColumn::make('Plugin')->view('filament.tables.columns.plugin-info'),
                // ViewColumn::make('Description')->view('filament.tables.columns.plugin-description'),    
                
                TextColumn::make('title')
                    ->wrap()
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Medium)
                    ->label('Plugin Name')
                    ->description(fn (Plugin $record): string => $record->description),
                
                ViewColumn::make('version')->view('filament.tables.columns.plugin-version'),
                
                // TextColumn::make('description')
                //     ->wrap()
                //     ->label('Plugin Name'),
                // TextColumn::make('name')
                //     ->label('Plugin Slug'),
                // TextColumn::make('version')
                //     ->label('Current version'),
                
                //     // TextColumn::make('update_version')
                //     // ->label('Update Available'),
                // TextColumn::make('status'),
                    

                // Tables\Columns\Layout\Split::make([  
                //     ViewColumn::make('')->view('filament.tables.columns.plugin-image'),     
                //     TextColumn::make('title')
                //     ->label('Plugin Name'),
                //     TextColumn::make('name')
                //     ->label('Plugin Slug'),
                //     TextColumn::make('version')
                //     ->label('Current version'),
                //     TextColumn::make('update_version')
                //     ->label('Update Available'),
                //     TextColumn::make('status'),
                // ]),
               
                // Tables\Columns\Layout\Panel::make([
                //     Tables\Columns\Layout\Stack::make([
                //         ViewColumn::make('vulnerabilities')->view('filament.tables.columns.plugin-vulnerabilities' )
                //     ]), 
                // ])->collapsed(false),
            ])
            ->defaultGroup('status')
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
                    Action::make('deactivate')
                        ->label('Deactivate Plugin')
                        ->modalHeading('Deactivating a plugin')
                        ->modalSubmitActionLabel('Deactivate')
                        ->successNotificationTitle( 'Would you like to deactivate this plugin?' )
                        ->color('info')
                        ->visible( fn (Plugin $record) => $record->status == 'active' )
                        ->requiresConfirmation()
                        ->action( fn (Plugin $record) => SwitchSinglePlugin::dispatchSync( $this->site_model, $record, true )  ),
                    
                    Action::make('activate')
                        ->label('Activate Plugin')
                        ->modalHeading('Activating a plugin')
                        ->modalSubmitActionLabel('Activate')
                        ->successNotificationTitle( 'Would you like to activate this plugin?' )
                        ->color('success')
                        ->visible( fn (Plugin $record) => $record->status == 'inactive' )
                        ->requiresConfirmation()
                        ->action( fn (Plugin $record) => SwitchSinglePlugin::dispatchSync( $this->site_model, $record )  ),

                    Action::make('updatesingle')
                        ->color('success')
                        ->label('Update Plugin')
                        ->modalHeading('Process of updating a plugin')
                        ->modalSubmitActionLabel('Update')
                        ->successNotificationTitle( 'Would you like to update this plugin?' )
                        ->color('success')
                        ->requiresConfirmation()
                        ->action( fn (Plugin $record) => UpdateSinglePlugin::dispatchSync( $this->site_model, $record, $record->update_version )  ),

                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->headerActions([
                // Sync plugin list
                Action::make('getallplugins')
                    ->label('Sync plugin list')
                    ->requiresConfirmation()
                    ->color('success')
                    ->action( fn () => GetAllPlugins::dispatchSync( $this->site_model )  ),
                
                ActionGroup::make([

                    Action::make('Update')
                    ->label('Safe update all')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action( fn (Plugin $record) => $record->name ),

                    Action::make('install')
                    ->label('Add new plugin')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action( fn (Plugin $record) => $record->name ),

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
            ->heading('Manage all plugins')
            ->description('Update, Add, Activate, Deactivate, and Access Changelogs - All Your Plugin Needs in One Place.')
            ->emptyStateHeading('No plugins found')
            ->emptyStateDescription('You haven\'t synced your website yet.')
            ->paginated(false);
    }

    public function checkVulnerability(Plugin $plugin)
    {
        // Get the current version of the plugin
        $currentVersion = $plugin->version;

        // Get the vulnerabilities of the plugin
        $vulnerabilities = json_decode($plugin->vulnerabilities);

        $vulnerabilityDetails = [];

        // Check each vulnerability
        foreach ($vulnerabilities as $vulnerability) {

            // check if the maximum version of vulnerability is set
            if(isset($vulnerability->operator->max_version)) {

                // Compare the vulnerability's max version with the plugin's version
                if(version_compare($currentVersion, $vulnerability->operator->max_version, '<')) {
                    // The plugin version is less than the max version of the vulnerability,
                    // so it's vulnerable, add to the array the name and the version affected
                    $vulnerabilityDetails[] = [
                        'name' => $vulnerability->name,
                        'max_version_affected' => $vulnerability->operator->max_version
                    ];
                }
            }
        }

        return [
            'has_vulnerability' => !empty($vulnerabilityDetails),
            'vulnerability_details' => $vulnerabilityDetails
        ];
    }

    public function check_vulnerability_database( array $vulnerability_array, string $version )
    {
        // Create an empty array to store the vulnerabilities.
	    $vulnerability = array();

        // If there are no vulnerabilities, return false.
        if ( empty( $vulnerability_array ) ) {
            return false;
        }

        // Loop through each vulnerability and check if it affects the specified version of the plugin.
        foreach ( $vulnerability_array as $v ) {

            // If the vulnerability has minimum and maximum versions, check if the specified version falls within that range.
            if ( isset( $v->operator->min_operator ) && $v->operator->min_operator && isset( $v->operator->max_operator ) && $v->operator->max_operator ) {
                
                if ( version_compare( $version, $v->operator->min_version, $v->operator->min_operator ) && version_compare( $version, $v->operator->max_version, $v->operator->max_operator ) ) {
                    
                    // Add the vulnerability to the array.
                    $vulnerability[] = array(
                        'name' => $v->name,
                        'description' => $v->description,
                        'versions' => $v->operator->min_operator . $v->operator->min_version . ' - ' .  $v->operator->max_operator  . $v->operator->max_version, 'strip' ,
                        'version' => $v->operator->min_version, 'strip',
                        'unfixed' => (int)$v->operator->unfixed,
                        'closed' => (int)$v->operator->closed,
                        'source' => $v->source,
                        'impact' => $v->impact,
                    );

                }

            // If the vulnerability has only a maximum version, check if the specified version is below that version.
            } elseif ( isset( $v->operator->max_operator ) && $v->operator->max_operator ) {

                if ( version_compare( $version, $v->operator->max_version, $v->operator->max_operator ) ) {

                    // Add the vulnerability to the list.
                    $vulnerability[] = array(
                        'name' => $v->name, 'strip' ,
                        'description' => $v->description,
                        'versions' => $v->operator->max_operator . $v->operator->max_version, 'strip',
                        'version' => $v->operator->max_version, 'strip',
                        'unfixed' => (int)$v->operator->unfixed,
                        'closed' => (int)$v->operator->closed,
                        'source' => $v->source,
                        'impact' => $v->impact,
                    );

                }

            // If the vulnerability has a minimum version and maximum version, check if the specified version is within that range.
            } elseif ( isset( $v->operator->min_operator ) && $v->operator->min_operator ) {

                if ( version_compare( $version, $v->operator->min_version, $v->operator->min_operator ) ) {

                    // Add the vulnerability to the list.
                    $vulnerability[] = array(
                        'name' => $v->name, 'strip' ,
                        'description' => $v->description,
                        'versions' => $v->operator->min_operator . $v->operator->min_version, 'strip',
                        'version' => $v->operator->min_version, 'strip',
                        'unfixed' => (int)$v->operator->unfixed,
                        'closed' => (int)$v->operator->closed,
                        'source' => $v->source,
                        'impact' => $v->impact,
                    );

                }

            }

        }

        return $vulnerability;
    }

    /**
     * ACtion to update the plugin
     *
     * @return void
     */
    public function updatePlugin( string $slug): Action
    {
        return Action::make('Update')
        ->requiresConfirmation()
        ->action(fn () => dd($slug));
    }

    public function deleteAction(): Action
    {
        Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => dd(12));
    }

    public function render(): View
    {
        return view('livewire.list-plugins'); 
    }
}