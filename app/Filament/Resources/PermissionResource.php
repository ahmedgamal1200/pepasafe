<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use Spatie\Permission\Models\Permission;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\Alignment;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 10;

    // استخدام الدوال الثابتة للترجمة
    public static function getNavigationGroup(): ?string
    {
        // استخدام المفتاح الموجود مسبقًا في RoleResource
        return trans_db('roles.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return trans_db('permissions.plural_model_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('permissions.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('permissions.plural_model_label');
    }
    // نهاية الدوال الثابتة للترجمة

    public static function canAccess(): bool
    {
        return auth()->user()?->can('full access');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    // استخدام trans_db
                    ->label(trans_db('permissions.permission_name'))
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn ($state) => strtolower($state))
                    ->autocomplete(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    // استخدام trans_db
                    ->label(trans_db('permissions.permission_name'))
                    ->searchable(),
                TextColumn::make('created_at')
                    // استخدام trans_db
                    ->label(trans_db('permissions.created_at'))
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
