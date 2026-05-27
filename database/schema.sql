CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    profile_image VARCHAR(255)
);

CREATE TABLE tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('High', 'Medium', 'Low') DEFAULT 'Medium',
    status ENUM('To Do', 'In Progress', 'Done') DEFAULT 'To Do',
    due_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);

ALTER TABLE users ADD COLUMN streak INT DEFAULT 0;
ALTER TABLE users ADD COLUMN last_active DATE NULL;

CREATE TABLE study_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    daily_focus_hours INT DEFAULT 4,
    weekly_tasks_target INT DEFAULT 14,
    target_completion INT DEFAULT 80,
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);