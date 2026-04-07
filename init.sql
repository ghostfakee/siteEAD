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
