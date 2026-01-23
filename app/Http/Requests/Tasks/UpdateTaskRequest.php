<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['nullable', 'string', Rule::in(['low', 'medium', 'high'])],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'currency' => ['nullable', 'string', 'size:3'],
            'completed' => ['sometimes', 'boolean'],
            'is_recurring' => ['boolean'],
            'recurrence_type' => ['nullable', 'string', Rule::in(['daily', 'weekly', 'monthly'])],
            'recurrence_interval' => ['nullable', 'integer', 'min:1', 'max:365'],
            'recurrence_ends_at' => ['nullable', 'date'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', Rule::exists('tags', 'id')->where('user_id', $this->user()->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title cannot exceed 255 characters.',
            'project_id.exists' => 'Selected project does not exist.',
            'priority.in' => 'Priority must be low, medium, or high.',
            'recurrence_type.in' => 'Recurrence type must be daily, weekly, or monthly.',
            'tag_ids.*.exists' => 'One or more selected tags do not exist.',
        ];
    }
}
