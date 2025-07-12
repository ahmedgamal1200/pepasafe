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
            'phone' => ['required', 'string', 'max:255'],
            'category' => ['required', 'exists:categories,id'],
            'role' => ['required', 'in:eventor'],
            'plan' => ['required', 'exists:plans,id'],
            'terms_agreement' => ['accepted'],
        ];
    }

    private function appendPlanReceiptRule(array &$rules): void
    {
        $planId = $this->input('plan');

        if (!$planId) {
            return;
        }

        $plan = Plan::query()->find($planId);

        if ($plan && $plan->price > 0) {
            $rules["payment_receipt.$planId"] = ['required', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
        }
    }
}
