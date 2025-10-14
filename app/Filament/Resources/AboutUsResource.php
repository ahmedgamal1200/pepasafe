<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutUsResource\Pages;
use App\Models\AboutUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AboutUsResource extends Resource
{
    protected static ?string $model = AboutUs::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    // يتم تعيين العناوين وتجميع التنقل عبر الدوال لضمان استخدام الترجمة
    protected static ?string $navigationGroup = 'Site Content';
    protected static ?string $navigationLabel = null;
    protected static ?string $modelLabel = null;
    protected static ?string $pluralModelLabel = null;

    public static function getNavigationGroup(): ?string
    {
        return trans_db('site_content.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return trans_db('about_us.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('about_us.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('about_us.plural_model_label');
    }


    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show about us', // سيتم ترجمة هذا المفتاح في seeder
            'add about us',  // سيتم ترجمة هذا المفتاح في seeder
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('description')
                    ->label(trans_db('about_us.description_label')) // ترجمة تسمية الحقل
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(trans_db('about_us.description_label')), // ترجمة رأس العمود
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
            'index' => Pages\ListAboutUs::route('/'),
            'create' => Pages\CreateAboutUs::route('/create'),
            'edit' => Pages\EditAboutUs::route('/{record}/edit'),
        ];
    }
}
