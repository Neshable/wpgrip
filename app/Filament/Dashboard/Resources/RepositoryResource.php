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

use Closure;

use Filament\Tables\Columns\IconColumn;
use Filament\Facades\Filament;
use Filament\Tables\Columns\ImageColumn;

use Filament\Forms\Components\Section;

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

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Repository info')
                // ->description('Prevent abuse by limiting the number of requests per period')
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->helperText(new HtmlString('Type of repo'))  
                        ->required()
                        ->options([
                            'plugin' => 'Plugin',
                            'theme' => 'Theme',
                            'other' => 'Other'
                        ]),
                    Forms\Components\TextInput::make('name')
                        ->maxLength(255)
                        ->helperText(new HtmlString('Friendly name to find the repository faster.'))  
                        ->required(),
                    Forms\Components\TextInput::make('remote')
                        ->label('GIT Remote URL')
                        ->helperText(new HtmlString('<strong>The remote SSH URL</strong> of the repo ( e.g. git@bitbucket.org... ).'))  
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('provider')
                        ->label('Provider')
                        ->helperText(new HtmlString('Repository provider'))  
                        ->required()
                        ->options([
                            'bitbucket' => 'BitBucket',
                            'github' => 'GitHub'
                        ]),
                ])
                
                    // ->alpha(),
                // Forms\Components\TextInput::make('notes')
                //     ->helperText(new HtmlString('Additional notes about this repo.'))  
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('path')
                //     ->helperText(new HtmlString('<strong>Absolute server path.</strong> If the path doesn\'t exist, it will be created.'))  
                //     ->required()
                //     ->maxLength(255),
                
              
                // Forms\Components\Radio::make('provider')
                //     ->label('Provider')
                //     ->options([
                //         'bitbucket' => 'BitBucket',
                //         'github' => 'GitHub'
                //     ]),
                
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
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->icon(fn (string $state): string => match ($state) {
                        'plugin' => 'icon-plugins',
                        'theme' => 'icon-wordpress',
                        'other' => 'icon-wordpress',
                        default => 'icon-wordpress'
                    })
                    // ->description(fn (Repository $record): string => $record->site->url ? 'Site: ' . $record->site->url : 'Not connected' )
                    ->sortable(),

                Tables\Columns\TextColumn::make('remote')
                    ->label('Remote')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('provider')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bitbucket' => 'info',  // Bitbucket blue
                        'github' => 'gray',     // GitHub gray
                        default => 'primary'    // WordPress blue
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'bitbucket' => 'icon-bitbucket',
                        'github' => 'icon-github',
                        default => 'icon-wordpress'
                    }),

                // Tables\Columns\TextColumn::make('remote')
                //     ->description(fn (Repository $record): string => $record->branch ? 'On branch: ' . $record->branch : 'No branch selected')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('sites_count')
                    ->badge()
                    ->label('Connected Sites')
                    ->counts('sites'),
            ])
            ->recordUrl(
                fn (Repository $record): string => Pages\ViewRepository::getUrl([$record->id]),
            )
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->options([
                        'bitbucket' => 'BitBucket',
                        'github' => 'GitHub',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'plugin' => 'Plugin',
                        'theme' => 'Theme',
                        'other' => 'Other',
                    ]),
                Tables\Filters\Filter::make('has_sites')
                    ->query(fn (Builder $query): Builder => $query->has('sites'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_repository')
                    ->label('Repo Source')
                    ->icon('heroicon-o-link')
                    ->url(fn ($record) => match($record->provider) {
                        'github' => str_replace(['git@github.com:', '.git'], ['https://github.com/', ''], $record->remote),
                        'bitbucket' => str_replace(['git@bitbucket.org:', '.git'], ['https://bitbucket.org/', ''], $record->remote),
                        default => null,
                    })
                    ->openUrlInNewTab(),
            ])
            ->defaultPaginationPageOption(25)
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'view' => Pages\ViewRepository::route('/{record}'),
             // 'commits' => Pages\Commits::route('/{record}/commits'),
        ];
 
    }
}
