<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranslationValueResource\Pages;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class TranslationValueResource extends Resource
{
    protected static ?string $model = TranslationValue::class;

    protected static ?string $navigationLabel = 'Translation';

    protected static ?string $navigationGroup = 'Translation settings';

    protected static ?string $navigationIcon = 'heroicon-o-language';

    // الدوال الثابتة للترجمة
    public static function getNavigationLabel(): string
    {
        return trans_db('translation.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return trans_db('translation_keys.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return trans_db('translation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('translation.model_label');
    }



    // إضافة isRtl لدعم اتجاه النص العربي في الجدول
    public static function isRtl(): bool
    {
        return App::isLocale('ar');
    }

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
                    ->label(trans_db('translation_keys.key_column'))
                    ->options(fn () => TranslationKey::all()->pluck('key', 'id'))
                    ->searchable()
                    ->required(),

                TextInput::make('locale')
                    ->required()
                    ->label (trans_db('translation.local')),
                Textarea::make('value')
                    ->required()
                    ->label(trans_db('translation.content')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('key.key')->label('Translation Key')->searchable()
                    ->label(trans_db('translation_keys.key_column')),
                Tables\Columns\TextColumn::make('locale')->label (trans_db('translation.local')),
                Tables\Columns\TextColumn::make('value')->label(trans_db('translation.content'))->searchable(),
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
