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
use App\Services\Plans\SubscriptionLimitChecker;
use Filament\Infolists\Components\Actions;
use Filament\Tables\Actions\Action;

use Filament\Forms\Components\Wizard;
use Illuminate\Support\HtmlString;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;

    public static function canViewAny(): bool
    {
        return SubscriptionLimitChecker::canUseGit();
    }

    public static function canCreate(): bool
    {
        return SubscriptionLimitChecker::canUseGit();
    }

    protected static ?string $navigationIcon = 'icon-git';

    protected static ?int $navigationSort = 4;

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
                    Forms\Components\ViewField::make('provider')
                        ->label('Provider')
                        ->view('filament.forms.components.provider-select')
                        ->required()
                        ->rule('in:bitbucket,github'),
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
                Tables\Columns\IconColumn::make('type')
                    ->label('')
                    ->icon(fn (string $state): string => match ($state) {
                        'plugin' => 'icon-plugins',
                        'theme' => 'icon-wordpress',
                        'other' => 'icon-wordpress',
                        default => 'icon-wordpress'
                    })
                    ->tooltip(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\IconColumn::make('provider')
                    ->label('')
                    ->icon(fn (string $state): string => match ($state) {
                        'bitbucket' => 'icon-bitbucket',
                        'github' => 'icon-github',
                        default => 'icon-wordpress'
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'bitbucket' => 'info',
                        'github' => 'gray',
                        default => 'primary'
                    })
                    ->tooltip(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Repository')
                    ->description(fn (Repository $record): string => $record->remote)
                    ->searchable(['name', 'remote'])
                    ->sortable(),

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
