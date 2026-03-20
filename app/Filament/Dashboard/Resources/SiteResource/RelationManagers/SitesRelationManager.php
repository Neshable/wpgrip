<?php

namespace App\Filament\Dashboard\Resources\SiteResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Facades\Filament;

use App\Models\Site;
use App\Filament\Dashboard\Resources\SiteResource\Pages;

use App\Filament\Dashboard\Resources\SiteResource;

class SitesRelationManager extends RelationManager
{
    protected static string $relationship = 'sites';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('url')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('url')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()))
            ->deferLoading()
            ->columns( SiteResource::inputTable() )
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                // Actions\EditAction::make(),
                // Actions\DeleteAction::make(),
            ])
            ->heading('All available websites')
            ->description('Sites hosted on this server')
            // ->headerActions([
            //     Action::make('create')
            //     ])
            ->recordUrl(
                fn (Site $record): string => Pages\ViewSite::getUrl([$record->id]),
            )
            ->emptyStateHeading('No sites found')
            ->emptyStateDescription('You haven\'t added any website yet.')
            ->bulkActions([
            
            ]);
    }
}
