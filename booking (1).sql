-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th5 01, 2026 lúc 03:20 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `booking`
--
CREATE DATABASE IF NOT EXISTS `booking` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `booking`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--
-- Tạo: Th4 29, 2026 lúc 02:44 AM
-- Cập nhật lần cuối: Th5 01, 2026 lúc 12:05 PM
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `time_end` time DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_name` varchar(100) DEFAULT NULL,
  `purpose` varchar(50) DEFAULT NULL,
  `students` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `building_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_room_date` (`room_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `bookings`:
--   `room_id`
--       `rooms` -> `id`
--

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `date`, `time_start`, `time_end`, `status`, `created_at`, `user_name`, `purpose`, `students`, `note`, `class`, `building_id`) VALUES
(27, 24, '2026-05-01', '11:11:00', '12:11:00', 'active', '2026-04-29 04:11:31', 'A', 'Học', 60, '', 'DC21V7N1', NULL),
(28, 25, '2026-05-01', '11:18:00', '12:18:00', 'active', '2026-04-29 04:19:07', 'A', 'Thi', 51, '', 'DC21V7N1', NULL),
(29, 26, '2026-05-10', '12:37:00', '13:36:00', 'active', '2026-04-29 04:36:47', 'B', 'Học', 20, '', 'DC21V7N4', NULL),
(30, 19, '2026-05-07', '05:37:00', '06:37:00', 'active', '2026-04-29 04:37:38', 'B', 'Học', 22, '', 'DC21V7N4', NULL),
(31, 24, '2026-05-03', '15:41:00', '17:41:00', 'active', '2026-04-29 04:41:55', 'A', 'Thi', 40, '', 'DC21V7N3', NULL),
(32, 27, '2026-05-02', '11:42:00', '12:43:00', 'active', '2026-04-29 04:43:19', 'A', 'Học', 25, '', 'DC21V7N2', NULL),
(33, 23, '2026-05-07', '13:46:00', '14:48:00', 'active', '2026-04-29 04:47:13', 'A', 'Học', 45, '', 'DC21V7N3', NULL),
(34, 23, '2026-05-10', '14:28:00', '15:28:00', 'active', '2026-04-30 07:28:43', 'B', 'Học', 20, '', 'DC21V7N4', NULL),
(35, 25, '2026-05-09', '16:36:00', '17:36:00', 'active', '2026-04-30 09:37:08', 'A', 'Họp', 24, '', 'DC21V7N1', NULL),
(36, 23, '2026-05-05', '16:37:00', '18:38:00', 'active', '2026-04-30 09:38:05', 'A', 'Học', 31, '', 'DC21V7N1', NULL),
(37, 24, '2026-04-30', '18:04:00', '20:04:00', 'active', '2026-04-30 12:04:29', 'B', 'Học', 45, '', 'DC21V7N4', NULL),
(38, 19, '2026-04-30', '19:15:00', '20:15:00', 'active', '2026-04-30 12:15:41', 'B', 'Học', 20, '', 'DC21V7N4', NULL),
(39, 27, '2026-04-30', '20:41:00', '23:41:00', 'active', '2026-04-30 13:41:39', 'B', 'Học', 30, '', 'DC21V7N4', NULL),
(40, 19, '2026-05-10', '21:03:00', '22:04:00', 'active', '2026-04-30 14:04:06', 'B', 'Học', 20, '', 'DC21V7N4', NULL),
(41, 25, '2026-04-30', '20:13:00', '23:13:00', 'active', '2026-04-30 14:14:10', 'A', 'Học', 45, '', 'DC21V7N3', NULL),
(42, 26, '2026-05-10', '21:14:00', '23:14:00', 'active', '2026-04-30 14:15:09', 'B', 'Thi', 24, '', 'DC21V7N4', NULL),
(43, 35, '2026-05-03', '18:49:00', '19:49:00', 'active', '2026-05-01 11:57:15', 'A', 'Thi', 30, '', 'DC21V7N1', NULL),
(44, 32, '2026-05-01', '19:05:00', '21:05:00', 'active', '2026-05-01 12:05:28', 'A', 'Thi', 34, '', 'DC21V7N1', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `buildings`
--
-- Tạo: Th4 29, 2026 lúc 02:44 AM
-- Cập nhật lần cuối: Th4 29, 2026 lúc 03:02 AM
--

DROP TABLE IF EXISTS `buildings`;
CREATE TABLE IF NOT EXISTS `buildings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `buildings`:
--

--
-- Đang đổ dữ liệu cho bảng `buildings`
--

INSERT INTO `buildings` (`id`, `name`) VALUES
(1, 'A1'),
(2, 'A2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms`
--
-- Tạo: Th4 29, 2026 lúc 02:44 AM
-- Cập nhật lần cuối: Th5 01, 2026 lúc 09:42 AM
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'available',
  `building_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `rooms`:
--

--
-- Đang đổ dữ liệu cho bảng `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `capacity`, `status`, `building_id`) VALUES
(19, '101', 30, 'available', 1),
(23, '101', 50, 'available', 2),
(24, '102', 60, 'available', 2),
(25, '102', 55, 'available', 1),
(26, '103', 25, 'available', 1),
(27, '103', 40, 'available', 2),
(28, '104', 50, 'available', 1),
(29, '105', 55, 'available', 1),
(31, '104', 35, 'available', 2),
(32, '105', 40, 'available', 2),
(33, '106', 65, 'available', 2),
(35, '106', 35, 'available', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `room_logs`
--
-- Tạo: Th4 29, 2026 lúc 03:26 AM
-- Cập nhật lần cuối: Th5 01, 2026 lúc 09:42 AM
--

DROP TABLE IF EXISTS `room_logs`;
CREATE TABLE IF NOT EXISTS `room_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(50) DEFAULT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `building_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `room_logs`:
--

--
-- Đang đổ dữ liệu cho bảng `room_logs`
--

INSERT INTO `room_logs` (`id`, `action`, `room_name`, `created_at`, `building_id`) VALUES
(1, 'Xóa', '107', '2026-04-16 02:26:17', NULL),
(2, 'Xóa', '105', '2026-04-16 02:35:28', NULL),
(3, 'Thêm', '105', '2026-04-16 02:35:38', NULL),
(4, 'Thêm', '', '2026-04-16 02:37:21', NULL),
(5, 'Xóa', '', '2026-04-16 02:37:24', NULL),
(6, 'Thêm', '106', '2026-04-16 02:39:37', NULL),
(7, 'Xóa', '104', '2026-04-16 02:39:53', NULL),
(8, 'Thêm', '104', '2026-04-16 02:43:44', NULL),
(9, 'Xóa', '106', '2026-04-16 02:43:49', NULL),
(10, 'Xóa', '105', '2026-04-16 02:43:54', NULL),
(11, 'Thêm', '105', '2026-04-16 03:50:28', NULL),
(12, 'Xóa', '105', '2026-04-16 03:50:41', NULL),
(13, 'Thêm', '105', '2026-04-16 05:40:58', NULL),
(14, 'Xóa', '105', '2026-04-16 05:41:07', NULL),
(15, 'Thêm', '105', '2026-04-28 01:25:27', NULL),
(16, 'Thêm', '101', '2026-04-29 02:55:37', NULL),
(17, 'Thêm', '101', '2026-04-29 02:55:43', NULL),
(18, 'Xóa', '101', '2026-04-29 02:56:02', NULL),
(19, 'Thêm', '106', '2026-04-29 02:56:35', NULL),
(20, 'Thêm', '107', '2026-04-29 02:57:10', NULL),
(21, 'Thêm', '101', '2026-04-29 03:02:09', NULL),
(22, 'Thêm', '102', '2026-04-29 03:02:35', NULL),
(23, 'Thêm', '102', '2026-04-29 03:21:32', NULL),
(24, 'Thêm', '103', '2026-04-29 03:47:42', 1),
(25, 'Xóa', '101', '2026-04-29 04:04:08', 0),
(26, 'Xóa', '102', '2026-04-29 04:04:12', 0),
(27, 'Xóa', '103', '2026-04-29 04:04:16', 0),
(28, 'Xóa', '104', '2026-04-29 04:04:19', 0),
(29, 'Xóa', '105', '2026-04-29 04:04:24', 0),
(30, 'Xóa', '106', '2026-04-29 04:04:28', 0),
(31, 'Xóa', '107', '2026-04-29 04:04:32', 0),
(32, 'Thêm', '103', '2026-04-29 04:05:04', 2),
(33, 'Thêm', '104', '2026-05-01 02:34:02', 1),
(34, 'Thêm', '105', '2026-05-01 02:34:18', 1),
(35, 'Thêm', '106', '2026-05-01 02:34:53', 1),
(36, 'Thêm', '104', '2026-05-01 02:43:25', 2),
(37, 'Thêm', '105', '2026-05-01 02:43:50', 2),
(38, 'Thêm', '106', '2026-05-01 09:33:34', 2),
(39, 'Xóa', '106', '2026-05-01 09:38:14', 1),
(40, 'Thêm', '106', '2026-05-01 09:38:58', 1),
(41, 'Xóa', '106', '2026-05-01 09:42:12', 1),
(42, 'Thêm', '106', '2026-05-01 09:42:36', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--
-- Tạo: Th4 29, 2026 lúc 05:07 AM
-- Cập nhật lần cuối: Th5 01, 2026 lúc 12:29 PM
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `users`:
--

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `avatar`, `class`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$JLObd.d2uLNBKQkCZzxCyOgl8rcKrtBLYCoVveLNRyKGMl/Goy3f.', 'admin', '2026-04-16 00:05:58', NULL, NULL),
(2, 'User', 'user@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', '2026-04-16 00:05:58', NULL, NULL),
(3, NULL, 'a@gmail.com', 'ca633b64b104b61cdc6100a2ea1f46e2', 'user', '2026-04-28 02:23:45', NULL, NULL),
(4, NULL, 'admin1@gmail.com', '202cb962ac59075b964b07152d234b70', 'admin', '2026-04-28 04:23:28', NULL, NULL),
(6, NULL, 'admin3@gmail.com', '202cb962ac59075b964b07152d234b70', 'admin', '2026-04-28 04:28:13', NULL, NULL),
(7, NULL, 'admin4@gmail.com', '202cb962ac59075b964b07152d234b70', 'admin', '2026-04-28 04:31:26', NULL, NULL),
(8, NULL, 'tamdc21v7n538@gmail.com', '3e41626bddc0d160cd3e836d93edb60a', 'user', '2026-04-28 06:51:09', NULL, NULL),
(10, NULL, 'u@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', '2026-04-28 09:10:06', NULL, NULL),
(11, NULL, 'u1@gmail.com', '$2y$10$7Zcm12CwU4ddR8DrZI/KIuWFwFoInEP//69BPfqOPwzbxC5f6c2.u', 'user', '2026-04-28 09:20:31', NULL, NULL),
(12, NULL, 'tamdc21v7n538@vlvh.ctu.edu.vn', '$2y$10$6h2ns5Wd0X9hHFNE1ofXVupH0kduibJ9nlFRF0/dPKSEvSuKaBUrC', 'admin', '2026-04-28 09:29:16', '69f49ab664e98.png', NULL),
(13, NULL, 'ab@gmail.com', '$2y$10$pOCKV7m5VxvPD5t4vW05EO3HeTN4J8saoBmBiuvnsN979btpESJ2a', 'user', '2026-04-29 04:52:08', '69f18fbff2c98.png', NULL),
(15, NULL, 'aa@gmail.com', '$2y$10$MxaH6iw8i6lpWc8EPWZTUexDZNudhSJ3SAI23bmT/1VdbK4BWnIXi', 'user', '2026-05-01 05:37:40', NULL, 'DC21V7N1'),
(16, NULL, 'abcd@gmail', '$2y$10$yP5oGPOieuUqZ9JsJe2SUesu3/fkAe2CQcgHI40bYaF98m3r8HNGm', 'user', '2026-05-01 05:47:05', NULL, 'DC21V7N1');

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
