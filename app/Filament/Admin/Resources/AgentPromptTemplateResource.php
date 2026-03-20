<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AgentPromptTemplateResource\Pages;
use App\Models\AgentPromptTemplate;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AgentPromptTemplateResource extends Resource
{
    protected static ?string $model = AgentPromptTemplate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string | \UnitEnum | null $navigationGroup = 'AI Settings';

    protected static ?string $navigationLabel = 'Prompt Templates';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Template Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL-friendly identifier. Use lowercase with hyphens.'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(2),

                        Forms\Components\Toggle::make('is_default')
                            ->label('Default Template')
                            ->helperText('Only one template can be the default. Setting this will unset others.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Prompt Content')
                    ->schema([
                        Forms\Components\MarkdownEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Available variables: {{site_name}}, {{site_url}}, {{site_snapshot}}'),
                    ]),

                Forms\Components\Section::make('Variables Reference')
                    ->schema([
                        Forms\Components\KeyValue::make('variables')
                            ->keyLabel('Variable')
                            ->valueLabel('Description')
                            ->addActionLabel('Add variable')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->label('Default'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgentPromptTemplates::route('/'),
            'create' => Pages\CreateAgentPromptTemplate::route('/create'),
            'edit' => Pages\EditAgentPromptTemplate::route('/{record}/edit'),
        ];
    }
}
