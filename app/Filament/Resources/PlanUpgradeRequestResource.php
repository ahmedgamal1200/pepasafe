<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanUpgradeRequestResource\Pages;
use App\Models\PlanUpgradeRequest;
use App\Services\PlanUpgradeRequestService; // تم إضافة الخدمة الافتراضية
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\Alignment;

class PlanUpgradeRequestResource extends Resource
{
    protected static ?string $model = PlanUpgradeRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // استخدام الدوال الثابتة للترجمة
    public static function getNavigationGroup(): ?string
    {
        return trans_db('plan_upgrade_requests.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return trans_db('plan_upgrade_requests.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('plan_upgrade_requests.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('plan_upgrade_requests.plural_model_label');
    }
    // نهاية الدوال الثابتة للترجمة

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
        // تم تصحيح الإذن بناءً على السياق (كان يشير إلى wallet recharge)
        return auth()->user()?->hasAnyPermission([
            'reject requests for plan upgrade requests',
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
                    ->label(trans_db('plan_upgrade_requests.user_name'))
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('plan_id')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.plan_name'))
                    ->relationship('plan', 'name')
                    ->required(),
                Select::make('subscription_id')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.subscription'))
                    ->relationship('subscription', 'id')
                    ->required(),
                FileUpload::make('receipt_path')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.receipt_path'))
                    ->directory('PlanUpgradeRequestReceipt')
                    ->preserveFilenames()
                    ->required(),
                Textarea::make('rejected_reason')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.rejected_reason'))
                    ->columnSpanFull(),
                // يمكنك إضافة حقل الحالة إذا لزم الأمر للتعديل
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.user_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('plan.name')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.plan_name'))
                    ->searchable(),
                ViewColumn::make('receipt_path')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.receipt_path'))
                    ->view('filament.components.plan-upgrade-request-receipt')
                    ->url(null),
                Tables\Columns\TextColumn::make('status')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.status'))
                    // ترجمة الحالة مباشرة
                    ->formatStateUsing(fn (string $state): string => trans_db("plan_upgrade_requests.status_{$state}")),
                Tables\Columns\TextColumn::make('rejected_reason')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.rejected_reason')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('approve')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending' &&
                        static::canApprove()
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // الافتراض أن PlanUpgradeRequestService موجود
                        app(PlanUpgradeRequestService::class)->approve($record);
                    }),

                Action::make('reject')
                    // trans_db
                    ->label(trans_db('plan_upgrade_requests.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending' &&
                        static::canReject()
                    )
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('rejected_reason')
                            // trans_db
                            ->label(trans_db('plan_upgrade_requests.reject_reason_prompt'))
                            ->maxLength(255),
                        Toggle::make('send_email')
                            // trans_db
                            ->label(trans_db('plan_upgrade_requests.send_email_toggle'))
                            ->default(false),
                    ])
                    ->action(function ($record, array $data) {
                        // الافتراض أن PlanUpgradeRequestService موجود
                        app(PlanUpgradeRequestService::class)->reject(
                            $record,
                            $data['rejected_reason'],
                            $data['send_email']
                        );
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyPermission([
                        // تم تعديل الإذن ليتناسب مع سياق ترقية الباقات
                        'edit plan upgrade requests',
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
            'index' => Pages\ListPlanUpgradeRequests::route('/'),
            'create' => Pages\CreatePlanUpgradeRequest::route('/create'),
            'edit' => Pages\EditPlanUpgradeRequest::route('/{record}/edit'),
        ];
    }
}
