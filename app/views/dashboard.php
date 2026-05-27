<?php
$pageTitle  = 'Dashboard';
$bodyClass  = 'page-dashboard';
$showSidebar = true;
$activePage = 'dashboard';
include('../public/includes/header.php');
?>

<section class="page-header">
  <div>
    <p class="eyebrow">Dashboard</p>
    <h2>Study at your own pace.</h2>
  </div>
  <div style="display:flex;gap:0.5rem;align-items:center;">
    <a href="index.php?page=tasks" class="pill-btn"><i class="fa-solid fa-plus"></i> New task</a>
    <a href="index.php?page=calendar" class="pill-btn"><i class="fa-solid fa-calendar"></i> Calendar</a>
  </div>
</section>

<!-- Stats -->
<section class="stats-grid">
  <article class="stat-card glass-card">
    <p class="eyebrow">Tasks today</p>
    <h3>08</h3>
    <span class="subtle">2 due soon</span>
  </article>
  <article class="stat-card glass-card">
    <p class="eyebrow">Study streak</p>
    <h3>12<span style="font-size:1rem;font-weight:500"> days</span></h3>
    <span class="subtle">Keep it up!</span>
  </article>
  <article class="stat-card glass-card">
    <p class="eyebrow">Completed</p>
    <h3>74<span style="font-size:1rem;font-weight:500">%</span></h3>
    <span class="subtle">This week</span>
  </article>
  <article class="stat-card glass-card">
    <p class="eyebrow">Focus time</p>
    <h3>4.5<span style="font-size:1rem;font-weight:500">h</span></h3>
    <span class="subtle">Planned today</span>
  </article>
</section>

<!-- Main grid -->
<section class="dashboard-grid">

  <!-- Left column -->
  <div style="display:grid;gap:0.85rem;">
    <article class="content-card glass-card">
      <div class="section-head">
        <h3>Upcoming tasks</h3>
        <a href="index.php?page=tasks" class="text-btn" style="font-size:0.8rem;">View all <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="mini-list">
        <div class="mini-item">
          <div>
            <strong>Read chapter 4</strong>
            <p>Biology review session</p>
          </div>
          <span class="badge high">High</span>
        </div>
        <div class="mini-item">
          <div>
            <strong>Practice quiz</strong>
            <p>Math set and corrections</p>
          </div>
          <span class="badge medium">Medium</span>
        </div>
        <div class="mini-item">
          <div>
            <strong>Revise summary notes</strong>
            <p>Short final pass</p>
          </div>
          <span class="badge low">Low</span>
        </div>
      </div>
    </article>

    <article class="content-card glass-card">
      <div class="section-head"><h3>Weekly progress</h3></div>
      <div class="progress-block">
        <div class="progress-row">
          <span>Completion rate</span>
          <strong>74%</strong>
        </div>
        <div class="progress-bar"><span style="width:74%"></span></div>
      </div>
      <div class="progress-block">
        <div class="progress-row">
          <span>Tasks done</span>
          <strong>6 / 8</strong>
        </div>
        <div class="progress-bar"><span style="width:75%"></span></div>
      </div>
      <div class="progress-block" style="margin-bottom:0">
        <div class="progress-row">
          <span>Focus hours</span>
          <strong>4.5 / 6h</strong>
        </div>
        <div class="progress-bar"><span style="width:75%"></span></div>
      </div>
    </article>
  </div>

  <!-- Right column -->
  <div style="display:grid;gap:0.85rem;">

    <!-- Pomodoro Timer -->
    <article class="pomodoro-widget glass-card">
      <div class="section-head" style="margin-bottom:0.5rem;">
        <h3>Focus Timer</h3>
        <span class="subtle" style="font-size:0.75rem;">Pomodoro</span>
      </div>
      <div class="pomo-modes">
        <button class="pomo-mode-btn active" data-pomo-mode="focus">Focus</button>
        <button class="pomo-mode-btn" data-pomo-mode="short">Short break</button>
        <button class="pomo-mode-btn" data-pomo-mode="long">Long break</button>
      </div>
      <div class="pomo-ring">
        <svg width="100" height="100" viewBox="0 0 100 100">
          <circle class="pomo-ring-track" cx="50" cy="50" r="45"/>
          <circle class="pomo-ring-fill" cx="50" cy="50" r="45"/>
        </svg>
        <div class="pomo-ring-text">25:00</div>
      </div>
      <div class="pomodoro-controls">
        <button class="pomo-btn" data-pomo-reset title="Reset"><i class="fa-solid fa-rotate-left"></i></button>
        <button class="pomo-btn" data-pomo-start title="Start/Pause" style="width:48px;height:48px;font-size:1rem;"><i class="fa-solid fa-play"></i></button>
        <button class="pomo-btn" title="Skip"><i class="fa-solid fa-forward-step"></i></button>
      </div>
    </article>

    <!-- Today's schedule -->
    <article class="content-card glass-card">
      <div class="section-head">
        <h3>Today's schedule</h3>
        <a href="index.php?page=calendar" class="text-btn" style="font-size:0.8rem;">Full view <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="schedule-item">
        <span class="dot blue"></span>
        <div>
          <strong>9:00 AM</strong>
          <p>Math formulas &amp; practice</p>
        </div>
      </div>
      <div class="schedule-item">
        <span class="dot purple"></span>
        <div>
          <strong>1:00 PM</strong>
          <p>Biology revision block</p>
        </div>
      </div>
      <div class="schedule-item">
        <span class="dot teal"></span>
        <div>
          <strong>6:00 PM</strong>
          <p>Summary notes &amp; review</p>
        </div>
      </div>
    </article>

  </div>
</section>

<?php include('includes/footer.php'); ?>