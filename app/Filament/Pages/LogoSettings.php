<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use App\Models\Logo;
use Filament\Forms\Form;

class LogoSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $title = 'Logo & Branding Settings';
    protected static ?string $navigationGroup = 'Site Content';
    protected static ?string $panel = 'admin';
    protected static string $view = 'filament.pages.logo-settings';

    public array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Branding')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('path')
                            ->label('Site Logo')
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->preserveFilenames()
                            ->visibility('public')
                            ->downloadable()
                            ->openable(),


                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('deleteLogo')
                                ->label('Delete Logo')
                                ->color('danger')
                                ->icon('heroicon-o-trash')
                                ->requiresConfirmation()
                                ->action(function () {
                                    $logo = \App\Models\Logo::first();
                                    if ($logo && $logo->path) {
                                        \Storage::disk('public')->delete($logo->path);
                                        $logo->update(['path' => null]);
                                    }
                                    \Filament\Notifications\Notification::make()
                                        ->title('Logo deleted successfully!')
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

        // **المنطق الجديد**
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
            ->title('Branding settings updated successfully!')
            ->success()
            ->send();
    }
}
