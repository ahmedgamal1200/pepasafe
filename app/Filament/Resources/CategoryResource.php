<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Helpers\IconHelper;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    // استخدام مفاتيح ترجمة
    protected static ?string $navigationLabel = 'Categories.navigation_label';
    protected static ?string $title = 'Categories.title';

    // إضافة هذه الدوال لترجمة العناوين ديناميكياً
    public static function getNavigationLabel(): string
    {
        return trans_db(static::$navigationLabel);
    }

    public static function getModelLabel(): string
    {
        return trans_db('Categories.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('Categories.plural_model_label');
    }

    public static function getTitle(): string
    {
        return trans_db(static::$title);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show categories',
            'add categories',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    // استخدام مفتاح الترجمة
                    ->label(trans_db('Categories.type_label'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('icon')
                    // استخدام مفتاح الترجمة
                    ->label(trans_db('Categories.icon_label'))
                    ->options(IconHelper::all())
                    ->required()
                    ->allowHtml()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // استخدام مفتاح الترجمة
                TextColumn::make('type')
                    ->label(trans_db('Categories.type_label'))
                    ->searchable()
                    ->sortable(),
                ViewColumn::make('icon')
                    ->label(trans_db('Categories.icon_label'))
                    ->view('filament.tables.columns.icon'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(trans_db('Categories.edit_action')),
                Tables\Actions\DeleteAction::make()
                    ->label(trans_db('Categories.delete_action')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(trans_db('Categories.delete_bulk_action')),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

