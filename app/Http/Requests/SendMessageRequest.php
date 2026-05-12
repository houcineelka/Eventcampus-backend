<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'recipient_id' => 'nullable|integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Le contenu du message est obligatoire.',
            'content.string' => 'Le contenu doit être du texte.',
            'content.max' => 'Le message ne peut pas dépasser 1000 caractères.',
            'recipient_id.integer' => 'L\'ID du destinataire doit être un entier.',
            'recipient_id.exists' => 'Le destinataire n\'existe pas.',
        ];
    }
}
