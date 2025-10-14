<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletRechargeRequestResource\Pages;
use App\Models\WalletRechargeRequest;
use App\Services\WalletRechargeRequestService; // الافتراض بأن الخدمة موجودة
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\Alignment;
use Filament\Facades\Filament; // لجلب معلومات المستخدم الحالي

class WalletRechargeRequestResource extends Resource
{
    protected static ?string $model = WalletRechargeRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    // استخدام الدوال الثابتة للترجمة
    public static function getNavigationGroup(): ?string
    {
        return trans_db('wallet_recharge_requests.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return trans_db('wallet_recharge_requests.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('wallet_recharge_requests.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('wallet_recharge_requests.plural_model_label');
    }

    // نهاية الدوال الثابتة للترجمة

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show wallet recharge requests',
        ]);
    }

    public static function canApprove(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'approve requests for wallet recharge requests',
            'full access',
        ]);
    }

    public static function canReject(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'reject requests for wallet recharge requests',
            'full access',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            // تحديد اتجاه النص (RTL) للغة العربية
            ->columns(3)
            ->schema([
                Select::make('user_id')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.user_name'))
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('plan_id')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.plan_name'))
                    ->relationship('plan', 'name')
                    ->required(),
                Select::make('subscription_id')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.subscription'))
                    ->relationship('subscription', 'id')
                    ->required(),
                TextInput::make('amount')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.amount'))
                    ->numeric()
                    ->required(),
                FileUpload::make('receipt_path')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.receipt_path'))
                    ->directory('walletRechargeRequestReceipt')
                    ->preserveFilenames()
                    ->required(),
                Textarea::make('admin_note')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.admin_note'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.user_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('plan.name')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.plan_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.amount'))
                    ->numeric()
                    ->sortable(),
                ViewColumn::make('receipt_path')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.receipt_path'))
                    ->view('filament.components.wallet-recharge-receipt')
                    ->url(null),
                Tables\Columns\TextColumn::make('status')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.status'))
                    // ترجمة الحالة مباشرة
                    ->formatStateUsing(fn (string $state): string => trans_db("wallet_recharge_requests.status_{$state}")),
                Tables\Columns\TextColumn::make('admin_note')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.admin_note'))
                    ->limit(50),
                Tables\Columns\TextColumn::make('approved_at')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.approved_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reviewer.name') // تم افتراض وجود علاقة 'reviewer' باسم حقل 'reviewed_by'
                // trans_db
                ->label(trans_db('wallet_recharge_requests.reviewed_by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('approve')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending' &&
                        static::canApprove()
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // الافتراض أن WalletRechargeRequestService موجود
                        app(WalletRechargeRequestService::class)->approve($record, Filament::auth()->id());
                    }),

                Action::make('reject')
                    // trans_db
                    ->label(trans_db('wallet_recharge_requests.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending' &&
                        static::canReject()
                    )
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('admin_note')
                            // trans_db
                            ->label(trans_db('wallet_recharge_requests.reject_reason_prompt'))
                            ->maxLength(255),
                        Toggle::make('send_email')
                            // trans_db
                            ->label(trans_db('wallet_recharge_requests.send_email_toggle'))
                            ->default(false),
                    ])
                    ->action(function ($record, array $data) {
                        // الافتراض أن WalletRechargeRequestService موجود
                        app(WalletRechargeRequestService::class)->reject(
                            $record,
                            $data['admin_note'],
                            $data['send_email']
                        );
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyPermission([
                        'edit wallets recharge requests',
                        'full access',
                    ])),

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
            'index' => Pages\ListWalletRechargeRequests::route('/'),
            'create' => Pages\CreateWalletRechargeRequest::route('/create'),
            'edit' => Pages\EditWalletRechargeRequest::route('/{record}/edit'),
        ];
    }
}
