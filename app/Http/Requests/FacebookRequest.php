<?php
// app/Http/Requests/StoreFacebookSessionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacebookRequest extends FormRequest
{
    /**
     * Autoriser la requête (l'authentification est vérifiée dans le contrôleur)
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'total_comments'          => 'required|integer',
            'total_clients'           => 'required|integer',
            'total_articles'          => 'required|integer',
            'total_phones'            => 'required|integer',
            'raw_data'                => 'nullable|string',
            'fb_username'             => 'nullable|string|max:255',
            'baskets'                 => 'nullable|array',
            'baskets.*.client_name'   => 'required_with:baskets|string',
            'baskets.*.articles'      => 'nullable|array',
            'baskets.*.phones'        => 'nullable|array',
            'baskets.*.time'          => 'nullable|string',
        ];
    }

    /**
     * Messages personnalisés (optionnel)
     */
    public function messages(): array
    {
        return [
            'total_comments.required' => 'Le total des commentaires est requis.',
            'total_clients.required'  => 'Le total des clients est requis.',
            'total_articles.required' => 'Le total des articles est requis.',
            'total_phones.required'   => 'Le total des téléphones est requis.',
        ];
    }
}