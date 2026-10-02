<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Support the app's split-name fields as well as the legacy single `name` field.
            'f_name' => ['sometimes', 'nullable', 'string', 'max:50'],
            'm_name' => ['nullable', 'string', 'max:50'],
            'l_name' => ['sometimes', 'nullable', 'string', 'max:50'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = trim((string) $this->input('name', ''));

        if ($name !== '' && ($this->missing('f_name') || $this->missing('l_name'))) {
            $parts = preg_split('/\s+/', $name, 2);
            $first = $parts[0] ?? '';
            $last = $parts[1] ?? '';

            if ($this->missing('f_name')) {
                $this->merge(['f_name' => $first]);
            }

            if ($this->missing('l_name')) {
                $this->merge(['l_name' => $last]);
            }
        }
    }
}