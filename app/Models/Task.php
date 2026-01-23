<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'user_id',
        'due_date',
        'priority',
        'hourly_rate',
        'currency',
        'completed',
        'is_recurring',
        'recurrence_type',
        'recurrence_interval',
        'recurrence_ends_at',
        'parent_task_id',
        'next_occurrence',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed' => 'boolean',
        'is_recurring' => 'boolean',
        'recurrence_ends_at' => 'date',
        'next_occurrence' => 'date',
        'hourly_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function timeSessions()
    {
        return $this->hasMany(TimeSession::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_task');
    }

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function childTasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function calculateNextOccurrence(): ?string
    {
        if (!$this->is_recurring || !$this->recurrence_type) {
            return null;
        }

        $baseDate = $this->next_occurrence ?? $this->due_date ?? now();
        
        $nextDate = match($this->recurrence_type) {
            'daily' => $baseDate->addDays($this->recurrence_interval),
            'weekly' => $baseDate->addWeeks($this->recurrence_interval),
            'monthly' => $baseDate->addMonths($this->recurrence_interval),
            default => null,
        };

        if ($nextDate && $this->recurrence_ends_at && $nextDate->gt($this->recurrence_ends_at)) {
            return null;
        }

        return $nextDate?->format('Y-m-d');
    }
}
