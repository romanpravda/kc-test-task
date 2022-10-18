CREATE TABLE IF NOT EXISTS `students`
(
    `student_id`          INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`             INT          NOT NULL,
    `full_name`           VARCHAR(255) NOT NULL,
    `group`               VARCHAR(255) NULL,
    `created_at`          DATETIME     NOT NULL,
    `created_at_timezone` VARCHAR(6)   NOT NULL,
    `updated_at`          DATETIME     NOT NULL,
    `updated_at_timezone` VARCHAR(6)   NOT NULL,
    CONSTRAINT `students_users_user_id_fk`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
            ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX `students_created_at_created_at_timezone_index`
    ON `students` (`created_at`, `created_at_timezone`);