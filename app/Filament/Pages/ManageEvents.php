<?php

namespace App\Filament\Pages;

use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException; // ⬅️ تم استيراد QueryException

class ManageEvents extends Page implements HasForms
{
    use InteractsWithForms;

    // تم تعديل المفاتيح الثابتة باستخدام دالة __()
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Events.navigation_label'; // مفتاح الترجمة للملاح
    // يبدو أن الاسم الفعلي لهذه الصفحة هو "إضافة موظف جديد"، لذا سنعدل العنوان ليعكس ذلك
    protected static ?string $title = 'Events.page_title';

    protected static string $view = 'filament.pages.manage-events';

    public $name;
    public $email;
    public $password;
    public $phone;
    public $permissions = [];

    // التعديلات على الـ Navigation Label و Title
    public static function getNavigationLabel(): string
    {
        return trans_db(static::$navigationLabel);
    }

    public function getTitle(): string
    {
        return trans_db(static::$title);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\View::make('filament.components.max-users-counter'),

            Forms\Components\TextInput::make('name')
                // استخدام مفتاح الترجمة
                ->label(trans_db('Events.name_label'))
                ->required(),

            Forms\Components\TextInput::make('email')
                ->label(trans_db('Events.email_label'))
                ->email()
                ->required(),

            Forms\Components\TextInput::make('password')
                ->label(trans_db('Events.password_label'))
                ->password()
                ->required(),

            Forms\Components\TextInput::make('phone')
                ->label(trans_db('Events.phone_label'))
                ->required(),

            Forms\Components\Select::make('permissions')
                ->label(trans_db('Events.permissions_label'))
                ->multiple()
                ->options([
                    // استخدام مفاتيح ترجمة للـ Options
                    'full access to events' => trans_db('Events.perm_full_access'),
                    'search for a document' => trans_db('Events.perm_search_doc'),
                    'search by qr code' => trans_db('Events.perm_search_qr'),
                    'create an event' => trans_db('Events.perm_create_event'),
                    'edit events' => trans_db('Events.perm_edit_events'),
                    'delete event' => trans_db('Events.perm_delete_event'),
                ]),

        ];
    }

    public function createUser()
    {
        // ⬅️ تم تحديث قواعد الفاليديشن لإضافة unique:users,phone
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            // الفاليديشن التلقائي للـ email يجب أن يمنع تكرار البريد قبل الوصول للـ try-catch
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            // هذا الفاليديشن يمنع تكرار رقم الهاتف قبل الوصول للـ try-catch
            'phone' => ['required', 'string', 'unique:users,phone'],
            'permissions' => ['nullable', 'array'],
        ]);

        try {
            $user = auth()->user();

            // التحقق مما إذا كان عدد المستخدمين المتاحين (max_users) قد نفد
            if ($user->max_users <= 0) {
                Notification::make()
                    ->title(trans_db('Events.max_users_exhausted'))
                    ->danger()
                    ->send();
                return;
            }

            // إنشاء المستخدم الجديد (بقية الكود كما هو)
            $newUser = \App\Models\User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'phone' => $this->phone,
            ]);

            if (! empty($this->permissions)) {
                $newUser->givePermissionTo($this->permissions);
            }

            // التأكد من وجود الدور
            Role::firstOrCreate(['name' => 'employee']);
            $newUser->assignRole('employee');

            // خصم 1 من عدد المستخدمين المتاحين للمستخدم الحالي
            $user->max_users = $user->max_users - 1;
            $user->save();

            $this->form->fill(); // مسح بيانات الفورم بعد النجاح

            // إشعار بالنجاح (تم استخدام مفتاح ترجمة)
            Notification::make()
                ->title(trans_db('Events.create_user_success'))
                ->success()
                ->send();

        } catch (QueryException $e) {
            // ⬅️ اعتراض خطأ تكرار الإدخال (SQLSTATE 23000)
            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'Duplicate entry')) {

                $errorMessageKey = 'Events.error_duplicate_entry_generic';

                // محاولة تحديد الحقل المكرر بدقة (email أو phone)
                if (str_contains($e->getMessage(), 'users_email_unique')) {
                    $errorMessageKey = 'Events.error_duplicate_email';
                } elseif (str_contains($e->getMessage(), 'users_phone_unique')) {
                    $errorMessageKey = 'Events.error_duplicate_phone';
                }

                Notification::make()
                    ->title(trans_db($errorMessageKey))
                    ->danger()
                    ->send();
            } else {
                // إظهار رسالة خطأ عامة لأي خطأ آخر في قاعدة البيانات
                Notification::make()
                    ->title(trans_db('Events.error_generic_db'))
                    ->body($e->getMessage()) // يمكن إظهار رسالة الخطأ التقنية هنا إذا لزم الأمر
                    ->danger()
                    ->send();
            }
        } catch (\Throwable $e) {
            // اعتراض أي خطأ عام آخر
            Notification::make()
                ->title(trans_db('Events.error_prefix'))
                ->body(trans_db('Events.error_unexpected')) // رسالة خطأ غير متوقعة
                ->danger()
                ->send();
        }
    }

    public function mount(): void
    {
        $this->form->fill();
    }
}
