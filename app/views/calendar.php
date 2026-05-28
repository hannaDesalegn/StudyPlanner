<?php
$pageTitle  = 'Calendar';
$bodyClass  = 'page-calendar';
$showSidebar = true;
$activePage = 'calendar';

$today = new DateTimeImmutable('today');

$requestedYear = isset($_GET['year']) ? (int) $_GET['year'] : (int) $today->format('Y');
$requestedMonth = isset($_GET['month']) ? (int) $_GET['month'] : (int) $today->format('n');

if ($requestedMonth < 1 || $requestedMonth > 12) {
    $requestedMonth = (int) $today->format('n');
}

if ($requestedYear < 1970 || $requestedYear > 2100) {
    $requestedYear = (int) $today->format('Y');
}

$monthStart = new DateTimeImmutable(sprintf('%04d-%02d-01', $requestedYear, $requestedMonth));
$monthLabel = $monthStart->format('F Y');

$prevMonth = $monthStart->modify('-1 month');
$nextMonth = $monthStart->modify('+1 month');

$gridStart = $monthStart->modify('-' . ((int)$monthStart->format('N') - 1) . ' days');

$todayKey = $today->format('Y-m-d');

/* TASK DATA (FROM DB) */
require_once "../app/models/Task.php";

$taskModel = new Task();
$user_id = $_SESSION['user_id'];

/* get tasks for this month */
$monthTasks = $taskModel->getTasksByMonth(
    $user_id,
    $requestedYear,
    $requestedMonth
);

/* group by date */
$monthEvents = [];

foreach ($monthTasks as $task) {
    if (!empty($task['due_date'])) {
        $monthEvents[$task['due_date']][] = $task['title'];
    }
}

/* calendar grid */
$monthDays = [];

for ($index = 0; $index < 42; $index++) {

    $date = $gridStart->modify('+' . $index . ' days');
    $dateKey = $date->format('Y-m-d');

    $monthDays[] = [
        'date' => $date,
        'day' => (int)$date->format('j'),
        'outside' => (int)$date->format('n') !== (int)$monthStart->format('n'),
        'isToday' => $dateKey === $todayKey,
        'events' => $monthEvents[$dateKey] ?? [],
    ];
}

include('../public/includes/header.php');
?>

<!-- HEADER -->
<section class="page-header compact calendar-header">
  <div>
    <p class="eyebrow">Calendar</p>
    <h2><?php echo htmlspecialchars($monthLabel); ?></h2>
  </div>

  <div class="calendar-switcher glass-card calendar-toolbar">

    <a class="pill-btn"
       href="index.php?page=calendar&year=<?php echo $prevMonth->format('Y'); ?>&month=<?php echo $prevMonth->format('n'); ?>">
       Previous
    </a>

    <a class="pill-btn"
       href="index.php?page=calendar&view=month&year=<?php echo $requestedYear; ?>&month=<?php echo $requestedMonth; ?>">
       Month
    </a>

    <a class="pill-btn"
       href="index.php?page=calendar&view=week">
       Week
    </a>

    <a class="pill-btn"
       href="index.php?page=calendar&year=<?php echo $nextMonth->format('Y'); ?>&month=<?php echo $nextMonth->format('n'); ?>">
       Next
    </a>

  </div>
</section>

<!-- MONTH VIEW-->
<section class="calendar-layout month-layout">

  <article class="calendar-card glass-card calendar-month-shell">

    <div class="calendar-grid month-grid">

      <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $label): ?>
        <div class="calendar-head"><?php echo $label; ?></div>
      <?php endforeach; ?>

      <?php foreach ($monthDays as $day): ?>

        <?php
        $classes = ['calendar-day'];

        if ($day['outside']) $classes[] = 'muted';
        if ($day['isToday']) $classes[] = 'active';
        ?>

        <div class="<?php echo implode(' ', $classes); ?>">

          <strong><?php echo $day['day']; ?></strong>

          <?php if (!empty($day['events'])): ?>

            <?php foreach (array_slice($day['events'], 0, 2) as $event): ?>
              <span class="event-chip">
                <?php echo htmlspecialchars($event); ?>
              </span>
            <?php endforeach; ?>

          <?php else: ?>
            <span class="subtle">No tasks</span>
          <?php endif; ?>

        </div>

      <?php endforeach; ?>

    </div>
  </article>

</section>

<?php include('../public/includes/footer.php'); ?>