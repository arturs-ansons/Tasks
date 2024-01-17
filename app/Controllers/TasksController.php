<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DatabaseService;
use App\Models\Task;
use App\Repositories\MySqlTaskRepository;
use App\TasksView;

class TasksController extends BaseController
{
    private const REQUIRED_FIELDS_MSG = "Both Task and Description are required.";
    private const DUPLICATE_TASK_MSG = "Task with the same title already exists. Please choose another!";


    public function __construct(DatabaseService $dbService, TasksView $tasksView, MySqlTaskRepository $taskRepository)
    {
        parent::__construct($dbService, $tasksView, $taskRepository);
    }

    /**
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    public function index(): string
    {
        $allTasks = $this->taskRepository->getAll();

        return $this->tasksView->renderIndex($allTasks);
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function view(array $params): string
    {
        $taskId = (string)($params['task_name'] ?? 0);

        $task = $this->dbService->getTaskByTitle($taskId);

        if (!$task) {
            header("Location: /not-found", true, 404);
            exit();
        }

        return $this->tasksView->renderSingleTask($task);
    }

    public function delete(array $params): void
    {
        $taskId = (int)($params['id'] ?? 0);

        if ($taskId === 0) {
            header("Location: /not-found", true, 404);
            exit();
        }

        $this->dbService->deleteTask($taskId);

        header("Location: /");
        exit();
    }

    public function insert(): void
    {
        $taskName = $_POST['task_name'] ?? '';
        $taskDescription = $_POST['task_description'] ?? '';

        if (empty($taskName) || empty($taskDescription)) {
            $this->handleValidationFailure(self::REQUIRED_FIELDS_MSG);
        }

        if ($this->dbService->getTaskByTitle($taskName) === null) {
            $taskData = new Task(
                0,
                $taskName,
                $taskDescription,
                date('Y-m-d H:i:s')
            );

            $this->dbService->createTask($taskData);
            http_response_code(201);
            die();
        } else {
            $this->handleValidationFailure(self::DUPLICATE_TASK_MSG);
        }
    }

    private function handleValidationFailure(string $validationMessage): void
    {
        http_response_code(400);
        die(json_encode(['validationMessage' => $validationMessage]));
    }
}
