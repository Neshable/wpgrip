<?php

namespace App\Filament\Dashboard\Resources;

use App\Filament\Dashboard\Resources\RepositoryResource\Pages;
use App\Filament\Dashboard\Resources\RepositoryResource\RelationManagers;
use App\Models\Repository;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use Filament\Tables\Columns\IconColumn;
use Filament\Facades\Filament;
use Filament\Tables\Columns\ImageColumn;

use Filament\Notifications\Notification;

use App\Jobs\Git\SshAndGitPull;
use Filament\Infolists\Components\Actions;
use Filament\Tables\Actions\Action;

use Filament\Forms\Components\Wizard;
use Illuminate\Support\HtmlString;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;

    protected static ?string $navigationIcon = 'icon-git';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->helperText(new HtmlString('Friendly name to find the repository faster.'))  
                    ->required(),
                    // ->alpha(),
                // Forms\Components\TextInput::make('notes')
                //     ->helperText(new HtmlString('Additional notes about this repo.'))  
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('path')
                //     ->helperText(new HtmlString('<strong>Absolute server path.</strong> If the path doesn\'t exist, it will be created.'))  
                //     ->required()
                //     ->maxLength(255),
                Forms\Components\TextInput::make('remote')
                    ->label('GIT Remote URL')
                    ->helperText(new HtmlString('<strong>The remote SSH URL</strong> of the repo ( e.g. git@bitbucket.org... ).'))  
                    ->required()
                    ->maxLength(255),
              
                // Forms\Components\Radio::make('provider')
                //     ->label('Provider')
                //     ->options([
                //         'bitbucket' => 'BitBucket',
                //         'github' => 'GitHub'
                //     ]),
                Forms\Components\Select::make('provider')
                    ->label('Provider')
                    ->helperText(new HtmlString('Repository provider'))  
                    ->required()
                    ->options([
                        'bitbucket' => 'BitBucket',
                        'github' => 'GitHub'
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    // ->description(fn (Repository $record): string => $record->site->url ? 'Site: ' . $record->site->url : 'Not connected' )
                    ->sortable(),

                Tables\Columns\TextColumn::make('remote')
                    ->label('Remote')
                    ->searchable()
                    // ->description(fn (Repository $record): string => $record->site->url ? 'Site: ' . $record->site->url : 'Not connected' )
                    ->sortable(),
                    Tables\Columns\TextColumn::make('provider')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'bitbucket' => 'icon-bitbucket',
                        'github' => 'icon-github'
                    }),

                // Tables\Columns\TextColumn::make('remote')
                //     ->description(fn (Repository $record): string => $record->branch ? 'On branch: ' . $record->branch : 'No branch selected')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('last_pull')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'draft' => 'gray',
                    'problems' => 'warning',
                    'active' => 'success',
                    'disconnected' => 'danger',
                    default =>'gray'
                }),
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
            'index' => Pages\ListRepositories::route('/'),
            'create' => Pages\CreateRepository::route('/create'),
            'edit' => Pages\EditRepository::route('/{record}/edit'),
        ];
    }
}
