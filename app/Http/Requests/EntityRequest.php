<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'session_id' => ['required', 'string', 'regex:/^[0-9A-HJKMNP-TV-Z]{26}$/'],
            'week' => ['nullable', 'int']
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes('week', 'required|integer|min:1', function () {
            return $this->routeIs('match.playWeek'); 
        });
        $validator->sometimes('session_id', 'exists:league,ulid', function () {
            return !$this->routeIs('league.create');
        });
    }

    public function authorize(): bool
    {
        return true;
    }
}