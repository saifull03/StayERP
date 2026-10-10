-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2026 at 03:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stayinghotel_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(100) DEFAULT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `organization_id`, `branch_id`, `user_id`, `action`, `target_type`, `target_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 1, 1, 'branch.created', 'App\\Models\\Branch', 1, NULL, '{\"name\":\"Dhaka Premier Branch\",\"code\":\"DHK\"}', '127.0.0.1', 'StayingHotel-Core/1.0', '2026-10-05 05:12:33'),
(2, 1, 1, 4, 'payment.collected', 'App\\Models\\Payment', 1, NULL, '{\"amount\":10000,\"method\":\"cash\"}', '127.0.0.1', 'StayingHotel-Core/1.0', '2026-10-10 01:12:33'),
(3, 1, 2, 1, 'user.login', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', '', '2026-10-10 05:26:13'),
(4, 1, 1, 2, 'user.login', 'App\\Models\\User', 2, NULL, NULL, '127.0.0.1', '', '2026-10-10 05:26:35'),
(5, 1, 1, 3, 'user.login', 'App\\Models\\User', 3, NULL, NULL, '127.0.0.1', '', '2026-10-10 05:26:37'),
(6, 1, 1, 4, 'user.login', 'App\\Models\\User', 4, NULL, NULL, '127.0.0.1', '', '2026-10-10 05:26:38'),
(7, 1, 1, 5, 'user.login', 'App\\Models\\User', 5, NULL, NULL, '127.0.0.1', '', '2026-10-10 05:26:40'),
(8, 1, 1, 6, 'user.login', 'App\\Models\\User', 6, NULL, NULL, '127.0.0.1', '', '2026-10-10 05:26:42'),
(9, 1, 1, 7, 'user.login', 'App\\Models\\User', 7, NULL, NULL, '127.0.0.1', '', '2026-10-10 05:26:43'),
(10, 1, 1, 8, 'user.login', 'App\\Models\\User', 8, NULL, NULL, '127.0.0.1', '', '2026-10-10 05:26:45'),
(11, 1, 2, 1, 'user.login', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 05:28:02'),
(12, 1, 2, 1, 'user.logout', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 05:30:17'),
(13, 1, 1, 5, 'user.login', 'App\\Models\\User', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 05:30:24'),
(14, 1, 1, 5, 'reservation.created', 'App\\Models\\Reservation', 4, NULL, '{\"reservation_number\":\"RES-20261010-01-AD0D\",\"room_number\":\"201\",\"guest\":\"Saiful Islam\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 05:33:09'),
(15, 1, 1, 5, 'user.logout', 'App\\Models\\User', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 05:57:24'),
(16, 1, 1, 8, 'user.login', 'App\\Models\\User', 8, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 05:57:32'),
(17, 1, 1, 8, 'user.logout', 'App\\Models\\User', 8, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 05:59:02'),
(18, 1, 2, 1, 'user.login', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 05:59:22'),
(19, 1, 2, 1, 'user.login', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', '', '2026-10-10 06:07:35'),
(20, 1, 2, 1, 'user.login', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', '', '2026-10-10 06:07:51'),
(21, 1, 2, 1, 'expense.created', 'App\\Models\\Expense', 2, NULL, '{\"expense_number\":\"EXP-20261010-02-424D\",\"amount\":\"980.00\",\"title\":\"Snacks\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:16:35'),
(22, 1, 2, 1, 'expense.approved', 'App\\Models\\Expense', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:16:47'),
(23, 1, 2, 1, 'user.logout', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:20:25'),
(24, 1, 1, 2, 'user.login', 'App\\Models\\User', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:20:47'),
(25, 1, 1, 2, 'user.logout', 'App\\Models\\User', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:21:04'),
(26, 1, 1, 3, 'user.login', 'App\\Models\\User', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:21:10'),
(27, 1, 1, 3, 'user.logout', 'App\\Models\\User', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:22:05'),
(28, 1, 1, 7, 'user.login', 'App\\Models\\User', 7, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:22:10'),
(29, 1, 1, 7, 'maintenance.status_updated', 'App\\Models\\MaintenanceRequest', 1, NULL, '{\"status\":\"resolved\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:22:40'),
(30, 1, 1, 7, 'maintenance.created', 'App\\Models\\MaintenanceRequest', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:27:01'),
(31, 1, 1, 7, 'maintenance.status_updated', 'App\\Models\\MaintenanceRequest', 2, NULL, '{\"status\":\"resolved\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:27:05'),
(32, 1, 1, 7, 'user.logout', 'App\\Models\\User', 7, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:29:43'),
(33, 1, 1, 2, 'user.login', 'App\\Models\\User', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:29:48'),
(34, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 12, '{\"status\":\"maintenance\"}', '{\"status\":\"available\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:29:58'),
(35, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 12, '{\"status\":\"available\"}', '{\"status\":\"available\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:30:17'),
(36, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 12, '{\"status\":\"available\"}', '{\"status\":\"out_of_order\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:30:23'),
(37, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 12, '{\"status\":\"out_of_order\"}', '{\"status\":\"out_of_order\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:30:23'),
(38, 1, 2, 1, 'user.login', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:33:25'),
(39, 1, 2, 1, 'user.logout', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:33:55'),
(40, 1, 1, 7, 'user.login', 'App\\Models\\User', 7, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:34:02'),
(41, 1, 1, 7, 'maintenance.created', 'App\\Models\\MaintenanceRequest', 3, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:34:57'),
(42, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 12, '{\"status\":\"maintenance\"}', '{\"status\":\"available\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:36:30'),
(43, 1, 1, 7, 'maintenance.status_updated', 'App\\Models\\MaintenanceRequest', 3, NULL, '{\"status\":\"resolved\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:36:41'),
(44, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 11, '{\"status\":\"available\"}', '{\"status\":\"maintenance\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:37:08'),
(45, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 11, '{\"status\":\"maintenance\"}', '{\"status\":\"available\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:37:54'),
(46, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 2, '{\"status\":\"cleaning\"}', '{\"status\":\"available\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:39:07'),
(47, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 11, '{\"status\":\"available\"}', '{\"status\":\"cleaning\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:39:13'),
(48, 1, 1, 2, 'user.logout', 'App\\Models\\User', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:39:34'),
(49, 1, 1, 5, 'user.login', 'App\\Models\\User', 5, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:39:40'),
(50, 1, 1, 5, 'reservation.created', 'App\\Models\\Reservation', 5, NULL, '{\"reservation_number\":\"RES-20261010-01-0C20\",\"room_number\":\"203\",\"guest\":\"Saiful Islam\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:42:54'),
(51, 1, 1, 5, 'reservation.created', 'App\\Models\\Reservation', 6, NULL, '{\"reservation_number\":\"RES-20261010-01-E21B\",\"room_number\":\"201\",\"guest\":\"Dr. Shahed Chowdhury\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:46:44'),
(52, 1, 1, 5, 'reservation.checkin', 'App\\Models\\Reservation', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:47:18'),
(53, 1, 1, 5, 'reservation.checkout', 'App\\Models\\Reservation', 6, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 06:47:21'),
(54, 1, 2, 1, 'user.login', 'App\\Models\\User', 1, NULL, NULL, '127.0.0.1', '', '2026-10-10 06:55:01'),
(55, 1, 1, 5, 'guest.registered', 'App\\Models\\Guest', 7, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:02:18'),
(56, 1, 1, 5, 'reservation.created', 'App\\Models\\Reservation', 7, NULL, '{\"reservation_number\":\"RES-20261010-01-8286\",\"room_number\":\"301\",\"guest\":\"Siam Islam\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:03:28'),
(57, 1, 1, 5, 'folio.item_added', 'App\\Models\\Reservation', 7, NULL, '{\"description\":\"7000\",\"amount\":7000}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:05:55'),
(58, 1, 1, 5, 'payment.recorded', 'App\\Models\\Payment', 6, NULL, '{\"payment_number\":\"PAY-20261010-01-D7B5\",\"amount\":\"16000.00\",\"method\":\"card\",\"reservation\":\"RES-20261010-01-8286\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:06:23'),
(59, 1, 1, 7, 'user.logout', 'App\\Models\\User', 7, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:12:30'),
(60, 1, 1, 8, 'user.login', 'App\\Models\\User', 8, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:12:37'),
(61, 1, 1, 5, 'reservation.checkin', 'App\\Models\\Reservation', 7, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:18:06'),
(62, 1, 1, 5, 'reservation.checkout', 'App\\Models\\Reservation', 7, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:18:21'),
(63, 1, 1, 8, 'user.logout', 'App\\Models\\User', 8, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:20:02'),
(64, 1, 1, 2, 'user.login', 'App\\Models\\User', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:20:07'),
(65, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 11, '{\"status\":\"cleaning\"}', '{\"status\":\"available\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:20:33'),
(66, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 5, '{\"status\":\"cleaning\"}', '{\"status\":\"available\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:20:39'),
(67, 1, 1, 2, 'room.status_updated', 'App\\Models\\Room', 9, '{\"status\":\"cleaning\"}', '{\"status\":\"available\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:20:44'),
(68, 1, 1, 5, 'reservation.created', 'App\\Models\\Reservation', 8, NULL, '{\"reservation_number\":\"RES-20261010-01-89BB\",\"room_number\":\"302\",\"guest\":\"David Reynolds\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:36:59'),
(69, 1, 1, 5, 'reservation.checkin', 'App\\Models\\Reservation', 8, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:37:11'),
(70, 1, 1, 5, 'reservation.checkout', 'App\\Models\\Reservation', 8, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:37:14'),
(71, 1, 1, 5, 'reservation.checkin', 'App\\Models\\Reservation', 2, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:37:31'),
(72, 1, 1, 5, 'reservation.checkin', 'App\\Models\\Reservation', 4, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-10 07:37:34');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'Asia/Dhaka',
  `currency` varchar(10) NOT NULL DEFAULT 'BDT',
  `tax_rate_percent` decimal(5,2) NOT NULL DEFAULT 15.00,
  `checkin_time` time NOT NULL DEFAULT '14:00:00',
  `checkout_time` time NOT NULL DEFAULT '12:00:00',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `organization_id`, `name`, `code`, `phone`, `email`, `address`, `city`, `state`, `postal_code`, `timezone`, `currency`, `tax_rate_percent`, `checkin_time`, `checkout_time`, `is_active`, `settings`, `created_at`, `updated_at`) VALUES
(1, 1, 'Dhaka Premier Branch', 'DHK', '+880 1711 111111', 'dhaka@stayinghotel.com', 'Plot 12, Road 45, Gulshan-2', 'Dhaka', 'Dhaka', '1212', 'Asia/Dhaka', 'BDT', 15.00, '14:00:00', '12:00:00', 1, NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(2, 1, 'Chattogram Portside Branch', 'CTG', '+880 1711 222222', 'chattogram@stayinghotel.com', '88 GEC Circle, Agrabad Commercial Area', 'Chattogram', 'Chattogram', '4000', 'Asia/Dhaka', 'BDT', 15.00, '14:00:00', '12:00:00', 1, NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(3, 1, 'Cox\'s Bazar Beach Resort', 'CXB', '+880 1711 333333', 'coxsbazar@stayinghotel.com', 'Kolatoli Marine Drive Beach Front', 'Cox\'s Bazar', 'Chattogram', '4700', 'Asia/Dhaka', 'BDT', 15.00, '14:00:00', '12:00:00', 1, NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(4, 1, 'Sylhet Tea Valley Branch', 'SYL', '+880 1711 444444', 'sylhet@stayinghotel.com', 'Airport Road, Dargah Gate', 'Sylhet', 'Sylhet', '3100', 'Asia/Dhaka', 'BDT', 15.00, '14:00:00', '12:00:00', 1, NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cashier_shifts`
--

CREATE TABLE `cashier_shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `shift_number` varchar(50) NOT NULL,
  `opening_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `closing_cash_counted` decimal(12,2) DEFAULT NULL,
  `expected_closing_cash` decimal(12,2) DEFAULT NULL,
  `cash_discrepancy` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discrepancy_reason` text DEFAULT NULL,
  `status` enum('open','closed','reviewed') NOT NULL DEFAULT 'open',
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cashier_shifts`
--

INSERT INTO `cashier_shifts` (`id`, `branch_id`, `user_id`, `shift_number`, `opening_cash`, `closing_cash_counted`, `expected_closing_cash`, `cash_discrepancy`, `discrepancy_reason`, `status`, `opened_at`, `closed_at`, `reviewed_by_user_id`, `reviewed_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'SHF-20261010-01-F375', 15000.00, NULL, NULL, 0.00, NULL, 'open', '2026-10-10 00:12:33', NULL, NULL, NULL, 'Morning front desk cashier shift opened with standard cash float.', '2026-10-10 05:12:33', '2026-10-10 05:12:33');

-- --------------------------------------------------------

--
-- Table structure for table `cashier_transactions`
--

CREATE TABLE `cashier_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cashier_shift_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_type` enum('payment_received','refund_paid','cash_in','cash_out','expense_paid') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cashier_transactions`
--

INSERT INTO `cashier_transactions` (`id`, `cashier_shift_id`, `branch_id`, `transaction_type`, `amount`, `payment_id`, `reference_number`, `notes`, `created_at`) VALUES
(1, 1, 1, 'payment_received', 10000.00, 1, 'PAY-20261010-01-9DE7', 'Advance cash payment for Res RES-20261010-01-BEB4', '2026-10-10 11:12:33');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `expense_category_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cashier_shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expense_number` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','card','mfs') NOT NULL DEFAULT 'cash',
  `expense_date` date NOT NULL,
  `status` enum('pending_approval','approved','rejected','paid') NOT NULL DEFAULT 'pending_approval',
  `requested_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `approved_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `branch_id`, `expense_category_id`, `vendor_id`, `cashier_shift_id`, `expense_number`, `title`, `description`, `amount`, `payment_method`, `expense_date`, `status`, `requested_by_user_id`, `approved_by_user_id`, `receipt_path`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, 'EXP-20261010-01-E1EC', 'Bulk Premium Bath Towels & Toiletries Restock', 'Delivery of 50 luxury bath towels and 200 hygiene amenity sets.', 34500.00, 'bank_transfer', '2026-10-08', 'approved', 6, 3, NULL, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(2, 2, 7, 1, NULL, 'EXP-20261010-02-424D', 'Snacks', NULL, 980.00, 'cash', '2026-10-30', 'approved', 1, 1, NULL, '2026-10-10 06:16:35', '2026-10-10 06:16:47');

-- --------------------------------------------------------

--
-- Table structure for table `expense_approvals`
--

CREATE TABLE `expense_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_id` bigint(20) UNSIGNED NOT NULL,
  `approver_user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('approved','rejected') NOT NULL,
  `comments` text DEFAULT NULL,
  `approved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_approvals`
--

INSERT INTO `expense_approvals` (`id`, `expense_id`, `approver_user_id`, `status`, `comments`, `approved_at`) VALUES
(1, 2, 1, 'approved', NULL, '2026-10-10 06:16:47');

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `branch_id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Housekeeping Supplies', 'EXP-HK', 'Detergents, linens, bath amenities', 1, '2026-10-10 05:12:33', '2026-10-10 06:02:58'),
(2, NULL, 'Utilities & Power', 'EXP-UTIL', 'DESCO electricity, gas, water supply', 1, '2026-10-10 05:12:33', '2026-10-10 06:02:58'),
(3, NULL, 'Maintenance & Repairs', 'EXP-MAINT', 'HVAC, plumbing, electrical spares', 1, '2026-10-10 05:12:33', '2026-10-10 06:02:58'),
(4, NULL, 'Food & Beverage Provisions', 'EXP-FB', 'Restaurant groceries, beverage stock', 1, '2026-10-10 05:12:33', '2026-10-10 06:02:58'),
(5, NULL, 'Stationery & Front Desk', 'EXP-STAT', 'Keycards, folios, office stationery', 1, '2026-10-10 05:12:33', '2026-10-10 06:02:58'),
(6, NULL, 'Laundry & Linen Services', 'EXP-LAUNDRY', 'External laundry and dry cleaning', 1, '2026-10-10 06:07:13', '2026-10-10 06:07:13'),
(7, NULL, 'Internet & IT Systems', 'EXP-IT', 'Broadband, software subscriptions, POS hardware', 1, '2026-10-10 06:07:13', '2026-10-10 06:07:13'),
(8, NULL, 'Staff Welfare & Meals', 'EXP-STAFF', 'Duty meals and staff refreshments', 1, '2026-10-10 06:07:13', '2026-10-10 06:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `id_type` enum('nid','passport','driving_license','other') NOT NULL DEFAULT 'nid',
  `id_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'Bangladesh',
  `vip_status` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`id`, `organization_id`, `first_name`, `last_name`, `email`, `phone`, `id_type`, `id_number`, `address`, `city`, `country`, `vip_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mahmudul', 'Hasan', 'mahmud.hasan@example.com', '+880 1812 345678', 'nid', '1990269250001', 'House 45, Road 7, Dhanmondi', 'Dhaka', 'Bangladesh', 1, 'Frequent business traveler, prefers upper floor quiet rooms.', '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(2, 1, 'Ayesha', 'Sultana', 'ayesha.sultana@example.com', '+880 1913 456789', 'passport', 'A08976542', 'Nasirabad Housing Society', 'Chattogram', 'Bangladesh', 0, 'Traveling with family.', '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(3, 1, 'David', 'Reynolds', 'david.reynolds@intlcorp.com', '+880 1614 567890', 'passport', 'USA99887711', 'Foreign Delegate Mission', 'Dhaka', 'Bangladesh', 1, 'Corporate account billing.', '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(4, 1, 'Dr. Shahed', 'Chowdhury', 'shahed.chowdhury@medicare.bd', '+880 1715 678901', 'nid', '1982269250099', 'Shahjalal Upashohor', 'Sylhet', 'Bangladesh', 0, 'Attending national medical convention.', '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(5, 1, 'Saiful', 'Islam', 'mdsaifulislam22203@gmail.com', '01669403900', 'nid', NULL, NULL, NULL, 'Bangladesh', 0, NULL, '2026-10-10 05:33:09', '2026-10-10 05:33:09'),
(6, 1, 'Saiful', 'Islam', 'mdsaifulislam22203@gmail.com', '01669473900', 'nid', '321323423423', NULL, NULL, 'Bangladesh', 0, NULL, '2026-10-10 06:42:54', '2026-10-10 06:42:54'),
(7, 1, 'Siam', 'Islam', 'siam@gmail.com', '01993847544', 'nid', '8839483922', 'Gazipur', NULL, 'Bangladesh', 1, NULL, '2026-10-10 07:02:18', '2026-10-10 07:02:18');

-- --------------------------------------------------------

--
-- Table structure for table `guest_folio_items`
--

CREATE TABLE `guest_folio_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `item_type` enum('room_charge','service','restaurant','laundry','minibar','damage','discount','tax','other') NOT NULL DEFAULT 'service',
  `description` varchar(255) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `added_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guest_folio_items`
--

INSERT INTO `guest_folio_items` (`id`, `reservation_id`, `branch_id`, `item_type`, `description`, `quantity`, `unit_price`, `total_amount`, `added_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'room_charge', 'Room 101 (3 nights @ BDT 4500.00)', 1, 13500.00, 13500.00, 5, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(2, 1, 1, 'restaurant', 'A la carte Dinner Room Service', 1, 1850.00, 1850.00, 5, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(3, 4, 1, 'room_charge', 'Room 201 (1 nights @ BDT 4500)', 1, 4500.00, 4500.00, 5, '2026-10-10 05:33:09', '2026-10-10 05:33:09'),
(4, 5, 1, 'room_charge', 'Room 203 (1 nights @ BDT 12000)', 1, 12000.00, 12000.00, 5, '2026-10-10 06:42:54', '2026-10-10 06:42:54'),
(5, 6, 1, 'room_charge', 'Room 201 (1 nights @ BDT 4500)', 1, 4500.00, 4500.00, 5, '2026-10-10 06:46:44', '2026-10-10 06:46:44'),
(6, 7, 1, 'room_charge', 'Room 301 (1 nights @ BDT 4500)', 1, 4500.00, 4500.00, 5, '2026-10-10 07:03:28', '2026-10-10 07:03:28'),
(7, 7, 1, 'damage', '7000', 1, 7000.00, 7000.00, 5, '2026-10-10 07:05:55', '2026-10-10 07:05:55'),
(8, 8, 1, 'room_charge', 'Room 302 (1 nights @ BDT 7500)', 1, 7500.00, 7500.00, 5, '2026-10-10 07:36:59', '2026-10-10 07:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_settings`
--

CREATE TABLE `hotel_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `housekeeping_tasks`
--

CREATE TABLE `housekeeping_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('dirty','cleaning','awaiting_inspection','clean') NOT NULL DEFAULT 'dirty',
  `cleaning_type` enum('stayover','checkout','deep_clean','touchup') NOT NULL DEFAULT 'checkout',
  `scheduled_for` date DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `inspected_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `inspected_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `housekeeping_tasks`
--

INSERT INTO `housekeeping_tasks` (`id`, `branch_id`, `room_id`, `assigned_to_user_id`, `priority`, `status`, `cleaning_type`, `scheduled_for`, `started_at`, `completed_at`, `inspected_by_user_id`, `inspected_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 6, 'high', 'cleaning', 'checkout', '2026-10-10', '2026-10-10 04:27:33', NULL, NULL, NULL, 'Deep sanitize and replenish VIP minibar provisions.', '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(2, 1, 5, NULL, 'high', 'dirty', 'checkout', '2026-10-10', NULL, NULL, NULL, NULL, 'Automatic departure turnover task for Room 201 (Guest: Dr. Shahed Chowdhury).', '2026-10-10 06:47:21', '2026-10-10 06:47:21'),
(3, 1, 9, NULL, 'high', 'dirty', 'checkout', '2026-10-10', NULL, NULL, NULL, NULL, 'Automatic departure turnover task for Room 301 (Guest: Siam Islam).', '2026-10-10 07:18:21', '2026-10-10 07:18:21'),
(4, 1, 10, NULL, 'high', 'dirty', 'checkout', '2026-10-10', NULL, NULL, NULL, NULL, 'Automatic departure turnover task for Room 302 (Guest: David Reynolds).', '2026-10-10 07:37:14', '2026-10-10 07:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance_due` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','unpaid','partially_paid','paid','void') NOT NULL DEFAULT 'unpaid',
  `issued_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `branch_id`, `reservation_id`, `invoice_number`, `subtotal`, `tax_amount`, `discount_amount`, `grand_total`, `paid_amount`, `balance_due`, `status`, `issued_date`, `due_date`, `notes`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'INV-20261010-01-6521', 15350.00, 2302.50, 500.00, 17152.50, 10000.00, 7152.50, 'partially_paid', '2026-10-09', '2026-10-12', 'Deposit collected upon check-in.', 5, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(2, 3, 3, 'INV-20261010-03-C2ED', 9000.00, 1350.00, 0.00, 10350.00, 10350.00, 0.00, 'paid', '2026-10-09', '2026-10-09', 'Settled via credit card upon departure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(3, 1, 4, 'INV-20261010-01-D6D5', 4500.00, 675.00, 0.00, 5175.00, 3000.00, 2175.00, 'partially_paid', '2026-10-10', '2026-10-11', 'Reservation invoice generated automatically.', 5, '2026-10-10 05:33:09', '2026-10-10 05:33:09'),
(4, 1, 5, 'INV-20261010-01-BAA0', 12000.00, 1800.00, 0.00, 13800.00, 1000.00, 12800.00, 'partially_paid', '2026-10-10', '2026-10-14', 'Reservation invoice generated automatically.', 5, '2026-10-10 06:42:54', '2026-10-10 06:42:54'),
(5, 1, 6, 'INV-20261010-01-5291', 4500.00, 675.00, 0.00, 5175.00, 800.00, 4375.00, 'partially_paid', '2026-10-10', '2026-10-14', 'Reservation invoice generated automatically.', 5, '2026-10-10 06:46:44', '2026-10-10 06:46:44'),
(6, 1, 7, 'INV-20261010-01-0BB9', 11500.00, 675.00, 0.00, 12175.00, 16000.00, 0.00, 'paid', '2026-10-10', '2026-10-12', 'Reservation invoice generated automatically.', 5, '2026-10-10 07:03:28', '2026-10-10 07:06:23'),
(7, 1, 8, 'INV-20261010-01-6105', 7500.00, 1125.00, 0.00, 8625.00, 0.00, 8625.00, 'unpaid', '2026-10-10', '2026-10-12', 'Reservation invoice generated automatically.', 5, '2026-10-10 07:36:59', '2026-10-10 07:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `folio_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `folio_item_id`, `description`, `quantity`, `unit_price`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 3, 3, 'Room 201 (1 nights @ BDT 4500)', 1, 4500.00, 4500.00, '2026-10-10 05:33:09', '2026-10-10 05:33:09'),
(2, 4, 4, 'Room 203 (1 nights @ BDT 12000)', 1, 12000.00, 12000.00, '2026-10-10 06:42:54', '2026-10-10 06:42:54'),
(3, 5, 5, 'Room 201 (1 nights @ BDT 4500)', 1, 4500.00, 4500.00, '2026-10-10 06:46:44', '2026-10-10 06:46:44'),
(4, 6, 6, 'Room 301 (1 nights @ BDT 4500)', 1, 4500.00, 4500.00, '2026-10-10 07:03:28', '2026-10-10 07:03:28'),
(5, 6, 7, '7000', 1, 7000.00, 7000.00, '2026-10-10 07:05:55', '2026-10-10 07:05:55'),
(6, 7, 8, 'Room 302 (1 nights @ BDT 7500)', 1, 7500.00, 7500.00, '2026-10-10 07:36:59', '2026-10-10 07:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reported_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('open','assigned','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `blocks_room` tinyint(1) NOT NULL DEFAULT 0,
  `estimated_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `actual_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_requests`
--

INSERT INTO `maintenance_requests` (`id`, `branch_id`, `room_id`, `reported_by_user_id`, `assigned_to_user_id`, `title`, `description`, `priority`, `status`, `blocks_room`, `estimated_cost`, `actual_cost`, `resolved_at`, `resolution_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 5, 7, 'Master Bathroom Water Pressure Fluctuation', 'Thermostatic shower valve requires recalibration before next check-in.', 'medium', 'resolved', 0, 1500.00, 0.00, '2026-10-10 06:22:40', NULL, '2026-10-10 05:12:33', '2026-10-10 06:22:40'),
(2, 1, 9, 7, 7, 'Floor Cracked', 'needed to be resolved under 4 hours', 'high', 'resolved', 1, 0.00, 0.00, '2026-10-10 06:27:05', NULL, '2026-10-10 06:27:01', '2026-10-10 06:27:05'),
(3, 1, 12, 7, 7, 'AC down', 'Solve this immediately', 'critical', 'resolved', 1, 16000.00, 0.00, '2026-10-10 06:36:41', NULL, '2026-10-10 06:34:57', '2026-10-10 06:36:41');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_01_000001_create_organizations_and_branches_table', 1),
(5, '2026_01_01_000002_create_roles_permissions_and_user_assignments_table', 1),
(6, '2026_01_01_000003_create_rooms_and_amenities_table', 1),
(7, '2026_01_01_000004_create_guests_and_reservations_table', 1),
(8, '2026_01_01_000005_create_billing_and_cashier_tables', 1),
(9, '2026_01_01_000006_create_expenses_and_vendors_table', 1),
(10, '2026_01_01_000007_create_operations_tasks_tables', 1),
(11, '2026_01_01_000008_create_audit_logs_and_notifications_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'info',
  `action_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `branch_id`, `title`, `message`, `type`, `action_url`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'VIP Arrival Scheduled Today', 'Guest David Reynolds is arriving today at Dhaka Premier Branch.', 'info', NULL, 1, '2026-10-10 06:36:07', '2026-10-10 05:12:33', '2026-10-10 06:36:07'),
(2, 3, 1, 'Cash Float Shift Opened', 'Cashier Mehedi Hasan initiated morning shift SHF-20261010-01-F375.', 'success', NULL, 1, '2026-10-10 01:12:33', '2026-10-10 05:12:33', '2026-10-10 05:12:33');

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'BDT',
  `timezone` varchar(50) NOT NULL DEFAULT 'Asia/Dhaka',
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `name`, `code`, `contact_email`, `contact_phone`, `currency`, `timezone`, `settings`, `created_at`, `updated_at`) VALUES
(1, 'StayingHotel Hospitality Group', 'STAY-HQ', 'corporate@stayinghotel.com', '+880 1711 000000', 'BDT', 'Asia/Dhaka', '{\"vat_reg_no\":\"BIN-9876543210\",\"website\":\"https:\\/\\/stayinghotel.com\"}', '2026-10-10 05:12:30', '2026-10-10 05:12:30');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cashier_shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_number` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('cash','card','bank_transfer','bkash','nagad','rocket','other') NOT NULL DEFAULT 'cash',
  `transaction_reference` varchar(255) DEFAULT NULL,
  `status` enum('completed','pending','refunded','reversed','failed') NOT NULL DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `received_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `branch_id`, `reservation_id`, `invoice_id`, `cashier_shift_id`, `payment_number`, `amount`, `payment_method`, `transaction_reference`, `status`, `notes`, `received_by_user_id`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 'PAY-20261010-01-9DE7', 10000.00, 'cash', 'CASH-DEP-01', 'completed', 'Check-in cash advance deposit', 4, '2026-10-10 01:12:33', '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(2, 3, 3, 2, NULL, 'PAY-20261010-03-B355', 10350.00, 'card', 'VISA-AUTH-991283', 'completed', 'Settled with City Bank Visa Card', 1, '2026-10-09 05:30:00', '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(3, 1, 4, 3, NULL, 'PAY-20261010-01-4805', 3000.00, 'cash', 'ADV-3C56', 'completed', 'Advance deposit collected at booking.', 5, '2026-10-10 05:33:09', '2026-10-10 05:33:09', '2026-10-10 05:33:09'),
(4, 1, 5, 4, NULL, 'PAY-20261010-01-7B9E', 1000.00, 'cash', 'ADV-1A3A', 'completed', 'Advance deposit collected at booking.', 5, '2026-10-10 06:42:54', '2026-10-10 06:42:54', '2026-10-10 06:42:54'),
(5, 1, 6, 5, NULL, 'PAY-20261010-01-1BA6', 800.00, 'cash', 'ADV-FED5', 'completed', 'Advance deposit collected at booking.', 5, '2026-10-10 06:46:44', '2026-10-10 06:46:44', '2026-10-10 06:46:44'),
(6, 1, 7, 6, NULL, 'PAY-20261010-01-D7B5', 16000.00, 'card', '7077099506', 'completed', NULL, 5, '2026-10-10 07:06:23', '2026-10-10 07:06:23', '2026-10-10 07:06:23');

-- --------------------------------------------------------

--
-- Table structure for table `payment_reversals`
--

CREATE TABLE `payment_reversals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `reason` text NOT NULL,
  `reversed_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `reversed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `module`, `description`, `created_at`, `updated_at`) VALUES
(1, 'View Reservations', 'reservations.view', 'reservations', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(2, 'Create Reservations', 'reservations.create', 'reservations', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(3, 'Update Reservations', 'reservations.update', 'reservations', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(4, 'Cancel Reservations', 'reservations.cancel', 'reservations', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(5, 'Check-in Guests', 'reservations.checkin', 'reservations', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(6, 'Check-out Guests', 'reservations.checkout', 'reservations', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(7, 'View Rooms & Availability', 'rooms.view', 'rooms', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(8, 'Manage Rooms & Types', 'rooms.manage', 'rooms', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(9, 'View Guests', 'guests.view', 'guests', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(10, 'Manage Guests', 'guests.manage', 'guests', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(11, 'View Payments & Invoices', 'payments.view', 'billing', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(12, 'Collect Payments', 'payments.collect', 'billing', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(13, 'Process Refunds', 'payments.refund', 'billing', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(14, 'Manage Cashier Shifts', 'shifts.manage', 'shifts', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(15, 'Reconcile Shifts', 'shifts.reconcile', 'shifts', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(16, 'View Financial Reports', 'finance.reports', 'finance', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(17, 'Create Expenses', 'expenses.create', 'finance', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(18, 'Approve Expenses', 'expenses.approve', 'finance', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(19, 'Manage Housekeeping', 'housekeeping.manage', 'operations', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(20, 'Manage Maintenance', 'maintenance.manage', 'operations', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(21, 'Manage Staff', 'staff.manage', 'administration', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(22, 'Manage Branches', 'branches.manage', 'administration', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(23, 'Export Reports', 'reports.export', 'reports', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(24, 'Manage System Settings', 'settings.manage', 'administration', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(25, 'View Audit Logs', 'audit_logs.view', 'administration', NULL, '2026-10-10 05:12:30', '2026-10-10 05:12:30');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `refund_number` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending_approval','approved','rejected','processed') NOT NULL DEFAULT 'pending_approval',
  `approved_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `processed_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `guest_id` bigint(20) UNSIGNED NOT NULL,
  `reservation_number` varchar(50) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled','no_show') NOT NULL DEFAULT 'pending',
  `source` enum('walk_in','direct','online','phone') NOT NULL DEFAULT 'walk_in',
  `adults` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `children` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `special_requests` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `checked_in_at` timestamp NULL DEFAULT NULL,
  `checked_out_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `branch_id`, `guest_id`, `reservation_number`, `check_in_date`, `check_out_date`, `status`, `source`, `adults`, `children`, `special_requests`, `cancellation_reason`, `checked_in_at`, `checked_out_at`, `cancelled_at`, `created_by_user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'RES-20261010-01-BEB4', '2026-10-09', '2026-10-12', 'checked_in', 'direct', 1, 0, 'Late check-out requested.', NULL, '2026-10-09 08:30:00', NULL, NULL, 5, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(2, 1, 3, 'RES-20261010-01-3E54', '2026-10-10', '2026-10-13', 'checked_in', 'online', 2, 0, 'Airport pickup confirmed for 3:00 PM.', NULL, '2026-10-10 07:37:31', NULL, NULL, 5, '2026-10-10 05:12:33', '2026-10-10 07:37:31'),
(3, 3, 2, 'RES-20261010-03-6925', '2026-10-07', '2026-10-09', 'checked_out', 'direct', 2, 1, NULL, NULL, '2026-10-07 08:00:00', '2026-10-09 05:45:00', NULL, 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(4, 1, 5, 'RES-20261010-01-AD0D', '2026-10-10', '2026-10-11', 'checked_in', 'walk_in', 1, 0, NULL, NULL, '2026-10-10 07:37:34', NULL, NULL, 5, '2026-10-10 05:33:09', '2026-10-10 07:37:34'),
(5, 1, 6, 'RES-20261010-01-0C20', '2026-10-13', '2026-10-14', 'confirmed', 'walk_in', 1, 0, '2313131', NULL, NULL, NULL, NULL, 5, '2026-10-10 06:42:54', '2026-10-10 06:42:54'),
(6, 1, 4, 'RES-20261010-01-E21B', '2026-10-13', '2026-10-14', 'checked_out', 'walk_in', 1, 0, '3232', NULL, '2026-10-10 06:47:18', '2026-10-10 06:47:21', NULL, 5, '2026-10-10 06:46:44', '2026-10-10 06:47:21'),
(7, 1, 7, 'RES-20261010-01-8286', '2026-10-11', '2026-10-12', 'checked_out', 'walk_in', 3, 0, NULL, NULL, '2026-10-10 07:18:06', '2026-10-10 07:18:21', NULL, 5, '2026-10-10 07:03:28', '2026-10-10 07:18:21'),
(8, 1, 3, 'RES-20261010-01-89BB', '2026-10-11', '2026-10-12', 'checked_out', 'direct', 3, 0, NULL, NULL, '2026-10-10 07:37:11', '2026-10-10 07:37:14', NULL, 5, '2026-10-10 07:36:59', '2026-10-10 07:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_rate_snapshots`
--

CREATE TABLE `reservation_rate_snapshots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `stay_date` date NOT NULL,
  `nightly_rate` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservation_rate_snapshots`
--

INSERT INTO `reservation_rate_snapshots` (`id`, `reservation_id`, `stay_date`, `nightly_rate`, `tax_amount`, `discount_amount`, `created_at`, `updated_at`) VALUES
(1, 4, '2026-10-10', 4500.00, 675.00, 0.00, '2026-10-10 05:33:09', '2026-10-10 05:33:09'),
(2, 5, '2026-10-13', 12000.00, 1800.00, 0.00, '2026-10-10 06:42:54', '2026-10-10 06:42:54'),
(3, 6, '2026-10-13', 4500.00, 675.00, 0.00, '2026-10-10 06:46:44', '2026-10-10 06:46:44'),
(4, 7, '2026-10-11', 4500.00, 675.00, 0.00, '2026-10-10 07:03:28', '2026-10-10 07:03:28'),
(5, 8, '2026-10-11', 7500.00, 1125.00, 0.00, '2026-10-10 07:36:59', '2026-10-10 07:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_rooms`
--

CREATE TABLE `reservation_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_type_id` bigint(20) UNSIGNED NOT NULL,
  `nightly_rate` decimal(12,2) NOT NULL DEFAULT 0.00,
  `nights_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservation_rooms`
--

INSERT INTO `reservation_rooms` (`id`, `reservation_id`, `room_id`, `room_type_id`, `nightly_rate`, `nights_count`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 4500.00, 3, 13500.00, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(2, 2, 3, 3, 12000.00, 3, 36000.00, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(3, 3, 25, 9, 4500.00, 2, 9000.00, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(4, 4, 5, 1, 4500.00, 1, 4500.00, '2026-10-10 05:33:09', '2026-10-10 05:33:09'),
(5, 5, 7, 3, 12000.00, 1, 12000.00, '2026-10-10 06:42:54', '2026-10-10 06:42:54'),
(6, 6, 5, 1, 4500.00, 1, 4500.00, '2026-10-10 06:46:44', '2026-10-10 06:46:44'),
(7, 7, 9, 1, 4500.00, 1, 4500.00, '2026-10-10 07:03:28', '2026-10-10 07:03:28'),
(8, 8, 10, 2, 7500.00, 1, 7500.00, '2026-10-10 07:36:59', '2026-10-10 07:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super-admin', 'Full unrestricted access across all organizations and branches', 1, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(2, 'Branch Manager', 'branch-manager', 'Full administrative control over authorized hotel branch operations', 1, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(3, 'Finance Manager', 'finance-manager', 'Financial operations, invoices, payment auditing, refunds, and expense management', 1, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(4, 'Cash Counter / Cashier', 'cashier', 'Counter shift management, receiving deposits and settlements, printing receipts', 1, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(5, 'Receptionist', 'receptionist', 'Front desk guest check-in/out, walk-in bookings, room status monitoring', 1, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(6, 'Housekeeping Staff', 'housekeeping', 'Room turnover pipeline, cleaning updates, maintenance issue reporting', 1, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(7, 'Maintenance Staff', 'maintenance', 'Handling repairs, room blocking, maintenance logs, and material tracking', 1, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(8, 'Inventory Manager', 'inventory-manager', 'Procurement, vendor coordination, and branch supplies', 1, '2026-10-10 05:12:30', '2026-10-10 05:12:30'),
(9, 'Auditor / Read-only Staff', 'auditor', 'Read-only access to audit logs, financial reports, and transaction histories', 1, '2026-10-10 05:12:30', '2026-10-10 05:12:30');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 23),
(2, 25),
(3, 1),
(3, 9),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(3, 15),
(3, 16),
(3, 17),
(3, 18),
(3, 23),
(3, 25),
(4, 1),
(4, 9),
(4, 11),
(4, 12),
(4, 14),
(5, 1),
(5, 2),
(5, 3),
(5, 4),
(5, 5),
(5, 6),
(5, 7),
(5, 9),
(5, 10),
(5, 11),
(5, 12),
(6, 7),
(6, 19),
(6, 20),
(7, 7),
(7, 20),
(8, 16),
(8, 17),
(9, 1),
(9, 7),
(9, 9),
(9, 11),
(9, 16),
(9, 23),
(9, 25);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `room_type_id` bigint(20) UNSIGNED NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `floor` varchar(20) NOT NULL DEFAULT '1',
  `operational_status` enum('available','cleaning','maintenance','out_of_order') NOT NULL DEFAULT 'available',
  `maintenance_reason` varchar(255) DEFAULT NULL,
  `operational_notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `branch_id`, `room_type_id`, `room_number`, `floor`, `operational_status`, `maintenance_reason`, `operational_notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '101', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(2, 1, 2, '102', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 06:39:07'),
(3, 1, 3, '103', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(4, 1, 4, '104', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(5, 1, 1, '201', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 07:20:39'),
(6, 1, 2, '202', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(7, 1, 3, '203', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(8, 1, 4, '204', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(9, 1, 1, '301', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 07:20:44'),
(10, 1, 2, '302', '3', 'cleaning', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 07:37:14'),
(11, 1, 3, '303', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 07:20:33'),
(12, 1, 4, '304', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 06:36:41'),
(13, 2, 5, '101', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(14, 2, 6, '102', '1', 'cleaning', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(15, 2, 7, '103', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(16, 2, 8, '104', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(17, 2, 5, '201', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(18, 2, 6, '202', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(19, 2, 7, '203', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(20, 2, 8, '204', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(21, 2, 5, '301', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(22, 2, 6, '302', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(23, 2, 7, '303', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(24, 2, 8, '304', '3', 'maintenance', 'Annual AC & balcony glass refurbishment', 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(25, 3, 9, '101', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(26, 3, 10, '102', '1', 'cleaning', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(27, 3, 11, '103', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(28, 3, 12, '104', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(29, 3, 9, '201', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(30, 3, 10, '202', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(31, 3, 11, '203', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(32, 3, 12, '204', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(33, 3, 9, '301', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(34, 3, 10, '302', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(35, 3, 11, '303', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(36, 3, 12, '304', '3', 'maintenance', 'Annual AC & balcony glass refurbishment', 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(37, 4, 13, '101', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(38, 4, 14, '102', '1', 'cleaning', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(39, 4, 15, '103', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(40, 4, 16, '104', '1', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(41, 4, 13, '201', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(42, 4, 14, '202', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(43, 4, 15, '203', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(44, 4, 16, '204', '2', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(45, 4, 13, '301', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(46, 4, 14, '302', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(47, 4, 15, '303', '3', 'available', NULL, 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(48, 4, 16, '304', '3', 'maintenance', 'Annual AC & balcony glass refurbishment', 'Well-maintained corner suite with sunset exposure.', 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33');

-- --------------------------------------------------------

--
-- Table structure for table `room_amenities`
--

CREATE TABLE `room_amenities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_amenities`
--

INSERT INTO `room_amenities` (`id`, `name`, `code`, `icon`, `description`, `created_at`, `updated_at`) VALUES
(1, 'High-Speed Wi-Fi', 'wifi', 'wifi', NULL, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(2, 'King Size Bed', 'king-bed', 'bed', NULL, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(3, 'Climate Control AC', 'ac', 'wind', NULL, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(4, '4K Smart TV', 'smart-tv', 'tv', NULL, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(5, 'Mini Bar & Fridge', 'mini-bar', 'coffee', NULL, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(6, 'Sea / City View Balcony', 'balcony', 'sun', NULL, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(7, 'Private Jacuzzi Bath', 'jacuzzi', 'bath', NULL, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(8, 'Electronic Safe', 'safe', 'shield', NULL, '2026-10-10 05:12:32', '2026-10-10 05:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price_per_night` decimal(12,2) NOT NULL DEFAULT 0.00,
  `max_adults` int(10) UNSIGNED NOT NULL DEFAULT 2,
  `max_children` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `total_capacity` int(10) UNSIGNED NOT NULL DEFAULT 2,
  `size_sqft` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `branch_id`, `name`, `slug`, `description`, `base_price_per_night`, `max_adults`, `max_children`, `total_capacity`, `size_sqft`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Deluxe Single', 'deluxe-single', 'Luxurious accommodations with modern comforts at Dhaka Premier Branch.', 4500.00, 1, 0, 1, 280, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(2, 1, 'Executive Double', 'executive-double', 'Luxurious accommodations with modern comforts at Dhaka Premier Branch.', 7500.00, 2, 1, 3, 420, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(3, 1, 'Family Royal Suite', 'family-royal-suite', 'Luxurious accommodations with modern comforts at Dhaka Premier Branch.', 12000.00, 4, 2, 6, 650, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(4, 1, 'Presidential Ocean Suite', 'presidential-ocean-suite', 'Luxurious accommodations with modern comforts at Dhaka Premier Branch.', 22000.00, 2, 2, 4, 950, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(5, 2, 'Deluxe Single', 'deluxe-single', 'Luxurious accommodations with modern comforts at Chattogram Portside Branch.', 4500.00, 1, 0, 1, 280, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(6, 2, 'Executive Double', 'executive-double', 'Luxurious accommodations with modern comforts at Chattogram Portside Branch.', 7500.00, 2, 1, 3, 420, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(7, 2, 'Family Royal Suite', 'family-royal-suite', 'Luxurious accommodations with modern comforts at Chattogram Portside Branch.', 12000.00, 4, 2, 6, 650, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(8, 2, 'Presidential Ocean Suite', 'presidential-ocean-suite', 'Luxurious accommodations with modern comforts at Chattogram Portside Branch.', 22000.00, 2, 2, 4, 950, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(9, 3, 'Deluxe Single', 'deluxe-single', 'Luxurious accommodations with modern comforts at Cox\'s Bazar Beach Resort.', 4500.00, 1, 0, 1, 280, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(10, 3, 'Executive Double', 'executive-double', 'Luxurious accommodations with modern comforts at Cox\'s Bazar Beach Resort.', 7500.00, 2, 1, 3, 420, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(11, 3, 'Family Royal Suite', 'family-royal-suite', 'Luxurious accommodations with modern comforts at Cox\'s Bazar Beach Resort.', 12000.00, 4, 2, 6, 650, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(12, 3, 'Presidential Ocean Suite', 'presidential-ocean-suite', 'Luxurious accommodations with modern comforts at Cox\'s Bazar Beach Resort.', 22000.00, 2, 2, 4, 950, 1, '2026-10-10 05:12:32', '2026-10-10 05:12:32'),
(13, 4, 'Deluxe Single', 'deluxe-single', 'Luxurious accommodations with modern comforts at Sylhet Tea Valley Branch.', 4500.00, 1, 0, 1, 280, 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(14, 4, 'Executive Double', 'executive-double', 'Luxurious accommodations with modern comforts at Sylhet Tea Valley Branch.', 7500.00, 2, 1, 3, 420, 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(15, 4, 'Family Royal Suite', 'family-royal-suite', 'Luxurious accommodations with modern comforts at Sylhet Tea Valley Branch.', 12000.00, 4, 2, 6, 650, 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33'),
(16, 4, 'Presidential Ocean Suite', 'presidential-ocean-suite', 'Luxurious accommodations with modern comforts at Sylhet Tea Valley Branch.', 22000.00, 2, 2, 4, 950, 1, '2026-10-10 05:12:33', '2026-10-10 05:12:33');

-- --------------------------------------------------------

--
-- Table structure for table `room_type_amenities`
--

CREATE TABLE `room_type_amenities` (
  `room_type_id` bigint(20) UNSIGNED NOT NULL,
  `room_amenity_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_type_amenities`
--

INSERT INTO `room_type_amenities` (`room_type_id`, `room_amenity_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(4, 5),
(4, 6),
(5, 1),
(5, 2),
(5, 3),
(5, 4),
(5, 5),
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(8, 1),
(8, 2),
(8, 3),
(8, 4),
(8, 5),
(9, 1),
(9, 2),
(9, 3),
(9, 4),
(9, 5),
(9, 6),
(10, 1),
(10, 2),
(10, 3),
(10, 4),
(11, 1),
(11, 2),
(11, 3),
(11, 4),
(11, 5),
(11, 6),
(12, 1),
(12, 2),
(12, 3),
(12, 4),
(13, 1),
(13, 2),
(13, 3),
(13, 4),
(13, 5),
(14, 1),
(14, 2),
(14, 3),
(14, 4),
(14, 5),
(14, 6),
(15, 1),
(15, 2),
(15, 3),
(15, 4),
(15, 5),
(16, 1),
(16, 2),
(16, 3),
(16, 4),
(16, 5),
(16, 6),
(16, 7);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1UoVbk8Aamt8ba5OyavjYTpuGoTrTeDs6NWyCUSF', 1, '127.0.0.1', '', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiRVVzbDc0a1RwZ2x1Z0c0ODZCUUd3UDNubTF0VTYxOTVmR01zaWI3MCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4MC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6IjE4NTUyZjVhMGI1Y2Q3ZjQwYjBmYTQyNTQzMDlmYjNkYTNjMDgyZjE5N2YzOTUwMzNjMDU3N2M1ODhiMDRhNTEiO3M6MTY6ImFjdGl2ZV9icmFuY2hfaWQiO2k6Mjt9', 1791634087),
('AMb9jZZ7GLEymGf3knlWrvCCegVwsR2odismPIEL', 1, '127.0.0.1', '', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMFdPRDBwcXFUUm44bGkzOFdaajFqWWRrUWJ6aEZwSkIyT0JpODNBNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4MC9leHBlbnNlcyI7czo1OiJyb3V0ZSI7czoxNDoiZXhwZW5zZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiIxODU1MmY1YTBiNWNkN2Y0MGIwZmE0MjU0MzA5ZmIzZGEzYzA4MmYxOTdmMzk1MDMzYzA1NzdjNTg4YjA0YTUxIjtzOjE2OiJhY3RpdmVfYnJhbmNoX2lkIjtpOjI7fQ==', 1791634056),
('eRhnbcNR92XWCYRqLgdbxizVRlQcz5Bo7Stu8iJp', NULL, '127.0.0.1', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZ0h5Q1VnOTFjVFl5aUQ1RjVRRUtORW5wMWlKUXRlcmlFVFNyV2xMYSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MjoiaHR0cDovLzEyNy4wLjAuMTo4MDgwL2d1ZXN0cy9zZWFyY2g/cT0xODEyIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4MC9ndWVzdHMvc2VhcmNoP3E9MTgxMiI7czo1OiJyb3V0ZSI7czoxMzoiZ3Vlc3RzLnNlYXJjaCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1791636937),
('gidF9qnAFBLTVXHMXvoGfU8oJvANK3vmXnepnkpv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibDh6bHpBaEtnbnZVakJ3OWFuZnNUSjlVVk1GMXN5QjM0QUNzeWlmOSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMDoiaHR0cDovLzEyNy4wLjAuMTo4MDgwL2V4cGVuc2VzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4MC9leHBlbnNlcyI7czo1OiJyb3V0ZSI7czoxNDoiZXhwZW5zZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1791634143),
('It8oCVluSQNBiykF9z8Y7ES1iJ4bpPNnqkcanU2m', 1, '127.0.0.1', 'curl/8.21.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiNEtxeU9tQlVoeVk0YWpzQWxVdWZDaVIzMEtLcHNGSlJFMngyU1dwdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4MC9yZXNlcnZhdGlvbnMiO3M6NToicm91dGUiO3M6MTg6InJlc2VydmF0aW9ucy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6IjE4NTUyZjVhMGI1Y2Q3ZjQwYjBmYTQyNTQzMDlmYjNkYTNjMDgyZjE5N2YzOTUwMzNjMDU3N2M1ODhiMDRhNTEiO3M6MTY6ImFjdGl2ZV9icmFuY2hfaWQiO2k6Mjt9', 1791639317),
('KdSUJKUsUBL9hj8C4gdUqyzsyo11AImD4kgWGjma', NULL, '127.0.0.1', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQm9kNGZ4cThUSURnRnlEb3QzRGtjejFZQmxUYXhoOHZnRkdxRGYydiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODA4MC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1791636938),
('sfmsdOitZhbri05cEZ8JNrb61xMlH2n9NnIoPCkd', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiYzZyQWNjT1BCMldHY0tGSUQ0c0NwYWpmQ3J5ejR5ZEJld1pJREx2aiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9yb29tcyI7czo1OiJyb3V0ZSI7czoxMToicm9vbXMuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiIxMzM4MzViOTA4ODY0MTE2YTA1OTNjZTc3NjQ5YTIxNTczZTIzYjMwZTRiZTYxMzgyZTI0ZTlmNmQ3NzExOWU4IjtzOjE2OiJhY3RpdmVfYnJhbmNoX2lkIjtpOjE7fQ==', 1791638445),
('tAQyM7JVD3cvtb1Zbz1GNa7nyOD5gVWGlCcmZFMt', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiblVuNUFtMXh6NFRsemw2aWVsRnI5aFlXcFZFMTk2V21oUGFwbGZpWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9yb29tcyI7czo1OiJyb3V0ZSI7czoxMToicm9vbXMuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo1O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiIyYTg3ZDYwOWJkZDk2ZTZkZWZjNDNhYzJmYWRiNDhiMWU1ODNkYjU2NzM0NDAzM2NhOGQxNGZiYzk1NDJiZTUyIjtzOjE2OiJhY3RpdmVfYnJhbmNoX2lkIjtpOjE7fQ==', 1791639477),
('vI1xruuu6nN8MICfWAIRz0BLcGkPdprBzFPjhWsz', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUWV6dXFJNXNrdTJxeDBONE1sNWhmUDNUMk9lczMyaW5UZ0xlaG4waiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNDoiaHR0cDovLzEyNy4wLjAuMTo4MDgwL3Jlc2VydmF0aW9ucyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwODAvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1791639308);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `organization_id`, `name`, `email`, `phone`, `designation`, `is_active`, `email_verified_at`, `password`, `remember_token`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Executive Super Admin', 'admin@stayinghotel.com', '+880 1700 000001', 'Chief Operations Officer', 1, NULL, '$2y$12$do6pQO57fI6ukk9AHZWLK.3MsKaXwzSjGh7T11lYfymT7MrSfjDhe', 'hc4N5nX7hv9l8SQzaRhTtO5ZCTppuD52Hi2TzAv4VoJj0gL95SMzOp4Zqocp', '2026-10-10 06:55:01', '2026-10-10 05:12:30', '2026-10-10 06:55:01'),
(2, 1, 'Tanvir Hasan (Dhaka Manager)', 'manager.dhaka@stayinghotel.com', '+880 1700 000002', 'General Manager - Dhaka', 1, NULL, '$2y$12$DJczS8l5UwK/0NUWkDJh3uVcQqsr0RQ/z.GVspLELx.qGEFgi7Tk.', 'qmBBDkFupSbIDD0tfsT9qizhaUBJz0zc5vDDK9VrSCQV9mZxFIGgSVQ1xGzt', '2026-10-10 07:20:07', '2026-10-10 05:12:30', '2026-10-10 07:20:07'),
(3, 1, 'Nusrat Jahan (Finance Manager)', 'finance@stayinghotel.com', '+880 1700 000003', 'Head of Accounts & Finance', 1, NULL, '$2y$12$aMe8Ec8oOpfc4EzJJF2YJOrh5gJ/ApoIBkTUaFA3pNnj3vhXkm74.', 'Ugc8SCfzfsC8SJJhpIdMWa6hQB3Clrf02uyzUejToiAVy1LlaSqxYJHZqYD0', '2026-10-10 06:21:10', '2026-10-10 05:12:31', '2026-10-10 06:21:10'),
(4, 1, 'Mehedi Hasan (Cashier)', 'cashier.dhaka@stayinghotel.com', '+880 1700 000004', 'Senior Cashier', 1, NULL, '$2y$12$TH7of9jAT9udszMyFykPUeqCXp48CrwPABrHJsbk2KeoHLoRSyZ3W', NULL, '2026-10-10 05:26:38', '2026-10-10 05:12:31', '2026-10-10 05:26:38'),
(5, 1, 'Sadia Islam (Receptionist)', 'reception.dhaka@stayinghotel.com', '+880 1700 000005', 'Front Desk Executive', 1, NULL, '$2y$12$K4lxVVN7h.CozZgUdWX5LuscPenIrYA4CBrzTErgXKz5YdFtAvPpa', 'P2ETyoCTYcMJ6yl7bu7RvyOyD4scXEenP2kIgDdbAMZbkU5cUCNh7YgFcUc5', '2026-10-10 06:39:40', '2026-10-10 05:12:31', '2026-10-10 06:39:40'),
(6, 1, 'Kamal Hossain (Housekeeping)', 'housekeeping.dhaka@stayinghotel.com', '+880 1700 000006', 'Housekeeping Supervisor', 1, NULL, '$2y$12$.IlPOqN.NPpvJBBFfGyqfOg3ldusZ0f0gGlbm3bL4mb6LGQqHlIVC', NULL, '2026-10-10 05:26:41', '2026-10-10 05:12:32', '2026-10-10 05:26:41'),
(7, 1, 'Rafiqul Alam (Maintenance)', 'maintenance.dhaka@stayinghotel.com', '+880 1700 000007', 'Chief Maintenance Engineer', 1, NULL, '$2y$12$UxWbuLhgBUOKpCcx3KDmMONwJT72LbZOcF5kxKuYFYwC1h8ol5ytS', 'VTGVL1R818L8i4oRNhq9oTBEXzrh5Uvub0vEvL89PFlgXQUZvfHZ3kq0GKTg', '2026-10-10 06:34:02', '2026-10-10 05:12:32', '2026-10-10 06:34:02'),
(8, 1, 'Farhana Akter (Internal Auditor)', 'auditor@stayinghotel.com', '+880 1700 000008', 'Internal Compliance Auditor', 1, NULL, '$2y$12$F7L/9lcDfDLYInzq5mjht.NjouYhJUxc6MeLUHZs0ATSMycgQs5aG', 'uwpo0JVjMLNJONJE6F32pHPblgGvHW2mGcHcMrcQLTEd1cgksoMyn23O0tMX', '2026-10-10 07:12:37', '2026-10-10 05:12:32', '2026-10-10 07:12:37');

-- --------------------------------------------------------

--
-- Table structure for table `user_branch_assignments`
--

CREATE TABLE `user_branch_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_branch_assignments`
--

INSERT INTO `user_branch_assignments` (`id`, `user_id`, `branch_id`, `role_id`, `is_primary`, `assigned_at`) VALUES
(1, 1, NULL, 1, 1, '2026-10-10 11:12:30'),
(2, 1, 2, 1, 0, '2026-10-10 11:12:30'),
(3, 1, 3, 1, 0, '2026-10-10 11:12:30'),
(4, 1, 4, 1, 0, '2026-10-10 11:12:30'),
(5, 2, 1, 2, 1, '2026-10-10 11:12:31'),
(6, 3, 1, 3, 1, '2026-10-10 11:12:31'),
(7, 3, 2, 3, 0, '2026-10-10 11:12:31'),
(8, 3, 3, 3, 0, '2026-10-10 11:12:31'),
(9, 3, 4, 3, 0, '2026-10-10 11:12:31'),
(10, 4, 1, 4, 1, '2026-10-10 11:12:31'),
(11, 5, 1, 5, 1, '2026-10-10 11:12:31'),
(12, 6, 1, 6, 1, '2026-10-10 11:12:32'),
(13, 7, 1, 7, 1, '2026-10-10 11:12:32'),
(14, 8, 1, 9, 1, '2026-10-10 11:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_id` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `branch_id`, `name`, `company_name`, `phone`, `email`, `address`, `tax_id`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'CleanPro Hotel Solutions Ltd.', 'CleanPro BD', '+880 1819 888777', 'sales@cleanpro.com.bd', 'Tejgaon Industrial Area, Dhaka', NULL, NULL, 1, '2026-10-10 05:12:33', '2026-10-10 06:03:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_organization_id_created_at_index` (`organization_id`,`created_at`),
  ADD KEY `audit_logs_branch_id_created_at_index` (`branch_id`,`created_at`),
  ADD KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `audit_logs_target_type_target_id_index` (`target_type`,`target_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_code_unique` (`code`),
  ADD KEY `branches_organization_id_is_active_index` (`organization_id`,`is_active`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cashier_shifts`
--
ALTER TABLE `cashier_shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cashier_shifts_shift_number_unique` (`shift_number`),
  ADD KEY `cashier_shifts_reviewed_by_user_id_foreign` (`reviewed_by_user_id`),
  ADD KEY `cashier_shifts_branch_id_status_index` (`branch_id`,`status`),
  ADD KEY `cashier_shifts_user_id_status_index` (`user_id`,`status`);

--
-- Indexes for table `cashier_transactions`
--
ALTER TABLE `cashier_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cashier_transactions_branch_id_foreign` (`branch_id`),
  ADD KEY `cashier_transactions_payment_id_foreign` (`payment_id`),
  ADD KEY `cashier_transactions_cashier_shift_id_transaction_type_index` (`cashier_shift_id`,`transaction_type`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expenses_expense_number_unique` (`expense_number`),
  ADD KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  ADD KEY `expenses_vendor_id_foreign` (`vendor_id`),
  ADD KEY `expenses_cashier_shift_id_foreign` (`cashier_shift_id`),
  ADD KEY `expenses_requested_by_user_id_foreign` (`requested_by_user_id`),
  ADD KEY `expenses_approved_by_user_id_foreign` (`approved_by_user_id`),
  ADD KEY `expenses_branch_id_status_index` (`branch_id`,`status`),
  ADD KEY `expenses_branch_id_expense_date_index` (`branch_id`,`expense_date`);

--
-- Indexes for table `expense_approvals`
--
ALTER TABLE `expense_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_approvals_approver_user_id_foreign` (`approver_user_id`),
  ADD KEY `expense_approvals_expense_id_status_index` (`expense_id`,`status`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_categories_code_unique` (`code`),
  ADD KEY `expense_categories_branch_id_is_active_index` (`branch_id`,`is_active`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guests_organization_id_phone_index` (`organization_id`,`phone`),
  ADD KEY `guests_organization_id_email_index` (`organization_id`,`email`);

--
-- Indexes for table `guest_folio_items`
--
ALTER TABLE `guest_folio_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guest_folio_items_branch_id_foreign` (`branch_id`),
  ADD KEY `guest_folio_items_added_by_user_id_foreign` (`added_by_user_id`),
  ADD KEY `guest_folio_items_reservation_id_branch_id_index` (`reservation_id`,`branch_id`);

--
-- Indexes for table `hotel_settings`
--
ALTER TABLE `hotel_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hotel_settings_branch_id_setting_key_unique` (`branch_id`,`setting_key`),
  ADD KEY `hotel_settings_organization_id_setting_key_index` (`organization_id`,`setting_key`);

--
-- Indexes for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `housekeeping_tasks_inspected_by_user_id_foreign` (`inspected_by_user_id`),
  ADD KEY `housekeeping_tasks_branch_id_status_index` (`branch_id`,`status`),
  ADD KEY `housekeeping_tasks_assigned_to_user_id_status_index` (`assigned_to_user_id`,`status`),
  ADD KEY `housekeeping_tasks_room_id_index` (`room_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_created_by_user_id_foreign` (`created_by_user_id`),
  ADD KEY `invoices_branch_id_status_index` (`branch_id`,`status`),
  ADD KEY `invoices_reservation_id_index` (`reservation_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_items_folio_item_id_foreign` (`folio_item_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenance_requests_reported_by_user_id_foreign` (`reported_by_user_id`),
  ADD KEY `maintenance_requests_branch_id_status_index` (`branch_id`,`status`),
  ADD KEY `maintenance_requests_room_id_blocks_room_index` (`room_id`,`blocks_room`),
  ADD KEY `maintenance_requests_assigned_to_user_id_status_index` (`assigned_to_user_id`,`status`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`),
  ADD KEY `notifications_branch_id_created_at_index` (`branch_id`,`created_at`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organizations_code_unique` (`code`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_payment_number_unique` (`payment_number`),
  ADD KEY `payments_invoice_id_foreign` (`invoice_id`),
  ADD KEY `payments_received_by_user_id_foreign` (`received_by_user_id`),
  ADD KEY `payments_branch_id_payment_method_index` (`branch_id`,`payment_method`),
  ADD KEY `payments_reservation_id_status_index` (`reservation_id`,`status`),
  ADD KEY `payments_cashier_shift_id_index` (`cashier_shift_id`);

--
-- Indexes for table `payment_reversals`
--
ALTER TABLE `payment_reversals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_reversals_branch_id_foreign` (`branch_id`),
  ADD KEY `payment_reversals_payment_id_foreign` (`payment_id`),
  ADD KEY `payment_reversals_reversed_by_user_id_foreign` (`reversed_by_user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`),
  ADD KEY `permissions_module_index` (`module`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `refunds_refund_number_unique` (`refund_number`),
  ADD KEY `refunds_payment_id_foreign` (`payment_id`),
  ADD KEY `refunds_reservation_id_foreign` (`reservation_id`),
  ADD KEY `refunds_approved_by_user_id_foreign` (`approved_by_user_id`),
  ADD KEY `refunds_processed_by_user_id_foreign` (`processed_by_user_id`),
  ADD KEY `refunds_branch_id_status_index` (`branch_id`,`status`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservations_reservation_number_unique` (`reservation_number`),
  ADD KEY `reservations_guest_id_foreign` (`guest_id`),
  ADD KEY `reservations_created_by_user_id_foreign` (`created_by_user_id`),
  ADD KEY `reservations_branch_id_status_index` (`branch_id`,`status`),
  ADD KEY `reservations_branch_id_check_in_date_check_out_date_index` (`branch_id`,`check_in_date`,`check_out_date`);

--
-- Indexes for table `reservation_rate_snapshots`
--
ALTER TABLE `reservation_rate_snapshots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_rate_snapshots_reservation_id_stay_date_unique` (`reservation_id`,`stay_date`);

--
-- Indexes for table `reservation_rooms`
--
ALTER TABLE `reservation_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_rooms_room_type_id_foreign` (`room_type_id`),
  ADD KEY `reservation_rooms_reservation_id_room_id_index` (`reservation_id`,`room_id`),
  ADD KEY `reservation_rooms_room_id_index` (`room_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rooms_branch_id_room_number_unique` (`branch_id`,`room_number`),
  ADD KEY `rooms_room_type_id_foreign` (`room_type_id`),
  ADD KEY `rooms_branch_id_operational_status_index` (`branch_id`,`operational_status`),
  ADD KEY `rooms_branch_id_room_type_id_index` (`branch_id`,`room_type_id`);

--
-- Indexes for table `room_amenities`
--
ALTER TABLE `room_amenities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_amenities_code_unique` (`code`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_types_branch_id_slug_unique` (`branch_id`,`slug`),
  ADD KEY `room_types_branch_id_is_active_index` (`branch_id`,`is_active`);

--
-- Indexes for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD PRIMARY KEY (`room_type_id`,`room_amenity_id`),
  ADD KEY `room_type_amenities_room_amenity_id_foreign` (`room_amenity_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_organization_id_foreign` (`organization_id`);

--
-- Indexes for table `user_branch_assignments`
--
ALTER TABLE `user_branch_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_branch_role_unique` (`user_id`,`branch_id`,`role_id`),
  ADD KEY `user_branch_assignments_role_id_foreign` (`role_id`),
  ADD KEY `user_branch_assignments_user_id_branch_id_index` (`user_id`,`branch_id`),
  ADD KEY `user_branch_assignments_branch_id_role_id_index` (`branch_id`,`role_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendors_branch_id_is_active_index` (`branch_id`,`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cashier_shifts`
--
ALTER TABLE `cashier_shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cashier_transactions`
--
ALTER TABLE `cashier_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expense_approvals`
--
ALTER TABLE `expense_approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `guest_folio_items`
--
ALTER TABLE `guest_folio_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `hotel_settings`
--
ALTER TABLE `hotel_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment_reversals`
--
ALTER TABLE `payment_reversals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reservation_rate_snapshots`
--
ALTER TABLE `reservation_rate_snapshots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservation_rooms`
--
ALTER TABLE `reservation_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `room_amenities`
--
ALTER TABLE `room_amenities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_branch_assignments`
--
ALTER TABLE `user_branch_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `audit_logs_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cashier_shifts`
--
ALTER TABLE `cashier_shifts`
  ADD CONSTRAINT `cashier_shifts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cashier_shifts_reviewed_by_user_id_foreign` FOREIGN KEY (`reviewed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cashier_shifts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cashier_transactions`
--
ALTER TABLE `cashier_transactions`
  ADD CONSTRAINT `cashier_transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cashier_transactions_cashier_shift_id_foreign` FOREIGN KEY (`cashier_shift_id`) REFERENCES `cashier_shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cashier_transactions_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_approved_by_user_id_foreign` FOREIGN KEY (`approved_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_cashier_shift_id_foreign` FOREIGN KEY (`cashier_shift_id`) REFERENCES `cashier_shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_requested_by_user_id_foreign` FOREIGN KEY (`requested_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_approvals`
--
ALTER TABLE `expense_approvals`
  ADD CONSTRAINT `expense_approvals_approver_user_id_foreign` FOREIGN KEY (`approver_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expense_approvals_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `expense_categories_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `guests_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guest_folio_items`
--
ALTER TABLE `guest_folio_items`
  ADD CONSTRAINT `guest_folio_items_added_by_user_id_foreign` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `guest_folio_items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guest_folio_items_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotel_settings`
--
ALTER TABLE `hotel_settings`
  ADD CONSTRAINT `hotel_settings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotel_settings_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  ADD CONSTRAINT `housekeeping_tasks_assigned_to_user_id_foreign` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `housekeeping_tasks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `housekeeping_tasks_inspected_by_user_id_foreign` FOREIGN KEY (`inspected_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `housekeeping_tasks_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_folio_item_id_foreign` FOREIGN KEY (`folio_item_id`) REFERENCES `guest_folio_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_assigned_to_user_id_foreign` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `maintenance_requests_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_requests_reported_by_user_id_foreign` FOREIGN KEY (`reported_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_requests_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_cashier_shift_id_foreign` FOREIGN KEY (`cashier_shift_id`) REFERENCES `cashier_shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_received_by_user_id_foreign` FOREIGN KEY (`received_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_reversals`
--
ALTER TABLE `payment_reversals`
  ADD CONSTRAINT `payment_reversals_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_reversals_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_reversals_reversed_by_user_id_foreign` FOREIGN KEY (`reversed_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_approved_by_user_id_foreign` FOREIGN KEY (`approved_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `refunds_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_processed_by_user_id_foreign` FOREIGN KEY (`processed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `refunds_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservations_guest_id_foreign` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservation_rate_snapshots`
--
ALTER TABLE `reservation_rate_snapshots`
  ADD CONSTRAINT `reservation_rate_snapshots_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservation_rooms`
--
ALTER TABLE `reservation_rooms`
  ADD CONSTRAINT `reservation_rooms_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_rooms_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservation_rooms_room_type_id_foreign` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rooms_room_type_id_foreign` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_types`
--
ALTER TABLE `room_types`
  ADD CONSTRAINT `room_types_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_type_amenities`
--
ALTER TABLE `room_type_amenities`
  ADD CONSTRAINT `room_type_amenities_room_amenity_id_foreign` FOREIGN KEY (`room_amenity_id`) REFERENCES `room_amenities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_type_amenities_room_type_id_foreign` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_branch_assignments`
--
ALTER TABLE `user_branch_assignments`
  ADD CONSTRAINT `user_branch_assignments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_branch_assignments_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_branch_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
