CREATE DATABASE IF NOT EXISTS newsora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'newsora_user'@'localhost' IDENTIFIED BY 'newsora_pass_2026';

GRANT ALL PRIVILEGES ON newsora.* TO 'newsora_user'@'localhost';

FLUSH PRIVILEGES;

