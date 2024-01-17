<?php

declare(strict_types=1);

namespace App\Models;

class TaskCollection
{
    private array $taskCollection = [];

    public function add(Task $task): void
    {
        $id = $task->getId();

        if (!isset($this->taskCollection[$id])) {
            $this->taskCollection[$id] = $task;
        }
    }

    public function getTaskCollection(): array
    {
        return $this->taskCollection;
    }
}
