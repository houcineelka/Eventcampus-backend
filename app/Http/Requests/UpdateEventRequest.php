<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Event;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = Event::findOrFail($this->route('id'));
        return $this->user()->id === $event->user_id && $this->user()->id === $event->club->createur_id;
    }

    public function rules(): array
    {
        return [
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'heure' => 'required|date_format:H:i',
            'date_fin' => 'required|date_format:Y-m-d|after_or_equal:date',
            'heure_fin' => 'required|date_format:H:i',
            'lieu' => 'required|string|max:255',
            'categorie' => 'required|string|in:Conférence,Atelier,Soirée,Réunion de club,Hackathon,Environnement',
            'club_id' => 'required|integer|exists:clubs,id',
            'capacite_max' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est requis.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',

            'description.required' => 'La description est requise.',
            'description.string' => 'La description doit être une chaîne de caractères.',

            'date.required' => 'La date est requise.',
            'date.date_format' => 'Le format de la date doit être YYYY-MM-DD.',
            'date.after_or_equal' => 'La date doit être aujourd\'hui ou dans le futur.',

            'heure.required' => 'L\'heure est requise.',
            'heure.date_format' => 'Le format de l\'heure doit être HH:MM.',

            'date_fin.required' => 'La date de fin est requise.',
            'date_fin.date_format' => 'Le format de la date de fin doit être YYYY-MM-DD.',
            'date_fin.after_or_equal' => 'La date de fin doit être égale ou supérieure à la date de début.',

            'heure_fin.required' => 'L\'heure de fin est requise.',
            'heure_fin.date_format' => 'Le format de l\'heure de fin doit être HH:MM.',

            'lieu.required' => 'Le lieu est requis.',
            'lieu.string' => 'Le lieu doit être une chaîne de caractères.',
            'lieu.max' => 'Le lieu ne peut pas dépasser 255 caractères.',

            'categorie.required' => 'La catégorie est requise.',
            'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
            'categorie.in' => 'La catégorie doit être l\'une des valeurs autorisées: Conférence, Atelier, Soirée, Réunion de club, Hackathon, Environnement.',

            'club_id.required' => 'L\'ID du club est requis.',
            'club_id.integer' => 'L\'ID du club doit être un entier.',
            'club_id.exists' => 'Le club sélectionné n\'existe pas.',

            'capacite_max.required' => 'La capacité maximale est requise.',
            'capacite_max.integer' => 'La capacité maximale doit être un entier.',
            'capacite_max.min' => 'La capacité maximale doit être supérieure à 0.',
        ];
    }
}
