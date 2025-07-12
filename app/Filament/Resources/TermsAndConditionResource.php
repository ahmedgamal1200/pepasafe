<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TermsAndConditionResource\Pages;
use App\Filament\Resources\TermsAndConditionResource\RelationManagers;
use App\Models\TermsAndCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TermsAndConditionResource extends Resource
{
    protected static ?string $model = TermsAndCondition::class;
    protected static ?string $navigationGroup = 'Site Content';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'add terms-and-conditions',
            'full access',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('content')
                    ->label('Terms & Conditions')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content'),
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
            'index' => Pages\ListTermsAndConditions::route('/'),
            'create' => Pages\CreateTermsAndCondition::route('/create'),
            'edit' => Pages\EditTermsAndCondition::route('/{record}/edit'),
        ];
    }
}
