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
use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Permission;

class UserResource extends Resource
{
    protected static ?string $model = User::class;


    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationGroup(): ?string
    {
        return trans_db('roles.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return trans_db('users.plural_model_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('users.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('users.plural_model_label');
    }

    public static function isRtl(): bool
    {
        return App::isLocale('ar');
    }
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
                    ->label(trans_db('users.name'))
                    ->required(fn (string $context) => $context === 'create')
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(trans_db('users.email'))
                    ->required(fn (string $context) => $context === 'create')
                    ->email()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->password()
                    ->label(trans_db('users.password'))
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),

                TextInput::make('password_confirmation')
                    ->password()
                    ->label(trans_db('users.password_confirmation'))
                    ->required(fn (string $context) => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->maxLength(255),
                TextInput::make('phone')
                    ->required()
                    ->unique(ignoreRecord: true),

                Select::make('category_id')
                    ->label(trans_db('users.category'))
                    ->options(Category::pluck('type', 'id'))
                    ->searchable(),

                Select::make('role_id')
                    ->label(trans_db('users.assign_role'))
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
                    ->label(trans_db('users.direct_permissions'))
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
                TextColumn::make('name')->sortable()
                    ->label(trans_db('users.name')),
                TextColumn::make('email')->searchable()
                    ->label(trans_db('users.email')),
                TextColumn::make('phone')->searchable()
                    ->label(trans_db('users.phone')),
                TextColumn::make('category.type')
                    ->label(trans_db('users.category'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label(trans_db('users.roles'))
                    ->badge()
                    ->separator(', ')
                    ->searchable(),

                TextColumn::make('computed_permissions')
                    ->label(trans_db('users.permissions'))
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->getAllPermissions()->pluck('name')->toArray())
                    ->separator(', ')
                    ->tooltip(fn ($record) => $record->getAllPermissions()->pluck('name')->join(', ')),

                TextColumn::make('created_at')->searchable()
                    ->label(trans_db('users.created_at')),
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
