CREATE TABLE IF NOT EXISTS `kc-test-task`.`users`
(
    `user_id`             INT AUTO_INCREMENT PRIMARY KEY,
    `email`               VARCHAR(255) NOT NULL,
    `username`            VARCHAR(255) NOT NULL,
    `password`            VARCHAR(255) NOT NULL,
    `created_at`          DATETIME     NOT NULL,
    `created_at_timezone` VARCHAR(6)   NOT NULL,
    `updated_at`          DATETIME     NOT NULL,
    `updated_at_timezone` VARCHAR(6)   NOT NULL,
    CONSTRAINT `users_email_uindex`
        UNIQUE (`email`),
    CONSTRAINT `users_username_uindex`
        UNIQUE (`username`)
);

CREATE INDEX `users_created_at_created_at_timezone_index`
    ON `kc-test-task`.`users` (`created_at`, `created_at_timezone`);