<?php

declare(strict_types=1);

namespace App\Models;

class Task extends TaskCollection
{
    private int $id;
    private string $task_name;
    private string $task_description;
    private string $created_at;


    public function __construct(int $id, string $task_name, string $task_description, string $created_at)
    {
        $this->id = $id;
        $this->task_name = $task_name;
        $this->task_description = $task_description;
        $this->created_at = $created_at;

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTaskName(): string
    {
        return $this->task_name;
    }


    public function getTaskDescription(): string
    {
        return $this->task_description;
    }

    /**
     * @throws \Exception
     */
    public function getDateCreated(): string
    {

            $dateCreated = new \DateTimeImmutable($this->created_at);

            return $dateCreated->format('Y-m-d H:i');
    }
}
