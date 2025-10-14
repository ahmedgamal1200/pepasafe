<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficialEmailResource\Pages;
use App\Models\OfficialEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfficialEmailResource extends Resource
{
    protected static ?string $model = OfficialEmail::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

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
        return trans_db('official_email.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('official_email.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('official_email.plural_model_label');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show official emails', // Permission key
            'add official emails',  // Permission key
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->label(trans_db('official_email.email_label')), // استخدام trans_db
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label(trans_db('official_email.email_label')), // استخدام trans_db
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

