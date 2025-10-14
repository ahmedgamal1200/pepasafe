<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    // استخدام الدوال لاسترداد القيم المترجمة
    protected static ?string $navigationGroup = 'Site Content'; // Default value before translation
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
        return trans_db('faq.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('faq.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('faq.plural_model_label');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show faq', // هذه المفاتيح يتم ترجمتها أيضاً في الـ seeder
            'add faq',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->label(trans_db('faq.question_label')) // استخدام trans_db
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('answer')
                    ->label(trans_db('faq.answer_label')) // استخدام trans_db
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->label(trans_db('faq.question_label')), // استخدام trans_db
                Tables\Columns\TextColumn::make('answer')
                    ->label(trans_db('faq.answer_label')), // استخدام trans_db
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
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
