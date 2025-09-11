<?php

namespace App\Filament\Pages;

use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class ManageEvents extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Manage Events';

    protected static ?string $title = 'Add new employee';

    protected static string $view = 'filament.pages.manage-events';

    public $name;

    public $email;

    public $password;

    public $phone;

    public $permissions = [];

    public function createUser()
    {
        // الفاليديشن خارج الـ try-catch ليتمكن Filament من عرض الأخطاء تلقائيًا
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'phone' => ['required', 'string'],
            'permissions' => ['nullable', 'array'],
        ]);

        try {
            $user = auth()->user(); // المستخدم الحالي الذي قام بتسجيل الدخول

            // التحقق مما إذا كان عدد المستخدمين المتاحين (max_users) قد نفد للمستخدم الحالي
            // تم تغيير الشرط ليستخدم $user->max_users بدلاً من $plan->max_users
            if ($user->max_users <= 0) {
                Notification::make()
                    ->title('لقد استنفدت عدد المستخدمين المسموح به لحسابك.')
                    ->danger()
                    ->send();

                return;
            }

            // إنشاء المستخدم الجديد
            $newUser = \App\Models\User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'phone' => $this->phone,
            ]);

            if (! empty($this->permissions)) {
                $newUser->givePermissionTo($this->permissions);
            }

            Role::firstOrCreate(['name' => 'employee']);

            $newUser->assignRole('employee');

            // خصم 1 من عدد المستخدمين المتاحين للمستخدم الحالي
            // تم تغيير هذا الجزء ليستخدم $user->max_users بدلاً من $plan->max_users
            $user->max_users = $user->max_users - 1;
            $user->save(); // حفظ التغيير في قاعدة البيانات لسجل المستخدم الحالي

            $this->form->fill(); // مسح بيانات الفورم بعد النجاح

            // إشعار بالنجاح
            Notification::make()
                ->title('تم إنشاء المستخدم بنجاح وخصم مستخدم من رصيد حسابك.')
                ->success()
                ->send();

        } catch (\Throwable $e) {
            // إشعار بالخطأ إذا حدث أي استثناء آخر
            Notification::make()
                ->title('حدث خطأ: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\View::make('filament.components.max-users-counter'),

            Forms\Components\TextInput::make('name')
                ->label('name')
                ->required(),

            Forms\Components\TextInput::make('email')
                ->label('email')
                ->email()
                ->required(),

            Forms\Components\TextInput::make('password')
                ->label('password')
                ->password()
                ->required(),
            Forms\Components\TextInput::make('phone')
                ->label('phone')
                ->required(),

            Forms\Components\Select::make('permissions')
                ->label('permissions')
                ->multiple()
                ->options([
                    'full access to events' => 'Full access to events',
                    'search for a document' => 'Search for a document',
                    'search by qr code' => 'Search by QR code',
                    'create an event' => 'Create an event',
                    'edit events' => 'Edit events',
                    'delete event' => 'Delete event',
                ]),

        ];
    }
}
