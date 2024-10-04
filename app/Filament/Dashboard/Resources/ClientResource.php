<?php

namespace App\Filament\Dashboard\Resources;

use App\Filament\Dashboard\Resources\ClientResource\Pages;
use App\Filament\Dashboard\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    
    public static function form(Form $form): Form
    {
        return $form
		->schema([
            Forms\Components\TextInput::make('name')
                ->required(),
            Forms\Components\TextInput::make('email')
                ->required()
                ->email(),
            Forms\Components\TextInput::make('country')
                ->label('Country')
                ->required(),
            Forms\Components\Textarea::make('notes')
                ->label('Notes')
                ->autosize(),
			
		]);
    }

    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('country'),
                Tables\Columns\TextColumn::make('sites_count')->counts('sites')->badge()->label('Sites'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Add client'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
