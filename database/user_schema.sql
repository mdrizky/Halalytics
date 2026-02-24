-- SQL Migration/Script to create the users table with Firebase and FCM support
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `firebase_uid` VARCHAR(128) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL,
    `display_name` VARCHAR(100) DEFAULT NULL,
    `fcm_token` TEXT DEFAULT NULL,
    `last_sync` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
