CREATE TABLE IF NOT EXISTS `kc-test-task`.`tokens`
(
    `token_id`            INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`             INT          NOT NULL,
    `created_at`          DATETIME     NOT NULL,
    `created_at_timezone` VARCHAR(6)   NOT NULL,
    CONSTRAINT `tokens_users_user_id_fk`
        FOREIGN KEY (`user_id`) REFERENCES `kc-test-task`.`users` (`user_id`)
            ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX `tokens_created_at_created_at_timezone_index`
    ON `kc-test-task`.`tokens` (`created_at`, `created_at_timezone`);