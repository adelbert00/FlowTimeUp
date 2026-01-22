<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'priority' => ['nullable', 'string', Rule::in(['low', 'medium', 'high'])],
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
            'due_date.after_or_equal' => 'Due date must be today or in the future.',
            'priority.in' => 'Priority must be low, medium, or high.',
            'tag_ids.*.exists' => 'One or more selected tags do not exist.',
        ];
    }
}
