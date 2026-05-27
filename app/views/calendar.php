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
$gridStart = $monthStart->modify('-' . ((int) $monthStart->format('N') - 1) . ' days');
$todayKey = $today->format('Y-m-d');

$monthEvents = [
    '2026-05-03' => ['Study block'],
    '2026-05-05' => ['Math quiz'],
    '2026-05-08' => ['Biology review'],
    '2026-05-14' => ['Essay deadline'],
    '2026-05-18' => ['Group study'],
    '2026-05-22' => ['Notes cleanup'],
    '2026-05-26' => ['Weekly recap'],
];

$monthDays = [];
for ($index = 0; $index < 42; $index++) {
    $date = $gridStart->modify('+' . $index . ' days');
    $dateKey = $date->format('Y-m-d');

    $monthDays[] = [
        'date' => $date,
        'day' => (int) $date->format('j'),
        'outside' => (int) $date->format('n') !== (int) $monthStart->format('n'),
        'isToday' => $dateKey === $todayKey,
        'events' => $monthEvents[$dateKey] ?? [],
    ];
}

$monthHighlights = [
    ['time' => 'Mon', 'title' => 'Math formulas and practice'],
    ['time' => 'Wed', 'title' => 'Biology revision block'],
    ['time' => 'Fri', 'title' => 'Essay deadline'],
];

include('../public/includes/header.php');
?>

<section class="page-header compact calendar-header">
  <div>
    <p class="eyebrow">Month calendar</p>
    <h2><?php echo htmlspecialchars($monthLabel); ?></h2>
  </div>
  <div class="calendar-switcher glass-card calendar-toolbar">
    <a class="pill-btn" href="?year=<?php echo (int) $prevMonth->format('Y'); ?>&month=<?php echo (int) $prevMonth->format('n'); ?>">Previous</a>
    <span class="calendar-range">Full month grid</span>
    <a class="pill-btn" href="?year=<?php echo (int) $nextMonth->format('Y'); ?>&month=<?php echo (int) $nextMonth->format('n'); ?>">Next</a>
  </div>
</section>

<section class="calendar-layout month-layout">
  <article class="calendar-card glass-card calendar-month-shell">
    <div class="calendar-grid month-grid">
      <?php foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayLabel): ?>
        <div class="calendar-head"><?php echo $dayLabel; ?></div>
      <?php endforeach; ?>

      <?php foreach ($monthDays as $day): ?>
        <?php
        $cellClasses = ['calendar-day'];
        if ($day['outside']) {
            $cellClasses[] = 'muted';
        }
        if ($day['isToday']) {
            $cellClasses[] = 'active';
        }
        ?>
        <div class="<?php echo implode(' ', $cellClasses); ?>" aria-label="<?php echo htmlspecialchars($day['date']->format('l, F j, Y')); ?>">
          <strong><?php echo $day['day']; ?></strong>
          <?php if (!empty($day['events'])): ?>
            <?php foreach (array_slice($day['events'], 0, 2) as $event): ?>
              <span class="event-chip"><?php echo htmlspecialchars($event); ?></span>
            <?php endforeach; ?>
          <?php else: ?>
            <span><?php echo $day['outside'] ? 'Spillover day' : 'Open study block'; ?></span>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </article>

  <aside class="schedule-column">
    <article class="content-card glass-card">
      <div class="section-head">
        <h3>This month</h3>
        <span class="subtle">Highlights</span>
      </div>
      <?php foreach ($monthHighlights as $highlight): ?>
        <div class="schedule-item">
          <span class="dot blue"></span>
          <div>
            <strong><?php echo htmlspecialchars($highlight['time']); ?></strong>
            <p><?php echo htmlspecialchars($highlight['title']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </article>

    <article class="content-card glass-card">
      <div class="section-head">
        <h3>Deadline highlights</h3>
      </div>
      <div class="deadline-row">
        <span class="badge high">High</span>
        <p>Essay due Wednesday</p>
      </div>
      <div class="deadline-row">
        <span class="badge medium">Medium</span>
        <p>Math quiz on Friday</p>
      </div>
      <div class="deadline-row">
        <span class="badge low">Low</span>
        <p>Notes cleanup Friday</p>
      </div>
    </article>
  </aside>
</section>

<?php include('includes/footer.php'); ?>