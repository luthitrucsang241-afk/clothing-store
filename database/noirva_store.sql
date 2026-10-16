-- =====================================================================
-- NOIRVA STORE - DATABASE (MySQL / MariaDB, XAMPP + phpMyAdmin)
-- Cach import: phpMyAdmin -> tab "Import" -> chon file nay -> Go
-- File tu tao database `noirva_store`, khong can tao tay.
-- Tai khoan mau (mat khau: Admin@123):
--   Admin    : admin@noirva.local
--   Developer: dev@noirva.local
--   Khach    : khach@noirva.local
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = '+07:00';

DROP DATABASE IF EXISTS `noirva_store`;
CREATE DATABASE `noirva_store` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `noirva_store`;

-- ---------------------------------------------------------------------
-- 1. roles
-- ---------------------------------------------------------------------
CREATE TABLE `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_name` (`name`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. membership_levels
-- ---------------------------------------------------------------------
CREATE TABLE `membership_levels` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `minimum_spending` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `discount_rate` DECIMAL(5,2) NOT NULL DEFAULT 0,
  `description` VARCHAR(255) DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_membership_name` (`name`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. users
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `membership_level_id` INT UNSIGNED DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=active, 0=locked',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role_id`),
  KEY `idx_users_membership` (`membership_level_id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `fk_users_membership` FOREIGN KEY (`membership_level_id`) REFERENCES `membership_levels` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. employees
-- ---------------------------------------------------------------------
CREATE TABLE `employees` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `employee_code` VARCHAR(30) NOT NULL,
  `position` VARCHAR(100) DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_employee_code` (`employee_code`),
  UNIQUE KEY `uq_employee_user` (`user_id`),
  CONSTRAINT `fk_employees_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. categories
-- ---------------------------------------------------------------------
CREATE TABLE `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `name_en` VARCHAR(120) DEFAULT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255) DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. products
-- ---------------------------------------------------------------------
CREATE TABLE `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  `sku` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `material` VARCHAR(255) DEFAULT NULL,
  `brand` VARCHAR(100) DEFAULT 'NOIRVA',
  `price` DECIMAL(15,2) NOT NULL,
  `sale_price` DECIMAL(15,2) DEFAULT NULL,
  `main_image` VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_new` TINYINT(1) NOT NULL DEFAULT 0,
  `sold_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=dang ban, 0=an',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_slug` (`slug`),
  UNIQUE KEY `uq_products_sku` (`sku`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_status` (`status`),
  FULLTEXT KEY `ft_products_name` (`name`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `chk_products_price` CHECK (`price` >= 0),
  CONSTRAINT `chk_products_sale` CHECK (`sale_price` IS NULL OR `sale_price` >= 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. product_variants
-- ---------------------------------------------------------------------
CREATE TABLE `product_variants` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `size` VARCHAR(20) NOT NULL,
  `color` VARCHAR(50) NOT NULL,
  `color_code` VARCHAR(10) DEFAULT NULL,
  `sku` VARCHAR(60) NOT NULL,
  `stock_quantity` INT UNSIGNED NOT NULL DEFAULT 0,
  `price_adjustment` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_variants_sku` (`sku`),
  UNIQUE KEY `uq_variants_combo` (`product_id`,`size`,`color`),
  CONSTRAINT `fk_variants_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 8. product_images
-- ---------------------------------------------------------------------
CREATE TABLE `product_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_images_product` (`product_id`),
  CONSTRAINT `fk_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 9. banners
-- ---------------------------------------------------------------------
CREATE TABLE `banners` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `title_en` VARCHAR(200) DEFAULT NULL,
  `subtitle` VARCHAR(300) DEFAULT NULL,
  `subtitle_en` VARCHAR(300) DEFAULT NULL,
  `button_text` VARCHAR(60) DEFAULT NULL,
  `button_text_en` VARCHAR(60) DEFAULT NULL,
  `image_desktop` VARCHAR(255) NOT NULL,
  `image_mobile` VARCHAR(255) DEFAULT NULL,
  `link_url` VARCHAR(255) DEFAULT NULL,
  `position` ENUM('hero','promo','middle') NOT NULL DEFAULT 'hero',
  `sort_order` INT NOT NULL DEFAULT 0,
  `start_at` DATETIME DEFAULT NULL,
  `end_at` DATETIME DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_banners_pos` (`position`,`status`,`sort_order`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 10. vouchers
-- ---------------------------------------------------------------------
CREATE TABLE `vouchers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(40) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `discount_type` ENUM('percent','fixed') NOT NULL,
  `discount_value` DECIMAL(15,2) NOT NULL,
  `minimum_order_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `maximum_discount_amount` DECIMAL(15,2) DEFAULT NULL,
  `usage_limit` INT UNSIGNED DEFAULT NULL COMMENT 'NULL = khong gioi han',
  `used_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `start_at` DATETIME DEFAULT NULL,
  `end_at` DATETIME DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_vouchers_code` (`code`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 11. orders
-- ---------------------------------------------------------------------
CREATE TABLE `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_code` VARCHAR(30) NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `recipient_name` VARCHAR(120) NOT NULL,
  `recipient_phone` VARCHAR(20) NOT NULL,
  `recipient_email` VARCHAR(150) DEFAULT NULL,
  `shipping_address` VARCHAR(500) NOT NULL,
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `discount_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `shipping_fee` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `voucher_id` INT UNSIGNED DEFAULT NULL,
  `payment_method` VARCHAR(30) NOT NULL DEFAULT 'cod',
  `payment_status` ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `order_status` ENUM('pending','confirmed','processing','shipping','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `stock_deducted` TINYINT(1) NOT NULL DEFAULT 0,
  `note` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_code` (`order_code`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`order_status`),
  KEY `idx_orders_created` (`created_at`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_orders_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 12. order_items
-- ---------------------------------------------------------------------
CREATE TABLE `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED DEFAULT NULL,
  `product_variant_id` INT UNSIGNED DEFAULT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `sku` VARCHAR(60) DEFAULT NULL,
  `size` VARCHAR(20) DEFAULT NULL,
  `color` VARCHAR(50) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `unit_price` DECIMAL(15,2) NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `subtotal` DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_order` (`order_id`),
  CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_items_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 13. carts
-- ---------------------------------------------------------------------
CREATE TABLE `carts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `session_id` VARCHAR(128) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_carts_user` (`user_id`),
  KEY `idx_carts_session` (`session_id`),
  CONSTRAINT `fk_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 14. cart_items
-- ---------------------------------------------------------------------
CREATE TABLE `cart_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cart_id` INT UNSIGNED NOT NULL,
  `product_variant_id` INT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart_variant` (`cart_id`,`product_variant_id`),
  CONSTRAINT `fk_cartitems_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cartitems_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 15. payments
-- ---------------------------------------------------------------------
CREATE TABLE `payments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `payment_method` VARCHAR(30) NOT NULL DEFAULT 'cod',
  `amount` DECIMAL(15,2) NOT NULL,
  `payment_status` ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `transaction_code` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_payments_order` (`order_id`),
  CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 16. voucher_usages
-- ---------------------------------------------------------------------
CREATE TABLE `voucher_usages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `voucher_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `order_id` INT UNSIGNED NOT NULL,
  `discount_amount` DECIMAL(15,2) NOT NULL,
  `used_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vu_voucher` (`voucher_id`),
  KEY `idx_vu_user` (`user_id`),
  CONSTRAINT `fk_vu_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_vu_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 17. promotions + promotion_products
-- ---------------------------------------------------------------------
CREATE TABLE `promotions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `discount_percent` DECIMAL(5,2) DEFAULT NULL COMMENT 'NULL = chi hien thi thong tin, khong tu dong giam gia',
  `banner_image` VARCHAR(255) DEFAULT NULL,
  `start_at` DATETIME DEFAULT NULL,
  `end_at` DATETIME DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `promotion_products` (
  `promotion_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`promotion_id`,`product_id`),
  CONSTRAINT `fk_pp_promotion` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pp_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 18. reviews
-- ---------------------------------------------------------------------
CREATE TABLE `reviews` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `order_id` INT UNSIGNED DEFAULT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `comment` TEXT,
  `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=hien thi, 0=an',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_review_once` (`user_id`,`product_id`,`order_id`),
  KEY `idx_reviews_product` (`product_id`,`status`),
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_reviews_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 19. order_status_history
-- ---------------------------------------------------------------------
CREATE TABLE `order_status_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `old_status` VARCHAR(20) DEFAULT NULL,
  `new_status` VARCHAR(20) NOT NULL,
  `changed_by` INT UNSIGNED DEFAULT NULL,
  `note` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_osh_order` (`order_id`),
  CONSTRAINT `fk_osh_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_osh_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 20. contacts
-- ---------------------------------------------------------------------
CREATE TABLE `contacts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('new','read','done') NOT NULL DEFAULT 'new',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 21. email_logs
-- ---------------------------------------------------------------------
CREATE TABLE `email_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recipient` VARCHAR(150) NOT NULL,
  `email_type` VARCHAR(50) NOT NULL,
  `status` ENUM('sent','failed') NOT NULL DEFAULT 'sent',
  `error_message` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 22. password_resets
-- ---------------------------------------------------------------------
CREATE TABLE `password_resets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `channel` VARCHAR(10) NOT NULL DEFAULT 'email' COMMENT 'email = link, phone = ma OTP',
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` DATETIME NOT NULL,
  `used_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pr_token` (`token_hash`),
  CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 22b. remember_tokens (ghi nho dang nhap)
-- ---------------------------------------------------------------------
CREATE TABLE `remember_tokens` (
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
CREATE TABLE `user_emails` (
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

-- ---------------------------------------------------------------------
-- 23. settings
-- ---------------------------------------------------------------------
CREATE TABLE `settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_key` (`setting_key`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 24. newsletter_subscribers (dang ky nhan khuyen mai o trang chu)
-- ---------------------------------------------------------------------
CREATE TABLE `newsletter_subscribers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_newsletter_email` (`email`)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- DU LIEU MAU
-- =====================================================================

INSERT INTO `roles` (`id`,`name`,`description`) VALUES
(1,'user','Khach hang'),
(2,'admin','Quan tri vien'),
(3,'developer','Nha phat trien');

INSERT INTO `membership_levels` (`id`,`name`,`minimum_spending`,`discount_rate`,`description`) VALUES
(1,'Silver',0,0,'Hang mac dinh cho thanh vien moi'),
(2,'Gold',3000000,3,'Chi tieu tu 3.000.000d, giam them 3%'),
(3,'Diamond',10000000,5,'Chi tieu tu 10.000.000d, giam them 5%');

-- Mat khau cua ca 3 tai khoan: Admin@123 (bcrypt)
INSERT INTO `users` (`id`,`full_name`,`email`,`phone`,`address`,`password`,`role_id`,`membership_level_id`,`status`) VALUES
(1,'NOIRVA Admin','admin@noirva.local','0900000001','Ha Noi','$2b$12$.rZg/WgFADvhDazPq90jz.5epqbj1r2BFE/r2FFeBjYWJMkB0UCcS',2,NULL,1),
(2,'NOIRVA Developer','dev@noirva.local','0900000002','Ha Noi','$2b$12$.rZg/WgFADvhDazPq90jz.5epqbj1r2BFE/r2FFeBjYWJMkB0UCcS',3,NULL,1),
(3,'Nguyen Van An','khach@noirva.local','0912345678','12 Pho Hue, Hai Ba Trung, Ha Noi','$2b$12$.rZg/WgFADvhDazPq90jz.5epqbj1r2BFE/r2FFeBjYWJMkB0UCcS',1,1,1);

INSERT INTO `employees` (`user_id`,`employee_code`,`position`) VALUES
(1,'NV001','Quan tri he thong'),
(2,'NV002','Lap trinh vien');

INSERT INTO `categories` (`id`,`name`,`name_en`,`slug`,`description`) VALUES
(1,'Áo thun','T-Shirts','ao-thun','Áo thun cotton thoáng mát, form chuẩn.'),
(2,'Áo polo','Polo Shirts','ao-polo','Áo polo lịch sự, đi làm đi chơi đều hợp.'),
(3,'Áo sơ mi','Shirts','ao-so-mi','Áo sơ mi công sở và thường ngày.'),
(4,'Quần short','Shorts','quan-short','Quần short năng động cho mùa hè.'),
(5,'Quần dài','Pants','quan-dai','Quần dài jeans, kaki, jogger.'),
(6,'Đồ thể thao','Sportswear','do-the-thao','Trang phục thể thao co giãn, nhanh khô.'),
(7,'Phụ kiện','Accessories','phu-kien','Mũ, tất, thắt lưng và phụ kiện khác.');

INSERT INTO `products` (`id`,`category_id`,`name`,`slug`,`sku`,`description`,`material`,`price`,`sale_price`,`main_image`,`is_featured`,`is_new`,`sold_count`) VALUES
(1,1,'Áo thun NOIRVA Basic','ao-thun-noirva-basic','NV-AT-001','Áo thun basic cotton 100%, mềm mại, thấm hút tốt, form regular dễ phối đồ.','Cotton 100%',199000,149000,'assets/images/products/ao-thun-basic.jpg',1,1,120),
(2,1,'Áo thun NOIRVA Oversize','ao-thun-noirva-oversize','NV-AT-002','Áo thun oversize phong cách streetwear, chất vải dày dặn đứng form.','Cotton compact 240gsm',259000,NULL,'assets/images/products/ao-thun-oversize.jpg',1,1,85),
(3,2,'Áo polo NOIRVA Classic','ao-polo-noirva-classic','NV-AP-001','Áo polo cổ bẻ lịch sự, vải cá sấu thoáng khí, ít nhăn.','Cotton pique',329000,279000,'assets/images/products/ao-polo-classic.jpg',1,0,96),
(4,2,'Áo polo NOIRVA Active','ao-polo-noirva-active','NV-AP-002','Áo polo thể thao co giãn 4 chiều, nhanh khô.','Polyester spandex',359000,NULL,'assets/images/products/ao-polo-active.jpg',0,1,40),
(5,3,'Áo sơ mi NOIRVA Oxford','ao-so-mi-noirva-oxford','NV-SM-001','Áo sơ mi Oxford dài tay, phù hợp đi làm và đi chơi.','Cotton Oxford',449000,399000,'assets/images/products/ao-so-mi-oxford.jpg',1,0,64),
(6,3,'Áo sơ mi NOIRVA Linen','ao-so-mi-noirva-linen','NV-SM-002','Áo sơ mi linen mát nhẹ cho mùa hè.','Linen blend',489000,NULL,'assets/images/products/ao-so-mi-linen.jpg',0,1,22),
(7,4,'Quần short NOIRVA Kaki','quan-short-noirva-kaki','NV-QS-001','Quần short kaki co giãn nhẹ, lưng thun tiện lợi.','Kaki cotton spandex',299000,249000,'assets/images/products/quan-short-kaki.jpg',1,0,110),
(8,4,'Quần short NOIRVA Sport','quan-short-noirva-sport','NV-QS-002','Quần short thể thao 2 lớp, nhẹ và thoáng.','Polyester',229000,NULL,'assets/images/products/quan-short-sport.jpg',0,1,73),
(9,5,'Quần jeans NOIRVA Slim','quan-jeans-noirva-slim','NV-QD-001','Quần jeans slim fit co giãn, màu xanh đậm bền màu.','Denim cotton spandex',549000,479000,'assets/images/products/quan-jeans-slim.jpg',1,0,58),
(10,5,'Quần jogger NOIRVA Daily','quan-jogger-noirva-daily','NV-QD-002','Quần jogger nỉ mỏng, bo gấu, mặc hằng ngày.','Cotton French terry',399000,NULL,'assets/images/products/quan-jogger-daily.jpg',0,1,47),
(11,6,'Áo tank thể thao NOIRVA Run','ao-tank-the-thao-noirva-run','NV-TT-001','Áo ba lỗ chạy bộ siêu nhẹ, thoát mồ hôi nhanh.','Polyester mesh',189000,159000,'assets/images/products/ao-tank-run.jpg',0,1,35),
(12,7,'Mũ lưỡi trai NOIRVA Logo','mu-luoi-trai-noirva-logo','NV-PK-001','Mũ lưỡi trai thêu logo NOIRVA, khóa điều chỉnh phía sau.','Cotton twill',149000,NULL,'assets/images/products/mu-logo.jpg',0,0,90);

-- Bien the: size S/M/L/XL x 2 mau cho cac san pham mac (phu kien: 1 size)
INSERT INTO `product_variants` (`product_id`,`size`,`color`,`color_code`,`sku`,`stock_quantity`,`price_adjustment`) VALUES
(1,'S','Đen','#111111','NV-AT-001-S-BLK',30,0),(1,'M','Đen','#111111','NV-AT-001-M-BLK',40,0),(1,'L','Đen','#111111','NV-AT-001-L-BLK',35,0),(1,'XL','Đen','#111111','NV-AT-001-XL-BLK',20,10000),
(1,'S','Trắng','#FFFFFF','NV-AT-001-S-WHT',25,0),(1,'M','Trắng','#FFFFFF','NV-AT-001-M-WHT',40,0),(1,'L','Trắng','#FFFFFF','NV-AT-001-L-WHT',30,0),(1,'XL','Trắng','#FFFFFF','NV-AT-001-XL-WHT',15,10000),
(2,'M','Đen','#111111','NV-AT-002-M-BLK',30,0),(2,'L','Đen','#111111','NV-AT-002-L-BLK',30,0),(2,'XL','Đen','#111111','NV-AT-002-XL-BLK',20,0),
(2,'M','Xám','#9A9A9A','NV-AT-002-M-GRY',25,0),(2,'L','Xám','#9A9A9A','NV-AT-002-L-GRY',25,0),(2,'XL','Xám','#9A9A9A','NV-AT-002-XL-GRY',10,0),
(3,'S','Xanh navy','#1F2A44','NV-AP-001-S-NVY',20,0),(3,'M','Xanh navy','#1F2A44','NV-AP-001-M-NVY',30,0),(3,'L','Xanh navy','#1F2A44','NV-AP-001-L-NVY',30,0),(3,'XL','Xanh navy','#1F2A44','NV-AP-001-XL-NVY',15,0),
(3,'M','Trắng','#FFFFFF','NV-AP-001-M-WHT',25,0),(3,'L','Trắng','#FFFFFF','NV-AP-001-L-WHT',25,0),
(4,'M','Đen','#111111','NV-AP-002-M-BLK',20,0),(4,'L','Đen','#111111','NV-AP-002-L-BLK',20,0),(4,'XL','Đen','#111111','NV-AP-002-XL-BLK',10,0),
(4,'M','Xám','#9A9A9A','NV-AP-002-M-GRY',15,0),(4,'L','Xám','#9A9A9A','NV-AP-002-L-GRY',15,0),
(5,'S','Trắng','#FFFFFF','NV-SM-001-S-WHT',15,0),(5,'M','Trắng','#FFFFFF','NV-SM-001-M-WHT',25,0),(5,'L','Trắng','#FFFFFF','NV-SM-001-L-WHT',25,0),(5,'XL','Trắng','#FFFFFF','NV-SM-001-XL-WHT',10,0),
(5,'M','Xanh nhạt','#A9C4E4','NV-SM-001-M-BLU',20,0),(5,'L','Xanh nhạt','#A9C4E4','NV-SM-001-L-BLU',20,0),
(6,'M','Be','#D9C9A8','NV-SM-002-M-BEG',20,0),(6,'L','Be','#D9C9A8','NV-SM-002-L-BEG',20,0),(6,'XL','Be','#D9C9A8','NV-SM-002-XL-BEG',10,0),
(7,'M','Be','#D9C9A8','NV-QS-001-M-BEG',30,0),(7,'L','Be','#D9C9A8','NV-QS-001-L-BEG',30,0),(7,'XL','Be','#D9C9A8','NV-QS-001-XL-BEG',15,0),
(7,'M','Đen','#111111','NV-QS-001-M-BLK',25,0),(7,'L','Đen','#111111','NV-QS-001-L-BLK',25,0),
(8,'M','Đen','#111111','NV-QS-002-M-BLK',30,0),(8,'L','Đen','#111111','NV-QS-002-L-BLK',30,0),(8,'XL','Đen','#111111','NV-QS-002-XL-BLK',15,0),
(8,'M','Xanh navy','#1F2A44','NV-QS-002-M-NVY',20,0),(8,'L','Xanh navy','#1F2A44','NV-QS-002-L-NVY',20,0),
(9,'29','Xanh đậm','#1B3A5C','NV-QD-001-29-DBL',15,0),(9,'30','Xanh đậm','#1B3A5C','NV-QD-001-30-DBL',20,0),(9,'31','Xanh đậm','#1B3A5C','NV-QD-001-31-DBL',20,0),(9,'32','Xanh đậm','#1B3A5C','NV-QD-001-32-DBL',20,0),(9,'34','Xanh đậm','#1B3A5C','NV-QD-001-34-DBL',10,0),
(10,'M','Xám','#9A9A9A','NV-QD-002-M-GRY',25,0),(10,'L','Xám','#9A9A9A','NV-QD-002-L-GRY',25,0),(10,'XL','Xám','#9A9A9A','NV-QD-002-XL-GRY',15,0),
(10,'M','Đen','#111111','NV-QD-002-M-BLK',25,0),(10,'L','Đen','#111111','NV-QD-002-L-BLK',25,0),
(11,'S','Đen','#111111','NV-TT-001-S-BLK',20,0),(11,'M','Đen','#111111','NV-TT-001-M-BLK',25,0),(11,'L','Đen','#111111','NV-TT-001-L-BLK',25,0),
(11,'M','Xanh lá','#2E8B57','NV-TT-001-M-GRN',15,0),(11,'L','Xanh lá','#2E8B57','NV-TT-001-L-GRN',15,0),
(12,'FREE','Đen','#111111','NV-PK-001-FR-BLK',50,0),(12,'FREE','Be','#D9C9A8','NV-PK-001-FR-BEG',40,0);

INSERT INTO `product_images` (`product_id`,`image_path`,`sort_order`) VALUES
(1,'assets/images/products/ao-thun-basic.jpg',0),(1,'assets/images/products/ao-thun-basic-2.jpg',1),
(2,'assets/images/products/ao-thun-oversize.jpg',0),(2,'assets/images/products/ao-thun-oversize-2.jpg',1),
(3,'assets/images/products/ao-polo-classic.jpg',0),(3,'assets/images/products/ao-polo-classic-2.jpg',1),
(5,'assets/images/products/ao-so-mi-oxford.jpg',0),(5,'assets/images/products/ao-so-mi-oxford-2.jpg',1),
(9,'assets/images/products/quan-jeans-slim.jpg',0),(9,'assets/images/products/quan-jeans-slim-2.jpg',1);

INSERT INTO `banners` (`id`,`title`,`title_en`,`subtitle`,`subtitle_en`,`button_text`,`button_text_en`,`image_desktop`,`image_mobile`,`link_url`,`position`,`sort_order`,`status`) VALUES
(1,'Bộ sưu tập NOIRVA mới','New NOIRVA Collection','Phong cách tối giản, chất liệu thoải mái cho mỗi ngày.','Minimal style, comfortable fabrics for every day.','Mua ngay','Shop now','assets/images/banners/hero-1.jpg','assets/images/banners/hero-1-m.jpg','/products?sort=newest','hero',1,1),
(2,'Giảm đến 30% áo polo','Up to 30% off polos','Ưu đãi có thời hạn cho dòng polo cao cấp.','Limited-time deals on premium polos.','Xem ưu đãi','View deals','assets/images/banners/hero-2.jpg','assets/images/banners/hero-2-m.jpg','/products?category=ao-polo','hero',2,1),
(3,'Đồ thể thao năng động','Active sportswear','Co giãn 4 chiều, nhanh khô, sẵn sàng vận động.','4-way stretch, quick-dry, ready to move.','Khám phá','Explore','assets/images/banners/hero-3.jpg','assets/images/banners/hero-3-m.jpg','/products?category=do-the-thao','hero',3,1),
(4,'Miễn phí vận chuyển','Free shipping','Cho đơn hàng từ 499.000đ.','For orders from 499,000 VND.','Tìm hiểu','Learn more','assets/images/banners/promo-1.jpg',NULL,'/policies','promo',1,1),
(5,'Đổi trả trong 30 ngày','30-day returns','Yên tâm mua sắm cùng NOIRVA.','Shop with confidence at NOIRVA.','Chính sách','Policy','assets/images/banners/promo-2.jpg',NULL,'/policies','promo',2,1),
(6,'Áo thun mùa hè','Summer tees','Chọn ngay chiếc áo thun yêu thích.','Pick your favorite tee.','Xem ngay','See now','assets/images/banners/promo-3.jpg',NULL,'/products?category=ao-thun','promo',3,1),
(7,'Thành viên NOIRVA','NOIRVA Membership','Tích lũy chi tiêu để nhận ưu đãi Gold và Diamond.','Spend more to unlock Gold and Diamond perks.','Đăng ký','Sign up','assets/images/banners/middle-1.jpg','assets/images/banners/middle-1-m.jpg','/register','middle',1,1);

INSERT INTO `vouchers` (`code`,`description`,`discount_type`,`discount_value`,`minimum_order_amount`,`maximum_discount_amount`,`usage_limit`,`start_at`,`end_at`,`status`) VALUES
('WELCOME10','Giảm 10% cho khách mới, tối đa 50.000đ','percent',10,200000,50000,1000,'2026-01-01 00:00:00','2027-12-31 23:59:59',1),
('NOIRVA50K','Giảm 50.000đ cho đơn từ 500.000đ','fixed',50000,500000,NULL,500,'2026-01-01 00:00:00','2027-12-31 23:59:59',1),
('SUMMER20','Giảm 20% tối đa 100.000đ','percent',20,300000,100000,200,'2026-01-01 00:00:00','2027-06-30 23:59:59',1);

INSERT INTO `promotions` (`id`,`name`,`description`,`discount_percent`,`start_at`,`end_at`,`status`) VALUES
(1,'Săn sale áo polo','Giảm giá các mẫu áo polo trong tháng.',NULL,'2026-01-01 00:00:00','2027-12-31 23:59:59',1),
(2,'Chào hè cùng NOIRVA','Ưu đãi quần short và áo thun mùa hè.',NULL,'2026-01-01 00:00:00','2027-12-31 23:59:59',1);

INSERT INTO `promotion_products` (`promotion_id`,`product_id`) VALUES (1,3),(1,4),(2,1),(2,7),(2,8);

-- Don hang mau da giao cua khach@noirva.local (de test danh gia / thong ke)
INSERT INTO `orders` (`id`,`order_code`,`user_id`,`recipient_name`,`recipient_phone`,`recipient_email`,`shipping_address`,`subtotal`,`discount_amount`,`shipping_fee`,`total_amount`,`payment_method`,`payment_status`,`order_status`,`stock_deducted`,`note`) VALUES
(1,'NV20260101001',3,'Nguyen Van An','0912345678','khach@noirva.local','12 Pho Hue, Hai Ba Trung, Ha Noi',428000,0,30000,458000,'cod','paid','delivered',1,'Giao gio hanh chinh');

INSERT INTO `order_items` (`order_id`,`product_id`,`product_variant_id`,`product_name`,`sku`,`size`,`color`,`image`,`unit_price`,`quantity`,`subtotal`) VALUES
(1,1,2,'Áo thun NOIRVA Basic','NV-AT-001-M-BLK','M','Đen','assets/images/products/ao-thun-basic.jpg',149000,1,149000),
(1,3,16,'Áo polo NOIRVA Classic','NV-AP-001-M-NVY','M','Xanh navy','assets/images/products/ao-polo-classic.jpg',279000,1,279000);

INSERT INTO `payments` (`order_id`,`payment_method`,`amount`,`payment_status`) VALUES (1,'cod',458000,'paid');

INSERT INTO `order_status_history` (`order_id`,`old_status`,`new_status`,`changed_by`,`note`) VALUES
(1,NULL,'pending',3,'Khach dat hang'),
(1,'pending','confirmed',1,'Admin xac nhan'),
(1,'confirmed','shipping',1,'Ban giao van chuyen'),
(1,'shipping','delivered',1,'Giao thanh cong');

INSERT INTO `reviews` (`user_id`,`product_id`,`order_id`,`rating`,`comment`,`status`) VALUES
(3,1,1,5,'Áo mềm, mặc rất thoải mái, giao hàng nhanh.',1),
(3,3,1,4,'Form đẹp, chất vải ổn, màu navy sang.',1);

INSERT INTO `settings` (`setting_key`,`setting_value`) VALUES
('site_name','NOIRVA'),
('site_email','support@noirva.vn'),
('site_phone','1900 0000'),
('site_address','Đại học Văn Hiến, 613 Âu Cơ, Tân Phú, TP. Hồ Chí Minh'),
('map_query','Đại học Văn Hiến, 613 Âu Cơ, Tân Phú, Hồ Chí Minh'),
('free_shipping_threshold','499000'),
('default_shipping_fee','30000'),
('facebook_url','https://facebook.com/'),
('instagram_url','https://instagram.com/'),
('tiktok_url','https://tiktok.com/');

-- =====================================================================
-- HET FILE
-- =====================================================================
