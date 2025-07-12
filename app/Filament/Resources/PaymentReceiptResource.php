<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentReceiptResource\Pages;
use App\Filament\Resources\PaymentReceiptResource\RelationManagers;
use App\Models\PaymentReceipt;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentReceiptResource extends Resource
{
    protected static ?string $model = PaymentReceipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Requests';
    protected static ?string $navigationLabel = 'Payment Receipt Requests';

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
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User Name')
                    ->relationship('user', 'name') // لو في علاقة user
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('plan_id')
                    ->label('Plan Name')
                    ->relationship('plan', 'name') // لو في علاقة user
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\FileUpload::make('image_path')
                    ->label('Receipt')
                    ->image() // للتأكد إن الملف صورة
                    ->directory('receipts') // يخزن الصورة داخل storage/app/public/receipts
                    ->required()
                    ->visibility('public'), // لو بتستخدم storage:link
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('plan.name'),
//                ImageColumn::make('image_path')
//                    ->label('Receipt')
//                    ->disk('public')
//                    ->visibility('public'),// ده يخلي يقرأ من storage/app/public تلقائيًا
                ViewColumn::make('image_path')
                    ->label('Receipt')
                    ->view('filament.components.payment-receipt')
                    ->url(null),
        Tables\Columns\BadgeColumn::make('status'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending' &&
                    static::canApprove()
                    )
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);
                        // create subscription
                        Subscription::query()->create([
                            'user_id' => $record->user_id,
                            'plan_id' => $record->plan_id,
                            'balance' => $record->plan->credit_amount,
                            'remaining' => $record->plan->credit_amount,
                            'start_date' => now(),
                            'end_date' => now()->addDays($record->plan->duration_days),
                            'status' => 'active',
                        ]);
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'pending' &&
                    static::canReject()
                    )
                    ->action(fn($record) => $record->update(['status' => 'rejected'])),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyPermission([
                        'edit payment receipt requests',
                        'full access',
                    ])),

            ])->recordUrl(null)

            ->filters([
                //
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
            'index' => Pages\ListPaymentReceipts::route('/'),
            'create' => Pages\CreatePaymentReceipt::route('/create'),
            'edit' => Pages\EditPaymentReceipt::route('/{record}/edit'),
        ];
    }
}
