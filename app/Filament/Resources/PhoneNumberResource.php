<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhoneNumberResource\Pages;
use App\Models\PhoneNumber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PhoneNumberResource extends Resource
{
    protected static ?string $model = PhoneNumber::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    // استخدام الدوال لاسترداد القيم المترجمة
    protected static ?string $navigationGroup = 'Site Content'; // Placeholder
    protected static ?string $navigationLabel = null;
    protected static ?string $modelLabel = null;
    protected static ?string $pluralModelLabel = null;

    public static function getNavigationGroup(): ?string
    {
        // استخدام المفتاح العام لمحتوى الموقع
        return trans_db('site_content.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return trans_db('phone_number.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('phone_number.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('phone_number.plural_model_label');
    }

    public static function canAccess(): bool
    {
        // هنا يمكنك استخدام مفاتيح الصلاحيات المترجمة إذا أردت
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show phone numbers', // سيتم ترجمة هذا المفتاح في seeder
            'add phone numbers',  // سيتم ترجمة هذا المفتاح في seeder
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('phone_number')
                    ->label(trans_db('phone_number.phone_label')) // استخدام trans_db
                    ->required()
                    ->maxLength(20),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone_number')
                    ->label(trans_db('phone_number.phone_label')), // استخدام trans_db
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
            'index' => Pages\ListPhoneNumbers::route('/'),
            'create' => Pages\CreatePhoneNumber::route('/create'),
            'edit' => Pages\EditPhoneNumber::route('/{record}/edit'),
        ];
    }
}
