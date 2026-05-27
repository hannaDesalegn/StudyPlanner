<?php

require_once "../config/db.php";

class Task
{
    // Get all tasks for logged in user
    public function getTasksByUser($userId)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            "SELECT * FROM tasks
             WHERE user_id = ?
             ORDER BY created_at DESC"
        );

        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    // Create new task
    public function createTask($data)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            "INSERT INTO tasks
            (user_id, title, description, priority, status, due_date)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            $data['user_id'],
            $data['title'],
            $data['description'],
            $data['priority'],
            $data['status'],
            !empty($data['due_date']) ? $data['due_date'] : null
        ]);
    }

    // Delete task
    public function deleteTask($taskId, $userId)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            "DELETE FROM tasks
             WHERE task_id = ?
             AND user_id = ?"
        );

        return $stmt->execute([
            $taskId,
            $userId
        ]);
    }

    // Get single task by ID
    public function getTaskById($taskId, $userId)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            "SELECT * FROM tasks
             WHERE task_id = ?
             AND user_id = ?"
        );

        $stmt->execute([
            $taskId,
            $userId
        ]);

        return $stmt->fetch();
    }

    // Update task
    public function updateTask($data)
    {
        global $pdo;

        $stmt = $pdo->prepare(
            "UPDATE tasks
             SET
                title = ?,
                description = ?,
                priority = ?,
                status = ?,
                due_date = ?
             WHERE task_id = ?
             AND user_id = ?"
        );

        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['priority'],
            $data['status'],
            !empty($data['due_date']) ? $data['due_date'] : null,
            $data['task_id'],
            $data['user_id']
        ]);
    }
}