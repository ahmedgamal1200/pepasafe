<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranslationValueResource\Pages;
use App\Filament\Resources\TranslationValueResource\RelationManagers;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TranslationValueResource extends Resource
{
    protected static ?string $model = TranslationValue::class;

    protected static ?string $navigationLabel = 'Translation';
    protected static ?string $navigationGroup = 'Translation settings';
    protected static ?string $navigationIcon = 'heroicon-o-language';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'add translation values',
            'full access',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('translation_key_id')
                    ->label('Translation Key')
                    ->options(fn () => TranslationKey::all()->pluck('key', 'id'))
                    ->searchable()
                    ->required(),

                        TextInput::make('locale')
                            ->required()
                            ->label('Locale (مثلاً ar أو en)'),
                        TextInput::make('value')
                            ->required()
                            ->label('Value'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('key.key')->label('Translation Key'),
                Tables\Columns\TextColumn::make('locale')->label('اللغة'),
                Tables\Columns\TextColumn::make('value')->label('النص'),

            //                Tables\Columns\TextColumn::make('key')->searchable(),
//
//                Tables\Columns\TextColumn::make('values.value')
//                    ->label('Translations')
//                    ->formatStateUsing(fn ($state, $record) =>
//                    $record->values
//                        ->pluck('locale', 'value')
//                        ->map(fn($locale, $val) => "$locale: $val")
//                        ->implode(' | ')
//                    ),
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
            'index' => Pages\ListTranslationValues::route('/'),
            'create' => Pages\CreateTranslationValue::route('/create'),
            'edit' => Pages\EditTranslationValue::route('/{record}/edit'),
        ];
    }
}
