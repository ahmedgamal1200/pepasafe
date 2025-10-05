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

    protected static ?string $navigationGroup = 'Notifications';

    // في App\Filament\Resources\ScheduledNotificationResource.php

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('send_to_all')
                    ->label('Send to all users')
                    ->reactive(),

                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->required(),

                Forms\Components\Select::make('user_ids')
                    ->label('Select users')
                    ->multiple()
                    ->options(
                        User::all()->pluck('name', 'id')
                    )
                    ->getSearchResultsUsing(fn (string $search) => User::where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn ($get) => ! $get('send_to_all')),

                Forms\Components\Select::make('channel')
                    ->label('Select channel')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'database' => 'Notification in system',
                    ])
                    ->required()
                    ->searchable()
                    ->reactive(),

                Forms\Components\TextInput::make('subject')
                    ->maxLength(255)
                    ->default(null)
                    ->visible(fn ($get) => $get('channel') === 'email'),

                // التعديل هنا: استخدام Tabs للحقول المترجمة
                Forms\Components\Tabs::make('Translations')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Arabic')
                            ->schema([
                                Forms\Components\Textarea::make('message.ar')
                                    ->label('Notification Message (Arabic)')
                                    ->placeholder('ادخل محتوى الرسالة بالعربية')
                                    ->required()
                                    ->rows(6),
                            ]),
                        Forms\Components\Tabs\Tab::make('English')
                            ->schema([
                                Forms\Components\Textarea::make('message.en')
                                    ->label('Notification Message (English)')
                                    ->placeholder('Enter the message content in English')
                                    ->required()
                                    ->rows(6),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending')
                    ->visibleOn('edit')
                    ->disabled(), // اجعل هذا الحقل للقراءة فقط
            ]);
    }

    public static function getBulkActions(): array
    {
        return [
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $hasSentNotifications = $records->contains(fn ($record) => $record->status !== 'pending');
                        if ($hasSentNotifications) {
                            Notification::make()
                                ->title('Cannot delete')
                                ->body('You can only delete pending notifications.')
                                ->danger()
                                ->send();

                            return;
                        }
                        $records->each->delete();
                        Notification::make()
                            ->title('Deleted successfully')
                            ->body('The selected pending notifications have been deleted.')
                            ->success()
                            ->send();
                    }),
            ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('channel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\IconColumn::make('send_to_all')
                    ->boolean(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                //                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
                    ->visible(fn (ScheduledNotification $record): bool => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
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
