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
                Tables\Columns\ViewColumn::make('name')
                    ->label('Repository')
                    ->searchable(['name', 'remote'])
                    ->sortable()
                    ->view('filament.tables.columns.repo-name'),

                Tables\Columns\TextColumn::make('sites_count')
                    ->badge()
                    ->label('Connected Sites')
                    ->counts('sites')
                    ->alignment('center'),

                Tables\Columns\IconColumn::make('webhook')
                    ->label('Webhook')
                    ->boolean()
                    ->getStateUsing(fn (Repository $record): bool => !empty($record->webhook))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (Repository $record): string => $record->webhook ? 'Webhook configured' : 'No webhook set up')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('last_pull')
                    ->label('Last Deploy')
                    ->since()
                    ->placeholder('Never')
                    ->sortable(),
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
