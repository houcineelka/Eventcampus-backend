<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Le contenu du message est obligatoire.',
            'content.string' => 'Le contenu doit être du texte.',
            'content.max' => 'Le message ne peut pas dépasser 1000 caractères.',
            'member_ids.array' => 'Les IDs des membres doivent être un tableau.',
            'member_ids.*.integer' => 'Chaque ID de membre doit être un entier.',
            'member_ids.*.exists' => 'Un des IDs de membre n\'existe pas.',
        ];
    }
}
