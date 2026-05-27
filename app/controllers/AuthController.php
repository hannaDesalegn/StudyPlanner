<?php

require_once "../config/db.php";

class AuthController
{
    public function storeRegister()
    {
        global $pdo;

        $username = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $plainPassword = $_POST['password'];
        // Validation (collect field-level errors)
        $errors = [];
        if (empty($username)) {
            $errors['full_name'] = 'Full name is required.';
        }
        if (empty($email)) {
            $errors['email'] = 'Email is required.';
        }
        if (empty($plainPassword)) {
            $errors['password'] = 'Password is required.';
        }

        if (!empty($plainPassword) && strlen($plainPassword) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        // Check duplicate email
        if (!isset($errors['email'])) {
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors['email'] = 'Email already exists.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form'] = 'signup';
            header("Location: index.php");
            exit();
        }

        $password = password_hash($plainPassword, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            "INSERT INTO users(username, email, password)
             VALUES(?, ?, ?)"
        );

        $stmt->execute([
            $username,
            $email,
            $password
        ]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        $_SESSION['success'] = "Account created successfully!";

        header("Location: index.php?page=dashboard");
        exit();
    }

    public function authenticate()
    {
        global $pdo;

        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Validation 
        $errors = [];
        if (empty($email)) {
            $errors['email'] = 'Email is required.';
        }
        if (empty($password)) {
            $errors['password'] = 'Password is required.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form'] = 'login';
            header("Location: index.php");
            exit();
        }

        $stmt = $pdo->prepare(
            "SELECT * FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if($user && password_verify($password, $user['password']))
        {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            $_SESSION['success'] = "Welcome back " . $user['username'] . "!";

            header("Location: index.php?page=dashboard");
            exit();
        } else {
            $_SESSION['errors'] = ['email' => 'Invalid email or password.'];
            $_SESSION['form'] = 'login';
            header("Location: index.php");
            exit();
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();

        header("Location: index.php");
        exit();
    }
}