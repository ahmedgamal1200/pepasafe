<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanUpgradeRequestResource\Pages;
use App\Filament\Resources\PlanUpgradeRequestResource\RelationManagers;
use App\Models\PlanUpgradeRequest;
use App\Services\PlanUpgradeRequestService;
use App\Services\WalletRechargeRequestService;
use Filament\Facades\Filament;
use Filament\Forms;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanUpgradeRequestResource extends Resource
{
    protected static ?string $model = PlanUpgradeRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Requests';


    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show plan upgrade requests',
        ]);
    }
    public static function canApprove(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'approve requests for plan upgrade requests',
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
                FileUpload::make('receipt_path')
                    ->label('وصل الدفع')
                    ->directory('PlanUpgradeRequestReceipt')
                    ->preserveFilenames()
                    ->required(),
                Textarea::make('rejected_reason')
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
                ViewColumn::make('receipt_path')
                    ->label('Receipt')
                    ->view('filament.components.plan-upgrade-request-receipt')
                    ->url(null),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('rejected_reason'),
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
                    ->visible(fn($record) => $record->status === 'pending' &&
                        static::canApprove()
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(PlanUpgradeRequestService::class)->approve($record);
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'pending' &&
                        static::canReject()

                    )
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('rejected_reason')
                            ->label('سبب الرفض')
                            ->maxLength(255),
                        Toggle::make('send_email')
                            ->label('هل تريد إرسال سبب الرفض على البريد الإلكتروني؟')
                            ->default(false),
                    ])
                    ->action(function ($record, array $data) {
                        app(PlanUpgradeRequestService::class)->reject(
                            $record,
                            $data['rejected_reason'],
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
            'index' => Pages\ListPlanUpgradeRequests::route('/'),
            'create' => Pages\CreatePlanUpgradeRequest::route('/create'),
            'edit' => Pages\EditPlanUpgradeRequest::route('/{record}/edit'),
        ];
    }
}
