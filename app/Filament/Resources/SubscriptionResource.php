<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\Alignment; // لاستخدام Alignment::Right

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Subscriptions';

    // استخدام الدوال الثابتة للترجمة
    public static function getNavigationLabel(): string
    {
        return trans_db('subscriptions.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return trans_db('plans.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return trans_db('subscriptions.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('subscriptions.plural_model_label');
    }
    // نهاية الدوال الثابتة للترجمة

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            // تحديد اتجاه النص (RTL) للغة العربية
            ->columns(3)
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    // trans_db
                    ->label(trans_db('subscriptions.user'))
                    ->preload(),
                Forms\Components\Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->required()
                    // trans_db
                    ->label(trans_db('subscriptions.plan'))
                    ->preload(),
                Forms\Components\TextInput::make('balance')
                    ->required()
                    // trans_db
                    ->label(trans_db('subscriptions.balance'))
                    ->numeric(),
                Forms\Components\TextInput::make('remaining')
                    ->required()
                    // trans_db
                    ->label(trans_db('subscriptions.remaining'))
                    ->numeric(),
                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    // trans_db
                    ->label(trans_db('subscriptions.start_date')),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    // trans_db
                    ->label(trans_db('subscriptions.end_date')),
                Forms\Components\Select::make('status')
                    // trans_db
                    ->label(trans_db('subscriptions.status'))
                    ->options([
                        // trans_db
                        'active' => trans_db('subscriptions.status_active'),
                        // trans_db
                        'pending' => trans_db('subscriptions.status_pending'),
                        // trans_db
                        'expired' => trans_db('subscriptions.status_expired'),
                    ])
                    ->required(),
                Forms\Components\Toggle::make('auto_renew')
                    ->required()
                    // trans_db
                    ->label(trans_db('subscriptions.auto_renew')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    // trans_db
                    ->label(trans_db('subscriptions.user'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.name')
                    // trans_db
                    ->label(trans_db('subscriptions.plan'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance')
                    // trans_db
                    ->label(trans_db('subscriptions.balance'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining')
                    // trans_db
                    ->label(trans_db('subscriptions.remaining'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    // trans_db
                    ->label(trans_db('subscriptions.start_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    // trans_db
                    ->label(trans_db('subscriptions.end_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    // trans_db
                    ->label(trans_db('subscriptions.status'))
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'expired',
                    ])
                    ->formatStateUsing(fn (string $state): string => trans_db("subscriptions.status_{$state}")), // ترجمة حالة Badge
                Tables\Columns\IconColumn::make('auto_renew')
                    // trans_db
                    ->label(trans_db('subscriptions.auto_renew'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    // trans_db
                    ->label(trans_db('subscriptions.filter_status'))
                    ->options([
                        // trans_db
                        'active' => trans_db('subscriptions.status_active'),
                        // trans_db
                        'pending' => trans_db('subscriptions.status_pending'),
                        // trans_db
                        'expired' => trans_db('subscriptions.status_expired'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
