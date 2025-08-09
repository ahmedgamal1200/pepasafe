<?php

namespace App\Filament\Pages;


use App\Jobs\ScheduleDashboardNotificationJob;
use Filament\Pages\Page;
use Filament\Forms;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use App\Notifications\CustomAdminNotification;



class SendMessage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationLabel = 'Send Notification';
    protected static ?string $navigationGroup = 'Notifications';
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'filament.pages.send-message';

    public array $users = [];
    public string $channel = 'email';
    public string $message = '';
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
                ->visible(fn ($get) => !$get('send_to_all')), // لو "إرسال إلى الكل" مفعل، نخفي اختيار المستخدمين



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
                ->label('Email Subject')
                ->placeholder('Enter the subject of the message')
                ->required()
                ->visible(fn ($get) => $get('channel') === 'email'),



            Forms\Components\Textarea::make('message')
                ->label('Email Message')
                ->required()
                ->rows(6),
        ];
    }


    public function submit(): void
    {
        $formData = $this->form->getState();

        if (!$this->send_to_all && empty($this->users)) {
            Notification::make()
                ->danger()
                ->title('No users selected')
                ->body('Please select at least one user or enable "Send to all".')
                ->send();
            return;
        }

        // نمر على كل التواريخ المجدولة ونرسل مهمة لكل تاريخ
        foreach ($formData['scheduled_dates'] as $scheduled_date) {
            \App\Jobs\ScheduleDashboardNotificationJob::dispatch(
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
