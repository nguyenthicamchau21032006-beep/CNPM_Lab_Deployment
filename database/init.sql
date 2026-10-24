CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    is_done TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tasks (title, description, is_done)
SELECT 'Tìm hiểu GitHub', 'Tạo repository cá nhân và push source code.', 0
WHERE NOT EXISTS (SELECT 1 FROM tasks);

INSERT INTO tasks (title, description, is_done)
SELECT 'Chạy website bằng Docker', 'Khởi chạy PHP và MySQL bằng Docker Compose.', 0
WHERE (SELECT COUNT(*) FROM tasks) < 2;
