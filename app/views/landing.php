<?php
$formToShow   = isset($_SESSION['form']) ? $_SESSION['form'] : 'login';
$pageTitle    = 'Welcome';
$bodyClass    = 'page-landing';
$showSidebar  = false;
$activePage   = 'landing';
include('../public/includes/header.php');
?>

<div class="landing-hero">
  <div class="landing-right">
    <div class="auth-panel single-auth">

      <!-- Login -->
      <form
        class="auth-card auth-form <?php echo $formToShow !== 'signup' ? 'is-active' : ''; ?>"
        action="index.php?page=authenticate" method="post" data-login-panel>
        <p class="eyebrow">Get started</p>
        <h2 class="auth-title">Welcome back</h2>

        <div class="form-group">
          <label for="login-email">Email address</label>
          <input id="login-email" name="email" type="email" placeholder="you@example.com" required autocomplete="email">
          <?php if($formToShow === 'login' && isset($_SESSION['errors']['email'])): ?>
            <p class="field-error"><?php echo $_SESSION['errors']['email']; ?></p>
          <?php endif; ?>
        </div>
        <div class="form-group password-group">
          <label for="login-password">Password</label>
          <div class="password-wrapper">
            <input
              id="login-password"
              name="password"
              type="password"
              placeholder="Your password"
              required
              autocomplete="current-password"
            >
            <button type="button" class="password-toggle" aria-label="Toggle password visibility">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <?php if($formToShow === 'login' && isset($_SESSION['errors']['password'])): ?>
            <p class="field-error"><?php echo $_SESSION['errors']['password']; ?></p>
          <?php endif; ?>
        </div>
        <button class="primary-btn full-btn" type="submit" style="margin-bottom:0.75rem;">
          <i class="fa-solid fa-arrow-right-to-bracket"></i> Sign in
        </button>
        <p class="inline-link-row">
          No account?
          <button class="link-btn" type="button" data-signup-toggle>Create one free</button>
        </p>
      </form>

      <!-- Sign up -->
      <form
        class="auth-card auth-form auth-panel-hidden <?php echo $formToShow === 'signup' ? 'is-visible is-active' : ''; ?>"
        action="index.php?page=store-register" method="post" data-signup-panel>
        <div class="minimal-head">
          <h3>Create account</h3>
          <button class="icon-btn small-icon" type="button" aria-label="Close" data-signup-close>
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <div class="form-group">
          <label for="signup-name">Full name</label>
          <input id="signup-name" name="full_name" type="text" placeholder="Your name" required>
          <?php if($formToShow === 'signup' && isset($_SESSION['errors']['full_name'])): ?>
            <p class="field-error"><?php echo $_SESSION['errors']['full_name']; ?></p>
          <?php endif; ?>
        </div>
        <div class="form-group">
          <label for="signup-email">Email address</label>
          <input id="signup-email" name="email" type="email" placeholder="you@example.com" required autocomplete="email">
          <?php if($formToShow === 'signup' && isset($_SESSION['errors']['email'])): ?>
            <p class="field-error"><?php echo $_SESSION['errors']['email']; ?></p>
          <?php endif; ?>
        </div>
        <div class="form-group password-group">
          <label for="signup-password">Password</label>
          <div class="password-wrapper">
            <input
              id="signup-password"
              name="password"
              type="password"
              placeholder="Create a strong password"
              required
              autocomplete="new-password"
            >
            <button type="button" class="password-toggle" aria-label="Toggle password visibility">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <?php if($formToShow === 'signup' && isset($_SESSION['errors']['password'])): ?>
            <p class="field-error"><?php echo $_SESSION['errors']['password']; ?></p>
          <?php endif; ?>
        </div>
        <button class="secondary-btn full-btn" type="submit" style="margin-bottom:0.75rem;">
          <i class="fa-solid fa-user-plus"></i> Create account
        </button>
        
      </form>

    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
<?php if(isset($_SESSION['errors'])) { unset($_SESSION['errors']); } ?>
<?php if(isset($_SESSION['form'])) { unset($_SESSION['form']); } ?>