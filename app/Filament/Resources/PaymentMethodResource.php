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
                    ->label('Name Of Payment Method')
                    ->maxLength(255)
                    ->placeholder('Like Bink Account Or VodCash'),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->label('Value Of Payment Method')
                    ->maxLength(255)
                    ->placeholder('123456789123456'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->searchable(),
                Tables\Columns\TextColumn::make('value'),
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
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
