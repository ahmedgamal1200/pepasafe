<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;


class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('full access',);
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
                    ->label('Role Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn ($state) => strtolower($state)),

                MultiSelect::make('permissions')
                    ->relationship('permissions', 'name')
                    ->label('Assign Permissions')
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Role Name'),
                TextColumn::make('permissions.name')
                    ->label('Permissions')
                    ->badge()
                    ->separator(', ')
                    ->limit(3)
                    ->tooltip(fn ($record) => $record->permissions->pluck('name')->join(', ')),
                TextColumn::make('created_at')->label('Created')->dateTime(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
