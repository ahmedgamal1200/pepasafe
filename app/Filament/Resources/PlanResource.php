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

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationGroup = 'Subscriptions';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

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
            ->schema([
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
