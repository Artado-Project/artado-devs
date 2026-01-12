-- visit_logs tablosunu oluştur
CREATE TABLE IF NOT EXISTS `visit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `visitor_ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_page_type` (`page_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NOT: Eğer announcements tablosunda user_id sütunu YOKSA aşağıdaki satırları çalıştırın:
-- ALTER TABLE `announcements` ADD COLUMN `user_id` int(11) DEFAULT NULL AFTER `id`;
-- ALTER TABLE `announcements` ADD KEY `idx_user_id` (`user_id`);
-- ALTER TABLE `announcements` ADD KEY `idx_created_at` (`created_at`);
