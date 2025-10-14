<?php

namespace App\Filament\Pages;

use App\Models\Logo;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class LogoSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    // استخدام مفاتيح الترجمة بدلاً من النصوص الثابتة
    protected static ?string $title = null;
    protected static ?string $navigationGroup = null;

    protected static ?string $panel = 'admin';

    protected static string $view = 'filament.pages.logo-settings';

    public array $data = [];

    public static function getNavigationGroup(): ?string
    {
        // استخدام المفتاح العام لمحتوى الموقع
        return trans_db('site_content.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return trans_db('logo_settings.page_title');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(trans_db('logo_settings.branding_section'))
                    ->schema([
                        TextInput::make('site_name')
                            ->label(trans_db('logo_settings.site_name_label'))
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('path')
                            ->label(trans_db('logo_settings.site_logo_label'))
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->preserveFilenames()
                            ->visibility('public')
                            ->downloadable()
                            ->openable(),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('deleteLogo')
                                ->label(trans_db('logo_settings.delete_logo_action'))
                                ->color('danger')
                                ->icon('heroicon-o-trash')
                                ->requiresConfirmation()
                                ->action(function () {
                                    $logo = Logo::first();
                                    if ($logo && $logo->path) {
                                        \Storage::disk('public')->delete($logo->path);
                                        $logo->update(['path' => null]);
                                    }
                                    Notification::make()
                                        ->title(trans_db('logo_settings.logo_delete_success'))
                                        ->success()
                                        ->send();
                                }),
                        ]),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function mount(): void
    {
        $logo = Logo::first();
        $this->form->fill($logo ? $logo->attributesToArray() : []);
    }

    public function save(): void
    {
        // التحقق من صحة البيانات
        $validatedData = $this->form->getState();

        // ابحث عن السجل الأول في الجدول
        $logo = Logo::first();

        if ($logo) {
            // إذا كان السجل موجودًا، قم بتحديثه
            $logo->update($validatedData);
        } else {
            // إذا لم يكن السجل موجودًا، قم بإنشاء سجل جديد
            Logo::create($validatedData);
        }

        Notification::make()
            ->title(trans_db('logo_settings.update_success'))
            ->success()
            ->send();
    }
}
