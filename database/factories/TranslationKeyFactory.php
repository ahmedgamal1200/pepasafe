<?php

namespace Database\Factories;


use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Model>
 */
class TranslationKeyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TranslationKey::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            // The key is typically defined when calling the factory in the seeder
            // or when extending the factory state.
            'key' => $this->faker->unique()->word() . '.' . $this->faker->word() . '.' . $this->faker->word(),
        ];
    }

    /**
     * Configure the factory to create a key with a specific Arabic value.
     *
     * @param string $key
     * @param string $value
     * @return Factory
     */
    public function withArabicValue(string $key, string $value): Factory
    {
        return $this->state(fn (array $attributes) => [
            'key' => $key,
        ])->afterCreating(function (TranslationKey $translationKey) use ($value) {
            $translationKey->values()->create([
                'locale' => 'ar',
                'value' => $value,
            ]);
        });
    }

    /**
     * Configure the factory to create a key with Arabic and English values.
     *
     * @param string $key
     * @param string $arValue
     * @param string|null $enValue
     * @return Factory
     */
    public function withValues(string $key, string $arValue, ?string $enValue = null): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => $key,
            // نخزن القيم هنا مؤقتاً
            'ar_value' => $arValue,
            'en_value' => $enValue,
        ]);
    }


    // أضف هذه الطريقة في TranslationKeyFactory
    public function createOrUpdateValues(string $key, string $arValue, ?string $enValue = null): TranslationKey
    {
        // 1. تحديث أو إنشاء الـ TranslationKey الرئيسي
        /** @var TranslationKey $translationKey */
        $translationKey = TranslationKey::updateOrCreate(
            ['key' => $key], // الشرط: البحث بناءً على المفتاح
            ['key' => $key] // البيانات: إذا كان جدول TranslationKey لا يحوي إلا الـ key
        );

        // 2. تحديث أو إنشاء قيم الترجمة المرتبطة (Translation Values)

        // القيم العربية (Mandatory)
        $translationKey->values()->updateOrCreate(
            ['locale' => 'ar'],
            ['value' => $arValue]
        );

        // القيم الإنجليزية (Optional)
        if ($enValue) {
            $translationKey->values()->updateOrCreate(
                ['locale' => 'en'],
                ['value' => $enValue]
            );
        }

        return $translationKey;
    }
}
