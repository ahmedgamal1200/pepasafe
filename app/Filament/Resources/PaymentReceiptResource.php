<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentReceiptResource\Pages;
use App\Models\PaymentReceipt;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;

// لاستخدام Alignment::Right

class PaymentReceiptResource extends Resource
{
    protected static ?string $model = PaymentReceipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // استخدام الدوال الثابتة للترجمة
    public static function getNavigationGroup(): ?string
    {
        return trans_db('payment_receipts.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return trans_db('payment_receipts.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('payment_receipts.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('payment_receipts.plural_model_label');
    }
    // نهاية الدوال الثابتة للترجمة

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show payment receipt requests',
        ]);
    }

    public static function canApprove(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'approve requests for payment receipts',
            'full access',
        ]);
    }

    public static function canReject(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'reject requests for payment receipts',
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
                    // trans_db
                    ->label(trans_db('payment_receipts.user_name'))
                    ->relationship('user', 'name')
                    ->preload()
                    ->searchable()












































































































                    ->required(),
                Forms\Components\Select::make('plan_id')
                    // trans_db
                    ->label(trans_db('payment_receipts.plan_name'))
                    ->relationship('plan', 'name')
                    ->preload()
                    ->required(),
                Forms\Components\FileUpload::make('image_path')
                    // trans_db
                    ->label(trans_db('payment_receipts.image_path'))
                    ->image()
                    ->directory('receipts')
                    ->required()
                    ->visibility('public'),
                Forms\Components\Select::make('status')
                    // trans_db
                    ->label(trans_db('payment_receipts.status'))
                    ->options([
                        // trans_db
                        'pending' => trans_db('payment_receipts.status_pending'),
                        // trans_db
                        'approved' => trans_db('payment_receipts.status_approved'),
                        // trans_db
                        'rejected' => trans_db('payment_receipts.status_rejected'),
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable()
                    // trans_db
                    ->label(trans_db('payment_receipts.user_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('plan.name')
                    // trans_db
                    ->label(trans_db('payment_receipts.plan_name'))
                    ->searchable(),
                ViewColumn::make('image_path')
                    // trans_db
                    ->label(trans_db('payment_receipts.image_path'))
                    ->view('filament.components.payment-receipt')
                    ->url(null),
                Tables\Columns\BadgeColumn::make('status')
                    // trans_db
                    ->label(trans_db('payment_receipts.status'))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => trans_db("payment_receipts.status_{$state}")), // ترجمة حالة Badge
                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans_db('payment_receipts.created_at'))
                    ->dateTime()
                    ->sortable() //
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('approve')
                    // trans_db
                    ->label(trans_db('payment_receipts.approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending' &&
                        static::canApprove()
                    )
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);
                        // create subscription
                        Subscription::query()->create([
                            'user_id' => $record->user_id,
                            'plan_id' => $record->plan_id,
                            // يجب التأكد من وجود حقل credit_amount في نموذج Plan
                            'balance' => $record->plan->credit_amount ?? 0,
                            'remaining' => $record->plan->credit_amount ?? 0,
                            'start_date' => now(),
                            // يجب التأكد من وجود حقل duration_days في نموذج Plan
                            'end_date' => now()->addDays($record->plan->duration_days ?? 30),
                            'status' => 'active',
                        ]);
                    }),
                Action::make('reject')
                    // trans_db
                    ->label(trans_db('payment_receipts.reject'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending' &&
                        static::canReject()
                    )
                    ->action(fn ($record) => $record->update(['status' => 'rejected'])),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyPermission([
                        'edit payment receipt requests',
                        'full access',
                    ])),

            ])->recordUrl(null)

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    // trans_db
                    ->label(trans_db('payment_receipts.status'))
                    ->options([
                        // trans_db
                        'pending' => trans_db('payment_receipts.status_pending'),
                        // trans_db
                        'approved' => trans_db('payment_receipts.status_approved'),
                        // trans_db
                        'rejected' => trans_db('payment_receipts.status_rejected'),
                    ]),

                // ⬅️ الفلتر الجديد للترتيب حسب التاريخ
                Tables\Filters\SelectFilter::make('sort_order')
                    ->label(trans_db('payment_receipts.sort_order'))
                    ->options([
                        'desc' => trans_db('payment_receipts.sort_order_desc'),
                        'asc' => trans_db('payment_receipts.sort_order_ASC'),
                    ])
                    ->default('desc')
                    ->query(fn (Builder $query, array $data): Builder =>
                        // تطبيق الترتيب بناءً على القيمة المختارة (desc أو asc)
                    $query->orderBy('created_at', $data['value'] ?? 'desc')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListPaymentReceipts::route('/'),
            'create' => Pages\CreatePaymentReceipt::route('/create'),
            'edit' => Pages\EditPaymentReceipt::route('/{record}/edit'),
        ];
    }
}
