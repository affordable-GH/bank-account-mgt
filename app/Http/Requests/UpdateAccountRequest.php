<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
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
            'account_number' => 'required|unique:accounts,account_number,' . $this->account->id . ',id',
            'bank_name' => 'required|string',
            // 'name' => 'required|string',
            // 'account_address' => 'required|string',
            // 'initial_amount' => 'required|numeric|min:0',
            'account_description' => 'nullable|string',
            'account_type' => 'required|exists:account_types,id',
            'account_status' => 'required|exists:account_statuses,id',
            'created_at' => 'date'
        ];
    }
    public function messages(): array
    {
        return [
            'account_number.unique' => 'Account Number already exists',
            'initial_amount.min' => 'Initial Amount must be at least zero'
        ];
    }
}
