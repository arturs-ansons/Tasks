<?php

namespace App\Repositories;

use App\DatabaseService;
use App\Models\TaskCollection;

class MySqlTaskRepository
{

    private DatabaseService $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function getAll(): TaskCollection
    {
        try {
            return $this->databaseService->getAllTasks();
        } catch (\PDOException $e) {
            return new TaskCollection();
        }
    }
}
