<?php

require_once "../app/models/Task.php";

class TaskController
{
    private $Task;

    public function __construct()
    {
        $this->Task = new Task();
    }

    public function index()
    {
        $tasks = $this->Task->getTasksByUser(
            $_SESSION['user_id']
        );

        require "../app/views/tasks.php";
    }

    public function store()
    {
        $data = [
            'user_id' => $_SESSION['user_id'],
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'priority' => $_POST['priority'],
            'status' => $_POST['status'],
            'due_date' => $_POST['due_date']
        ];

        $this->Task->createTask($data);

        $_SESSION['success'] = "Task added successfully.";

        header("Location: index.php?page=tasks");
        exit();
    }

    public function delete()
    {
        $taskId = $_POST['task_id'];

        $this->Task->deleteTask(
            $taskId,
            $_SESSION['user_id']
        );

        $_SESSION['success'] = "Task deleted successfully.";

        header("Location: index.php?page=tasks");
        exit();
    }
public function edit()
{
    $taskId = $_GET['task_id'];

    $editingTask = $this->Task->getTaskById(
        $taskId,
        $_SESSION['user_id']
    );

    $tasks = $this->Task->getTasksByUser(
        $_SESSION['user_id']
    );

    require "../app/views/tasks.php";
}

    public function update()
{
    $data = [
        'task_id' => $_POST['task_id'],
        'user_id' => $_SESSION['user_id'],
        'title' => trim($_POST['title']),
        'description' => trim($_POST['description']),
        'priority' => $_POST['priority'],
        'status' => $_POST['status'],
        'due_date' => $_POST['due_date']
    ];

    $this->Task->updateTask($data);

    $_SESSION['success'] = "Task updated successfully.";

    header("Location: index.php?page=tasks");
    exit();
}
}