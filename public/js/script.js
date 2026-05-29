// Theme
const THEME_KEY = 'sp_theme';
function applyTheme(dark) {
  document.body.classList.toggle('dark-mode', dark);
  localStorage.setItem(THEME_KEY, dark ? 'dark' : 'light');
}
(function () {
  const saved = localStorage.getItem(THEME_KEY);
  document.body.classList.toggle('dark-mode', saved === 'dark');
})();

document.querySelectorAll('[data-theme-toggle]').forEach(btn => {
  btn.addEventListener('click', () => {
    applyTheme(!document.body.classList.contains('dark-mode'));
  });
});



//  Toast Notifications 
function showToast(message, type = 'info', duration = 3500) {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const icons = { success: 'fa-circle-check', error: 'fa-circle-xmark', info: 'fa-circle-info' };
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.classList.add('removing');
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
  }, duration);
}
window.showToast = showToast;

// Auth panel toggle 
const loginPanel  = document.querySelector('[data-login-panel]');
const signupPanel = document.querySelector('[data-signup-panel]');

function showSignup() {
  if (!loginPanel || !signupPanel) return;
  loginPanel.classList.remove('is-active');
  signupPanel.classList.add('is-visible', 'is-active');
}
function showLogin() {
  if (!loginPanel || !signupPanel) return;
  signupPanel.classList.remove('is-visible', 'is-active');
  loginPanel.classList.add('is-active');
}

document.querySelectorAll('[data-signup-toggle]').forEach(b => b.addEventListener('click', showSignup));
document.querySelectorAll('[data-signup-close]').forEach(b => b.addEventListener('click', showLogin));

// Clear inline field errors when the user starts editing a field
const authInputs = document.querySelectorAll('[data-login-panel] input, [data-signup-panel] input');

const clearFieldErrors = () => {
  document.querySelectorAll('.field-error').forEach(el => el.remove());
};

if (authInputs.length) {
  authInputs.forEach(input => {
    input.addEventListener('input', clearFieldErrors, { once: true });
    input.addEventListener('change', clearFieldErrors, { once: true });
  });
}

// Password visibility toggle 
document.querySelectorAll('.password-toggle').forEach(button => {

  button.addEventListener('click', () => {

    const input = button.previousElementSibling;
    const icon = button.querySelector('i');

    if (input.type === 'password')
    {
      input.type = 'text';

      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    }
    else
    {
      input.type = 'password';

      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }

  });

});
// Profile modal 
const profileModal = document.querySelector('[data-profile-edit-panel]');
const profileInitials = document.querySelector('[data-profile-initials]');

function getInitials(name) {
  return (name || '')
    .trim()
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map(part => part[0].toUpperCase())
    .join('') || 'YP';
}



document.querySelectorAll('[data-profile-edit-toggle]').forEach(btn => {
  btn.addEventListener('click', () => {
    profileModal && profileModal.hasAttribute('hidden') ? openProfile() : closeProfile();
  });
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeProfile(); });

// Profile image live preview 
document.getElementById('profile-image')?.addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = e => {
    const avatar = document.querySelector('.profile-avatar');
    if (!avatar) return;

    // Replace initials span with preview image
    avatar.innerHTML = `<img src="${e.target.result}" 
      alt="Profile preview"
      style="width:100%;height:100%;border-radius:50%;object-fit:cover;display:block;">`;
  };
  reader.readAsDataURL(file);
});

//Task search/filter
const taskSearch = document.getElementById('taskSearch');
const taskFilter = document.getElementById('taskFilter');

function filterTasks() {
  const q = taskSearch?.value.toLowerCase() || '';
  const p = taskFilter?.value || 'all';
  
}
taskSearch?.addEventListener('input', filterTasks);
taskFilter?.addEventListener('change', filterTasks);



// animate stat counters
function animateCounter(el) {
  const target = parseFloat(el.textContent.replace(/[^0-9.]/g, ''));
  const suffix = el.textContent.replace(/[0-9.]/g, '');
  if (isNaN(target)) return;
  let start = 0;
  const dur = 900;
  const step = timestamp => {
    if (!start) start = timestamp;
    const progress = Math.min((timestamp - start) / dur, 1);
    const ease = 1 - Math.pow(1 - progress, 3);
    el.textContent = (target < 10 ? String(Math.floor(ease * target)).padStart(2, '0') : Math.floor(ease * target)) + suffix;
    if (progress < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}

const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.querySelectorAll('.stat-card h3').forEach(animateCounter);
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.3 });
document.querySelectorAll('.stats-grid').forEach(el => observer.observe(el));

// progress bar animation 
document.querySelectorAll('.progress-bar span').forEach(bar => {
  const target = bar.style.width;
  bar.style.width = '0';
  setTimeout(() => { bar.style.width = target; }, 300);
});

//pomodoro Timer 
(function () {
  const ring      = document.querySelector('.pomo-ring-fill');
  const display   = document.querySelector('.pomo-ring-text');
  const startBtn  = document.querySelector('[data-pomo-start]');
  const resetBtn  = document.querySelector('[data-pomo-reset]');
  const modebtns  = document.querySelectorAll('[data-pomo-mode]');
  if (!ring || !display) return;

  const MODES = { focus: 25 * 60, short: 5 * 60, long: 15 * 60 };
  let current = MODES.focus;
  let total   = MODES.focus;
  let timer   = null;
  let running = false;
let focusSeconds = parseInt(localStorage.getItem('focusSeconds')) || 0;
  const CIRC = 283; // 2π × 45

  function fmt(s) {
    const m = Math.floor(s / 60);
    const sec = s % 60;
    return `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
  }
  function updateRing() {
    const pct = current / total;
    ring.style.strokeDashoffset = CIRC * (1 - pct);
    display.textContent = fmt(current);
  }
  function stop() {
    clearInterval(timer);
    running = false;
    startBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
    startBtn.classList.remove('active');
  }

  startBtn?.addEventListener('click', () => {
    if (running) { stop(); return; }
    running = true;
    startBtn.innerHTML = '<i class="fa-solid fa-pause"></i>';
    startBtn.classList.add('active');
    timer = setInterval(() => {
      current--;

if (
  document.querySelector('[data-pomo-mode].active')?.dataset.pomoMode === 'focus'
) {

  focusSeconds++;

  localStorage.setItem(
    'focusSeconds',
    focusSeconds
  );

  updateFocusGoal();

}

updateRing();
      function updateFocusGoal() {

  const hours = (focusSeconds / 3600).toFixed(1);

  const goalEl = document.querySelector('[data-focus-goal]');

  if (goalEl) {
    goalEl.textContent = `${hours} / 6h`;
  }

}
      if (current <= 0) {
        stop();
        showToast('Pomodoro complete! Take a break.', 'success');
        current = total;
        updateRing();
      }
    }, 1000);
  });

  resetBtn?.addEventListener('click', () => {
    stop();
    current = total;
    updateRing();
  });

  modebtns.forEach(btn => {
    btn.addEventListener('click', function () {
      modebtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      stop();
      total = current =
  (parseInt(this.dataset.minutes) || 25) * 60;
      updateRing();
    });
  });

  updateRing();
updateFocusGoal();
})();

//keyboard shortcut
document.addEventListener('keydown', e => {

  // ignore shortcuts while typing
  if (
    e.target.tagName === 'INPUT' ||
    e.target.tagName === 'TEXTAREA' ||
    e.target.tagName === 'SELECT'
  ) {
    return;
  }

  // press N to create new task
  if (e.key === 'n' || e.key === 'N') {

    e.preventDefault();

    const taskInput = document.getElementById('task-title');

    // if already on tasks page
    if (taskInput) {

      taskInput.focus();

      taskInput.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });

      showToast(
        'Ready to add a new task',
        'info',
        2000
      );

    } else {

      // redirect and tell tasks page to autofocus
      window.location.href =
        'index.php?page=tasks&focusTask=1';

    }

  }

});

// mobile swipe to close sidebar 
let touchStart = 0;
document.addEventListener('touchstart', e => { touchStart = e.touches[0].clientX; });
document.addEventListener('touchend', e => {
  const diff = touchStart - e.changedTouches[0].clientX;
  if (diff > 60) document.body.classList.remove('sidebar-open');
  if (diff < -60 && touchStart < 40) document.body.classList.add('sidebar-open');
});
// auto focus task input after redirect 
window.addEventListener('load', () => {

  const params = new URLSearchParams(window.location.search);

  if (params.get('focusTask') === '1') {

    const taskInput = document.getElementById('task-title');

    if (taskInput) {

      taskInput.focus();

      taskInput.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });

      showToast(
        'Ready to add a new task',
        'info',
        2000
      );

    }

  }

});