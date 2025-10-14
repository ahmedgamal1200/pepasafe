<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduledNotificationResource\Pages;
use App\Models\Notification;
use App\Models\ScheduledNotification;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class ScheduledNotificationResource extends Resource
{
    protected static ?string $model = ScheduledNotification::class;

    protected static ?string $navigationIcon = 'heroicon-s-bell';

    // تعريب الخصائص الثابتة باستخدام الدوال
    protected static ?string $navigationGroup = null;
    protected static ?string $navigationLabel = null;
    protected static ?string $modelLabel = null;
    protected static ?string $pluralModelLabel = null;


    public static function getNavigationLabel(): string
    {
        return trans_db('scheduled_notification.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return trans_db('notifications.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return trans_db('scheduled_notification.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('scheduled_notification.plural_model_label');
    }

    public static function canAccess(): bool
    {
        // استخدام نفس صلاحيات صفحة الإرسال
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show notifications',
            'send notifications', // يمكن أن يحتاج المستخدم الذي يرسل الإشعارات إلى رؤية جدول الإرسال
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('send_to_all')
                    ->label(trans_db('send_message.send_to_all_label'))
                    ->reactive()
                    ->disabledOn('edit') // لا يجب تغيير هذه القيمة بعد الجدولة
                    ->columnSpan('full'),

                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->label(trans_db('scheduled_notification.scheduled_at_label'))
                    ->required()
                    ->disabledOn('edit') // لا يجب تغيير التاريخ بعد الجدولة
                    ->columnSpan('full'),

                Forms\Components\Select::make('user_ids')
                    ->label(trans_db('send_message.select_users_label'))
                    ->multiple()
                    ->options(
                        User::all()->pluck('name', 'id')
                    )
                    ->getSearchResultsUsing(fn (string $search) => User::where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->disabledOn('edit')
                    ->visible(fn ($get) => ! $get('send_to_all'))
                    ->columnSpan('full'),

                Forms\Components\Select::make('channel')
                    ->label(trans_db('send_message.select_channel_label'))
                    ->options([
                        'whatsapp' => trans_db('send_message.channel_whatsapp'),
                        'email' => trans_db('send_message.channel_email'),
                        'sms' => trans_db('send_message.channel_sms'),
                        'database' => trans_db('send_message.channel_database'),
                    ])
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->disabledOn('edit')
                    ->columnSpan('full'),

                Forms\Components\TextInput::make('status')
                    ->label(trans_db('scheduled_notification.status_label'))
                    ->required()
                    ->maxLength(255)
                    ->default('pending')
                    ->visibleOn('edit')
                    ->disabled()
                    ->columnSpan('full'),

                Forms\Components\Tabs::make(trans_db('send_message.translations_tab_title'))
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(trans_db('send_message.tab_arabic'))
                            ->schema([
                                Forms\Components\TextInput::make('subject.ar')
                                    ->label(trans_db('send_message.subject_ar_label'))
                                    ->placeholder(trans_db('send_message.subject_ar_placeholder'))
                                    ->visible(fn ($get) => $get('channel') === 'email')
                                    ->disabledOn('edit'),

                                Forms\Components\Textarea::make('message.ar')
                                    ->label(trans_db('send_message.message_ar_label'))
                                    ->placeholder(trans_db('send_message.message_ar_placeholder'))
                                    ->required()
                                    ->disabledOn('edit')
                                    ->rows(6),
                            ]),
                        Forms\Components\Tabs\Tab::make(trans_db('send_message.tab_english'))
                            ->schema([
                                Forms\Components\TextInput::make('subject.en')
                                    ->label(trans_db('send_message.subject_en_label'))
                                    ->placeholder(trans_db('send_message.subject_en_placeholder'))
                                    ->visible(fn ($get) => $get('channel') === 'email')
                                    ->disabledOn('edit'),

                                Forms\Components\Textarea::make('message.en')
                                    ->label(trans_db('send_message.message_en_label'))
                                    ->placeholder(trans_db('send_message.message_en_placeholder'))
                                    ->required()
                                    ->disabledOn('edit')
                                    ->rows(6),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('channel')
                    ->label(trans_db('scheduled_notification.channel_label'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label(trans_db('scheduled_notification.subject_label'))
                    ->searchable()
                    ->limit(50),
                Tables\Columns\IconColumn::make('send_to_all')
                    ->label(trans_db('scheduled_notification.send_to_all_label'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label(trans_db('scheduled_notification.scheduled_at_label'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(trans_db('scheduled_notification.status_label'))
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans_db('scheduled_notification.created_at_label'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(trans_db('scheduled_notification.updated_at_label'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (ScheduledNotification $record): bool => $record->status === 'pending'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (ScheduledNotification $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (ScheduledNotification $record) {
                        try {
                            $record->delete();
                            Notification::make()
                                ->title(trans_db('scheduled_notification.delete_success_title'))
                                ->body(trans_db('scheduled_notification.delete_success_body'))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(trans_db('scheduled_notification.delete_failed_title'))
                                ->body(trans_db('scheduled_notification.delete_failed_body'))
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(trans_db('scheduled_notification.delete_bulk_action_label'))
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $pendingRecords = $records->filter(fn ($record) => $record->status === 'pending');
                            $deletedCount = $pendingRecords->count();

                            if ($deletedCount < $records->count()) {
                                Notification::make()
                                    ->title(trans_db('scheduled_notification.bulk_delete_partial_title'))
                                    ->body(trans_db('scheduled_notification.bulk_delete_partial_body', ['deleted_count' => $deletedCount, 'total_count' => $records->count()]))
                                    ->warning()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title(trans_db('scheduled_notification.delete_success_title'))
                                    ->body(trans_db('scheduled_notification.delete_success_body_bulk'))
                                    ->success()
                                    ->send();
                            }

                            $pendingRecords->each->delete();

                            if ($pendingRecords->isEmpty() && $records->isNotEmpty()) {
                                // إذا لم يكن هناك أي شيء للحذف (لأنها ليست pending)
                                Notification::make()
                                    ->title(trans_db('scheduled_notification.bulk_delete_failed_title'))
                                    ->body(trans_db('scheduled_notification.bulk_delete_failed_body'))
                                    ->danger()
                                    ->send();
                            }

                        }),
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
            'index' => Pages\ListScheduledNotifications::route('/'),
            'create' => Pages\CreateScheduledNotification::route('/create'),
            'edit' => Pages\EditScheduledNotification::route('/{record}/edit'),
        ];
    }
}
