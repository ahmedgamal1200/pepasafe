<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
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
use Filament\Support\Enums\Alignment;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationGroup = 'Subscriptions';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    // استخدام الدوال الثابتة للترجمة
    public static function getNavigationLabel(): string
    {
        return trans_db('plans.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return trans_db('plans.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans_db('plans.plural_model_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return trans_db('plans.navigation_group');
    }
    // نهاية الدوال الثابتة للترجمة

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'add plan',
            'full access',
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            // تحديد اتجاه النص (RTL) للغة العربية
            ->columns(3)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    // trans_db
                    ->label(trans_db('plans.plan_name'))
                    ->maxLength(255),

                Forms\Components\TextInput::make('price')
                    ->required()
                    // trans_db
                    ->label(trans_db('plans.price_after_discount'))
                    ->maxLength(255)
                    ->minValue(0)
                    ->numeric(),

                Forms\Components\TextInput::make('compare_price')
                    // trans_db
                    ->label(trans_db('plans.price_before_discount'))
                    ->maxLength(255)
                    ->numeric(),

                Forms\Components\TextInput::make('credit_amount')
                    ->required()
                    // trans_db
                    ->label(trans_db('plans.credit_amount'))
                    ->numeric()
                    ->minValue(0),

                Forms\Components\TextInput::make('duration_days')
                    ->required()
                    // trans_db
                    ->label(trans_db('plans.duration_days'))
                    // trans_db
                    ->placeholder(trans_db('plans.duration_days_placeholder'))
                    ->maxLength(255)
                    ->minValue(0)
                    ->numeric(),

                TextInput::make('max_users')
                    ->required()
                    // trans_db
                    ->label(trans_db('plans.maximum_users'))
                    ->numeric()
                    ->minValue(0),

                TextInput::make('document_price_in_plan')
                    // trans_db
                    ->label(trans_db('plans.document_price_in_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('document_price_outside_plan')
                    // trans_db
                    ->label(trans_db('plans.document_price_outside_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                // SMS Prices
                TextInput::make('sms_price_outside_plan')
                    // trans_db
                    ->label(trans_db('plans.sms_price_outside_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('sms_price_in_plan')
                    // trans_db
                    ->label(trans_db('plans.sms_price_in_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                // WhatsApp Prices
                TextInput::make('whatsapp_price_outside_plan')
                    // trans_db
                    ->label(trans_db('plans.whatsapp_price_outside_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('whatsapp_price_in_plan')
                    // trans_db
                    ->label(trans_db('plans.whatsapp_price_in_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                // Email Prices
                TextInput::make('email_price_outside_plan')
                    // trans_db
                    ->label(trans_db('plans.email_price_outside_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                TextInput::make('email_price_in_plan')
                    // trans_db
                    ->label(trans_db('plans.email_price_in_plan'))
                    ->required()
                    ->numeric()
                    ->minValue(0),

                Grid::make(3)
                    // ✅ خيار ترحيل الرصيد
                    ->schema([
                        Checkbox::make('carry_over_credit')
                            // trans_db
                            ->label(trans_db('plans.carry_over_credit'))
                            ->default(false),

                        Checkbox::make('enable_attendance')
                            // trans_db
                            ->label(trans_db('plans.enable_attendance'))
                            ->default(false),

                        Checkbox::make('enable_multiple_templates')
                            // trans_db
                            ->label(trans_db('plans.enable_multiple_templates'))
                            ->default(false),
                    ])->columnSpanFull(),

                Select::make('enabled_channels.documents')
                    // trans_db
                    ->label(trans_db('plans.enabled_channels_documents'))
                    ->multiple()
                    ->options([
                        // trans_db
                        'email' => trans_db('plans.channel_email'),
                        // trans_db
                        'sms' => trans_db('plans.channel_sms'),
                        // trans_db
                        'whatsapp' => trans_db('plans.channel_whatsapp'),
                    ])
                    ->columnSpanFull(),

                Select::make('enabled_channels.attendance')
                    // trans_db
                    ->label(trans_db('plans.enabled_channels_attendance'))
                    ->multiple()
                    ->options([
                        // trans_db
                        'email' => trans_db('plans.channel_email'),
                        // trans_db
                        'sms' => trans_db('plans.channel_sms'),
                        // trans_db
                        'whatsapp' => trans_db('plans.channel_whatsapp'),
                    ])
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('feature')
                    // trans_db
                    ->label(trans_db('plans.features'))
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans_db('plans.plan_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(trans_db('plans.price'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('compare_price')
                    ->label(trans_db('plans.compare_price'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('credit_amount')
                    ->label(trans_db('plans.credit_amount'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_days')
                    ->label(trans_db('plans.duration_days'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('feature')
                    ->label(trans_db('plans.features'))
                    ->html()
                    ->limit(50),
                Tables\Columns\IconColumn::make('carry_over_credit')
                    ->label(trans_db('plans.carry_over_credit_short'))
                    ->boolean()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_users')
                    ->label(trans_db('plans.maximum_users'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('enable_attendance')
                    ->label(trans_db('plans.enable_attendance_short'))
                    ->boolean(),
                TextColumn::make('document_price_in_plan')
                    ->label(trans_db('plans.doc_price_in'))
                    ->sortable(),
                TextColumn::make('document_price_outside_plan')
                    ->label(trans_db('plans.doc_price_out'))
                    ->sortable(),
                TextColumn::make('sms_price_in_plan')
                    ->label(trans_db('plans.sms_price_in'))
                    ->sortable(),
                TextColumn::make('sms_price_outside_plan')
                    ->label(trans_db('plans.sms_price_out'))
                    ->sortable(),
                TextColumn::make('whatsapp_price_in_plan')
                    ->label(trans_db('plans.whatsapp_price_in'))
                    ->sortable(),
                TextColumn::make('whatsapp_price_outside_plan')
                    ->label(trans_db('plans.whatsapp_price_out'))
                    ->sortable(),
                TextColumn::make('email_price_in_plan')
                    ->label(trans_db('plans.email_price_in'))
                    ->sortable(),
                TextColumn::make('email_price_outside_plan')
                    ->label(trans_db('plans.email_price_out'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('enable_multiple_templates')
                    ->label(trans_db('plans.multiple_templates_short'))
                    ->boolean(),
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
            ])
            // تفعيل RTL للعربية
            ->modifyQueryUsing(fn ($query) => $query)
            ->defaultSort('id', 'asc');
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
