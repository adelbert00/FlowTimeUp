<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TimeSession;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'color',
        'user_id',
        'hourly_rate',
        'budget',
        'currency',
        'access',
        'is_archived',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'budget' => 'decimal:2',
        'is_archived' => 'boolean',
    ];

    public function getTotalTrackedSecondsAttribute(): int
    {
        return TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->where('tasks.project_id', $this->id)
            ->whereNotNull('time_sessions.end_time')
            ->get()
            ->sum(fn($session) => $session->start_time->diffInSeconds($session->end_time));
    }

    public function getFormattedTrackedTimeAttribute(): string
    {
        $seconds = $this->total_tracked_seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function getTotalAmountAttribute(): float
    {
        $sessions = TimeSession::join('tasks', 'time_sessions.task_id', '=', 'tasks.id')
            ->where('tasks.project_id', $this->id)
            ->where('time_sessions.is_billable', true)
            ->whereNotNull('time_sessions.end_time')
            ->select('time_sessions.*', 'tasks.hourly_rate as task_rate')
            ->get();
        
        $total = $sessions->sum(function ($session) {
            $rate = $session->billable_rate ?? $session->task_rate ?? $this->hourly_rate;
            if (!$rate) {
                return 0;
            }
            $hours = $session->start_time->diffInSeconds($session->end_time) / 3600;
            return $hours * $rate;
        });
        
        return round($total, 2);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
