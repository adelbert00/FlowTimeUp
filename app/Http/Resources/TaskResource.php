<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'project_id' => $this->project_id,
            'user_id' => $this->user_id,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'priority' => $this->priority,
            'completed' => $this->completed ?? false,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'project' => $this->whenLoaded('project', fn() => new ProjectResource($this->project)),
            'tags' => $this->relationLoaded('tags') ? TagResource::collection($this->tags)->resolve() : [],
            'time_sessions' => $this->whenLoaded('timeSessions', fn() => TimeSessionResource::collection($this->timeSessions)),
            'total_time' => $this->when(
                $this->relationLoaded('timeSessions'),
                fn() => $this->calculateTotalTime()
            ),
        ];
    }

    private function calculateTotalTime(): string
    {
        if (!$this->relationLoaded('timeSessions')) {
            return '00:00:00';
        }

        $totalSeconds = $this->timeSessions->sum(function ($session) {
            if (!$session->end_time) {
                return 0;
            }
            return $session->start_time->diffInSeconds($session->end_time);
        });

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
