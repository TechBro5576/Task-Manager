-- ============================================
-- TASKS GO — DATABASE SCHEMA
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- USERS
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)        NOT NULL,
    email           VARCHAR(255)        NOT NULL UNIQUE,
    password_hash   VARCHAR(255)        NOT NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PROJECTS
-- ============================================
CREATE TABLE IF NOT EXISTS projects (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED        NOT NULL,
    name            VARCHAR(100)        NOT NULL,
    color           VARCHAR(20)         NOT NULL DEFAULT '#6366f1',
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_projects_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    INDEX idx_projects_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TASKS
-- ============================================
CREATE TABLE IF NOT EXISTS tasks (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED        NOT NULL,
    project_id      INT UNSIGNED        NULL,
    title           VARCHAR(255)        NOT NULL,
    notes           TEXT                NULL,
    priority        ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    status          ENUM('todo','done') NOT NULL DEFAULT 'todo',
    due_date        DATE                NULL,
    completed_at    DATETIME            NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_tasks_project
        FOREIGN KEY (project_id) REFERENCES projects(id)
        ON DELETE SET NULL,
    INDEX idx_tasks_user (user_id),
    INDEX idx_tasks_project (project_id),
    INDEX idx_tasks_status (status),
    INDEX idx_tasks_due (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CONTACT MESSAGES
-- ============================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)        NOT NULL,
    email           VARCHAR(255)        NOT NULL,
    subject         VARCHAR(50)         NOT NULL,
    message         TEXT                NOT NULL,
    ip_address      VARCHAR(45)         NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contact_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;