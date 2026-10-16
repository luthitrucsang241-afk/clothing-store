-- Chạy file này MỘT LẦN trong phpMyAdmin nếu bạn đã import noirva_store.sql trước đó
-- (chọn database noirva_store -> tab SQL -> dán -> Go). Nếu import mới hoàn toàn thì KHÔNG cần.

ALTER TABLE `password_resets`
  ADD COLUMN `channel` VARCHAR(10) NOT NULL DEFAULT 'email' AFTER `token_hash`,
  ADD COLUMN `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `channel`;

CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `selector` CHAR(24) NOT NULL,
  `validator_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rt_selector` (`selector`),
  CONSTRAINT `fk_rt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;


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

UPDATE `settings` SET `setting_value`='Đại học Văn Hiến, 613 Âu Cơ, Tân Phú, TP. Hồ Chí Minh' WHERE `setting_key`='site_address';
INSERT INTO `settings` (`setting_key`,`setting_value`) VALUES ('map_query','Đại học Văn Hiến, 613 Âu Cơ, Tân Phú, Hồ Chí Minh')
  ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);
