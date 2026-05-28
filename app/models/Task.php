<?php

require_once "../config/db.php";

class Task
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // GET ALL USER TASKS
    public function getTasksByUser($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tasks
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");

        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // CREATE TASK
    public function createTask($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO tasks
            (user_id, title, description, priority, status, due_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['user_id'],
            $data['title'],
            $data['description'] ?? '',
            $data['priority'],
            $data['status'],
            $data['due_date'] ?? null
        ]);
    }

    // DELETE TASK
    public function deleteTask($taskId, $userId)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM tasks
            WHERE task_id = ? AND user_id = ?
        ");

        return $stmt->execute([$taskId, $userId]);
    }

    // GET SINGLE TASK
    public function getTaskById($taskId, $userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tasks
            WHERE task_id = ? AND user_id = ?
        ");

        $stmt->execute([$taskId, $userId]);
        return $stmt->fetch();
    }

    // UPDATE TASK
    public function updateTask($data)
    {
        $stmt = $this->pdo->prepare("
            UPDATE tasks SET
                title = ?,
                description = ?,
                priority = ?,
                status = ?,
                due_date = ?
            WHERE task_id = ? AND user_id = ?
        ");

        return $stmt->execute([
            $data['title'],
            $data['description'] ?? '',
            $data['priority'],
            $data['status'],
            $data['due_date'] ?? null,
            $data['task_id'],
            $data['user_id']
        ]);
    }

    // DATE RANGE
    public function getTasksByDateRange($user_id, $start, $end)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM tasks
            WHERE user_id = ?
            AND due_date BETWEEN ? AND ?
            ORDER BY due_date ASC
        ");

        $stmt->execute([$user_id, $start, $end]);
        return $stmt->fetchAll();
    }

    // MONTHLY TASKS 
    public function getTasksByMonth($user_id, $year, $month)
    {
        $start = sprintf("%04d-%02d-01", $year, $month);
        $end = date("Y-m-t", strtotime($start));

        return $this->getTasksByDateRange($user_id, $start, $end);
    }
}