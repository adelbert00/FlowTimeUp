<?php

namespace App\Http\Requests\TimeSessions;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Task;

class StoreTimeSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_id' => [
                'required',
                'exists:tasks,id',
                function ($attribute, $value, $fail) {
                    $task = Task::find($value);
                    if (!$task || $task->user_id !== auth()->id()) {
                        $fail('The selected task is invalid.');
                    }
                },
            ],
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_billable' => 'boolean',
            'billable_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
