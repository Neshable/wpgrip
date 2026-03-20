<?php

namespace App\Filament\Dashboard\Resources\ServerResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Models\Site;
use App\Filament\Dashboard\Resources\SiteResource\Pages;

class SitesRelationManager extends RelationManager
{
    protected static string $relationship = 'sites';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                    Tables\Columns\ImageColumn::make('')
                        ->width(35)
                        ->height(35)
                        ->defaultImageUrl(url('/images/wordpress.svg')),
                    Tables\Columns\ViewColumn::make('name')
                        ->searchable()
                        ->view('filament.tables.columns.sitename'),
                    Tables\Columns\ViewColumn::make('stack')
                        ->label('Stack')
                        ->view('filament.tables.columns.stack'),
                    Tables\Columns\ViewColumn::make('performance')->view('filament.tables.columns.sitespeed'),
                    
        

                    // Tables\Columns\TextColumn::make('server.provider')
                    //     ->label('Hosted')
                    //     ->badge(),
                
                    // Tables\Columns\TextColumn::make('wp_ver')
                    //     ->icon('icon-wordpress')
                    //     ->weight(FontWeight::Bold)
                    //     ->label('WP'),
                    // Tables\Columns\TextColumn::make('php_ver')
                    //     ->weight(FontWeight::Bold)
                    //     ->label('PHP'),
                    
                    // Tables\Columns\ViewColumn::make('stack')->view('filament.tables.columns.stackinfo'), 
                    // Tables\Columns\ViewColumn::make('PHP')
                    //     ->label('PHP')
                    //     ->view('filament.tables.columns.phpversion'),  
                    // Tables\Columns\TextColumn::make('client.name')->label('Owner')->sortable(),
                    // Tables\Columns\IconColumn::make('status')
                    //     ->icon(fn (string $state): string => match ($state) {
                    //         'draft' => 'heroicon-o-pencil',
                    //         'reviewing' => 'heroicon-o-clock',
                    //         'published' => 'heroicon-o-check-circle',
                    //     }),
                    // Tables\Columns\TextColumn::make('url')->label('URL'),
                    // Tables\Columns\ToggleColumn::make('uptime_monitor'),
                    // Tables\Columns\ToggleColumn::make('active_webhook_slack')->label('Slack Notifications'),
                    // Tables\Columns\TextColumn::make('backups_count')
                    //     ->counts('backups')
                    //     ->label('Backups'),
                
                
                    // Tables\Columns\TextColumn::make('php_ver')->label('PHP'),
                

                    Tables\Columns\TextColumn::make('last_sync')
                        ->label('Last sync')
                        ->since(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\ViewAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->heading('All available websites hosted on this server.')
            ->description('')
            // ->headerActions([
            //     Action::make('create')
            //     ])
            ->recordUrl(
                fn (Site $record): string => Pages\ViewSite::getUrl([$record->id]),
            )
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
