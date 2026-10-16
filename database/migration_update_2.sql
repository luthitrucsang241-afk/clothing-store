-- Chạy MỘT LẦN nếu bạn đã chạy migration_update_1.sql trước đó (chỉ thêm bảng hộp thư).
-- ---------------------------------------------------------------------
-- 22c. user_emails (hop thu: email/thong bao admin gui cho khach)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_emails` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `body` TEXT NOT NULL,
  `sent_by` INT UNSIGNED DEFAULT NULL COMMENT 'NULL = he thong tu dong',
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ue_user` (`user_id`,`is_read`),
  CONSTRAINT `fk_ue_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
