<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_id',
        'start_time',
        'end_time',
        'is_billable',
        'billable_rate',
        'description',
        'notes',
    ];
    
    protected $casts = [
        'start_time' => 'datetime:Y-m-d H:i:s.u',
        'end_time' => 'datetime:Y-m-d H:i:s.u',
        'is_billable' => 'boolean',
        'billable_rate' => 'decimal:2',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function getDurationInSecondsAttribute(): int
    {
        if (!$this->end_time) {
            return 0;
        }
        return $this->start_time->diffInSeconds($this->end_time);
    }

    public function getEarningsAttribute(): ?float
    {
        if (!$this->is_billable || !$this->end_time) {
            return null;
        }

        $rate = $this->billable_rate ?? $this->task?->hourly_rate;
        if (!$rate) {
            return null;
        }

        $hours = $this->duration_in_seconds / 3600;
        return round($hours * $rate, 2);
    }
}
