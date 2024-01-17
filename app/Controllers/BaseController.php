<?php

namespace App\Controllers;

use App\DatabaseService;
use App\Repositories\MySqlTaskRepository;
use App\TasksView;

class BaseController
{
    protected DatabaseService $dbService;
    protected TasksView $tasksView;
    protected MySqlTaskRepository $taskRepository;

    public function __construct(DatabaseService $dbService, TasksView $tasksView, MySqlTaskRepository $taskRepository)
    {
        $this->dbService = $dbService;
        $this->tasksView = $tasksView;
        $this->taskRepository = $taskRepository;
    }

}
