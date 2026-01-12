-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 11 Kas 2024, 13:10:49
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `if0_36661912_newdev`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `project_link` text NOT NULL,
  `upload_date` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Tablo döküm verisi `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `title`, `description`, `version`, `features`, `file_path`, `created_at`, `updated_at`, `project_link`, `upload_date`) VALUES
(1, 1, 'Artado Search', 'Today, the internet is the greatest tool for communication and information exchange. Thanks to the internet, we can meet new people, learn new information, and share our experiences with others. The internet is one of the greatest inventions of the human race. It makes our lives easier. Unfortunately, not everyone can access this great invention freely. We want the internet to be accessible to everyone, without restrictions from any power or authority. Accessing the wealth of information on the internet is a fundamental right.\r\n\r\nWe advocate for an internet where everyone can engage in free information exchange, where personal data is respected, and where ideas can be expressed without censorship.', '1.0', 'Gizlilik , Mahremiyet', '../public/uploads/files/25be97f2744df301e8b359e1b055f07e.jpg', '2024-10-24 15:10:02', '2024-10-24 15:10:02', '', '2024-11-11 14:53:45'),
(2, 2, 'MerkÃ¼r Theme', 'MerkÃ¼r Theme\r\nhttps://github.com/KerimCan05/merkur-artadotheme/', '2.1', '', '../public/uploads/files/theme.css', '2024-10-25 20:12:05', '2024-10-25 20:12:05', '', '2024-11-11 14:53:45'),
(3, 4, 'DSAFGH', 'DSAFGHNMJ', '1', 'SAFDGNHJ', '../public/uploads/files/47920304.jpeg', '2024-10-25 20:48:18', '2024-10-25 20:48:18', '', '2024-11-11 14:53:45'),
(4, 1, 'Test ediyorum', 'sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh  v sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhhsgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh vsgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhhsgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh sgdhh', '1.0', 'proje , test', NULL, '2024-11-11 11:45:34', '2024-11-11 11:45:34', 'https://github.com/AkaneTan/Gramophone/releases/download/1.0.15/Gramophone-1.0.15-release.apk', '2024-11-11 14:53:45');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `project_images`
--

CREATE TABLE `project_images` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Tablo döküm verisi `project_images`
--

INSERT INTO `project_images` (`id`, `project_id`, `image_path`) VALUES
(1, 1, '../public/uploads/files/25be97f2744df301e8b359e1b055f07e.jpg'),
(2, 2, '../public/uploads/files/screenshot2-1.png'),
(3, 3, '../public/uploads/files/47920304.jpeg'),
(4, 1, '../public/uploads/files/Ekran görüntüsü 2024-10-27 151819.png');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `profile_photo`, `two_factor_enabled`, `two_factor_secret`, `role`) VALUES
(1, 'Sxinar', 'oshidev@proton.me', '$2y$10$W2zd0Gl8CYMEgjX3C/gn6uH9AHkNiDwKMgcP50TuVeJ5GdMIPEPoS', '../public/uploads/pp/25be97f2744df301e8b359e1b055f07e.jpg', 0, NULL, 'admin'),
(2, 'KerimCan05', 'kerim_can05@proton.me', '$2y$10$6XsiOaqOmnKD4CJYvj0W8OC0iJWUETfdpOmdcJWu74z0AN.J0dUSG', '../public/uploads/pp/6kT8tJqZ_400x400.jpg', 0, NULL, 'user'),
(3, 'JodieHolmes', 'ardaakkaya812@gmail.com', '$2y$10$tAVnt9Lm8kFsT2u2RyMXj.dSl.Nl5IQP8B89QLeBJg5aO6XzB4rIa', NULL, 0, NULL, 'user'),
(4, 'ardatdev', 'arda@artadosearch.com', '$2y$10$U0la30fh9N3ZVAxXfYyAjuisbEwMPen6N/vUMwEqWH8dI1faMluBi', NULL, 0, NULL, 'user'),
(5, 'admin', 'admin@admin.com', '$2y$10$W2zd0Gl8CYMEgjX3C/gn6uH9AHkNiDwKMgcP50TuVeJ5GdMIPEPoS', NULL, 0, NULL, 'user');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `project_images`
--
ALTER TABLE `project_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `project_images`
--
ALTER TABLE `project_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;


CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE projects ADD COLUMN category VARCHAR(255) NOT NULL;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;