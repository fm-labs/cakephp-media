CREATE TABLE `media_posts` (
                               `id` INT(10) UNSIGNED NULL DEFAULT NULL,
                               `title` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
                               `image` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
                               `images` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
                               `text` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
                               `html` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci'
)
    COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB
;
