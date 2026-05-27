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

    public function findById($id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        return $stmt->fetch();
    }

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

    public function getLastInsertedId()
    {
        return $this->pdo->lastInsertId();
    }

    public function updateProfile($data)
    {
        $fields = "username = ?, email = ?";
        $params = [
            $data['username'],
            $data['email']
        ];

        // password update
        if (!empty($data['password'])) {

            $fields .= ", password = ?";

            $params[] = password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            );
        }

        // profile image update
        if (!empty($data['profile_image'])) {

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
             AND status = 'done'"
        );

        $stmt->execute([$user_id]);

        $completedTasks = $stmt->fetch()['completed_tasks'];

        // completion %
        $completionRate = 0;

        if ($totalTasks > 0) {

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

        //  default goals a
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

        $lastActive = new DateTime($user['last_active']);
        $currentDay = new DateTime($today);

        $difference = $lastActive->diff($currentDay)->days;

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

        // same day 

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
}