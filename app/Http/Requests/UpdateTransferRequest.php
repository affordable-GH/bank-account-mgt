<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransferRequest extends FormRequest
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
            'to_account' => 'required|exists:accounts,id|different:from_account',
            'from_account' => 'required|exists:accounts,id|different:to_account',
            'amount' => 'required|numeric|min:1',
            'notes' => 'required|string',
        ];
    }
    public function messages(): array
    {
        return  [
            'notes.required' => 'Description field is required',
            'to_account.different' => 'FROM ACCOUNT and TO ACCOUNT must not be the same',
            'from_account.different' => 'TO ACCOUNT and FROM ACCOUNT must not be the same'
        ];
    }
}
