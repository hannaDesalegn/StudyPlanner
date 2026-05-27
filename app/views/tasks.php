<?php
$pageTitle  = 'Tasks';
$bodyClass  = 'page-tasks';
$showSidebar = true;
$activePage = 'tasks';

$taskSuccess = null;
$taskError   = null;
$editingTask = null;
$tasks = [
  ['task_id'=>1,'title'=>'Read chapter 4','description'=>'Biology review session','priority'=>'High','status'=>'To Do','due_date'=>'2026-05-30','created_at'=>'2026-05-25'],
  ['task_id'=>2,'title'=>'Practice quiz','description'=>'Math set and corrections','priority'=>'Medium','status'=>'In Progress','due_date'=>'2026-06-02','created_at'=>'2026-05-24'],
  ['task_id'=>3,'title'=>'Revise summary notes','description'=>'Short final pass','priority'=>'Low','status'=>'Done','due_date'=>null,'created_at'=>'2026-05-20'],
];

include('../public/includes/header.php');
?>

<section class="page-header compact">
  <div>
    <p class="eyebrow">Task hub</p>
    <h2>Keep your list short and clear.</h2>
    <?php if ($taskSuccess): ?>
      <p class="form-message success" style="margin-top:0.75rem;"><?php echo htmlspecialchars($taskSuccess); ?></p>
    <?php endif; ?>
    <?php if ($taskError): ?>
      <p class="form-message error" style="margin-top:0.75rem;"><?php echo htmlspecialchars($taskError); ?></p>
    <?php endif; ?>
  </div>
  <div class="search-bar glass-card">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" id="taskSearch" placeholder="Search tasks…" autocomplete="off">
    <select id="taskFilter">
      <option value="all">All</option>
      <option value="high">High</option>
      <option value="medium">Medium</option>
      <option value="low">Low</option>
    </select>
  </div>
</section>

<section class="tasks-layout">

  <!-- Add/Edit form -->
  <form class="content-card glass-card task-form" method="post" action="#">
    <input type="hidden" name="action" value="<?php echo $editingTask ? 'update_task' : 'add_task'; ?>">
    <?php if ($editingTask): ?>
      <input type="hidden" name="task_id" value="<?php echo (int) $editingTask['task_id']; ?>">
    <?php endif; ?>

    <div class="section-head">
      <h3><?php echo $editingTask ? 'Edit task' : 'New task'; ?></h3>
      <span class="subtle" style="font-size:0.75rem;"><kbd>N</kbd> to focus</span>
    </div>

    <div class="form-group">
      <label for="task-title">Task title</label>
      <input id="task-title" name="title" type="text" placeholder="What do you need to do?" value="<?php echo htmlspecialchars($editingTask['title'] ?? ''); ?>" required autocomplete="off">
    </div>

    <div class="form-grid">
      <div class="form-group" style="margin-bottom:0">
        <label for="task-date">Due date</label>
        <input id="task-date" name="due_date" type="date" value="<?php echo htmlspecialchars(isset($editingTask['due_date']) && $editingTask['due_date'] ? date('Y-m-d', strtotime($editingTask['due_date'])) : ''); ?>">
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label for="task-priority">Priority</label>
        <select id="task-priority" name="priority">
          <?php foreach (['High','Medium','Low'] as $opt): ?>
            <option value="<?php echo $opt; ?>" <?php echo (($editingTask['priority'] ?? 'Medium') === $opt) ? 'selected' : ''; ?>><?php echo $opt; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-group" style="margin-top:0.75rem;">
      <label for="task-note">Notes</label>
      <textarea id="task-note" name="description" rows="3" placeholder="Optional context or details…"><?php echo htmlspecialchars($editingTask['description'] ?? ''); ?></textarea>
    </div>

    <div class="task-form-footer">
      <div class="form-group" style="margin-bottom:0;min-width:140px;">
        <label for="task-status">Status</label>
        <select id="task-status" name="status">
          <?php foreach (['To Do','In Progress','Done'] as $opt): ?>
            <option value="<?php echo $opt; ?>" <?php echo (($editingTask['status'] ?? 'To Do') === $opt) ? 'selected' : ''; ?>><?php echo $opt; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="task-form-actions">
        <?php if ($editingTask): ?>
          <a class="link-btn" href="tasks.php">Cancel</a>
        <?php endif; ?>
        <button class="primary-btn" type="submit">
          <i class="fa-solid fa-<?php echo $editingTask ? 'check' : 'plus'; ?>"></i>
          <?php echo $editingTask ? 'Save changes' : 'Add task'; ?>
        </button>
      </div>
    </div>
  </form>

  <!-- Task list -->
  <div class="tasks-list">
    <?php if (empty($tasks)): ?>
      <article class="task-card glass-card empty-state">
        <i class="fa-solid fa-clipboard-list"></i>
        <h3>No tasks yet</h3>
        <p>Add your first task on the left to get started.</p>
      </article>
    <?php else: ?>
      <?php foreach ($tasks as $task):
        $pClass = strtolower($task['priority']);
        $sMap = ['To Do'=>'todo','In Progress'=>'pending','Done'=>'done'];
        $sClass = $sMap[$task['status']] ?? 'todo';
      ?>
        <article
          class="task-card glass-card"
          data-priority="<?php echo strtolower($task['priority']); ?>"
          data-title="<?php echo htmlspecialchars(strtolower($task['title'].' '.($task['description']??''))); ?>">
          <div class="task-card-head">
            <div>
              <h3><?php echo htmlspecialchars($task['title']); ?></h3>
              <p><?php echo htmlspecialchars($task['description'] ?: 'No notes'); ?></p>
            </div>
            <span class="badge <?php echo $pClass; ?>"><?php echo htmlspecialchars($task['priority']); ?></span>
          </div>
          <div class="task-meta">
            <span class="status <?php echo $sClass; ?>"><?php echo htmlspecialchars($task['status']); ?></span>
            <span><?php echo $task['due_date'] ? htmlspecialchars(date('M d, Y', strtotime($task['due_date']))) : 'No due date'; ?></span>
          </div>
          <div class="task-actions">
            <a class="text-btn" href="tasks.php?edit=<?php echo (int)$task['task_id']; ?>">
              <i class="fa-solid fa-pen"></i> Edit
            </a>
            <form class="task-delete-form" method="post" action="#" onsubmit="return confirm('Delete this task?');">
              <input type="hidden" name="action" value="delete_task">
              <input type="hidden" name="task_id" value="<?php echo (int)$task['task_id']; ?>">
              <button type="submit" class="text-btn danger">
                <i class="fa-solid fa-trash"></i> Delete
              </button>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</section>

<?php include('includes/footer.php'); ?>