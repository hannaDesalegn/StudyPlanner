<?php
function profile_initials(string $name): string
{
  $parts = preg_split('/\s+/', trim($name)) ?: [];
  $initials = '';

  foreach (array_slice(array_filter($parts), 0, 2) as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
  }

  return $initials !== '' ? $initials : 'YP';
}

$currentUserName = $_SESSION['username'] ?? 'Guest';
$currentUserEmail = $_SESSION['email'] ?? 'Logged in user';
$currentUserInitials = profile_initials($currentUserName);
$pageTitle  = 'Profile';
$bodyClass  = 'page-profile';
$showSidebar = true;
$activePage = 'profile';
include('../public/includes/header.php');
?>

<section class="page-header compact">
  <div>
    <p class="eyebrow">Your Profile</p>
    <h2>Account details at a glance.</h2>
  </div>
</section>

<div class="profile-layout">

  <!-- Profile card -->
  <article class="profile-card glass-card">
    <div class="profile-card-top">
      <div class="profile-avatar">
        <span data-profile-initials><?php echo htmlspecialchars($currentUserInitials); ?></span>
      </div>
      <div class="profile-summary-copy">
        <p class="eyebrow">Your profile</p>
        <h2 data-profile-name-display><?php echo htmlspecialchars($currentUserName); ?></h2>
        <p class="subtle" data-profile-email-display><?php echo htmlspecialchars($currentUserEmail); ?></p>
      </div>
      <button class="icon-btn small-icon profile-edit-btn" type="button" aria-label="Edit profile" data-profile-edit-toggle>
        <i class="fa-solid fa-pen"></i>
      </button>
    </div>
    <div class="profile-stats">
      <div>
        <strong>24</strong>
        <span>Tasks</span>
      </div>
      <div>
        <strong>12</strong>
        <span>Day streak</span>
      </div>
      <div>
        <strong>74%</strong>
        <span>Completion</span>
      </div>
    </div>
  </article>

  <div class="profile-stack">
    <article class="content-card glass-card">
      <div class="section-head"><h3>Study goals</h3></div>
      <div class="goal-item"><span>Daily focus time</span><strong>4 hours</strong></div>
      <div class="goal-item"><span>Weekly tasks</span><strong>14 tasks</strong></div>
      <div class="goal-item"><span>Target completion</span><strong>80%</strong></div>
    </article>

    <article class="content-card glass-card">
      <div class="section-head"><h3>Preferences</h3></div>
      <button class="theme-toggle" type="button" data-theme-toggle>
        <span><i class="fa-solid fa-moon"></i> Dark mode</span>
        <span class="toggle-track"><span class="toggle-thumb"></span></span>
      </button>
    </article>
  </div>

</div>

<!-- Edit modal -->
<div class="profile-modal" data-profile-edit-panel hidden>
  <div class="profile-modal-backdrop" data-profile-edit-toggle></div>
  <section class="profile-modal-card" role="dialog" aria-modal="true" aria-labelledby="edit-title">
    <div class="minimal-head">
      <h3 id="edit-title">Edit profile</h3>
      <button class="icon-btn small-icon" type="button" aria-label="Close" data-profile-edit-toggle>
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div class="profile-edit-grid">
      <div class="form-group">
        <label for="profile-name">Name</label>
        <input id="profile-name" type="text" value="<?php echo htmlspecialchars($currentUserName); ?>">
      </div>
      <div class="form-group">
        <label for="profile-email">Email</label>
        <input id="profile-email" type="email" value="<?php echo htmlspecialchars($currentUserEmail); ?>">
      </div>
      <div class="form-group">
        <label for="profile-password">New password</label>
        <input id="profile-password" type="password" placeholder="Leave blank to keep current">
      </div>
    </div>
    <div class="profile-edit-actions">
      <button class="link-btn" type="button" data-profile-edit-toggle>Cancel</button>
      <button class="primary-btn" type="button" data-profile-save>
        <i class="fa-solid fa-check"></i> Save changes
      </button>
    </div>
  </section>
</div>

<?php include('includes/footer.php'); ?>