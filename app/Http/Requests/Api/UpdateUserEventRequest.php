<?php

namespace App\Http\Requests\Api;

use App\Rules\Api\ValidFullCalendarEventStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['event' => $this->route('event')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event' => 'required|string|uuid',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => ['nullable', new ValidFullCalendarEventStatus]
        ];
    }
}
