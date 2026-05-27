<?php

require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/TaskController.php";

$page = $_GET['page'] ?? 'home';

$auth = new AuthController();
$task = new TaskController();

$protectedPages = [
    'dashboard',
    'tasks',
    'calendar',
    'profile',
    'update-profile',
    'store-task',
    'delete-task',
    'edit-task',
    'update-task',
    'logout'
];

if(in_array($page, $protectedPages) && !isset($_SESSION['user_id']))
{
    header("Location: index.php");
    exit();
}

switch($page)
{
    case 'home':
        require_once "../app/views/landing.php";
        break;

    case 'dashboard':
        require_once "../app/views/dashboard.php";
        break;

    case 'tasks':
    $task->index();
    break;

    case 'store-task':
    $task->store();
    break;

    case 'delete-task':
    $task->delete();
    break;
    case 'edit-task':
    $task->edit();
    break;

    case 'update-task':
    $task->update();
    break;

    case 'calendar':
        require_once "../app/views/calendar.php";
        break;

    case 'profile':
        require_once "../app/views/profile.php";
        break;
    case 'update-profile':
        $auth->updateProfile();
        break;
    case 'store-register':
        $auth->storeRegister();
        break;

    case 'authenticate':
        $auth->authenticate();
        break;

    case 'logout':
        $auth->logout();
        break;

    default:
    http_response_code(404);
    require_once "../app/views/404.php";
    exit();
}