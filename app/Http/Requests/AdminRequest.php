<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'                => 'required|email',
            'password'             => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'                => 'L\'adresse email est obligatoire.',
            'email.email'                   => 'L\'adresse email n\'est pas valide.',
            'password.required'             => 'Le mot de passe est obligatoire.',
            'g-recaptcha-response.required' => 'Veuillez compléter le reCAPTCHA.',
            'g-recaptcha-response.captcha'  => 'Vérification reCAPTCHA échouée. Réessayez.',
        ];
    }
}