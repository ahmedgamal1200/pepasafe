<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TermsAndConditionResource\Pages;
use App\Models\TermsAndCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TermsAndConditionResource extends Resource
{
    protected static ?string $model = TermsAndCondition::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

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
        return trans_db('terms_and_conditions.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('terms_and_conditions.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('terms_and_conditions.plural_model_label');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'add terms and conditions',
            'show terms and conditions', // تمت إضافته للاتساق مع نمط الموارد الأخرى
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('content')
                    ->label(trans_db('terms_and_conditions.content_label')) // استخدام trans_db
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content')
                    ->label(trans_db('terms_and_conditions.content_label_short')), // استخدام تسمية قصيرة للعمود
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
