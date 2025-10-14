<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    // استخدام الدوال الثابتة للترجمة
    public static function getNavigationGroup(): ?string
    {
        return trans_db('roles.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return trans_db('roles.plural_model_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('roles.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('roles.plural_model_label');
    }
    // نهاية الدوال الثابتة للترجمة

    public static function canAccess(): bool
    {
        return auth()->user()?->can('full access');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('permissions');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    // استخدام trans_db
                    ->label(trans_db('roles.role_name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn ($state) => strtolower($state)),

                MultiSelect::make('permissions')
                    ->relationship('permissions', 'name')
                    // استخدام trans_db
                    ->label(trans_db('roles.permissions'))
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    // استخدام trans_db
                    ->label(trans_db('roles.role_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('permissions.name')
                    // استخدام trans_db
                    ->label(trans_db('roles.permissions'))
                    ->badge()
                    ->separator(', ')
                    ->limit(3)
                    ->tooltip(fn ($record) => $record->permissions->pluck('name')->join(', ')),
                TextColumn::make('created_at')
                    // استخدام trans_db
                    ->label(trans_db('roles.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            // تفعيل RTL للعربية
            // تفعيل RTL للعربية
            ->modifyQueryUsing(fn ($query) => $query)
            ->defaultSort('id', 'asc');
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}

