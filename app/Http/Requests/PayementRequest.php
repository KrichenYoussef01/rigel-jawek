<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayementRequest extends FormRequest
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
        'plan_name'      => 'required|string|in:Starter,Business,Enterprise',
        'amount'         => 'required|numeric|min:0.01',
        'payment_method' => 'required|string|in:card,cash,carte,especes',
        'payment_token'  => 'nullable|string',

        // Champs carte — nullable car pas requis si espèces
        'card_name'      => 'nullable|string|max:100',
        'card_number'    => 'nullable|string|max:19',
        'expiry_date'    => 'nullable|string|max:5',
        'cvv'            => 'nullable|string|max:4',
    ];
}
}
