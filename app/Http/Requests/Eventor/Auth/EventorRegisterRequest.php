<?php

namespace App\Http\Requests\Eventor\Auth;

use App\Models\Plan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class EventorRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    protected function prepareForValidation(): void
    {
        // 1. تنظيف رقم الهاتف وإزالة أي مسافات أو رموز باستثناء الأرقام
        $phoneNumber = preg_replace('/[^\d]/', '', $this->phone_number);
        
        // 2. التأكد من أن مفتاح الدولة يبدأ بـ '+'
        $countryCode = trim($this->country_code);
        if (!empty($countryCode) && $countryCode[0] !== '+') {
            $countryCode = '+' . $countryCode;
        }

        // 3. دمج المفتاح والرقم في حقل 'phone'
        $fullPhone = $countryCode . $phoneNumber;

        $this->merge([
            'phone' => $fullPhone, 
        ]);
    }
    public function rules(): array
    {
        $rules = $this->baseRules();

        $this->appendPlanReceiptRule($rules);

        return $rules;
    }

    private function baseRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:255','unique:users,phone'],
            // ✅ الحقول المنفصلة للتحقق من إدخال المستخدم
            'country_code' => ['required', 'string', 'max:5'], 
            'phone_number' => ['required', 'string', 'max:15'],
            'category' => ['required', 'exists:categories,id'],
            'role' => ['required', 'in:eventor'],
            'plan' => ['required', 'exists:plans,id'],
            'terms_agreement' => ['accepted'],
        ];
    }

    private function appendPlanReceiptRule(array &$rules): void
    {
        $planId = $this->input('plan');

        if (! $planId) {
            return;
        }

        $plan = Plan::query()->find($planId);

        if ($plan && $plan->price > 0) {
            $rules["payment_receipt.$planId"] = ['required', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
        }
    }
}
