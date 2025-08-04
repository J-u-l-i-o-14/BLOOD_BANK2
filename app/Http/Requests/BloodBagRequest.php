<?php

namespace App\Http\Requests;

use App\Traits\ValidatesDateRanges;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BloodBagRequest extends FormRequest
{
    use ValidatesDateRanges;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Vérifie que l'utilisateur a le rôle admin ou manager
        return $this->user()->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'blood_type_id' => ['required', 'exists:blood_types,id'],
            'quantity_ml' => ['required', 'integer', 'min:200', 'max:500'],
            'center_id' => ['required', 'exists:blood_donation_centers,id'],
            'status' => ['required', Rule::in(['available', 'reserved', 'used', 'expired'])],
            'collection_date' => [
                'required', 
                'date',
                'before_or_equal:now',
                // Date minimale est 1970 pour éviter les erreurs de saisie
                'after:1970-01-01'
            ],
            'expiry_date' => [
                'required',
                'date',
                'after:collection_date',
                function ($attribute, $value, $fail) {
                    $collectionDate = Carbon::parse($this->collection_date);
                    $expiryDate = Carbon::parse($value);
                    
                    // Vérifie que la durée de conservation ne dépasse pas 42 jours
                    if ($collectionDate->diffInDays($expiryDate) > 42) {
                        $fail('La durée de conservation ne peut pas dépasser 42 jours.');
                    }

                    // Si la poche est expirée, le statut doit être "expired"
                    if ($expiryDate->isPast() && $this->status !== 'expired') {
                        $fail('Cette poche est expirée. Le statut doit être "expired".');
                    }
                }
            ],
            'donor_name' => ['required', 'string', 'max:255'],
            'donor_phone' => ['required', 'regex:/^[0-9]{10}$/', 'max:10'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'blood_type_id' => 'type sanguin',
            'quantity_ml' => 'volume',
            'center_id' => 'centre de collecte',
            'status' => 'statut',
            'collection_date' => 'date de prélèvement',
            'expiry_date' => 'date d\'expiration',
            'donor_name' => 'nom du donneur',
            'donor_phone' => 'téléphone du donneur',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'blood_type_id.required' => 'Le type sanguin est obligatoire.',
            'blood_type_id.exists' => 'Le type sanguin sélectionné n\'existe pas.',
            
            'quantity_ml.required' => 'Le volume est obligatoire.',
            'quantity_ml.integer' => 'Le volume doit être un nombre entier.',
            'quantity_ml.min' => 'Le volume minimum est de 200 ml.',
            'quantity_ml.max' => 'Le volume maximum est de 500 ml.',
            
            'center_id.required' => 'Le centre de collecte est obligatoire.',
            'center_id.exists' => 'Le centre de collecte sélectionné n\'existe pas.',
            
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être l\'un des suivants : disponible, réservé, utilisé ou expiré.',
            
            'collection_date.required' => 'La date de prélèvement est obligatoire.',
            'collection_date.date' => 'La date de prélèvement n\'est pas valide.',
            'collection_date.before_or_equal' => 'La date de prélèvement ne peut pas être dans le futur.',
            'collection_date.after' => 'La date de prélèvement n\'est pas valide.',
            
            'expiry_date.required' => 'La date d\'expiration est obligatoire.',
            'expiry_date.date' => 'La date d\'expiration n\'est pas valide.',
            'expiry_date.after' => 'La date d\'expiration doit être postérieure à la date de prélèvement.',
            
            'donor_name.required' => 'Le nom du donneur est obligatoire.',
            'donor_name.string' => 'Le nom du donneur doit être une chaîne de caractères.',
            'donor_name.max' => 'Le nom du donneur ne peut pas dépasser 255 caractères.',
            
            'donor_phone.required' => 'Le téléphone du donneur est obligatoire.',
            'donor_phone.regex' => 'Le téléphone du donneur doit contenir exactement 10 chiffres.',
            'donor_phone.max' => 'Le téléphone du donneur doit contenir exactement 10 chiffres.',
        ];
    }
}
