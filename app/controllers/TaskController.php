<?php

require_once "../app/models/Task.php";

class TaskController
{
    private $taskModel;

    public function __construct()
    {
        $this->taskModel = new Task();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // SHOW TASKS
    public function index()
    {
        $tasks = $this->taskModel->getTasksByUser($_SESSION['user_id']);

        $editingTask = null;

        require "../app/views/tasks.php";
    }

    // CREATE TASK
    public function store()
    {
        $title = trim($_POST['title'] ?? '');

        if ($title === '') {
            $_SESSION['error'] = "Task title is required.";
            header("Location: index.php?page=tasks");
            exit();
        }

        $this->taskModel->createTask([
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'description' => trim($_POST['description'] ?? ''),
            'priority' => $_POST['priority'] ?? 'Medium',
            'status' => $_POST['status'] ?? 'To Do',
            'due_date' => $_POST['due_date'] ?? null
        ]);

        $_SESSION['success'] = "Task added successfully.";
        header("Location: index.php?page=tasks");
        exit();
    }
    // DELETE TASK
    public function delete()
    {
        $taskId = $_POST['task_id'] ?? null;

        if (!$taskId) {
            $_SESSION['error'] = "Invalid task.";
            header("Location: index.php?page=tasks");
            exit();
        }

        $this->taskModel->deleteTask($taskId, $_SESSION['user_id']);

        $_SESSION['success'] = "Task deleted successfully.";
        header("Location: index.php?page=tasks");
        exit();
    }

    // EDIT TASK VIEW
    public function edit()
    {
        $taskId = $_GET['task_id'] ?? null;

        if (!$taskId) {
            header("Location: index.php?page=tasks");
            exit();
        }

        $editingTask = $this->taskModel->getTaskById(
            $taskId,
            $_SESSION['user_id']
        );

        if (!$editingTask) {
            header("Location: index.php?page=tasks");
            exit();
        }

        $tasks = $this->taskModel->getTasksByUser($_SESSION['user_id']);

        require "../app/views/tasks.php";
    }

    // UPDATE TASK
    public function update()
    {
        $taskId = $_POST['task_id'] ?? null;

        if (!$taskId) {
            $_SESSION['error'] = "Invalid task.";
            header("Location: index.php?page=tasks");
            exit();
        }

        $title = trim($_POST['title'] ?? '');

        if ($title === '') {
            $_SESSION['error'] = "Title is required.";
            header("Location: index.php?page=tasks");
            exit();
        }

        $this->taskModel->updateTask([
            'task_id' => $taskId,
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'description' => trim($_POST['description'] ?? ''),
            'priority' => $_POST['priority'] ?? 'Medium',
            'status' => $_POST['status'] ?? 'To Do',
            'due_date' => $_POST['due_date'] ?? null
        ]);

        $_SESSION['success'] = "Task updated successfully.";
        header("Location: index.php?page=tasks");
        exit();
    }
}