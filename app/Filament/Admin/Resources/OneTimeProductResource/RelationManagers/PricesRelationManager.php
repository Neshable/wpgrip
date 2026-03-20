<?php

namespace App\Filament\Admin\Resources\OneTimeProductResource\RelationManagers;

use App\Models\Currency;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public function form(Schema $schema): Schema
    {
        $defaultCurrency = Currency::where('code', config('app.default_currency'))->first()->id;

        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make([
                    Forms\Components\TextInput::make('price')
                        ->required()
                        ->type('number')
                        ->gte(0)
                        ->helperText(__('Enter price in lowest denomination for a currency (cents). E.g. 1000 = 10.00')),
                    Forms\Components\Select::make('currency_id')
                        ->label('Currency')
                        ->options(
                            \App\Models\Currency::all()->sortBy('name')
                                ->mapWithKeys(function ($currency) {
                                    return [$currency->id => $currency->name.' ('.$currency->symbol.')'];
                                })
                                ->toArray()
                        )
                        ->default($defaultCurrency)
                        ->required()
                        ->unique(modifyRuleUsing: function (Unique $rule, \Filament\Forms\Get $get, RelationManager $livewire) {
                            return $rule->where('one_time_product_id', $livewire->ownerRecord->id)->ignore($get('id'));
                        })
                        ->preload(),

                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('price')
                    // divide by 100 to get price in dollars
                    ->formatStateUsing(function (string $state, $record) {
                        return money($state, $record->currency->code);
                    }),
                Tables\Columns\TextColumn::make('currency.name')
                    ->label('Currency'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ])->modelLabel(__('Price'));
    }
}
