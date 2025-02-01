<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSession extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'start_time', 'end_time'];
    protected $casts = [
        'start_time' => 'datetime:Y-m-d H:i:s.u',
        'end_time'   => 'datetime:Y-m-d H:i:s.u',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
