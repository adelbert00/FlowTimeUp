<?php

namespace App\Http\Requests\TimeSessions;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimeSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'task_id.required' => 'Task is required.',
            'task_id.exists' => 'Selected task does not exist.',
            'start_time.required' => 'Start time is required.',
            'start_time.date' => 'Start time must be a valid date.',
            'end_time.after_or_equal' => 'End time must be after or equal to start time.',
        ];
    }
}
