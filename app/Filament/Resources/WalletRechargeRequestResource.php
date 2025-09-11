<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletRechargeRequestResource\Pages;
use App\Models\WalletRechargeRequest;
use App\Services\WalletRechargeRequestService;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;

class WalletRechargeRequestResource extends Resource
{
    protected static ?string $model = WalletRechargeRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = 'Requests';

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
            ->schema([
                Select::make('user_id')
                    ->label('User Name')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('plan_id')
                    ->label('الباقة')
                    ->relationship('plan', 'name')
                    ->required(),
                Select::make('subscription_id')
                    ->label('الاشتراك')
                    ->relationship('subscription', 'id')
                    ->required(),
                TextInput::make('amount')
                    ->label('قيمة الشحن')
                    ->numeric()
                    ->required(),
                FileUpload::make('receipt_path')
                    ->label('وصل الدفع')
                    ->directory('walletRechargeRequestReceipt')
                    ->preserveFilenames()
                    ->required(),
                Textarea::make('admin_note')
                    ->label('Rejection reason')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('plan.name'),
                Tables\Columns\TextColumn::make('amount'),
                ViewColumn::make('receipt_path')
                    ->label('Receipt')
                    ->view('filament.components.wallet-recharge-receipt')
                    ->url(null),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('admin_note'),
                Tables\Columns\TextColumn::make('approved_at'),
                Tables\Columns\TextColumn::make('reviewed_by'),
                //                Tables\Columns\TextColumn::make('subscription_id'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending' &&
                        static::canApprove()
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(WalletRechargeRequestService::class)->approve($record, Filament::auth()->id());
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending' &&
                        static::canReject()

                    )
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('admin_note')
                            ->label('سبب الرفض')
                            ->maxLength(255),
                        Toggle::make('send_email')
                            ->label('هل تريد إرسال سبب الرفض على البريد الإلكتروني؟')
                            ->default(false),
                    ])
                    ->action(function ($record, array $data) {
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
            'index' => Pages\ListWalletRechargeRequests::route('/'),
            'create' => Pages\CreateWalletRechargeRequest::route('/create'),
            'edit' => Pages\EditWalletRechargeRequest::route('/{record}/edit'),
        ];
    }
}
