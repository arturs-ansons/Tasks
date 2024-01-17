<?php

declare(strict_types=1);

namespace App;

use App\Models\Task;
use App\Models\TaskCollection;
use PDO;
use Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class DatabaseService
{
    private PDO $db;
    private ?Environment $twig = null;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $dsn = $_ENV['DB_DSN'] ?? null;
        $user = $_ENV['DB_USER'] ?? null;
        $password = $_ENV['DB_PASSWORD'] ?? null;

        try {
            $this->db = new PDO($dsn, $user, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            exit("Database connection error");
        }
    }

    public function getTaskByTitle(string $task): ?Task
    {
        try {
            $statement = $this->db->prepare("
        SELECT * FROM tasks 
        WHERE task_name = :task");

            $statement->execute(['task' => $task]);

            $taskData = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$taskData) {
                return null;
            }

            return new Task(
                (int)$taskData['id'],
                $taskData['task_name'],
                $taskData['task_description'],
                $taskData['created_at'],
            );
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            return null;
        }
    }

    public function createTask(Task $taskData): void
    {
        try {
            $statement = $this->db->prepare("
            INSERT INTO tasks (task_name, task_description, created_at) 
            VALUES (:task_name, :task_description, :created_at)");

            $statement->execute([
                'task_name' => $taskData->getTaskName(),
                'task_description' => $taskData->getTaskDescription(),
                'created_at' => $taskData->getDateCreated(),
            ]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
        }
    }

    public function getAllTasks(): TaskCollection
    {
        try {
            $statement = $this->db->query("SELECT * FROM tasks");
            $taskData = $statement->fetchAll(PDO::FETCH_ASSOC);

            $allTasks = new TaskCollection();

            foreach ($taskData as $taskData) {
                $task = new Task(
                    (int)$taskData['id'],
                    $taskData['task_name'],
                    $taskData['task_description'],
                    $taskData['created_at'],
                );

                $allTasks->add($task);
            }

            return $allTasks;
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            return new TaskCollection();
        }
    }

    public function deleteTask(int $taskId): void
    {
        try {
            $statement = $this->db->prepare("
            DELETE FROM tasks
            WHERE id = :id");

            $statement->execute(['id' => $taskId]);
        } catch (\PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
        }
    }
    public function getTwig(): Environment
    {
        if ($this->twig === null) {
            $loader = new FilesystemLoader(__DIR__ . '/Views');
            $this->twig = new Environment($loader);
        }

        return $this->twig;
    }

}
