<?php

require_once "../app/models/User.php";

$userModel = new User();

$user_id = $_SESSION['user_id'];

$userStats = $userModel->getStats($user_id);

$todayTasks = $userModel->getTodayTasksCount($user_id);

$dueSoon = $userModel->getDueSoonTasksCount($user_id);

$upcomingTasks = $userModel->getUpcomingTasks($user_id);

$weeklyProgress = $userModel->getWeeklyProgress($user_id);

$studyGoals = $userModel->getStudyGoals($user_id);
$todayFocusMinutes =
    $userModel->getTodayFocusMinutes($user_id);

$todayFocusHours =
    round($todayFocusMinutes / 60, 1);

$dailyGoalMinutes =
    $studyGoals['daily_focus_hours'] * 60;

$focusProgress = 0;

if ($dailyGoalMinutes > 0)
{
    $focusProgress = min(
        100,
        round(
            ($todayFocusMinutes / $dailyGoalMinutes)
            * 100
        )
    );
}

$pageTitle  = 'Dashboard';
$bodyClass  = 'page-dashboard';
$showSidebar = true;
$activePage = 'dashboard';

include('../public/includes/header.php');

?>

<section class="page-header">
  <div>
    <p class="eyebrow">Dashboard</p>
    <h2>
      Welcome back,
      <?php echo htmlspecialchars($_SESSION['username']); ?>
    </h2>
  </div>

  <div style="display:flex;gap:0.5rem;align-items:center;">
    <a href="index.php?page=tasks" class="pill-btn">
      <i class="fa-solid fa-plus"></i>
      New task
    </a>

    <a href="index.php?page=calendar" class="pill-btn">
      <i class="fa-solid fa-calendar"></i>
      Calendar
    </a>
  </div>
</section>

<!-- Stats -->
<section class="stats-grid">

  <article class="stat-card glass-card">
    <p class="eyebrow">Tasks today</p>

    <h3>
      <?php echo $todayTasks; ?>
    </h3>

    <span class="subtle">
      <?php echo $dueSoon; ?> due soon
    </span>
  </article>

  <article class="stat-card glass-card">
    <p class="eyebrow">Study streak</p>

    <h3>
      <?php echo $userStats['streak']; ?>

      <span style="font-size:1rem;font-weight:500">
        days
      </span>
    </h3>

    <span class="subtle">
      Keep it up!
    </span>
  </article>

  <article class="stat-card glass-card">
    <p class="eyebrow">Completed</p>

    <h3>
      <?php echo $userStats['completion_rate']; ?>

      <span style="font-size:1rem;font-weight:500">
        %
      </span>
    </h3>

    <span class="subtle">
      Overall completion
    </span>
  </article>

  <article class="stat-card glass-card">
    <p class="eyebrow">Focus goal</p>

    <h3>
  <?php echo $todayFocusHours; ?>

  <span style="font-size:1rem;font-weight:500">
    /
    <?php echo $studyGoals['daily_focus_hours']; ?>h
  </span>
</h3>

    <span class="subtle">
      Daily target
    </span>
  </article>

</section>

<!-- Main grid -->
<section class="dashboard-grid">

  <!-- Left -->
  <div style="display:grid;gap:0.85rem;">

    <!-- Upcoming tasks -->
    <article class="content-card glass-card">

      <div class="section-head">
        <h3>Upcoming tasks</h3>

        <a href="index.php?page=tasks"
           class="text-btn"
           style="font-size:0.8rem;">

          View all
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>

      <div class="mini-list">

        <?php if (!empty($upcomingTasks)): ?>

          <?php foreach ($upcomingTasks as $task): ?>

            <?php
              $badgeClass = strtolower(
                $task['priority']
              );
            ?>

            <div class="mini-item">

              <div>
                <strong>
                  <?php echo htmlspecialchars($task['title']); ?>
                </strong>

                <p>
                  Due:
                  <?php echo htmlspecialchars($task['due_date']); ?>
                </p>
              </div>

              <span class="badge <?php echo $badgeClass; ?>">
                <?php echo htmlspecialchars($task['priority']); ?>
              </span>

            </div>

          <?php endforeach; ?>

        <?php else: ?>

          <p class="subtle">
            No upcoming tasks.
          </p>

        <?php endif; ?>

      </div>

    </article>

    <!-- Weekly progress -->
    <article class="content-card glass-card">

      <div class="section-head">
        <h3>Weekly progress</h3>
      </div>

      <div class="progress-block">

        <div class="progress-row">
          <span>Completion rate</span>

          <strong>
            <?php echo $weeklyProgress['completion_rate']; ?>%
          </strong>
        </div>

        <div class="progress-bar">
          <span
            style="width:
            <?php echo $weeklyProgress['completion_rate']; ?>%">
          </span>
        </div>

      </div>

      <div class="progress-block">

        <div class="progress-row">
          <span>Tasks done</span>

          <strong>
            <?php echo $weeklyProgress['completed_tasks']; ?>

            /

            <?php echo $weeklyProgress['total_tasks']; ?>
          </strong>
        </div>

        <div class="progress-bar">
          <span
            style="width:
            <?php echo $weeklyProgress['completion_rate']; ?>%">
          </span>
        </div>

      </div>

      <div class="progress-block" style="margin-bottom:0">

        <div class="progress-row">
          <span>Focus goal</span>

          <strong>
  <?php echo $todayFocusHours; ?>

  /

  <?php echo $studyGoals['daily_focus_hours']; ?>h
</strong>
        </div>

        <div class="progress-bar">
          <span
  style="width:
  <?php echo $focusProgress; ?>%">
</span>
        </div>

      </div>

    </article>

  </div>

  <!-- Right -->
  <div style="display:grid;gap:0.85rem;">

    <!-- Pomodoro -->
    <article class="pomodoro-widget glass-card">

      <div class="section-head" style="margin-bottom:0.5rem;">
        <h3>Focus Timer</h3>

        <span class="subtle" style="font-size:0.75rem;">
          Pomodoro
        </span>
      </div>

      <div class="pomo-modes">
        <button class="pomo-mode-btn active">Focus</button>
        <button class="pomo-mode-btn">Short break</button>
        <button class="pomo-mode-btn">Long break</button>
      </div>

      <div class="pomo-ring">
        <svg width="100" height="100" viewBox="0 0 100 100">
          <circle class="pomo-ring-track" cx="50" cy="50" r="45"/>
          <circle class="pomo-ring-fill" cx="50" cy="50" r="45"/>
        </svg>

        <div class="pomo-ring-text">25:00</div>
        <div class="pomodoro-controls">

  <button class="pomo-btn"
          data-pomo-reset
          title="Reset">

    <i class="fa-solid fa-rotate-left"></i>
  </button>

  <button class="pomo-btn"
          data-pomo-start
          title="Start/Pause"
          style="width:48px;height:48px;font-size:1rem;">

    <i class="fa-solid fa-play"></i>
  </button>

  <button class="pomo-btn"
          data-pomo-skip
          title="Skip">

    <i class="fa-solid fa-forward-step"></i>
  </button>

</div>
        
      </div>

    </article>

    <!-- Schedule -->
    <article class="content-card glass-card">

      <div class="section-head">
        <h3>Today's schedule</h3>
      </div>

      <?php if (!empty($upcomingTasks)): ?>

        <?php foreach ($upcomingTasks as $task): ?>

          <div class="schedule-item">

            <span class="dot blue"></span>

            <div>

              <strong>
                <?php echo htmlspecialchars($task['due_date']); ?>
              </strong>

              <p>
                <?php echo htmlspecialchars($task['title']); ?>
              </p>

            </div>

          </div>

        <?php endforeach; ?>

      <?php else: ?>

        <p class="subtle">
          No tasks scheduled.
        </p>

      <?php endif; ?>

    </article>

  </div>

</section>
<script>

let focusMinutes = 25;
let currentSeconds = focusMinutes * 60;

let timer;
let isRunning = false;

const timerText = document.querySelector(
    ".pomo-ring-text"
);

const startBtn = document.querySelector(
    "[data-pomo-start]"
);

function updateTimerUI()
{
    let minutes = Math.floor(currentSeconds / 60);

    let seconds = currentSeconds % 60;

    timerText.innerText =
        String(minutes).padStart(2, '0')
        + ":" +
        String(seconds).padStart(2, '0');
}

function startTimer()
{
    if (isRunning) return;

    isRunning = true;

    timer = setInterval(() => {

        currentSeconds--;

        updateTimerUI();

        if (currentSeconds <= 0)
        {
            clearInterval(timer);

            isRunning = false;

            saveFocusSession();
        }

    }, 1000);
}

function saveFocusSession()
{
    fetch("index.php?page=save-focus-session", {

        method: "POST",

        headers: {
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body: "minutes=" + focusMinutes
    })
    .then(res => res.json())
    .then(data => {

        if (data.success)
        {
            alert(
                "Focus session completed!"
            );

            location.reload();
        }

    });
}

startBtn.addEventListener(
    "click",
    startTimer
);

updateTimerUI();

</script>
<?php include('includes/footer.php'); ?>