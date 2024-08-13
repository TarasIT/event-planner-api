<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:50',
            'description' => 'string|max:255',
            'date' => 'required|string',
            'time' => 'required|string',
            'location' => 'string|max:50',
            'category' => 'string|max:50',
            'picture' => 'image|mimes:jpeg,png,jpg,avif,gif,webp,svg|max:20',
            'priority' => 'string|in:Low,Medium,High'
        ];
    }
}
