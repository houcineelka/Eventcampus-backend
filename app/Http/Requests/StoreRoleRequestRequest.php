<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'email'              => 'required|email',
            'student_id'         => 'nullable|string|max:50',
            'is_existing_student' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'               => 'Le nom est obligatoire.',
            'email.required'              => 'L\'email est obligatoire.',
            'email.email'                 => 'L\'email doit être une adresse valide.',
            'is_existing_student.required' => 'Le champ is_existing_student est obligatoire.',
            'is_existing_student.boolean'  => 'Le champ is_existing_student doit être vrai ou faux.',
        ];
    }
}
