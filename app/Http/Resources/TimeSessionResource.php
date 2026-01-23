<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $durationSeconds = $this->start_time && $this->end_time 
            ? $this->start_time->diffInSeconds($this->end_time) 
            : 0;

        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'start_time' => $this->start_time?->format('Y-m-d H:i:s'),
            'end_time' => $this->end_time?->format('Y-m-d H:i:s'),
            'duration' => $durationSeconds,
            'is_billable' => $this->is_billable ?? true,
            'billable_rate' => $this->billable_rate ? (float) $this->billable_rate : null,
            'description' => $this->description,
            'earnings' => $this->earnings,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'task' => $this->whenLoaded('task', fn() => new TaskResource($this->task)),
        ];
    }
}
