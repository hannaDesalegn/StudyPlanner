<?php
$pageTitle  = $pageTitle  ?? 'Study Planner';
$bodyClass  = $bodyClass  ?? '';
$activePage = $activePage ?? 'landing';
$showSidebar = $showSidebar ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Study Planner — focus, track, achieve.">
  <title><?php echo htmlspecialchars($pageTitle); ?> · Study Planner</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="<?php echo htmlspecialchars(trim($bodyClass)); ?>">

<div class="app-shell <?php echo $showSidebar ? 'has-sidebar' : 'no-sidebar'; ?>">

  <!-- Topbar -->
  <header class="topbar">
    <div class="brand">
      <a href="index.php?page=dashboard" style="display:flex;align-items:center;gap:0.85rem;">
        <span class="brand-mark"><i class="fa-solid fa-bolt"></i></span>
        <h1>StudyPlanner</h1>
      </a>
    </div>

    <div class="topbar-actions">

  <a class="profile-btn" href="index.php?page=profile">
    <i class="fa-solid fa-user-circle"></i>
    <span>Profile</span>
  </a>

  <?php if(isset($_SESSION['user_id'])): ?>
    <a class="logout-btn" href="index.php?page=logout">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span>Logout</span>
    </a>
  <?php endif; ?>

</div>
  </header>

  <div class="app-layout <?php echo $showSidebar ? 'with-sidebar' : 'no-sidebar'; ?>">
    <?php if ($showSidebar): ?>
      <aside class="sidebar" aria-label="Sidebar navigation">
        <p class="nav-section-label">Menu</p>
        <nav>
          <a class="nav-link <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
            <i class="fa-solid fa-house"></i><span>Dashboard</span>
          </a>
          <a class="nav-link <?php echo $activePage === 'tasks' ? 'active' : ''; ?>" href="index.php?page=tasks">
            <i class="fa-solid fa-list-check"></i><span>Tasks</span>
          </a>
          <a class="nav-link <?php echo $activePage === 'calendar' ? 'active' : ''; ?>" href="index.php?page=calendar">
            <i class="fa-solid fa-calendar-days"></i><span>Calendar</span>
          </a>
          <a class="nav-link <?php echo $activePage === 'profile' ? 'active' : ''; ?>" href="index.php?page=profile">
            <i class="fa-solid fa-user"></i><span>Profile</span>
          </a>
        </nav>

        <p class="nav-section-label" style="margin-top:1rem;">Focus</p>
        <nav>
          <a class="nav-link" href="#" onclick="document.querySelector('.pomodoro-widget')?.scrollIntoView({behavior:'smooth'});return false;">
            <i class="fa-solid fa-clock"></i><span>Pomodoro</span>
          </a>
        </nav>

        <div class="sidebar-footer" style="margin-top:auto;">
          <p class="eyebrow">Today</p>
          <strong>Stay consistent</strong>
          <p>Small daily progress compounds into big results.</p>
        </div>
      </aside>
    <?php endif; ?>

    <main class="page-content <?php echo $showSidebar ? '' : 'landing-content'; ?>">