<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'user_id',
        'due_date',
        'priority',
        'completed',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed' => 'boolean',
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
}
