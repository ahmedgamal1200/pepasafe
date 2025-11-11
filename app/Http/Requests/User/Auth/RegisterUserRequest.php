<?php

namespace App\Http\Requests\User\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation(): void
    {
        // 1. تنظيف رقم الهاتف وإزالة أي مسافات أو رموز
        // يتم استخدام `phone_number` لأنه الحقل الذي أدخله المستخدم بدون المفتاح
        $phoneNumber = preg_replace('/[^\d]/', '', $this->phone_number);
        
        // 2. التأكد من أن مفتاح الدولة يبدأ بـ '+'
        $countryCode = trim($this->country_code);
        if (!empty($countryCode) && $countryCode[0] !== '+') {
            $countryCode = '+' . $countryCode;
        }

        // 3. دمج المفتاح والرقم في حقل 'phone'
        $fullPhone = $countryCode . $phoneNumber;

        $this->merge([
            // هذا هو الحقل النهائي الذي سيتم تخزينه في قاعدة البيانات
            'phone' => $fullPhone, 
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:255','unique:users,phone'],
            'country_code' => ['required', 'string', 'max:5'], 
            'phone_number' => ['required', 'string', 'max:15'],
            'category' => ['nullable'],
            'role' => ['required', 'in:user'],
            'terms_agreement' => ['accepted'],
        ];
    }
}
