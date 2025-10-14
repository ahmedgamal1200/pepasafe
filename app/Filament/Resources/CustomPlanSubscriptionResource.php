<?php


namespace App\Filament\Resources;

use App\Filament\Resources\CustomPlanSubscriptionResource\Pages;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomPlanSubscriptionResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Subscriptions';

    // تم التعديل: استخدام دالة ثابتة بدلاً من خاصية ثابتة لضمان عمل الترجمة
    public static function getNavigationLabel(): string
    {
        return trans_db('custom_plans.navigation_label');
    }

    // تم التعديل: استخدام دالة ثابتة بدلاً من خاصية ثابتة لضمان عمل الترجمة
    public static function getModelLabel(): string
    {
        return trans_db('custom_plans.model_label');
    }

    // تم التعديل: استخدام دالة ثابتة بدلاً من خاصية ثابتة لضمان عمل الترجمة
    public static function getPluralModelLabel(): string
    {
        return trans_db('custom_plans.plural_model_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return trans_db('plans.navigation_group');
    }

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
                Grid::make(2)
                    ->schema([
                        Select::make('user_id')
                            // trans_db
                            ->label(trans_db('custom_plans.user'))
                            ->options(User::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Forms\Components\Checkbox::make('is_public')
                            // trans_db
                            ->label(trans_db('custom_plans.is_public_label'))
                            ->default(true)
                            ->disabled(),
                    ]),

                Forms\Components\TextInput::make('name')
                    ->required()
                    // trans_db
                    ->label(trans_db('custom_plans.plan_name'))
                    ->maxLength(255),

                Forms\Components\TextInput::make('price')
                    ->required()
                    // trans_db
                    ->label(trans_db('custom_plans.price_after_discount'))
                    ->maxLength(255)
                    ->minValue(0),

                Forms\Components\TextInput::make('compare_price')
                    // trans_db
                    ->label(trans_db('custom_plans.price_before_discount'))
                    ->maxLength(255),

                Forms\Components\TextInput::make('credit_amount')
                    ->required()
                    // trans_db
                    ->label(trans_db('custom_plans.credit_amount'))
                    ->numeric()
                    ->minValue(0),

                Forms\Components\TextInput::make('duration_days')
                    ->required()
                    // trans_db
                    ->label(trans_db('custom_plans.duration_days'))
                    // trans_db
                    ->placeholder(trans_db('custom_plans.duration_days_placeholder'))
                    ->maxLength(255)
                    ->minValue(0)
                    ->numeric(),

                TextInput::make('max_users')
                    ->required()
                    // trans_db
                    ->label(trans_db('custom_plans.maximum_users'))
                    ->numeric()
                    ->minValue(0),

                TextInput::make('document_price_in_plan')
                    // trans_db
                    ->label(trans_db('custom_plans.document_price_in_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('document_price_outside_plan')
                    // trans_db
                    ->label(trans_db('custom_plans.document_price_outside_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                // SMS Prices
                TextInput::make('sms_price_outside_plan')
                    // trans_db
                    ->label(trans_db('custom_plans.sms_price_outside_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('sms_price_in_plan')
                    // trans_db
                    ->label(trans_db('custom_plans.sms_price_in_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                // WhatsApp Prices
                TextInput::make('whatsapp_price_outside_plan')
                    // trans_db
                    ->label(trans_db('custom_plans.whatsapp_price_outside_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('whatsapp_price_in_plan')
                    // trans_db
                    ->label(trans_db('custom_plans.whatsapp_price_in_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                // Email Prices
                TextInput::make('email_price_outside_plan')
                    // trans_db
                    ->label(trans_db('custom_plans.email_price_outside_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('email_price_in_plan')
                    // trans_db
                    ->label(trans_db('custom_plans.email_price_in_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                Grid::make(2)
                    ->schema([
                        // Checkbox: Carry over credit (ترحيل الرصيد)
                        Checkbox::make('carry_over_credit')
                            // trans_db
                            ->label(trans_db('custom_plans.carry_over_credit'))
                            ->default(false),

                        Checkbox::make('enable_attendance')
                            // trans_db
                            ->label(trans_db('custom_plans.enable_attendance'))
                            ->default(false),

                        Checkbox::make('enable_multiple_templates')
                            // trans_db
                            ->label(trans_db('custom_plans.enable_multiple_templates'))
                            ->default(false),
                    ]),

                Select::make('enabled_channels.documents')
                    // trans_db
                    ->label(trans_db('custom_plans.enabled_channels_documents'))
                    ->multiple()
                    ->options([
                        // trans_db
                        'email' => trans_db('custom_plans.channel_email'),
                        // trans_db
                        'sms' => trans_db('custom_plans.channel_sms'),
                        // trans_db
                        'whatsapp' => trans_db('custom_plans.channel_whatsapp'),
                    ])
                    ->columnSpanFull(),

                Select::make('enabled_channels.attendance')
                    // trans_db
                    ->label(trans_db('custom_plans.enabled_channels_attendance'))
                    ->multiple()
                    ->options([
                        // trans_db
                        'email' => trans_db('custom_plans.channel_email'),
                        // trans_db
                        'sms' => trans_db('custom_plans.channel_sms'),
                        // trans_db
                        'whatsapp' => trans_db('custom_plans.channel_whatsapp'),
                    ])
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('feature')
                    // trans_db
                    ->label(trans_db('custom_plans.features'))
                    ->required(),
            ]);

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $plan = $this->record;

        // حساب تاريخ انتهاء الاشتراك بناءً على مدة الخطة
        $endDate = Carbon::now()->addDays($plan->duration_days);

        // إنشاء الاشتراك الجديد
        Subscription::create([
            'user_id' => $this->data['user_id'],
            'plan_id' => $plan->id,
            'balance' => $plan->credit_amount,
            'remaining' => $plan->credit_amount,
            'start_date' => Carbon::now(),
            'end_date' => $endDate,
            'status' => 'active', // يمكنك تحديد الحالة الأولية كما تريد
            'auto_renew' => false, // يمكنك تحديد القيمة الافتراضية كما تريد
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // trans_db
                Tables\Columns\TextColumn::make('name')->label(trans_db('custom_plans.plan_name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('price')->label(trans_db('custom_plans.price'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('compare_price')->label(trans_db('custom_plans.compare_price'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('credit_amount')->label(trans_db('custom_plans.credit_amount'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('duration_days')->label(trans_db('custom_plans.duration_days'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('feature')->label(trans_db('custom_plans.features'))->html(),
                // trans_db
                Tables\Columns\IconColumn::make('carry_over_credit')->label(trans_db('custom_plans.carry_over_credit_short'))->boolean()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('max_users')->label(trans_db('custom_plans.maximum_users'))->searchable()->sortable(),
                // trans_db
                Tables\Columns\IconColumn::make('enable_attendance')->label(trans_db('custom_plans.enable_attendance_short'))->boolean(),
                // trans_db
                TextColumn::make('document_price_in_plan')->label(trans_db('custom_plans.doc_price_in'))->sortable(),
                // trans_db
                TextColumn::make('document_price_outside_plan')->label(trans_db('custom_plans.doc_price_out'))->sortable(),
                // trans_db
                TextColumn::make('sms_price_in_plan')->label(trans_db('custom_plans.sms_price_in'))->sortable(),
                // trans_db
                TextColumn::make('sms_price_outside_plan')->label(trans_db('custom_plans.sms_price_out'))->sortable(),
                // trans_db
                TextColumn::make('whatsapp_price_in_plan')->label(trans_db('custom_plans.whatsapp_price_in'))->sortable(),
                // trans_db
                TextColumn::make('whatsapp_price_outside_plan')->label(trans_db('custom_plans.whatsapp_price_out'))->sortable(),
                // trans_db
                TextColumn::make('email_price_in_plan')->label(trans_db('custom_plans.email_price_in'))->sortable(),
                // trans_db
                TextColumn::make('email_price_outside_plan')->label(trans_db('custom_plans.email_price_out'))->sortable(),
                // trans_db
                Tables\Columns\IconColumn::make('enable_multiple_templates')->label(trans_db('custom_plans.multiple_templates_short'))->boolean(),
                // trans_db
                Tables\Columns\IconColumn::make('is_public')->label(trans_db('custom_plans.is_public_short'))->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCustomPlanSubscriptions::route('/'),
            'create' => Pages\CreateCustomPlanSubscription::route('/create'),
            'edit' => Pages\EditCustomPlanSubscription::route('/{record}/edit'),
        ];
    }
}

