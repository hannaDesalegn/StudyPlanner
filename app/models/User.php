<?php

require_once "../config/db.php";

class User
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // FIND USER BY ID
    public function findById($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    // FIND USER BY EMAIL
    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        return $stmt->fetch();
    }

    // CREATE USER
    public function createUser($username, $email, $password)
    {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $this->pdo->prepare(
            "INSERT INTO users(username, email, password)
             VALUES(?, ?, ?)"
        );

        return $stmt->execute([
            $username,
            $email,
            $hashedPassword
        ]);
    }
    // LAST INSERTED ID
    public function getLastInsertedId()
    {
        return $this->pdo->lastInsertId();
    }
    // UPDATE PROFILE
    public function updateProfile($data)
    {
        $fields = "username = ?, email = ?";

        $params = [
            $data['username'],
            $data['email']
        ];

        // password update
        if (!empty($data['password']))
        {
            $fields .= ", password = ?";

            $params[] = password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            );
        }

        // profile image update
        if (!empty($data['profile_image']))
        {
            $fields .= ", profile_image = ?";

            $params[] = $data['profile_image'];
        }

        $params[] = $data['user_id'];

        $stmt = $this->pdo->prepare(
            "UPDATE users
             SET $fields
             WHERE id = ?"
        );

        return $stmt->execute($params);
    }

    // PROFILE STATS
    public function getStats($user_id)
    {
        // total tasks
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS total_tasks
             FROM tasks
             WHERE user_id = ?"
        );

        $stmt->execute([$user_id]);

        $totalTasks = $stmt->fetch()['total_tasks'];

        // completed tasks
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS completed_tasks
             FROM tasks
             WHERE user_id = ?
             AND status = 'Done'"
        );

        $stmt->execute([$user_id]);

        $completedTasks = $stmt->fetch()['completed_tasks'];

        // completion %
        $completionRate = 0;

        if ($totalTasks > 0)
        {
            $completionRate = round(
                ($completedTasks / $totalTasks) * 100
            );
        }

        // streak
        $stmt = $this->pdo->prepare(
            "SELECT streak
             FROM users
             WHERE id = ?"
        );

        $stmt->execute([$user_id]);

        $streak = $stmt->fetch()['streak'] ?? 0;

        return [
            'total_tasks'     => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => $completionRate,
            'streak'          => $streak
        ];
    }

    // TASKS DUE TODAY
    public function getTodayTasksCount($user_id)
    {
        $today = date('Y-m-d');

        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS total
             FROM tasks
             WHERE user_id = ?
             AND due_date = ?"
        );

        $stmt->execute([
            $user_id,
            $today
        ]);

        return $stmt->fetch()['total'];
    }

    // DUE SOON TASKS
    public function getDueSoonTasksCount($user_id)
    {
        $today = date('Y-m-d');

        $next3Days = date(
            'Y-m-d',
            strtotime('+3 days')
        );

        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) AS total
             FROM tasks
             WHERE user_id = ?
             AND due_date BETWEEN ? AND ?
             AND status != 'Done'"
        );

        $stmt->execute([
            $user_id,
            $today,
            $next3Days
        ]);

        return $stmt->fetch()['total'];
    }

    // UPCOMING TASKS
    public function getUpcomingTasks($user_id, $limit = 5)
    {
        $limit = (int) $limit;

        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM tasks
             WHERE user_id = ?
             AND status != 'Done'
             ORDER BY due_date ASC
             LIMIT $limit"
        );

        $stmt->execute([$user_id]);

        return $stmt->fetchAll();
    }
    // WEEKLY PROGRESS
    public function getWeeklyProgress($user_id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                COUNT(*) AS total_tasks,

                SUM(
                    CASE
                        WHEN status = 'Done'
                        THEN 1
                        ELSE 0
                    END
                ) AS completed_tasks

             FROM tasks
             WHERE user_id = ?"
        );

        $stmt->execute([$user_id]);

        $data = $stmt->fetch();

        $total = $data['total_tasks'] ?? 0;

        $completed = $data['completed_tasks'] ?? 0;

        $rate = 0;

        if ($total > 0)
        {
            $rate = round(
                ($completed / $total) * 100
            );
        }

        return [
            'total_tasks' => $total,
            'completed_tasks' => $completed,
            'completion_rate' => $rate
        ];
    }
    // CALENDAR TASKS
    public function getTasksByMonth(
        $user_id,
        $year,
        $month
    )
    
    
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM tasks
             WHERE user_id = ?
             AND YEAR(due_date) = ?
             AND MONTH(due_date) = ?
             ORDER BY due_date ASC"
        );

        $stmt->execute([
            $user_id,
            $year,
            $month
        ]);

        return $stmt->fetchAll();
    }

    // STUDY GOALS
    public function getStudyGoals($user_id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM study_goals
             WHERE user_id = ?"
        );

        $stmt->execute([$user_id]);

        $goals = $stmt->fetch();

        // create default goals automatically
        if (!$goals)
        {
            $insert = $this->pdo->prepare(
                "INSERT INTO study_goals
                (
                    user_id,
                    daily_focus_hours,
                    weekly_tasks_target,
                    target_completion
                )
                VALUES (?, 4, 14, 80)"
            );

            $insert->execute([$user_id]);

            return [
                'daily_focus_hours' => 4,
                'weekly_tasks_target' => 14,
                'target_completion' => 80
            ];
        }

        return $goals;
    }

    // LOGIN STREAK
    public function updateStreak($user_id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT streak, last_active
             FROM users
             WHERE id = ?"
        );

        $stmt->execute([$user_id]);

        $user = $stmt->fetch();

        $today = date('Y-m-d');

        // first login
        if (empty($user['last_active']))
        {
            $update = $this->pdo->prepare(
                "UPDATE users
                 SET streak = 1,
                     last_active = ?
                 WHERE id = ?"
            );

            return $update->execute([
                $today,
                $user_id
            ]);
        }

        $lastActive = new DateTime(
            $user['last_active']
        );

        $currentDay = new DateTime($today);

        $difference = $lastActive
            ->diff($currentDay)
            ->days;

        $newStreak = $user['streak'];

        // consecutive day
        if ($difference === 1)
        {
            $newStreak++;
        }

        // missed days
        elseif ($difference > 1)
        {
            $newStreak = 1;
        }

        // same day => unchanged
        $update = $this->pdo->prepare(
            "UPDATE users
             SET streak = ?,
                 last_active = ?
             WHERE id = ?"
        );

        return $update->execute([
            $newStreak,
            $today,
            $user_id
        ]);
    }
// SAVE FOCUS SESSION
public function saveFocusSession($user_id, $minutes)
{
    // create table automatically if not exists
    $this->pdo->exec(
        "CREATE TABLE IF NOT EXISTS focus_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            minutes INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE CASCADE
        )"
    );

    $stmt = $this->pdo->prepare(
        "INSERT INTO focus_sessions
        (
            user_id,
            minutes
        )
        VALUES (?, ?)"
    );

    return $stmt->execute([
        $user_id,
        $minutes
    ]);
}
// TODAY FOCUS MINUTES
public function getTodayFocusMinutes($user_id)
{
    $today = date('Y-m-d');

    $stmt = $this->pdo->prepare(
        "SELECT COALESCE(SUM(minutes), 0) AS total
         FROM focus_sessions
         WHERE user_id = ?
         AND DATE(created_at) = ?"
    );

    $stmt->execute([
        $user_id,
        $today
    ]);

    return (int) $stmt->fetch()['total'];
}
}