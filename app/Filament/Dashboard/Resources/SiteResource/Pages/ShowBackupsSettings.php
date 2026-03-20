<?php

namespace App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;
use App\Models\User;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Pages\EditRecord;

use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;

use Filament\Forms;
use Filament\Schemas\Schema;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Illuminate\Contracts\View\View;


class ShowBackupsSettings extends EditRecord 
{
    protected static string $resource = SiteResource::class;

    protected string $view = 'site.single.backups_settings';

    public function getHeader(): ?View
    {
        return view('site.single.header');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
        ->schema([
            Forms\Components\Toggle::make('backup_enabled')
            ->label('Enable backup'),

            Fieldset::make('Backup schedule')
                ->schema([
                    Forms\Components\Select::make('db_schedule')
                        ->options([
                            '12hours' => 'Twice a Day',
                            'daily' => 'Daily',
                            'biweekly' => 'BiWeekly',
                            'weekly' => 'Weekly',
                            'monthly' => 'Monthly',
                        ]),
                    Forms\Components\Select::make('files_schedule')
                        ->options([
                            'daily' => 'Daily',
                            'biweekly' => 'BiWeekly',
                            'weekly' => 'Weekly',
                            'monthly' => 'Monthly',
                        ]),
                ])
                ->columns(2),
                // dd($this->record),

                Select::make('excluded_tables')
                    ->label('Exclude DB tables from the backup')
                    ->multiple()
                    ->options($this->getDBTables()),

                Forms\Components\Textarea::make('excluded_files')
                    ->rows(10)
                    ->cols(20)
            ]);
    }

    public function getDBTables()
    {
        $tablesJson = $this->record->sitemeta->db_tables;

        $tablesArray = json_decode($tablesJson);

        if (json_last_error() == JSON_ERROR_NONE) 
        {
            return $tableNames = array_reduce($tablesArray, function($carry, $table) {
                $carry[$table->Name] = $table->Name;
                return $carry;
            }, []);
        }
    }

    protected function getActions(): array
    {
        return [                    
            Actions\EditAction::make()
                ->label('Site Settings')
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
