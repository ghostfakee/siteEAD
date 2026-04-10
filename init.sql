-- UNIVAG CMS Database Schema
CREATE DATABASE IF NOT EXISTS univag_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE univag_cms;

-- Main site content (JSON blob - mirrors the current content.json structure)
CREATE TABLE IF NOT EXISTS content_store (
    store_key VARCHAR(100) PRIMARY KEY,
    store_value LONGTEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Manual categories configuration
CREATE TABLE IF NOT EXISTS manual_categories (
    category_key VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL DEFAULT '',
    manuals_title VARCHAR(255) DEFAULT '',
    videos_title VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO manual_categories (category_key, title, manuals_title, videos_title, sort_order) VALUES
    ('aluno', 'Manual do Aluno', 'Manual do Aluno', 'Videos Tutoriais', 1),
    ('professor', 'Manual do Professor', 'Manual do Professor', 'Videos Tutoriais', 2)
ON DUPLICATE KEY UPDATE category_key = category_key;

-- Manuals (PDFs, links, or embedded videos) with image stored in DB
CREATE TABLE IF NOT EXISTS manuals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_data LONGBLOB,
    image_mime VARCHAR(100),
    image_filename VARCHAR(255),
    media_type ENUM('pdf', 'video') DEFAULT 'pdf',
    pdf_url VARCHAR(2000),
    pdf_data LONGBLOB,
    pdf_mime VARCHAR(100),
    pdf_filename VARCHAR(255),
    video_url VARCHAR(2000),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category) REFERENCES manual_categories(category_key) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Video tutorials per category
CREATE TABLE IF NOT EXISTS manual_videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    video_url VARCHAR(2000),
    sort_order INT DEFAULT 0,
    FOREIGN KEY (category) REFERENCES manual_categories(category_key) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Manuals page header config
CREATE TABLE IF NOT EXISTS manual_page_config (
    config_key VARCHAR(100) PRIMARY KEY,
    config_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO manual_page_config (config_key, config_value) VALUES
    ('title', 'Manuais e Tutoriais'),
    ('intro', 'Selecione a categoria de manuais abaixo'),
    ('hero_image', ''),
    ('hero_logo', '')
ON DUPLICATE KEY UPDATE config_key = config_key;

-- Site images (cover images, etc.) stored as blobs
CREATE TABLE IF NOT EXISTS site_images (
    image_key VARCHAR(100) PRIMARY KEY,
    image_data LONGBLOB NOT NULL,
    image_mime VARCHAR(100) NOT NULL,
    image_filename VARCHAR(255),
    image_size INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Modality cards (max 4)
CREATE TABLE IF NOT EXISTS modalities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    cta_label VARCHAR(100),
    cta_url VARCHAR(2000),
    image_data LONGBLOB,
    image_mime VARCHAR(100),
    image_filename VARCHAR(255),
    sort_order INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- News articles (max 5, one featured)
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT,
    url VARCHAR(2000),
    image_data LONGBLOB,
    image_mime VARCHAR(100),
    image_filename VARCHAR(255),
    published_at DATE NOT NULL,
    featured TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CMS users with Argon2id hashed passwords
CREATE TABLE IF NOT EXISTS cms_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','editor') DEFAULT 'admin',
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Security: audit log
CREATE TABLE IF NOT EXISTS audit_log (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    severity ENUM('info','warning','error','critical') DEFAULT 'info',
    user VARCHAR(100),
    ip VARCHAR(45),
    user_agent VARCHAR(500),
    description TEXT,
    context JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_type (event_type),
    INDEX idx_severity (severity),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Security: rate limiting for login
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username_ip (username, ip),
    INDEX idx_attempted_at (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Observability: slow query metrics
CREATE TABLE IF NOT EXISTS db_metrics (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    query_hash CHAR(32) NOT NULL,
    query_sample TEXT,
    exec_time_ms FLOAT NOT NULL,
    rows_affected INT DEFAULT 0,
    called_from VARCHAR(300),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_exec_time (exec_time_ms),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
