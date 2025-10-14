<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranslationKeyResource\Pages;
use App\Models\TranslationKey;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class TranslationKeyResource extends Resource
{
    protected static ?string $model = TranslationKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    // الدوال الثابتة للترجمة
    public static function getNavigationLabel(): string
    {
        return trans_db('translation_keys.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return trans_db('translation_keys.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return trans_db('translation_keys.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('translation_keys.plural_model_label');
    }

    // إضافة isRtl لدعم اتجاه النص العربي في الجدول
    public static function isRtl(): bool
    {
        return App::isLocale('ar');
    }
    // نهاية الدوال الثابتة للترجمة

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('key')
                    ->label(trans_db('translation_keys.key_label')) // ترجمة
                    ->required()
                    ->unique()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label(trans_db('translation_keys.key_column'))->searchable(), // ترجمة
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
            ->defaultSort('id', 'desc');
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
            'index' => Pages\ListTranslationKeys::route('/'),
            'create' => Pages\CreateTranslationKey::route('/create'),
            'edit' => Pages\EditTranslationKey::route('/{record}/edit'),
        ];
    }
}
