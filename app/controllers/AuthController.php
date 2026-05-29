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
        // UPDATE STREAK
        $this->userModel->updateStreak($user['id']);

        // reload fresh user
        $updatedUser = $this->userModel->findById($user['id']);

        $_SESSION['user_id'] = $updatedUser['id'];
        $_SESSION['username'] = $updatedUser['username'];
        $_SESSION['email'] = $updatedUser['email'];
        $_SESSION['profile_image'] = $updatedUser['profile_image'];

        $_SESSION['success'] =
    "Welcome back " . $updatedUser['username'] . "!";

/* Remember Me Cookie */
if (isset($_POST['remember'])) {

    setcookie(
        'remember_email',
        $email,
        time() + (30 * 24 * 60 * 60),
        '/'
    );

} else {

    setcookie(
        'remember_email',
        '',
        time() - 3600,
        '/'
    );
}

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
  public function updateProfile()
{
    $user_id  = $_SESSION['user_id'];

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($email)) {
        $_SESSION['error'] = "Username and email are required.";
        header("Location: index.php?page=profile");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: index.php?page=profile");
        exit();
    }

    // PROFILE IMAGE UPLOAD
    $profileImage = null;

    if (!empty($_FILES['profile_image']['name'])) {

        $file = $_FILES['profile_image'];
        if ($file['size'] > 2 * 1024 * 1024) {
    $_SESSION['error'] = "Image size must be under 2MB.";
    header("Location: index.php?page=profile");
    exit();
}
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = "Only JPG, PNG, WEBP allowed.";
            header("Location: index.php?page=profile");
            exit();
        }

        // create unique name
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = "profile_" . $user_id . "_" . time() . "." . $ext;

        $uploadPath = "../public/uploads/" . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {

    $_SESSION['error'] = "Failed to upload image.";

    header("Location: index.php?page=profile");
    exit();
}

        $profileImage = $fileName;
    }

    // send to model
    $data = [
    'user_id'       => $user_id,
    'username'      => $username,
    'email'         => $email,
    'password'      => $password,
    'profile_image' => $profileImage
];

$this->userModel->updateProfile($data);

// reload fresh user from DB
$updatedUser = $this->userModel->findById($user_id);

// sync session with DB 
$_SESSION['username'] = $updatedUser['username'];
$_SESSION['email'] = $updatedUser['email'];
$_SESSION['profile_image'] = $updatedUser['profile_image'];

$_SESSION['success'] = "Profile updated successfully!";
header("Location: index.php?page=profile");
exit();
}

public function updateGoals()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $data = [
        'daily_focus_hours' =>
            (int) ($_POST['daily_focus_hours'] ?? 4),

        'weekly_tasks_target' =>
            (int) ($_POST['weekly_tasks_target'] ?? 14),

        'target_completion' =>
            (int) ($_POST['target_completion'] ?? 80)
    ];

    // validation
    if ($data['daily_focus_hours'] < 1) {
        $data['daily_focus_hours'] = 1;
    }

    if ($data['weekly_tasks_target'] < 1) {
        $data['weekly_tasks_target'] = 1;
    }

    if ($data['target_completion'] < 1 ||
        $data['target_completion'] > 100) {

        $data['target_completion'] = 80;
    }

    $this->userModel->updateStudyGoals(
        $user_id,
        $data
    );

    $_SESSION['success'] =
        "Study goals updated successfully.";

    header("Location: index.php?page=profile");
    exit();
}
}