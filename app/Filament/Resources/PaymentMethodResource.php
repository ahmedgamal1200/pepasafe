<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    // استخدام مفاتيح الترجمة
    protected static ?string $navigationLabel = 'PaymentMethods.navigation_label';
    protected static ?string $modelLabel = 'PaymentMethods.model_label';
    protected static ?string $pluralModelLabel = 'PaymentMethods.plural_model_label';

    // دوال لترجمة العناوين ديناميكياً
    public static function getNavigationLabel(): string
    {
        return trans_db(static::$navigationLabel);
    }

    public static function getModelLabel(): string
    {
        return trans_db(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db(static::$pluralModelLabel);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show all payment methods',
        ]);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'create new payment method',
        ]);
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'edit all payment methods',
        ]);
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'delete payment method',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    // استخدام مفتاح الترجمة للـ Label
                    ->label(trans_db('PaymentMethods.key_label'))
                    ->maxLength(255)
                    // استخدام مفتاح الترجمة للـ Placeholder
                    ->placeholder(trans_db('PaymentMethods.key_placeholder')),
                Forms\Components\TextInput::make('value')
                    ->required()
                    // استخدام مفتاح الترجمة للـ Label
                    ->label(trans_db('PaymentMethods.value_label'))
                    ->maxLength(255)
                    // استخدام مفتاح الترجمة للـ Placeholder
                    ->placeholder(trans_db('PaymentMethods.value_placeholder')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    // استخدام مفتاح الترجمة لعنوان العمود
                    ->label(trans_db('PaymentMethods.key_label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    // استخدام مفتاح الترجمة لعنوان العمود
                    ->label(trans_db('PaymentMethods.value_label')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    // استخدام مفتاح الترجمة للإجراء
                    ->label(trans_db('PaymentMethods.edit_action')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        // استخدام مفتاح الترجمة للإجراء الجماعي
                        ->label(trans_db('PaymentMethods.delete_bulk_action')),
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
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
