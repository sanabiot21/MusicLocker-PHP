-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 04:16 PM
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
-- Database: `music_locker`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `LogUserActivity` (IN `p_user_id` INT, IN `p_action` VARCHAR(100), IN `p_target_type` VARCHAR(50), IN `p_target_id` INT, IN `p_description` TEXT, IN `p_ip_address` VARCHAR(45), IN `p_user_agent` TEXT)   BEGIN
    INSERT INTO activity_log (
        user_id, action, target_type, target_id, 
        description, ip_address, user_agent
    ) VALUES (
        p_user_id, p_action, p_target_type, p_target_id,
        p_description, p_ip_address, p_user_agent
    );
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `GetUserDiscoveryRate` (`p_user_id` INT, `p_days` INT) RETURNS DECIMAL(10,2) DETERMINISTIC READS SQL DATA BEGIN
    DECLARE discovery_rate DECIMAL(10,2);
    
    SELECT COUNT(*) / p_days INTO discovery_rate
    FROM music_entries 
    WHERE user_id = p_user_id 
    AND date_added >= DATE_SUB(NOW(), INTERVAL p_days DAY);
    
    RETURN IFNULL(discovery_rate, 0);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `target_type`, `target_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(6, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 18:25:20'),
(7, 3, 'music_entry_add', 'music_entry', 1, 'Added music entry: Doomer by Tokyo Manaka', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 22:34:10'),
(8, 3, 'music_entry_add', 'music_entry', 2, 'Added music entry: Retry Now by NAKISO', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 22:35:35'),
(9, 3, 'music_entry_add', 'music_entry', 3, 'Added music entry: Bohemian Rhapsody by Queen', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 23:18:58'),
(10, 3, 'delete_music_entry', 'music_entry', 3, 'Deleted music entry: Bohemian Rhapsody', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 23:19:10'),
(11, 3, 'music_entry_add', 'music_entry', 4, 'Added music entry: No Surprises by Radiohead', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 23:19:48'),
(12, 3, 'logout', 'user', 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 23:20:40'),
(19, 6, 'login', 'user', 6, 'User logged in successfully', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-09-05 07:54:50'),
(37, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-11 11:11:12'),
(52, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-23 02:46:20'),
(53, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 13:44:07'),
(54, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 13:57:13'),
(55, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 13:57:20'),
(56, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 14:21:56'),
(57, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:25:21'),
(59, 1, 'admin_password_reset', 'user', 3, 'Admin reset password for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:34:05'),
(60, 1, 'admin_password_reset', 'user', 3, 'Admin reset password for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:34:21'),
(61, 1, 'admin_password_reset', 'user', 3, 'Admin reset password for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:35:04'),
(62, 1, 'admin_password_reset', 'user', 3, 'Admin reset password for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:35:26'),
(63, 1, 'admin_password_reset', 'user', 3, 'Admin reset password for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:35:40'),
(64, 1, 'admin_password_reset', 'user', 3, 'Admin reset password for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:42:41'),
(65, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:42:52'),
(66, 3, 'logout', 'user', 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:43:50'),
(69, 1, 'admin_approve_reset', 'user', 3, 'Admin approved password reset for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:54:45'),
(70, 1, 'admin_approve_reset', 'user', 3, 'Admin approved password reset for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:54:49'),
(72, 1, 'admin_approve_reset', 'user', 3, 'Admin approved password reset for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 15:55:23'),
(73, 3, 'password_reset_approved', 'user', 3, 'Password reset requested for email: reynaldogrande9@gmail.com - APPROVED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 16:01:45'),
(74, 1, 'admin_approve_reset', 'user', 3, 'Admin approved password reset for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 16:02:00'),
(75, 3, 'password_reset_approved', 'user', 3, 'Password reset requested for email: reynaldogrande9@gmail.com - APPROVED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 16:02:06'),
(76, 1, 'admin_approve_reset', 'user', 3, 'Admin approved password reset for user ID: 3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 16:07:35'),
(78, 1, 'admin_approve_reset', 'user', 5, 'Admin approved password reset for user ID: 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 16:07:59'),
(82, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-10-09 21:36:52'),
(83, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 22:05:05'),
(84, 3, 'logout', 'user', 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 22:18:35'),
(85, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 23:09:59'),
(86, 3, 'logout', 'user', 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 23:19:30'),
(87, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 23:19:46'),
(88, 1, 'music_create', 'music_entry', 23, 'Created music entry', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 23:30:41'),
(89, 1, 'playlist_create', 'playlist', 3, 'Created playlist', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 23:48:24'),
(90, 1, 'music_delete', 'music_entry', 15, 'Deleted music entry', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-11 23:58:35'),
(91, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 08:52:34'),
(92, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 09:09:49'),
(94, 1, 'admin_approve_reset', 'user', 5, 'Admin approved password reset for user ID: 5', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 09:10:06'),
(132, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:02:48'),
(134, 9, 'login', 'user', 9, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:07:27'),
(136, 9, 'music_create', 'music_entry', 29, 'Created: multo by cup of joe', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:08:27'),
(137, 9, 'music_create', 'music_entry', 30, 'Created: Multo by Cup of Joe', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:10:07'),
(138, 9, 'music_delete', 'music_entry', 29, 'Deleted music entry', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:10:10'),
(139, 9, 'music_create', 'music_entry', 31, 'Created: a thousand bad times by killhussein', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:10:49'),
(140, 9, 'music_toggle_favorite', 'music_entry', 31, 'Toggled favorite: a thousand bad times by killhussein', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:12:37'),
(141, 9, 'music_toggle_favorite', 'music_entry', 30, 'Toggled favorite: Multo by Cup of Joe', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:12:38'),
(142, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:18:50'),
(143, 3, 'playlist_add_track', 'playlist', 2, 'Added track 21 - Bohemian Rhapsody to egeqg', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:23:26'),
(144, 3, 'playlist_add_track', 'playlist', 2, 'Added track 22 - Multo to egeqg', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:23:27'),
(145, 3, 'music_delete', 'music_entry', 4, 'Deleted music entry', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:23:46'),
(146, 3, 'logout', 'user', 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:25:11'),
(147, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:25:21'),
(148, 1, 'admin_toggle_user_status', 'user', 9, 'Toggled user status: dayananshawn@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:30:41'),
(149, 1, 'admin_toggle_user_status', 'user', 9, 'Toggled user status: dayananshawn@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:30:44'),
(150, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:37:45'),
(151, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:39:10'),
(152, 3, 'logout', 'user', 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:41:50'),
(153, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:50:36'),
(154, 3, 'logout', 'user', 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:51:14'),
(155, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:53:08'),
(156, 3, 'logout', 'user', 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 11:58:04'),
(157, 11, 'login', 'user', 11, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:21:26'),
(158, 11, 'logout', 'user', 11, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:25:30'),
(159, 3, 'login', 'user', 3, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:25:42'),
(160, 3, 'logout', 'user', 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:25:59'),
(161, 11, 'login', 'user', 11, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:26:09'),
(162, 11, 'music_create', 'music_entry', 32, 'Created: Glimpse of Us by Joji', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:27:10'),
(163, 11, 'logout', 'user', 11, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:36:07'),
(164, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:36:31'),
(165, 1, 'admin_toggle_user_status', 'user', 11, 'Toggled user status: testing@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:51:06'),
(166, 1, 'admin_toggle_user_status', 'user', 11, 'Toggled user status: testing@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:51:08'),
(167, 1, 'admin_toggle_user_status', 'user', 11, 'Toggled user status: testing@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:51:11'),
(168, 1, 'admin_toggle_user_status', 'user', 11, 'Toggled user status: testing@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:51:15'),
(169, 1, 'admin_delete_user', 'user', 10, 'Deleted user: User ID 10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:51:22'),
(170, 1, 'profile_update', 'user', 1, 'Profile information updated', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 12:51:58'),
(171, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 13:09:59'),
(172, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 13:10:08'),
(173, 1, 'playlist_add_track', 'playlist', 3, 'Added track 17 - ザムザ (feat. 宵崎奏&朝比奈まふゆ&東雲絵名&暁山瑞希&KAITO) to playlist1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 13:10:50'),
(174, 1, 'playlist_add_track', 'playlist', 3, 'Added track 16 - キティ (feat. 宵崎奏&朝比奈まふゆ&東雲絵名&暁山瑞希&鏡音レン) to playlist1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 13:10:50'),
(175, 1, 'playlist_add_track', 'playlist', 3, 'Added track 23 - Welcome to The Internet to playlist1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 13:10:50'),
(176, 1, 'playlist_add_track', 'playlist', 3, 'Added track 19 - PPPP (feat. Hatsune Miku, Kasane Teto) to playlist1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 13:10:51'),
(177, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 13:12:30'),
(178, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 13:13:07'),
(179, 1, 'admin_update_settings', 'system_setting', NULL, 'Updated system settings (6 keys)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-12 13:13:22'),
(180, 12, 'login', 'user', 12, 'User logged in successfully', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:31:49'),
(181, 12, 'music_create', 'music_entry', 33, 'Created: TruE by HOYO-MiX, 黄龄', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:33:00'),
(182, 12, 'music_toggle_favorite', 'music_entry', 33, 'Toggled favorite: TruE by HOYO-MiX, 黄龄', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:33:08'),
(183, 12, 'music_toggle_favorite', 'music_entry', 33, 'Toggled favorite: TruE by HOYO-MiX, 黄龄', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:33:10'),
(184, 12, 'logout', 'user', 12, 'User logged out', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:34:01'),
(185, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:34:43'),
(186, 1, 'admin_toggle_user_status', 'user', 12, 'Toggled user status: testis@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:36:16'),
(187, 1, 'admin_toggle_user_status', 'user', 11, 'Toggled user status: testing@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:36:27'),
(188, 1, 'admin_toggle_user_status', 'user', 11, 'Toggled user status: testing@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:36:30'),
(189, 1, 'admin_toggle_user_status', 'user', 11, 'Toggled user status: testing@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:36:45'),
(190, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:36:50'),
(191, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:38:43'),
(192, 9, 'login', 'user', 9, 'User logged in successfully', '::1', 'Mozilla/5.0 (Linux; Android 13; CPH2237 Build/TP1A.220905.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/140.0.7339.207 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/528.0.0.62.107;]', '2025-10-12 19:38:57'),
(193, 9, 'logout', 'user', 9, 'User logged out', '::1', 'Mozilla/5.0 (Linux; Android 13; CPH2237 Build/TP1A.220905.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/140.0.7339.207 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/528.0.0.62.107;]', '2025-10-12 19:39:51'),
(194, 1, 'admin_toggle_user_status', 'user', 9, 'Toggled user status: dayananshawn@gmail.com', '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '2025-10-12 19:40:08'),
(195, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '2025-10-13 13:28:26'),
(196, 1, 'logout', 'user', 1, 'User logged out', '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '2025-10-13 13:29:39'),
(197, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 13:30:44'),
(198, 1, 'admin_toggle_user_status', 'user', 11, 'Toggled user status: testing@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 13:31:30'),
(200, 1, 'admin_approve_reset', 'user', 11, 'Admin approved password reset for user ID: 11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 13:32:10'),
(201, 11, 'login', 'user', 11, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 13:32:33'),
(202, 11, 'playlist_create', 'playlist', 7, 'Created playlist: nigga', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 13:32:54'),
(203, 11, 'playlist_add_track', 'playlist', 7, 'Added track 32 - Glimpse of Us to nigga', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 13:32:59'),
(204, 11, 'logout', 'user', 11, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 13:35:48'),
(205, 11, 'login', 'user', 11, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 13:44:06'),
(206, 11, 'logout', 'user', 11, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 13:44:55'),
(207, 1, 'login', 'user', 1, 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-13 13:45:04');

-- --------------------------------------------------------

--
-- Table structure for table `music_entries`
--

CREATE TABLE `music_entries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `album` varchar(255) DEFAULT NULL,
  `genre` varchar(100) NOT NULL,
  `release_year` year(4) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `spotify_id` varchar(255) DEFAULT NULL,
  `spotify_url` varchar(500) DEFAULT NULL,
  `album_art_url` varchar(500) DEFAULT NULL,
  `personal_rating` tinyint(4) NOT NULL CHECK (`personal_rating` between 1 and 5),
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_discovered` date DEFAULT NULL,
  `is_favorite` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `music_entries`
--

INSERT INTO `music_entries` (`id`, `user_id`, `title`, `artist`, `album`, `genre`, `release_year`, `duration`, `spotify_id`, `spotify_url`, `album_art_url`, `personal_rating`, `date_added`, `date_discovered`, `is_favorite`, `created_at`, `updated_at`) VALUES
(1, 3, 'Doomer', 'Tokyo Manaka', 'Doomer', '', '2025', 148, '3J8h8AUh100VIaFbSjCxB4', 'https://open.spotify.com/track/3J8h8AUh100VIaFbSjCxB4', 'https://i.scdn.co/image/ab67616d0000b273438093c80a894ce3391a327f', 0, '2025-09-04 22:34:10', '2025-09-05', 0, '2025-09-04 22:34:10', '2025-09-04 22:34:10'),
(2, 3, 'Retry Now', 'NAKISO', 'Retry Now', '', '2025', 122, '7gKt5lOImlJ2bOOcrODFQY', 'https://open.spotify.com/track/7gKt5lOImlJ2bOOcrODFQY', 'https://i.scdn.co/image/ab67616d0000b2732429f90dbf06e9b229abcda1', 4, '2025-09-04 22:35:35', '2025-09-05', 0, '2025-09-04 22:35:35', '2025-09-04 22:35:35'),
(16, 1, 'キティ (feat. 宵崎奏&朝比奈まふゆ&東雲絵名&暁山瑞希&鏡音レン)', '25時、ナイトコードで。', 'ザムザ/キティ', '', '2023', 208, '6Ho58lFgmMy2Frbwj5zfzY', 'https://open.spotify.com/track/6Ho58lFgmMy2Frbwj5zfzY', 'https://i.scdn.co/image/ab67616d0000b273cf4de70b65d447091979be0f', 4, '2025-10-09 19:41:14', '2025-10-09', 0, '2025-10-09 19:41:14', '2025-10-09 19:41:14'),
(17, 1, 'ザムザ (feat. 宵崎奏&朝比奈まふゆ&東雲絵名&暁山瑞希&KAITO)', '25時、ナイトコードで。', 'ザムザ/キティ', '', '2023', 214, '62kWs1t5ncSoao7EY7yVBD', 'https://open.spotify.com/track/62kWs1t5ncSoao7EY7yVBD', 'https://i.scdn.co/image/ab67616d0000b273cf4de70b65d447091979be0f', 4, '2025-10-09 19:41:39', '2025-10-09', 0, '2025-10-09 19:41:39', '2025-10-09 19:41:39'),
(19, 1, 'PPPP (feat. Hatsune Miku, Kasane Teto)', 'TAK, Hatsune Miku, Kasane Teto', 'PPPP', 'Vocaloid', '2025', 155, '6J3pPfXLujwsWQpvR6XMgC', 'https://open.spotify.com/track/6J3pPfXLujwsWQpvR6XMgC', 'https://i.scdn.co/image/ab67616d0000b273e2acdafe9b0c10beeda9a277', 5, '2025-10-09 23:05:05', '2025-10-10', 0, '2025-10-09 23:05:05', '2025-10-09 23:28:13'),
(20, 1, 'Medicine', 'Sasuke Haraguchi', 'Medicine', 'Vocaloid', '2024', 120, '6oQd9KEbRMERESY1pftFyn', 'https://open.spotify.com/track/6oQd9KEbRMERESY1pftFyn', 'https://i.scdn.co/image/ab67616d0000b27369fc8b848dc3ccb2824b18de', 5, '2025-10-09 23:11:07', '2025-10-10', 1, '2025-10-09 23:11:07', '2025-10-10 08:07:47'),
(21, 3, 'Bohemian Rhapsody', 'Queen', 'Bohemian Rhapsody (The Original Soundtrack)', 'Classic Rock', '2018', 355, '3z8h0TU7ReDPLIbEnYhWZb', 'https://open.spotify.com/track/3z8h0TU7ReDPLIbEnYhWZb', 'https://i.scdn.co/image/ab67616d0000b273e8b066f70c206551210d902b', 3, '2025-10-11 22:06:35', '2025-10-12', 1, '2025-10-11 22:06:35', '2025-10-11 22:06:35'),
(22, 3, 'Multo', 'Cup of Joe', 'Multo', 'Opm', '2024', 238, '4cBm8rv2B5BJWU2pDaHVbF', 'https://open.spotify.com/track/4cBm8rv2B5BJWU2pDaHVbF', 'https://i.scdn.co/image/ab67616d0000b273394048503e3be0e65e962638', 4, '2025-10-11 23:11:18', '2025-10-12', 0, '2025-10-11 23:11:18', '2025-10-11 23:11:42'),
(23, 1, 'Welcome to The Internet', 'Bo Burnham', 'INSIDE', 'Comedy', '2021', 276, '3s44Qv8x974tm0ueLexMWN', 'https://open.spotify.com/track/3s44Qv8x974tm0ueLexMWN', 'https://i.scdn.co/image/ab67616d0000b27388fed14b936c38007a302413', 3, '2025-10-11 23:30:41', '2025-10-12', 0, '2025-10-11 23:30:41', '2025-10-11 23:30:41'),
(30, 9, 'Multo', 'Cup of Joe', 'Multo', 'Opm', '2024', 238, '4cBm8rv2B5BJWU2pDaHVbF', 'https://open.spotify.com/track/4cBm8rv2B5BJWU2pDaHVbF', 'https://i.scdn.co/image/ab67616d0000b273394048503e3be0e65e962638', 3, '2025-10-12 11:10:07', '2025-10-12', 1, '2025-10-12 11:10:07', '2025-10-12 11:12:38'),
(31, 9, 'a thousand bad times', 'killhussein', 'a thousand bad times', 'Dark ambient', '2022', 178, '53aQjyHqKLg7f7XjiEmRhK', 'https://open.spotify.com/track/53aQjyHqKLg7f7XjiEmRhK', 'https://i.scdn.co/image/ab67616d0000b27322c328820eb111b2839b3cc2', 3, '2025-10-12 11:10:49', '2025-10-12', 1, '2025-10-12 11:10:49', '2025-10-12 11:12:37'),
(32, 11, 'Glimpse of Us', 'Joji', 'Glimpse of Us', 'sadboi type', '2022', 233, '3aBGKDiAAvH2H7HLOyQ4US', 'https://open.spotify.com/track/3aBGKDiAAvH2H7HLOyQ4US', 'https://i.scdn.co/image/ab67616d0000b273f3f7d2ea2ad435b57d6697df', 3, '2025-10-12 12:27:10', '2025-10-12', 0, '2025-10-12 12:27:10', '2025-10-12 12:27:10'),
(33, 12, 'TruE', 'HOYO-MiX, 黄龄', 'TruE (Honkai Impact 3rd \"Because of You\" Animated Short Theme Song)', 'Soundtrack', '2022', 188, '56aR8fCNORk8XIrQGo75IQ', 'https://open.spotify.com/track/56aR8fCNORk8XIrQGo75IQ', 'https://i.scdn.co/image/ab67616d0000b2736d2a60f14703d1ddf9b5334e', 5, '2025-10-12 19:33:00', '2025-10-12', 0, '2025-10-12 19:33:00', '2025-10-12 19:33:10');

-- --------------------------------------------------------

--
-- Table structure for table `music_entry_tags`
--

CREATE TABLE `music_entry_tags` (
  `id` int(11) NOT NULL,
  `music_entry_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `music_entry_tags`
--

INSERT INTO `music_entry_tags` (`id`, `music_entry_id`, `tag_id`, `created_at`) VALUES
(5, 19, 7, '2025-10-09 23:28:13'),
(6, 19, 1, '2025-10-09 23:28:13'),
(7, 19, 6, '2025-10-09 23:28:13'),
(8, 20, 2, '2025-10-10 08:07:47'),
(9, 20, 7, '2025-10-10 08:07:47'),
(10, 20, 1, '2025-10-10 08:07:47'),
(11, 21, 18, '2025-10-11 22:06:35'),
(12, 21, 22, '2025-10-11 22:06:35'),
(13, 22, 22, '2025-10-11 23:11:42'),
(14, 22, 19, '2025-10-11 23:11:42'),
(32, 32, 83, '2025-10-12 12:27:10'),
(33, 33, 96, '2025-10-12 19:33:00'),
(34, 33, 98, '2025-10-12 19:33:00');

-- --------------------------------------------------------

--
-- Table structure for table `music_notes`
--

CREATE TABLE `music_notes` (
  `id` int(11) NOT NULL,
  `music_entry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `note_text` text NOT NULL,
  `mood` varchar(50) DEFAULT NULL,
  `memory_context` varchar(255) DEFAULT NULL,
  `listening_context` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `music_notes`
--

INSERT INTO `music_notes` (`id`, `music_entry_id`, `user_id`, `note_text`, `mood`, `memory_context`, `listening_context`, `created_at`, `updated_at`) VALUES
(3, 19, 1, 'ssdsd', '', '', '', '2025-10-09 23:05:05', '2025-10-09 23:28:13'),
(4, 20, 1, 'ssds', 'dsds', 'sdsd', 'sdsds', '2025-10-09 23:11:07', '2025-10-10 08:07:47'),
(5, 21, 3, 'comfort song', '', '', '', '2025-10-11 22:06:35', '2025-10-11 22:06:35'),
(6, 22, 3, 'minumulto na ko nang damdamin ko~', '', '', '', '2025-10-11 23:11:18', '2025-10-11 23:11:42'),
(8, 32, 11, 'adsadasdasdasdsadasdsad', '', '', '', '2025-10-12 12:27:10', '2025-10-12 12:27:10'),
(9, 33, 12, 'cyrene', '', '', '', '2025-10-12 19:33:00', '2025-10-12 19:33:00');

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `cover_image_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlists`
--

INSERT INTO `playlists` (`id`, `user_id`, `name`, `description`, `is_public`, `cover_image_url`, `created_at`, `updated_at`) VALUES
(2, 3, 'egeqg', 'dgdgdgs', 0, NULL, '2025-10-11 22:06:55', '2025-10-12 11:23:27'),
(3, 1, 'playlist1', 'sdasdas', 0, NULL, '2025-10-11 23:48:24', '2025-10-12 13:10:51'),
(7, 11, 'nigga', 'asdasdsa', 0, NULL, '2025-10-13 13:32:54', '2025-10-13 13:32:59');

-- --------------------------------------------------------

--
-- Table structure for table `playlist_entries`
--

CREATE TABLE `playlist_entries` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `music_entry_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `added_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist_entries`
--

INSERT INTO `playlist_entries` (`id`, `playlist_id`, `music_entry_id`, `position`, `added_by_user_id`, `created_at`) VALUES
(4, 2, 21, 1, 3, '2025-10-12 11:23:26'),
(5, 2, 22, 2, 3, '2025-10-12 11:23:27'),
(6, 3, 17, 1, 1, '2025-10-12 13:10:50'),
(7, 3, 16, 2, 1, '2025-10-12 13:10:50'),
(8, 3, 23, 3, 1, '2025-10-12 13:10:50'),
(9, 3, 19, 4, 1, '2025-10-12 13:10:51'),
(10, 7, 32, 1, 11, '2025-10-13 13:32:59');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'Music Lockerr', 'string', 'Application name', 1, '2025-09-04 17:18:47', '2025-10-12 13:13:22'),
(2, 'app_version', '1.0.0', 'string', 'Current application version', 1, '2025-09-04 17:18:47', '2025-10-12 13:13:22'),
(3, 'max_music_entries_per_user', '10000', 'integer', 'Maximum music entries per user', 0, '2025-09-04 17:18:47', '2025-10-12 13:13:22'),
(4, 'session_timeout', '3600', 'integer', 'Session timeout in seconds', 0, '2025-09-04 17:18:47', '2025-10-12 13:13:22'),
(5, 'enable_spotify_integration', '1', 'boolean', 'Enable Spotify API integration', 0, '2025-09-04 17:18:47', '2025-10-12 13:13:22'),
(6, 'default_items_per_page', '20', 'integer', 'Default pagination limit', 1, '2025-09-04 17:18:47', '2025-10-12 13:13:22');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(7) DEFAULT '#6c757d',
  `description` text DEFAULT NULL,
  `is_system_tag` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `user_id`, `name`, `color`, `description`, `is_system_tag`, `created_at`, `updated_at`) VALUES
(1, 1, 'Favorites', '#ff6b6b', 'Personal favorite tracks', 1, '2025-09-04 17:18:47', '2025-09-04 17:18:47'),
(2, 1, 'Chill', '#4ecdc4', 'Relaxing and calm music', 1, '2025-09-04 17:18:47', '2025-09-04 17:18:47'),
(3, 1, 'Workout', '#45b7d1', 'High energy tracks for exercise', 1, '2025-09-04 17:18:47', '2025-09-04 17:18:47'),
(4, 1, 'Study', '#96ceb4', 'Focus music for studying', 1, '2025-09-04 17:18:47', '2025-09-04 17:18:47'),
(5, 1, 'Party', '#feca57', 'Upbeat music for social gatherings', 1, '2025-09-04 17:18:47', '2025-09-04 17:18:47'),
(6, 1, 'Nostalgic', '#ff9ff3', 'Music that brings back memories', 1, '2025-09-04 17:18:47', '2025-09-04 17:18:47'),
(7, 1, 'Discover', '#00d4ff', 'Recently discovered tracks', 1, '2025-09-04 17:18:47', '2025-09-04 17:18:47'),
(8, 1, 'Top Rated', '#8a2be2', '5-star personal ratings', 1, '2025-09-04 17:18:47', '2025-09-04 17:18:47'),
(17, 3, 'Favorites', '#ff6b6b', 'Personal favorite tracks', 1, '2025-09-04 18:25:13', '2025-09-04 18:25:13'),
(18, 3, 'Chill', '#4ecdc4', 'Relaxing and calm music', 1, '2025-09-04 18:25:13', '2025-09-04 18:25:13'),
(19, 3, 'Workout', '#45b7d1', 'High energy tracks for exercise', 1, '2025-09-04 18:25:13', '2025-09-04 18:25:13'),
(20, 3, 'Study', '#96ceb4', 'Focus music for studying', 1, '2025-09-04 18:25:13', '2025-09-04 18:25:13'),
(21, 3, 'Party', '#feca57', 'Upbeat music for social gatherings', 1, '2025-09-04 18:25:13', '2025-09-04 18:25:13'),
(22, 3, 'Nostalgic', '#ff9ff3', 'Music that brings back memories', 1, '2025-09-04 18:25:13', '2025-09-04 18:25:13'),
(23, 3, 'Discover', '#00d4ff', 'Recently discovered tracks', 1, '2025-09-04 18:25:13', '2025-09-04 18:25:13'),
(24, 3, 'Top Rated', '#8a2be2', '5-star personal ratings', 1, '2025-09-04 18:25:13', '2025-09-04 18:25:13'),
(41, 6, 'Favorites', '#ff6b6b', 'Personal favorite tracks', 1, '2025-09-05 07:54:34', '2025-09-05 07:54:34'),
(42, 6, 'Chill', '#4ecdc4', 'Relaxing and calm music', 1, '2025-09-05 07:54:34', '2025-09-05 07:54:34'),
(43, 6, 'Workout', '#45b7d1', 'High energy tracks for exercise', 1, '2025-09-05 07:54:34', '2025-09-05 07:54:34'),
(44, 6, 'Study', '#96ceb4', 'Focus music for studying', 1, '2025-09-05 07:54:34', '2025-09-05 07:54:34'),
(45, 6, 'Party', '#feca57', 'Upbeat music for social gatherings', 1, '2025-09-05 07:54:34', '2025-09-05 07:54:34'),
(46, 6, 'Nostalgic', '#ff9ff3', 'Music that brings back memories', 1, '2025-09-05 07:54:34', '2025-09-05 07:54:34'),
(47, 6, 'Discover', '#00d4ff', 'Recently discovered tracks', 1, '2025-09-05 07:54:34', '2025-09-05 07:54:34'),
(48, 6, 'Top Rated', '#8a2be2', '5-star personal ratings', 1, '2025-09-05 07:54:34', '2025-09-05 07:54:34'),
(67, 9, 'Favorites', '#ff6b6b', 'Personal favorite tracks', 1, '2025-10-12 11:07:04', '2025-10-12 11:07:04'),
(68, 9, 'Chill', '#4ecdc4', 'Relaxing and calm music', 1, '2025-10-12 11:07:04', '2025-10-12 11:07:04'),
(69, 9, 'Workout', '#45b7d1', 'High energy tracks for exercise', 1, '2025-10-12 11:07:04', '2025-10-12 11:07:04'),
(70, 9, 'Study', '#96ceb4', 'Focus music for studying', 1, '2025-10-12 11:07:04', '2025-10-12 11:07:04'),
(71, 9, 'Party', '#feca57', 'Upbeat music for social gatherings', 1, '2025-10-12 11:07:04', '2025-10-12 11:07:04'),
(72, 9, 'Nostalgic', '#ff9ff3', 'Music that brings back memories', 1, '2025-10-12 11:07:04', '2025-10-12 11:07:04'),
(73, 9, 'Discover', '#00d4ff', 'Recently discovered tracks', 1, '2025-10-12 11:07:04', '2025-10-12 11:07:04'),
(74, 9, 'Top Rated', '#8a2be2', '5-star personal ratings', 1, '2025-10-12 11:07:04', '2025-10-12 11:07:04'),
(83, 11, 'Favorites', '#ff6b6b', 'Personal favorite tracks', 1, '2025-10-12 12:21:14', '2025-10-12 12:21:14'),
(84, 11, 'Chill', '#4ecdc4', 'Relaxing and calm music', 1, '2025-10-12 12:21:14', '2025-10-12 12:21:14'),
(85, 11, 'Workout', '#45b7d1', 'High energy tracks for exercise', 1, '2025-10-12 12:21:14', '2025-10-12 12:21:14'),
(86, 11, 'Study', '#96ceb4', 'Focus music for studying', 1, '2025-10-12 12:21:14', '2025-10-12 12:21:14'),
(87, 11, 'Party', '#feca57', 'Upbeat music for social gatherings', 1, '2025-10-12 12:21:14', '2025-10-12 12:21:14'),
(88, 11, 'Nostalgic', '#ff9ff3', 'Music that brings back memories', 1, '2025-10-12 12:21:14', '2025-10-12 12:21:14'),
(89, 11, 'Discover', '#00d4ff', 'Recently discovered tracks', 1, '2025-10-12 12:21:14', '2025-10-12 12:21:14'),
(90, 11, 'Top Rated', '#8a2be2', '5-star personal ratings', 1, '2025-10-12 12:21:14', '2025-10-12 12:21:14'),
(91, 12, 'Favorites', '#ff6b6b', 'Personal favorite tracks', 1, '2025-10-12 19:31:37', '2025-10-12 19:31:37'),
(92, 12, 'Chill', '#4ecdc4', 'Relaxing and calm music', 1, '2025-10-12 19:31:37', '2025-10-12 19:31:37'),
(93, 12, 'Workout', '#45b7d1', 'High energy tracks for exercise', 1, '2025-10-12 19:31:37', '2025-10-12 19:31:37'),
(94, 12, 'Study', '#96ceb4', 'Focus music for studying', 1, '2025-10-12 19:31:37', '2025-10-12 19:31:37'),
(95, 12, 'Party', '#feca57', 'Upbeat music for social gatherings', 1, '2025-10-12 19:31:37', '2025-10-12 19:31:37'),
(96, 12, 'Nostalgic', '#ff9ff3', 'Music that brings back memories', 1, '2025-10-12 19:31:37', '2025-10-12 19:31:37'),
(97, 12, 'Discover', '#00d4ff', 'Recently discovered tracks', 1, '2025-10-12 19:31:37', '2025-10-12 19:31:37'),
(98, 12, 'Top Rated', '#8a2be2', '5-star personal ratings', 1, '2025-10-12 19:31:37', '2025-10-12 19:31:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` timestamp NULL DEFAULT NULL,
  `spotify_access_token` text DEFAULT NULL,
  `spotify_refresh_token` text DEFAULT NULL,
  `spotify_token_expires` timestamp NULL DEFAULT NULL,
  `spotify_user_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `email_verified`, `verification_token`, `reset_token`, `reset_token_expires`, `spotify_access_token`, `spotify_refresh_token`, `spotify_token_expires`, `spotify_user_id`, `created_at`, `updated_at`, `last_login`, `status`, `role`) VALUES
(1, 'Roche', 'Plando', 'admin@musiclocker.local', '$2y$10$Q/LetPPPaWnd60XA.VpHo.36e0sKAuuDM2kcASfvU0zx6lirJGbPe', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-04 17:18:47', '2025-10-13 13:45:04', '2025-10-13 13:45:04', 'active', 'admin'),
(3, 'Reynaldo', 'Jr.', 'reynaldogrande9@gmail.com', '$2y$10$11WveYDY/nt7KVSnNWUZH.tMi4VCdXNXu4We45NJOgqiPUsKghhYC', 0, '5c567482054f88096ba1b975636467b6c88877a8def2a0e0dd36d0159cd7190a', 'f1cff0cb95d567b763201d35fa2c1a8e118141e3e4d24b2965b5bbcb4d631b13', '2025-10-09 10:44:00', NULL, NULL, NULL, NULL, '2025-09-04 18:25:13', '2025-10-12 12:25:42', '2025-10-12 12:25:42', 'active', 'user'),
(6, 'Shawn Patrick ', 'Dayanan', 'alexismae69420@gmail.com', '$2y$10$BAvH7PlZIsn/Q8m2tVRYzO8nGedQsy4Ibs6PswsCfpC4W4LX5MLcC', 0, '0d69fe6eb874cfdcbcc0723aa693162c553a10683d3b87741b6ee50f1d855ba0', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-05 07:54:34', '2025-10-11 23:22:29', '2025-09-05 07:54:50', 'active', 'user'),
(9, 'shawn ', 'Everest', 'dayananshawn@gmail.com', '$2y$10$wVHG2/C9t0DhFNLjR3YPXeaR/EEfA2yUg7bF/zhML9lm09SVQ0c2u', 0, '8a908f3e63bead14a6e165d1f1c258bcbd84daae77edd574151730fc4bb6260e', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-12 11:07:04', '2025-10-12 19:41:00', '2025-10-12 19:41:00', 'inactive', 'user'),
(11, 'Reynaldo', 'Grande Jr. II', 'testing@gmail.com', '$2y$10$Yle0MGXEp0zLzpsW6P2wMev6hAoJ5slYOQi3kNNC5pIUhwREliI7K', 0, '0c5b534bdb83640b565c28288dd8fd69922bd734aa565e5c6bc19dd5b6351cfe', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-12 12:21:14', '2025-10-13 13:44:55', '2025-10-13 13:44:06', 'active', 'user'),
(12, 'Louis', 'Grande Jr. II', 'testis@gmail.com', '$2y$10$/H97caXG318HMTOgK0.Yc.b3VyGDc1qz3B7pj2juZFVYDu5s8B9C2', 0, 'b9cc9b3f37cbacbfcd21237c5155ef73795f0f46642fd001664eba1ad2b951bb', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-12 19:31:37', '2025-10-12 19:38:09', '2025-10-12 19:38:09', 'inactive', 'user');

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_music_stats`
-- (See below for the actual view)
--
CREATE TABLE `user_music_stats` (
`user_id` int(11)
,`first_name` varchar(50)
,`last_name` varchar(50)
,`total_entries` bigint(21)
,`five_star_entries` bigint(21)
,`favorite_entries` bigint(21)
,`average_rating` decimal(7,4)
,`unique_artists` bigint(21)
,`unique_genres` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `csrf_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `csrf_token`, `created_at`, `expires_at`, `last_activity`, `is_active`) VALUES
('08c438lp73a18csurmvgq1dhgb', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '89d1fad15f9c965ff6c5d726c9cb5908524c471d7e0f6a4251e763937d72d0f5', '2025-10-12 13:10:08', '2025-10-12 08:10:08', '2025-10-12 13:12:30', 0),
('2sr4qnkf3jbojh6fd63oubj0ml', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '1cfc9d43e8be80b5c2d21946a1e038c97a5642d652e09cd47b8601354358c4ce', '2025-10-12 13:13:07', '2025-10-12 08:13:07', '2025-10-12 19:34:43', 0),
('b8gq4fd2hpa7tqmq39lc0ufkui', 12, '::1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Mobile Safari/537.36', '8ed2305751c754023f2a574874f531fa87221d181f0f1dbeb7d7e24612fe5750', '2025-10-12 19:31:49', '2025-11-11 12:31:49', '2025-10-12 19:34:01', 0),
('dnmnl5ch5uf1nbbtectcvopbm1', 1, '::1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '516f0f8a2e885dbf8efee8ebb6dae3a8c6de09f7b97892bf6cc527f42e00ad6b', '2025-10-13 13:28:26', '2025-11-12 06:28:26', '2025-10-13 13:29:39', 0),
('gsjde5gcojjp3bcdb1j82k1joh', 11, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '79d6ae39fa687172a9b79853a1e069eff40997b68c4c8f237c29e66f27b6601c', '2025-10-13 13:44:06', '2025-10-13 08:44:06', '2025-10-13 13:44:55', 0),
('outbtc5binh4mletphsi3kt60t', 9, '::1', 'Mozilla/5.0 (Linux; Android 13; CPH2237 Build/TP1A.220905.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/140.0.7339.207 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/528.0.0.62.107;]', '7e5076213372349c70c7b6a07115469272e0b1c142ece4919bfcd3aba8967280', '2025-10-12 19:38:57', '2025-10-12 14:38:57', '2025-10-12 19:39:51', 0),
('v1t2c83k7rfmae5cpfj193pivj', 11, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '0b6d06603f0068ec6bffc84ad00534cb6b41b6ad23e78a75cd98d3bddca73f48', '2025-10-13 13:32:33', '2025-10-13 08:32:33', '2025-10-13 13:35:48', 0);

--
-- Triggers `user_sessions`
--
DELIMITER $$
CREATE TRIGGER `update_user_last_activity` AFTER UPDATE ON `user_sessions` FOR EACH ROW BEGIN
    IF NEW.last_activity > OLD.last_activity THEN
        UPDATE users SET updated_at = NOW() WHERE id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure for view `user_music_stats`
--
DROP TABLE IF EXISTS `user_music_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_music_stats`  AS SELECT `u`.`id` AS `user_id`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, count(`me`.`id`) AS `total_entries`, count(case when `me`.`personal_rating` = 5 then 1 end) AS `five_star_entries`, count(case when `me`.`is_favorite` = 1 then 1 end) AS `favorite_entries`, avg(`me`.`personal_rating`) AS `average_rating`, count(distinct `me`.`artist`) AS `unique_artists`, count(distinct `me`.`genre`) AS `unique_genres` FROM (`users` `u` left join `music_entries` `me` on(`u`.`id` = `me`.`user_id`)) WHERE `u`.`status` = 'active' GROUP BY `u`.`id`, `u`.`first_name`, `u`.`last_name` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_target_type` (`target_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_activity_log_recent` (`user_id`,`created_at`);

--
-- Indexes for table `music_entries`
--
ALTER TABLE `music_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_spotify` (`user_id`,`spotify_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_artist` (`artist`),
  ADD KEY `idx_genre` (`genre`),
  ADD KEY `idx_title` (`title`),
  ADD KEY `idx_date_added` (`date_added`),
  ADD KEY `idx_personal_rating` (`personal_rating`),
  ADD KEY `idx_is_favorite` (`is_favorite`),
  ADD KEY `idx_music_entries_search` (`title`,`artist`,`album`),
  ADD KEY `idx_music_entries_date_rating` (`date_added`,`personal_rating`);

--
-- Indexes for table `music_entry_tags`
--
ALTER TABLE `music_entry_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_entry_tag` (`music_entry_id`,`tag_id`),
  ADD KEY `idx_music_entry_id` (`music_entry_id`),
  ADD KEY `idx_tag_id` (`tag_id`);

--
-- Indexes for table `music_notes`
--
ALTER TABLE `music_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_music_entry_id` (`music_entry_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_mood` (`mood`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_public` (`is_public`),
  ADD KEY `idx_updated_at` (`updated_at`);

--
-- Indexes for table `playlist_entries`
--
ALTER TABLE `playlist_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_playlist_position` (`playlist_id`,`position`),
  ADD KEY `added_by_user_id` (`added_by_user_id`),
  ADD KEY `idx_playlist_id` (`playlist_id`),
  ADD KEY `idx_music_entry_id` (`music_entry_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `idx_is_public` (`is_public`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_tag` (`user_id`,`name`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_system_tag` (`is_system_tag`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_spotify_user_id` (`spotify_user_id`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT for table `music_entries`
--
ALTER TABLE `music_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `music_entry_tags`
--
ALTER TABLE `music_entry_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `music_notes`
--
ALTER TABLE `music_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `playlist_entries`
--
ALTER TABLE `playlist_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `music_entries`
--
ALTER TABLE `music_entries`
  ADD CONSTRAINT `music_entries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `music_entry_tags`
--
ALTER TABLE `music_entry_tags`
  ADD CONSTRAINT `music_entry_tags_ibfk_1` FOREIGN KEY (`music_entry_id`) REFERENCES `music_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `music_entry_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `music_notes`
--
ALTER TABLE `music_notes`
  ADD CONSTRAINT `music_notes_ibfk_1` FOREIGN KEY (`music_entry_id`) REFERENCES `music_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `music_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlists`
--
ALTER TABLE `playlists`
  ADD CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `playlist_entries`
--
ALTER TABLE `playlist_entries`
  ADD CONSTRAINT `playlist_entries_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `playlist_entries_ibfk_2` FOREIGN KEY (`music_entry_id`) REFERENCES `music_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `playlist_entries_ibfk_3` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `tags_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `cleanup_expired_sessions` ON SCHEDULE EVERY 1 DAY STARTS '2025-08-28 20:29:52' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM user_sessions WHERE expires_at < NOW() OR is_active = FALSE$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
