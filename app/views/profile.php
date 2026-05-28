<?php
function profile_initials(string $name): string
{
  $parts = preg_split('/\s+/', trim($name)) ?: [];
  $initials = '';

  foreach (array_slice(array_filter($parts), 0, 2) as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
  }

  return $initials !== '' ? $initials : 'U';
}

$userModel = $data['userModel'];


$user_id = $_SESSION['user_id'] ?? 0;

$currentUserName  = $_SESSION['username'] ?? 'Guest';
$currentUserEmail = $_SESSION['email'] ?? 'No email';
$currentUserImage = $_SESSION['profile_image'] ?? null;

$currentUserInitials = profile_initials($currentUserName);

/* DATA FETCH */
$userStats = $userModel->getStats($user_id) ?? [
  'total_tasks' => 0,
  'streak' => 0,
  'completion_rate' => 0
];

$studyGoals = $userModel->getStudyGoals($user_id) ?? [
    'daily_focus_hours' => 4,
    'weekly_tasks_target' => 14,
    'target_completion' => 80
];

/* IMAGE PATH */
$imagePath = "/public/uploads/" . $currentUserImage;

/* PAGE SETUP*/
$pageTitle  = 'Profile';
$bodyClass  = 'page-profile';
$showSidebar = true;
$activePage = 'profile';

include('../public/includes/header.php');
?>

<section class="page-header compact">
  <div>
    <p class="eyebrow">Your Profile</p>
    <h2>Account overview</h2>
  </div>
</section>

<div class="profile-layout">

  <!-- PROFILE CARD -->
  <article class="profile-card glass-card">

    <div class="profile-card-top">

      <div class="profile-avatar">

        <?php if (!empty($currentUserImage) && file_exists($imagePath)): ?>
          <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Profile">
        <?php else: ?>
          <span data-profile-initials>
            <?php echo htmlspecialchars($currentUserInitials); ?>
          </span>
        <?php endif; ?>

      </div>

      <div class="profile-summary-copy">
        <p class="eyebrow">Account</p>

        <h2 data-profile-name-display>
          <?php echo htmlspecialchars($currentUserName); ?>
        </h2>

        <p class="subtle" data-profile-email-display>
          <?php echo htmlspecialchars($currentUserEmail); ?>
        </p>
      </div>

      <button class="icon-btn small-icon profile-edit-btn"
              type="button"
              aria-label="Edit profile"
              data-profile-edit-toggle>
        <i class="fa-solid fa-pen"></i>
      </button>

    </div>

    <!-- STATS -->
    <div class="profile-stats">

      <div>
        <strong><?php echo (int)$userStats['total_tasks']; ?></strong>
        <span>Tasks</span>
      </div>

      <div>
        <strong><?php echo (int)$userStats['streak']; ?></strong>
        <span>Day streak</span>
      </div>

      <div>
        <strong><?php echo (int)$userStats['completion_rate']; ?>%</strong>
        <span>Completion</span>
      </div>

    </div>

  </article>

  <!-- RIGHT SIDE -->
  <div class="profile-stack">

    <!-- STUDY GOALS -->
    <article class="content-card glass-card">

      <div class="section-head">
        <h3>Study goals</h3>
        <span class="subtle">Your current targets</span>
      </div>

      <div class="goal-item">
        <span>Daily focus time</span>
        <strong>
          <?php echo (int)$studyGoals['daily_focus_hours']; ?> hours
        </strong>
      </div>

      <div class="goal-item">
        <span>Weekly tasks</span>
        <strong>
          <?php echo (int)$studyGoals['weekly_tasks_target']; ?> tasks
        </strong>
      </div>

      <div class="goal-item">
        <span>Target completion</span>
        <strong>
          <?php echo (int)$studyGoals['target_completion']; ?>%
        </strong>
      </div>

    </article>

    <!-- PREFERENCES -->
    <article class="content-card glass-card">

      <div class="section-head">
        <h3>Preferences</h3>
      </div>

      <button class="theme-toggle" type="button" data-theme-toggle>
        <span><i class="fa-solid fa-moon"></i> Dark mode</span>
      </button>

    </article>

  </div>

</div>

<!-- EDIT MODAL -->
<div class="profile-modal" data-profile-edit-panel hidden>

  <div class="profile-modal-backdrop" data-profile-edit-toggle></div>

  <section class="profile-modal-card"
           role="dialog"
           aria-modal="true"
           aria-labelledby="edit-title">

    <form action="index.php?page=update-profile"
          method="POST"
          enctype="multipart/form-data">

      <div class="minimal-head">
        <h3 id="edit-title">Edit profile</h3>

        <button class="icon-btn small-icon"
                type="button"
                data-profile-edit-toggle>
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <div class="profile-edit-grid">

        <div class="form-group">
          <label>Profile Image</label>
          <input name="profile_image" type="file" accept="image/*">
        </div>

        <div class="form-group">
          <label>Name</label>
          <input name="username" type="text"
                 value="<?php echo htmlspecialchars($currentUserName); ?>"
                 required>
        </div>

        <div class="form-group">
          <label>Email</label>
          <input name="email" type="email"
                 value="<?php echo htmlspecialchars($currentUserEmail); ?>"
                 required>
        </div>

        <div class="form-group">
          <label>New password</label>
          <input name="password" type="password"
                 placeholder="Leave blank to keep current">
        </div>

      </div>

      <div class="profile-edit-actions">

        <button class="link-btn" type="button" data-profile-edit-toggle>
          Cancel
        </button>

        <button class="primary-btn" type="submit">
          <i class="fa-solid fa-check"></i>
          Save changes
        </button>

      </div>

    </form>

  </section>

</div>

<?php include('includes/footer.php'); ?>