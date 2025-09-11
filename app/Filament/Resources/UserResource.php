<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Category;
use App\Models\User;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Access Control';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'add own users',
            'show own users',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(fn (string $context) => $context === 'create')
                    ->maxLength(255),
                TextInput::make('email')
                    ->required(fn (string $context) => $context === 'create')
                    ->email()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),

                TextInput::make('password_confirmation')
                    ->password()
                    ->label('Password Confirmation')
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),
                TextInput::make('phone')
                    ->required()
                    ->unique(ignoreRecord: true),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('type', 'id'))
                    ->searchable(),

                Select::make('role_id')
                    ->label('Assign Role')
                    ->options(\App\Models\Role::pluck('name', 'id'))
                    ->searchable()
                    ->native(false)
                    ->required(fn (string $context) => $context === 'create')
                    ->afterStateHydrated(function ($state, $set, $record) {
                        // لما تفتح الصفحة، خلي الفيلمنت يحط الرول الحالي
                        if ($record) {
                            $set('role_id', $record->roles->pluck('id')->first());
                        }
                    })
                    ->afterStateUpdated(function ($state, $set, $get, $record) {
                        if ($record) {
                            $role = \Spatie\Permission\Models\Role::find($state);
                            if ($role) {
                                $record->syncRoles([$role->name]);
                            }
                        }
                    }),

                MultiSelect::make('permissions')
                    ->label('Direct Permissions')
                    ->options(fn () => Permission::all()->pluck('name', 'id'))
                    ->preload()
                    ->searchable()
                    ->required(fn (string $context) => $context === 'create')
                    ->afterStateHydrated(function ($set, $record) {
                        if ($record) {
                            $set('permissions', $record->getDirectPermissions()->pluck('id')->toArray());
                        }
                    })
                    ->dehydrated()
                    ->saveRelationshipsUsing(function ($record, $state) {
                        // مباشرة: بجيبهم بالـ id مش name، وبالتالي مفيش مشكلة هنا
                        $permissionNames = Permission::whereIn('id', $state)->pluck('name')->toArray();
                        $record->syncPermissions($permissionNames);
                    }),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('category.type')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(', ')
                    ->searchable(),

                TextColumn::make('computed_permissions')
                    ->label('Permissions')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->getAllPermissions()->pluck('name')->toArray())
                    ->separator(', ')
                    ->tooltip(fn ($record) => $record->getAllPermissions()->pluck('name')->join(', ')),

                TextColumn::make('created_at')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['roles', 'permissions', 'category']);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
