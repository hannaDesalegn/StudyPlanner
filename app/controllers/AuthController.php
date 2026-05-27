<?php

require_once "../app/models/User.php";

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function storeRegister()
    {
        $username = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $plainPassword = $_POST['password'];

        // Validation
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

        // Duplicate email check
        $existingUser = $this->userModel->findByEmail($email);

        if ($existingUser) {
            $errors['email'] = 'Email already exists.';
        }

        // Redirect back if errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form'] = 'signup';

            header("Location: index.php");
            exit();
        }

        // Create user
        $this->userModel->createUser($username, $email, $plainPassword);

        // Session
        $_SESSION['user_id'] = $this->userModel->getLastInsertedId();
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        $_SESSION['success'] = "Account created successfully!";

        header("Location: index.php?page=dashboard");
        exit();
    }

    public function authenticate()
    {
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

        // Redirect back if errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form'] = 'login';

            header("Location: index.php");
            exit();
        }

        // Find user
        $user = $this->userModel->findByEmail($email);

        // Verify password
        if ($user && password_verify($password, $user['password']))
        {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            $_SESSION['success'] = "Welcome back " . $user['username'] . "!";

            header("Location: index.php?page=dashboard");
            exit();
        }
        else
        {
            $_SESSION['errors'] = [
                'email' => 'Invalid email or password.'
            ];

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