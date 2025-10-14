<?php

namespace App\Filament\Pages;

use App\Models\ScheduledNotification;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class SendMessage extends Page
{
    // تعريب التسميات الرئيسية باستخدام الدوال بدلاً من الخصائص الثابتة
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = null;

    public static function getNavigationLabel(): string
    {
        return trans_db('send_message.navigation_label');
    }

    public function getTitle(): string
    {
        return trans_db('send_message.page_title');
    }

    public static function getNavigationGroup(): ?string
    {
        return trans_db('notifications.navigation_group');
    }

    protected static bool $shouldRegisterNavigation = true;

    protected static string $view = 'filament.pages.send-message';

    public array $users = [];

    public string $channel = 'email';

    public array $message = [];

    public array $subject = [];

    public bool $send_to_all = false;

    public $scheduled_at;

    public array $scheduled_dates = [['scheduled_at' => null]];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
            'show notifications',
            'send notifications',
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Toggle::make('send_to_all')
                ->label(trans_db('send_message.send_to_all_label'))
                ->reactive(),

            Forms\Components\Repeater::make('scheduled_dates')
                ->label(trans_db('send_message.schedule_dates_label'))
                ->schema([
                    Forms\Components\DateTimePicker::make('scheduled_at')
                        ->label(trans_db('send_message.date_time_label'))
                        ->placeholder(trans_db('send_message.date_time_placeholder'))
                        ->required(),
                ])
                ->createItemButtonLabel(trans_db('send_message.add_date_button'))
                ->required(),

            Forms\Components\Select::make('users')
                ->label(trans_db('send_message.select_users_label'))
                ->multiple()
                ->options(
                    User::all()->pluck('name', 'id')
                )
                ->getSearchResultsUsing(fn(string $search) => User::where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->visible(fn($get) => !$get('send_to_all')),

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
                ->reactive(),

            Forms\Components\Tabs::make(trans_db('send_message.translations_tab_title'))
                ->tabs([
                    Forms\Components\Tabs\Tab::make(trans_db('send_message.tab_arabic'))
                        ->schema([
                            Forms\Components\TextInput::make('subject.ar')
                                ->label(trans_db('send_message.subject_ar_label'))
                                ->placeholder(trans_db('send_message.subject_ar_placeholder'))
                                ->visible(fn($get) => $get('channel') === 'email'),

                            Forms\Components\Textarea::make('message.ar')
                                ->label(trans_db('send_message.message_ar_label'))
                                ->placeholder(trans_db('send_message.message_ar_placeholder'))
                                ->rows(6),
                        ])
                    // لضبط اتجاه النص للعربية، يمكن إضافة حقل نصي منفصل أو استخدام ميزة Right to Left (RTL) في CSS للحقول داخل الـ View
                    ,
                    Forms\Components\Tabs\Tab::make(trans_db('send_message.tab_english'))
                        ->schema([
                            Forms\Components\TextInput::make('subject.en')
                                ->label(trans_db('send_message.subject_en_label'))
                                ->placeholder(trans_db('send_message.subject_en_placeholder'))
                                ->visible(fn($get) => $get('channel') === 'email'),

                            Forms\Components\Textarea::make('message.en')
                                ->label(trans_db('send_message.message_en_label'))
                                ->placeholder(trans_db('send_message.message_en_placeholder'))
                                ->rows(6),
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }

    public function submit(): void
    {
        $formData = $this->form->getState();

        // التحقق من وجود رسالة في أي لغة
        if (empty(array_filter($formData['message']))) {
            Notification::make()
                ->danger()
                ->title(trans_db('send_message.validation_message_required_title'))
                ->body(trans_db('send_message.validation_message_required_body'))
                ->send();

            return;
        }

        // التحقق من وجود موضوع في أي لغة إذا كانت القناة email
        if ($formData['channel'] === 'email' && empty($formData['subject']['ar']) && empty($formData['subject']['en'])) {
            Notification::make()
                ->danger()
                ->title(trans_db('send_message.validation_subject_required_title'))
                ->body(trans_db('send_message.validation_subject_required_body'))
                ->send();

            return;
        }

        if (!$this->send_to_all && empty($this->users)) {
            Notification::make()
                ->danger()
                ->title(trans_db('send_message.validation_users_required_title'))
                ->body(trans_db('send_message.validation_users_required_body'))
                ->send();

            return;
        }

        foreach ($formData['scheduled_dates'] as $scheduled_date) {
            $scheduledNotification = ScheduledNotification::create([
                'channel' => $formData['channel'],
                'message' => $formData['message'],
                'subject' => $formData['subject'] ?? null,
                'send_to_all' => $formData['send_to_all'],
                'user_ids' => $formData['send_to_all'] ? null : $formData['users'],
                'scheduled_at' => Carbon::parse($scheduled_date['scheduled_at']),
                'status' => 'pending',
            ]);

            \App\Jobs\ScheduleDashboardNotificationJob::dispatch(
                $scheduledNotification->id,
                $formData['channel'],
                $formData['message'],
                $formData['subject'] ?? null,
                $formData['send_to_all'] ? [] : $formData['users']
            )->delay(Carbon::parse($scheduled_date['scheduled_at']));
        }

        Notification::make()
            ->success()
            ->title(trans_db('send_message.success_title'))
            ->body(trans_db('send_message.success_body'))
            ->send();
    }
}
