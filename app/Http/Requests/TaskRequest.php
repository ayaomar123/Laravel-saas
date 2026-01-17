<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['completed'] = ['boolean'];
        }

        return $rules;
    }
}