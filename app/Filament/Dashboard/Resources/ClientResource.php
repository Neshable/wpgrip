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

    protected static ?int $navigationSort = 5;

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
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->searchable()
                        ->weight(\Filament\Support\Enums\FontWeight::Bold)
                        ->icon('heroicon-m-user')
                        ->size(Tables\Columns\TextColumn\TextColumnSize::Large),
                    
                    Tables\Columns\TextColumn::make('email')
                        ->icon('heroicon-m-envelope')
                        ->color('gray')
                        ->copyable(),
                        
                    Tables\Columns\TextColumn::make('country')
                        ->icon('heroicon-m-globe-alt')
                        ->color('gray'),

                    Tables\Columns\TextColumn::make('sites_count')
                        ->counts('sites')
                        ->badge()
                        ->color('primary')
                        ->formatStateUsing(fn ($state) => $state . ' Connected Sites'),
                ])->space(3),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
