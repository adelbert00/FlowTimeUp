<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'budget' => 'decimal:2',
    ];

    public function getTotalTrackedSecondsAttribute(): int
    {
        return $this->tasks()
            ->with('timeSessions')
            ->get()
            ->flatMap(fn($task) => $task->timeSessions)
            ->filter(fn($session) => $session->end_time !== null)
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
        if (!$this->hourly_rate) {
            return 0;
        }
        $hours = $this->total_tracked_seconds / 3600;
        return round($hours * $this->hourly_rate, 2);
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
