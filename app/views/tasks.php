<?php

$pageTitle  = 'Tasks';
$bodyClass  = 'page-tasks';
$showSidebar = true;
$activePage = 'tasks';

$taskSuccess = $_SESSION['success'] ?? null;
$taskError   = $_SESSION['error'] ?? null;

unset($_SESSION['success']);
unset($_SESSION['error']);

if (!isset($editingTask)) {
    $editingTask = null;
}

include('../public/includes/header.php');



?>

<section class="page-header compact">
  <div>
    <p class="eyebrow">Task hub</p>
    <h2>Keep your list short and clear.</h2>

    <?php if ($taskSuccess): ?>
      <p class="form-message success flash-message" style="margin-top:0.75rem;">
        <?php echo htmlspecialchars($taskSuccess); ?>
      </p>
    <?php endif; ?>

    <?php if ($taskError): ?>
      <p class="form-message error flash-message" style="margin-top:0.75rem;">
        <?php echo htmlspecialchars($taskError); ?>
      </p>
    <?php endif; ?>
  </div>

  <div class="search-bar glass-card">
    <i class="fa-solid fa-magnifying-glass"></i>

    <input
      type="text"
      id="taskSearch"
      placeholder="Search tasks…"
      autocomplete="off"
    >

    <select id="taskFilter">
      <option value="all">All</option>
      <option value="high">High</option>
      <option value="medium">Medium</option>
      <option value="low">Low</option>
    </select>
  </div>
</section>

<section class="tasks-layout">

  <!-- Add / Edit Task Form -->
  <form
    class="content-card glass-card task-form"
    method="post"
    action="index.php?page=<?php echo $editingTask ? 'update-task' : 'store-task'; ?>"
  >

    <?php if ($editingTask): ?>
      <input
        type="hidden"
        name="task_id"
        value="<?php echo $editingTask['task_id']; ?>"
      >
    <?php endif; ?>

    <div class="section-head">

      <h3>
        <?php echo $editingTask ? 'Edit task' : 'New task'; ?>
      </h3>

      <span class="subtle" style="font-size:0.75rem;">
        <kbd>N</kbd> to focus
      </span>

    </div>

    <div class="form-group">

      <label for="task-title">Task title</label>

      <input
        id="task-title"
        name="title"
        type="text"
        placeholder="What do you need to do?"
        value="<?php echo htmlspecialchars($editingTask['title'] ?? ''); ?>"
        required
        autocomplete="off"
      >

    </div>

    <div class="form-grid">

      <div class="form-group" style="margin-bottom:0">

        <label for="task-date">Due date</label>

        <input
          id="task-date"
          name="due_date"
          type="date"
          value="<?php echo htmlspecialchars($editingTask['due_date'] ?? ''); ?>"
        >

      </div>

      <div class="form-group" style="margin-bottom:0">

        <label for="task-priority">Priority</label>

        <select id="task-priority" name="priority">

          <option
            value="High"
            <?php echo (($editingTask['priority'] ?? '') === 'High') ? 'selected' : ''; ?>
          >
            High
          </option>

          <option
            value="Medium"
            <?php echo (($editingTask['priority'] ?? 'Medium') === 'Medium') ? 'selected' : ''; ?>
          >
            Medium
          </option>

          <option
            value="Low"
            <?php echo (($editingTask['priority'] ?? '') === 'Low') ? 'selected' : ''; ?>
          >
            Low
          </option>

        </select>

      </div>

    </div>

    <div class="form-group" style="margin-top:0.75rem;">

      <label for="task-note">Notes</label>

      <textarea
        id="task-note"
        name="description"
        rows="3"
        placeholder="Optional context or details…"
      ><?php echo htmlspecialchars($editingTask['description'] ?? ''); ?></textarea>

    </div>

    <div class="task-form-footer">

      <div class="form-group" style="margin-bottom:0;min-width:140px;">

        <label for="task-status">Status</label>

        <select id="task-status" name="status">

          <option
            value="To Do"
            <?php echo (($editingTask['status'] ?? 'To Do') === 'To Do') ? 'selected' : ''; ?>
          >
            To Do
          </option>

          <option
            value="In Progress"
            <?php echo (($editingTask['status'] ?? '') === 'In Progress') ? 'selected' : ''; ?>
          >
            In Progress
          </option>

          <option
            value="Done"
            <?php echo (($editingTask['status'] ?? '') === 'Done') ? 'selected' : ''; ?>
          >
            Done
          </option>

        </select>

      </div>

      <div class="task-form-actions">

        <?php if ($editingTask): ?>

          <a
            class="link-btn"
            href="index.php?page=tasks"
          >
            Cancel
          </a>

        <?php endif; ?>

        <button class="primary-btn" type="submit">

          <i class="fa-solid fa-<?php echo $editingTask ? 'check' : 'plus'; ?>"></i>

          <?php echo $editingTask ? 'Update task' : 'Add task'; ?>

        </button>

      </div>

    </div>

  </form>

  <!-- Tasks List -->
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

        $sMap = [
          'To Do' => 'todo',
          'In Progress' => 'pending',
          'Done' => 'done'
        ];

        $sClass = $sMap[$task['status']] ?? 'todo';

      ?>

        <article
          class="task-card glass-card"
          data-priority="<?php echo strtolower($task['priority']); ?>"
          data-title="<?php echo htmlspecialchars(strtolower($task['title'] . ' ' . ($task['description'] ?? ''))); ?>"
        >

          <div class="task-card-head">

            <div>

              <h3><?php echo htmlspecialchars($task['title']); ?></h3>

              <p>
                <?php echo htmlspecialchars($task['description'] ?: 'No notes'); ?>
              </p>

            </div>

            <span class="badge <?php echo $pClass; ?>">
              <?php echo htmlspecialchars($task['priority']); ?>
            </span>

          </div>

          <div class="task-meta">

            <span class="status <?php echo $sClass; ?>">
              <?php echo htmlspecialchars($task['status']); ?>
            </span>

            <span>

              <?php
                echo $task['due_date']
                  ? htmlspecialchars(date('M d, Y', strtotime($task['due_date'])))
                  : 'No due date';
              ?>

            </span>

          </div>

          <div class="task-actions">

            <a
              class="text-btn"
              href="index.php?page=edit-task&task_id=<?php echo (int)$task['task_id']; ?>"
            >
              <i class="fa-solid fa-pen"></i>
              Edit
            </a>

            <form
              class="task-delete-form"
              method="post"
              action="index.php?page=delete-task"
              onsubmit="return confirm('Delete this task?');"
            >

              <input
                type="hidden"
                name="task_id"
                value="<?php echo (int)$task['task_id']; ?>"
              >

              <button type="submit" class="text-btn danger">

                <i class="fa-solid fa-trash"></i>

                Delete

              </button>

            </form>

          </div>

        </article>

      <?php endforeach; ?>

    <?php endif; ?>

  </div>

</section>

<script>

setTimeout(() => {

    document.querySelectorAll(".flash-message")
    .forEach(message => {

        message.style.transition =
            "opacity 0.5s ease";

        message.style.opacity = "0";

        setTimeout(() => {
            message.remove();
        }, 500);

    });

}, 2500);

</script>

<?php include('includes/footer.php'); ?>