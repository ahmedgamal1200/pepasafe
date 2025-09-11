<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomPlanSubscriptionResource\Pages;
use App\Filament\Resources\CustomPlanSubscriptionResource\RelationManagers;
use App\Models\CustomPlanSubscription;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomPlanSubscriptionResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Subscriptions';
    protected static ?string $navigationLabel = 'Custom Plans';




    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make(2)
                    ->schema([
                Select::make('user_id')
                    ->label('User')
                    ->options(User::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),

                        Forms\Components\Checkbox::make('is_public')
                            ->label('Custom Private Plan ?')
                            ->default(true)
                            ->disabled(),

        ]),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Plan Name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->label('Price after discount')
                    ->maxLength(255)
                    ->minLength(0),
                Forms\Components\TextInput::make('compare_price')
                    ->label('Price before discount')
                    ->maxLength(255),
                Forms\Components\TextInput::make('credit_amount')
                    ->required()
                    ->label('Credit Amount')
                    ->numeric()
                    ->minValue(0),
                Forms\Components\TextInput::make('duration_days')
                    ->required()
                    ->label('Duration Days')
                    ->placeholder('30 days')
                    ->maxLength(255)
                    ->minValue(0)
                    ->numeric(),

                TextInput::make('max_users')
                    ->required()
                    ->label('Maximum Users')
                    ->numeric()
                    ->minValue(0),

                TextInput::make('document_price_in_plan')
                    ->label('In-Plan Document Price (Within Plan)')
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('document_price_outside_plan')
                    ->label('Pay-As-You-Go Document Price (Outside Plan)')
                    ->required()
                    ->numeric()
                    ->minValue(0),

                // SMS Prices
                TextInput::make('sms_price_outside_plan')
                    ->label('Pay-As-You-Go SMS Message Price (Outside Plan)')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                TextInput::make('sms_price_in_plan')
                    ->label('In-Plan SMS Message Price (Within Plan)')
                    ->required()
                    ->numeric()
                    ->minValue(0),

                // WhatsApp Prices
                TextInput::make('whatsapp_price_outside_plan')
                    ->label('Pay-As-You-Go WhatsApp Message Price (Outside Plan)')
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('whatsapp_price_in_plan')
                    ->label('In-Plan WhatsApp Message Price (Within Plan)')
                    ->required()
                    ->numeric()
                    ->minValue(0),

                // Email Prices
                TextInput::make('email_price_outside_plan')
                    ->label('Pay-As-You-Go Email Message Price (Outside Plan)')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                TextInput::make('email_price_in_plan')
                    ->label('In-Plan Email Message Price (Within Plan)')
                    ->required()
                    ->numeric()
                    ->minValue(0),

                Grid::make(2)
                    // ✅ خيار ترحيل الرصيد
                    ->schema([
                        Checkbox::make('carry_over_credit')
                            ->label('السماح بترحيل الرصيد المتبقي عند التجديد ؟')
                            ->default(false),

                        Checkbox::make('enable_attendance')
                            ->label('تفعيل الحضور في هذه الباقة ؟')
                            ->default(false),

                        Checkbox::make('enable_multiple_templates')
                            ->label('تفعيل استخدام أكثر من نموذج في هذه الباقة ؟')
                            ->default(false),
                    ]),


                Select::make('enabled_channels.documents')
                    ->label('قنوات إرسال الوثائق')
                    ->multiple()
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'whatsapp' => 'WhatsApp',
                    ])
                    ->columnSpanFull(),

                Select::make('enabled_channels.attendance')
                    ->label('قنوات إرسال الحضور')
                    ->multiple()
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'whatsapp' => 'WhatsApp',
                    ])
                    ->columnSpanFull(),


                Forms\Components\RichEditor::make('feature')
                    ->label('Features')
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
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('price')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('compare_price')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('credit_amount')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('duration_days')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('feature')->html(),
                Tables\Columns\IconColumn::make('carry_over_credit')->searchable()->sortable()->boolean(),
                Tables\Columns\TextColumn::make('max_users')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('enable_attendance')->boolean(),
                TextColumn::make('document_price_in_plan')->sortable(),
                TextColumn::make('document_price_outside_plan')->sortable(),
                TextColumn::make('sms_price_in_plan')->sortable(),
                TextColumn::make('sms_price_outside_plan')->sortable(),
                TextColumn::make('whatsapp_price_in_plan')->sortable(),
                TextColumn::make('whatsapp_price_outside_plan')->sortable(),
                TextColumn::make('email_price_in_plan')->sortable(),
                TextColumn::make('email_price_outside_plan')->sortable(),
                Tables\Columns\IconColumn::make('enable_multiple_templates')->boolean(),
                Tables\Columns\IconColumn::make('is_public')->boolean(),

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
