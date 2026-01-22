<?php

namespace App\DTO;

readonly class TimeSessionDTO
{
    public function __construct(
        public int $taskId,
        public string $startTime,
        public ?string $endTime = null,
        public ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            taskId: $data['task_id'],
            startTime: $data['start_time'],
            endTime: $data['end_time'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'task_id' => $this->taskId,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'notes' => $this->notes,
        ], fn($value) => $value !== null);
    }
}
