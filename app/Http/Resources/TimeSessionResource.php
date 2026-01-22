<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'start_time' => $this->start_time?->format('Y-m-d H:i:s'),
            'end_time' => $this->end_time?->format('Y-m-d H:i:s'),
            'duration' => $this->when(
                $this->start_time && $this->end_time,
                fn() => $this->start_time->diffInSeconds($this->end_time)
            ),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'task' => $this->whenLoaded('task', fn() => new TaskResource($this->task)),
        ];
    }
}
