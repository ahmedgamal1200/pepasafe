<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrivacyPolicyResource\Pages;
use App\Models\PrivacyPolicy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PrivacyPolicyResource extends Resource
{
    protected static ?string $model = PrivacyPolicy::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

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
        return trans_db('privacy_policy.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('privacy_policy.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('privacy_policy.plural_model_label');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            // استخدام مفاتيح صلاحيات موحدة ونظيفة
            'full access',
            'add privacy policy',
            'show privacy policy', // تمت إضافته للاتساق مع نمط الموارد الأخرى
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('content')
                    ->label(trans_db('privacy_policy.content_label')) // استخدام trans_db
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content')
                    ->label(trans_db('privacy_policy.content_label_short')), // استخدام تسمية قصيرة للعمود
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
            'index' => Pages\ListPrivacyPolicies::route('/'),
            'create' => Pages\CreatePrivacyPolicy::route('/create'),
            'edit' => Pages\EditPrivacyPolicy::route('/{record}/edit'),
        ];
    }
}
