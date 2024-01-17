<?php

declare(strict_types=1);

namespace App;

use App\Models\TaskCollection;
use Twig\Environment;

class TasksView
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function renderIndex(TaskCollection $allTasks): string
    {
        return $this->twig->render('index.twig', [
            'allTasks' => $allTasks
        ]);
    }
}
