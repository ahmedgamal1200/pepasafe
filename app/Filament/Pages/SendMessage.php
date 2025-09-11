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
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationLabel = 'Send Notification';

    protected static ?string $navigationGroup = 'Notifications';

    protected static bool $shouldRegisterNavigation = true;

    protected static string $view = 'filament.pages.send-message';

    public array $users = [];

    public string $channel = 'email';

    public array $message = [];

    public string $subject = '';

    public bool $send_to_all = false;

    public $scheduled_at;

    public array $scheduled_dates = [['scheduled_at' => null]]; // هذا هو التعديل الأساسي

    public static function canAccess(): bool
    {
        return auth()->user()?->can('full access');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Toggle::make('send_to_all')
                ->label('Send to all users')
                ->reactive(), // علشان نقدر نغير اختيار المستخدمين بناءً عليه

            Forms\Components\Repeater::make('scheduled_dates')
                ->label('Schedule Dates & Times')
                ->schema([
                    Forms\Components\DateTimePicker::make('scheduled_at')
                        ->label('Date & Time')
                        ->placeholder('Choose a date and time')
                        ->required(),
                ])
                ->createItemButtonLabel('Add another date')
                ->required(), // نخليه مطلوب عشان المستخدم لازم يحدد تاريخ واحد على الأقل

            Forms\Components\Select::make('users')
                ->label('Select users')
                ->multiple()
                ->options(
                    User::all()->pluck('name', 'id')
                )
                ->getSearchResultsUsing(fn (string $search) => User::where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->visible(fn ($get) => ! $get('send_to_all')), // لو "إرسال إلى الكل" مفعل، نخفي اختيار المستخدمين

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

            // الكود الجديد هنا
            Forms\Components\Tabs::make('Translations')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Arabic')
                        ->schema([
                            Forms\Components\TextInput::make('subject.ar')
                                ->label('Email Subject (Arabic)')
                                ->placeholder('ادخل موضوع الرسالة بالعربية')
                                ->visible(fn ($get) => $get('channel') === 'email'),

                            Forms\Components\Textarea::make('message.ar')
                                ->label('Notification Message (Arabic)')
                                ->placeholder('ادخل محتوى الرسالة بالعربية')
                                ->rows(6),
                        ]),
                    Forms\Components\Tabs\Tab::make('English')
                        ->schema([
                            Forms\Components\TextInput::make('subject.en')
                                ->label('Email Subject (English)')
                                ->placeholder('Enter the subject of the message in English')
                                ->visible(fn ($get) => $get('channel') === 'email'),

                            Forms\Components\Textarea::make('message.en')
                                ->label('Notification Message (English)')
                                ->placeholder('Enter the message content in English')
                                ->rows(6),
                        ]),
                ])
                ->columnSpanFull(), // يجعل التبويبات تظهر على عرض كامل
        ];
    }

    public function submit(): void
    {
        $formData = $this->form->getState();

        // التحقق من وجود رسالة في أي لغة
        if (empty(array_filter($formData['message']))) {
            Notification::make()
                ->danger()
                ->title('Message is required')
                ->body('Please enter the message in at least one language (Arabic or English).')
                ->send();

            return;
        }

        // التحقق من وجود موضوع في أي لغة إذا كانت القناة email
        if ($formData['channel'] === 'email' && empty($formData['subject']['ar']) && empty($formData['subject']['en'])) {
            Notification::make()
                ->danger()
                ->title('Subject is required')
                ->body('Please enter a subject for the email in at least one language.')
                ->send();

            return;
        }

        if (! $this->send_to_all && empty($this->users)) {
            Notification::make()
                ->danger()
                ->title('No users selected')
                ->body('Please select at least one user or enable "Send to all".')
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
                $scheduledNotification->id, // هنا نضيف الـID
                $formData['channel'],
                $formData['message'],
                $formData['subject'] ?? null,
                $formData['send_to_all'] ? [] : $formData['users']
            )->delay(Carbon::parse($scheduled_date['scheduled_at']));
        }

        Notification::make()
            ->success()
            ->title('Scheduled successfully!')
            ->body('Your message has been scheduled for sending.')
            ->send();
    }
}
