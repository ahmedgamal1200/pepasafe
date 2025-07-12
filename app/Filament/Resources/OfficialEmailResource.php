<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficialEmailResource\Pages;
use App\Filament\Resources\OfficialEmailResource\RelationManagers;
use App\Models\OfficialEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfficialEmailResource extends Resource
{
    protected static ?string $model = OfficialEmail::class;

    protected static ?string $navigationGroup = 'Site Content';

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show official emails',
            'add official emails',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->label('Official Email'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email'),
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
            'index' => Pages\ListOfficialEmails::route('/'),
            'create' => Pages\CreateOfficialEmail::route('/create'),
            'edit' => Pages\EditOfficialEmail::route('/{record}/edit'),
        ];
    }
}
