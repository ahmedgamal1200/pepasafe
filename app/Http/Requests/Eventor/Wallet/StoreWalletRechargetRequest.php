<?php

namespace App\Http\Requests\Eventor\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class StoreWalletRechargetRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'plan_id' => ['required', 'exists:plans,id'],
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'receipt_path' => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ];
    }
}
