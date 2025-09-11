<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranslationKeyResource\Pages;
use App\Models\TranslationKey;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TranslationKeyResource extends Resource
{
    protected static ?string $model = TranslationKey::class;

    protected static ?string $navigationLabel = 'Translation keys';

    protected static ?string $navigationGroup = 'Translation settings';

    protected static ?string $navigationIcon = 'heroicon-o-key';

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
                    ->label('Key (مثلاً: document.title.default)')
                    ->required()
                    ->unique()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('Key')->searchable(),
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
            'index' => Pages\ListTranslationKeys::route('/'),
            'create' => Pages\CreateTranslationKey::route('/create'),
            'edit' => Pages\EditTranslationKey::route('/{record}/edit'),
        ];
    }
}
