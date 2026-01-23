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
        // Sum earnings from all tasks in this project using their actual rates
        // This ensures we use task rates or session rates, not just project rate
        $total = 0;
        
        $tasks = $this->tasks()->with('timeSessions')->get();
        
        foreach ($tasks as $task) {
            foreach ($task->timeSessions as $session) {
                if (!$session->is_billable || !$session->end_time) {
                    continue;
                }
                
                // Use session rate, fallback to task rate, fallback to project rate
                $rate = $session->billable_rate 
                    ?? $task->hourly_rate 
                    ?? $this->hourly_rate;
                
                if ($rate) {
                    $hours = $session->start_time->diffInSeconds($session->end_time) / 3600;
                    $total += $hours * $rate;
                }
            }
        }
        
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
