-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.3.9-MariaDB-log - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for shopu
DROP DATABASE IF EXISTS `shopu`;
CREATE DATABASE IF NOT EXISTS `shopu` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `shopu`;

-- Dumping structure for table shopu.access_token
CREATE TABLE IF NOT EXISTS `access_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Dumping data for table shopu.access_token: ~10 rows (approximately)
/*!40000 ALTER TABLE `access_token` DISABLE KEYS */;
INSERT INTO `access_token` (`id`, `user_id`, `token`, `expires_at`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 1, 'INFERNO4209887463772', '2018-11-22 01:26:14', '2018-11-21 16:26:36', 1, '2019-01-06 11:06:17', 1, NULL, NULL),
	(2, 1, '90e2477f55b67eb7c3856cd3be4a3f5210650e4f524ae3ccfe0a836eb725507f', '2019-01-07 08:59:32', '2019-01-06 08:59:32', 1, '2019-01-06 11:10:37', 1, NULL, NULL),
	(3, 1, '70436c5589d04aaf4dfea37ada8a02499b89a6eb48d5fb3db7992a6867f426c4', '2019-01-07 11:59:11', '2019-01-06 11:59:11', 1, '2019-01-06 12:10:31', 1, '2019-01-06 12:10:31', 1),
	(4, 3, '2d0109c7f4c7faabdeb5ebcd440c705b23a474ddef525dc88bb4fccdc50d3c5e', '2019-01-08 17:58:45', '2019-01-07 17:58:45', 3, '2019-01-07 17:58:45', 3, NULL, NULL),
	(5, 1, 'cb81863c55c0a0ebedbfde5781c8823e6da550873507df915f67da4d2cce445c', '2019-01-08 18:03:33', '2019-01-07 18:03:33', 1, '2019-01-07 18:03:33', 1, NULL, NULL),
	(6, 2, 'e69e71118b18e60f5beb4a7c437914b00e53385d2e40b6a3ee4582f08078d783', '2019-01-08 18:03:40', '2019-01-07 18:03:40', 2, '2019-01-07 18:03:40', 2, NULL, NULL),
	(7, 3, '2e99b577810e7b65299676955c61df24d1954f2ae982cbbfef598580e8fd8211', '2019-01-08 18:03:55', '2019-01-07 18:03:55', 3, '2019-01-07 18:03:55', 3, NULL, NULL),
	(8, 5, '145c0273eb115dd86d2520b99da6098c0e92829b62fa72c1806c6908bb2caf32', '2019-01-09 19:02:02', '2019-01-07 19:02:02', 5, '2019-01-08 17:45:18', 5, NULL, NULL),
	(9, 1, '4ed67b0f6fb1d13c3774896cf09a31e1cd21b010f73f87e3793f0060b60f5f1c', '2019-01-12 18:17:29', '2019-01-11 18:17:29', 1, '2019-01-11 18:17:29', 1, NULL, NULL),
	(10, 6, '10d063d2ccc27ffb1e5a11221038616c6b035493aa306c067c8f2c2fa6f0ec7b', '2019-01-12 18:41:53', '2019-01-11 18:41:53', 6, '2019-01-11 18:41:53', 6, NULL, NULL);
/*!40000 ALTER TABLE `access_token` ENABLE KEYS */;

-- Dumping structure for table shopu.blog
CREATE TABLE IF NOT EXISTS `blog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_en` varchar(255) NOT NULL,
  `title_tc` varchar(255) DEFAULT NULL,
  `title_sc` varchar(255) DEFAULT NULL,
  `content_en` text NOT NULL,
  `content_tc` text DEFAULT NULL,
  `content_sc` text DEFAULT NULL,
  `is_top` tinyint(2) NOT NULL DEFAULT 0,
  `shop_id` int(11) NOT NULL,
  `date_publish_start` datetime DEFAULT NULL,
  `date_publish_end` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dumping data for table shopu.blog: ~5 rows (approximately)
/*!40000 ALTER TABLE `blog` DISABLE KEYS */;
INSERT INTO `blog` (`id`, `title_en`, `title_tc`, `title_sc`, `content_en`, `content_tc`, `content_sc`, `is_top`, `shop_id`, `date_publish_start`, `date_publish_end`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 'Soon To Open', '很快就要開了', '很快就要开了', 'We are happy to announce the opening of our toy collections and action figures shop!', '我們很高興地宣布開設我們的玩具收藏品和動作人物店！', '我们很高兴地宣布开设我们的玩具收藏品和动作人物店！', 0, 3, NULL, NULL, '2018-12-09 04:09:12', 1, '2018-12-09 04:09:12', 1, NULL, NULL),
	(2, 'TNA Action Figures PROMO!', 'TNA行動人物PROMO！', 'TNA行动人物PROMO！', 'Check out for news update.', 'Chákàn xīnwén gēngxīn.', '查看新闻更新。', 0, 3, NULL, NULL, '2018-12-09 04:13:23', 1, '2018-12-09 04:13:23', 1, NULL, NULL),
	(3, 'WWE Action Figures PROMO!', 'WWE行動人物宣傳！', 'WWE行动人物宣传！', 'Check out for news update.', 'Chákàn xīnwén gēngxīn.', '查看新闻更新。', 0, 3, NULL, NULL, '2018-12-09 04:13:57', 1, '2018-12-09 04:13:57', 1, NULL, NULL),
	(5, '1231232', '1231', '3123132', '1231', '3213', '123213', 1, 3, '2018-01-01 00:00:00', '2020-01-01 00:00:00', '2018-12-23 09:36:25', 1, '2018-12-23 16:45:22', 1, NULL, NULL),
	(6, '1231', '31312312', '1231231', '132131', '123213', '13213', 0, 1, NULL, NULL, '2018-12-23 09:37:08', 1, '2018-12-23 09:39:46', 1, '2018-12-23 09:39:46', 1);
/*!40000 ALTER TABLE `blog` ENABLE KEYS */;

-- Dumping structure for table shopu.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.category: ~50 rows (approximately)
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` (`id`, `entity`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 1, 'Electronics', '2018-11-11 08:39:52', 1, '2018-11-11 08:39:52', 1, NULL, NULL),
	(2, 1, 'Computers', '2018-11-11 08:39:52', 1, '2018-11-11 08:39:52', 1, NULL, NULL),
	(4, 1, 'Laptops', '2018-11-11 08:39:52', 1, '2018-11-11 08:39:52', 1, NULL, NULL),
	(5, 1, 'Gadgets', '2018-11-11 08:39:52', 1, '2018-11-11 08:39:52', 1, NULL, NULL),
	(6, 1, 'Cellphones', '2018-11-11 08:39:52', 1, '2018-11-11 08:39:52', 1, NULL, NULL),
	(30, 1, 'Weaponry', '2018-11-12 08:02:32', 1, '2018-12-02 09:06:49', 1, '2018-12-02 09:06:48', 1),
	(32, 1, 'Explosives', '2018-11-12 09:16:13', 1, '2018-12-02 09:06:23', 1, '2018-12-02 09:06:22', 1),
	(33, 1, 'Firearms', '2018-11-12 09:16:50', 1, '2018-12-02 09:06:44', 1, '2018-12-02 09:06:44', 1),
	(34, 1, 'Artillery', '2018-11-12 09:16:56', 1, '2018-12-02 08:54:57', 1, '2018-12-02 08:54:56', 1),
	(35, 1, 'Flash Bombs', '2018-11-12 09:17:08', 1, '2018-12-02 08:54:17', 1, '2018-12-02 08:54:16', 1),
	(36, 1, 'Rifles', '2018-11-12 09:17:16', 1, '2018-12-02 08:54:31', 1, '2018-12-02 08:54:31', 1),
	(40, 1, 'High Powered', '2018-11-15 11:24:44', 1, '2018-12-02 08:55:15', 1, '2018-12-02 08:55:15', 1),
	(41, 1, 'Cables', '2018-11-15 12:28:45', 1, '2018-11-15 16:30:07', 1, NULL, NULL),
	(42, 1, 'Armor-piercing Rounds', '2018-11-15 12:31:07', 1, '2018-12-02 08:54:40', 1, '2018-12-02 08:54:39', 1),
	(43, 4, 'Black Market', '2018-11-15 12:32:01', 1, '2018-12-02 08:08:09', 1, '2018-12-02 08:08:08', 1),
	(44, 4, 'Weapons Dealer', '2018-11-15 12:32:17', 1, '2018-12-02 08:08:18', 1, '2018-12-02 08:08:18', 1),
	(45, 4, 'Nuclear Facility', '2018-11-15 12:32:50', 1, '2018-12-02 08:08:23', 1, '2018-12-02 08:08:22', 1),
	(46, 4, 'Guitar Shop', '2018-11-15 12:32:51', 1, '2018-11-16 07:39:38', 1, NULL, NULL),
	(49, 4, 'Mall', '2018-11-15 12:35:24', 1, '2018-11-16 07:37:33', 1, NULL, NULL),
	(52, 1, 'Kevlars', '2018-11-16 04:25:23', 1, '2018-12-02 08:09:54', 1, '2018-12-02 08:09:54', 1),
	(69, 1, 'Apparel', '2018-11-16 04:50:37', 1, '2018-11-16 04:50:37', 1, NULL, NULL),
	(71, 1, 'RPGs', '2018-11-16 04:51:01', 1, '2018-12-02 09:06:35', 1, '2018-12-02 09:06:34', 1),
	(77, 1, 'Appliances', '2018-11-16 05:43:31', 1, '2018-11-16 05:52:26', 1, NULL, NULL),
	(80, 1, 'Home Appliances', '2018-11-16 05:51:42', 1, '2018-11-16 05:52:38', 1, NULL, NULL),
	(81, 1, 'Kitchen Appliances', '2018-11-16 05:53:11', 1, '2018-12-07 17:12:51', 1, '2018-12-07 17:12:51', 1),
	(82, 1, 'Grilling', '2018-11-16 05:53:54', 1, '2018-12-07 17:12:43', 1, '2018-12-07 17:12:43', 1),
	(87, 4, 'Wholesaler', '2018-11-16 07:29:49', 1, '2018-11-16 07:29:49', 1, NULL, NULL),
	(88, 4, 'Retailer', '2018-11-16 07:29:59', 1, '2018-11-16 07:29:59', 1, NULL, NULL),
	(92, 4, 'Wines & Booze', '2018-11-23 15:41:16', 1, '2018-11-27 17:19:50', 1, NULL, NULL),
	(93, 1, 'Electric Grillers', '2018-11-24 06:30:40', 1, '2018-12-07 17:12:23', 1, '2018-12-07 17:12:23', 1),
	(94, 1, 'Manual Grillers', '2018-11-24 06:32:37', 1, '2018-12-07 17:12:28', 1, '2018-12-07 17:12:28', 1),
	(97, 1, 'Armors', '2018-11-27 15:42:48', 1, '2018-11-27 15:52:57', 1, '2018-11-27 15:52:56', 1),
	(98, 1, 'Shotguns', '2018-11-27 15:48:11', 1, '2018-11-27 16:55:11', 1, '2018-11-27 16:55:11', 1),
	(99, 4, 'Undergroung', '2018-11-27 16:10:26', 1, '2018-11-27 16:12:08', 1, '2018-11-27 16:12:08', 1),
	(100, 4, 'Biological Weapons Facility', '2018-11-27 16:47:24', 1, '2018-11-27 16:47:53', 1, '2018-11-27 16:47:53', 1),
	(101, 1, 'Mufflers', '2018-11-27 16:54:39', 1, '2018-12-02 08:09:31', 1, '2018-12-02 08:09:30', 1),
	(102, 4, 'Buy & Sell', '2018-12-02 16:38:05', 1, '2018-12-02 16:38:05', 1, NULL, NULL),
	(103, 1, 'School Supplies', '2018-12-07 15:49:07', 1, '2018-12-07 15:59:31', 1, '2018-12-07 23:59:28', 1),
	(104, 1, 'School Supplies', '2018-12-07 15:51:04', 1, '2018-12-07 15:51:58', 1, '2018-12-07 15:51:57', 1),
	(105, 1, 'Office Supplies', '2018-12-07 16:03:46', 1, '2018-12-07 16:03:46', 1, NULL, NULL),
	(106, 1, 'Office Hardware', '2018-12-07 16:04:07', 1, '2018-12-07 16:07:32', 1, '2018-12-07 16:07:32', 1),
	(107, 1, 'Musical Instruments', '2018-12-07 16:05:14', 1, '2018-12-07 16:08:14', 1, NULL, NULL),
	(108, 1, 'Acoustic Guitars', '2018-12-07 16:05:31', 1, '2018-12-07 16:05:31', 1, NULL, NULL),
	(109, 1, 'Electric Guitars', '2018-12-07 16:05:43', 1, '2018-12-07 16:05:43', 1, NULL, NULL),
	(110, 4, 'Street Shop', '2018-12-07 16:10:53', 1, '2018-12-07 16:11:44', 1, '2018-12-07 16:11:44', 1),
	(111, 4, 'SURPLUS', '2018-12-07 16:11:05', 1, '2018-12-07 16:12:19', 1, NULL, NULL),
	(112, 6, 'Announcement', '2018-12-09 04:24:23', 1, '2018-12-09 04:24:27', 1, NULL, NULL),
	(114, 6, 'Promotional', '2018-12-09 05:40:00', 1, '2018-12-09 05:40:00', 1, NULL, NULL),
	(115, 6, 'Sale', '2018-12-09 05:40:10', 1, '2018-12-09 05:42:52', 1, NULL, NULL),
	(116, 1, 'Books', '2019-01-06 09:54:00', 1, '2019-01-06 09:57:43', 1, '2019-01-06 09:57:43', 1);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;

-- Dumping structure for table shopu.category_level
CREATE TABLE IF NOT EXISTS `category_level` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `parent_category_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.category_level: ~36 rows (approximately)
/*!40000 ALTER TABLE `category_level` DISABLE KEYS */;
INSERT INTO `category_level` (`id`, `category_id`, `parent_category_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(2, 4, 2, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(3, 5, 1, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(4, 6, 5, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(8, 2, 0, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(14, 30, 0, '2018-11-26 15:27:43', 1, '2018-12-02 09:06:49', 1, '2018-12-02 09:06:48', 1),
	(16, 32, 30, '2018-11-26 15:27:43', 1, '2018-12-02 09:06:23', 1, '2018-12-02 09:06:22', 1),
	(17, 33, 30, '2018-11-26 15:27:43', 1, '2018-12-02 09:06:44', 1, '2018-12-02 09:06:44', 1),
	(18, 34, 30, '2018-11-26 15:27:43', 1, '2018-12-02 08:54:57', 1, '2018-12-02 08:54:56', 1),
	(19, 35, 32, '2018-11-26 15:27:43', 1, '2018-12-02 08:54:17', 1, '2018-12-02 08:54:16', 1),
	(21, 36, 33, '2018-11-26 15:27:43', 1, '2018-12-02 08:54:31', 1, '2018-12-02 08:54:31', 1),
	(23, 1, 0, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(26, 40, 30, '2018-11-26 15:27:43', 1, '2018-12-02 08:55:15', 1, '2018-12-02 08:55:15', 1),
	(27, 41, 0, '2018-11-26 15:27:43', 1, '2018-12-02 08:10:27', 1, NULL, NULL),
	(28, 42, 33, '2018-11-26 15:27:43', 1, '2018-12-02 08:54:40', 1, '2018-12-02 08:54:39', 1),
	(29, 43, 0, '2018-11-26 15:27:43', 1, '2018-12-02 08:08:09', 1, '2018-12-02 08:08:08', 1),
	(30, 44, 0, '2018-11-26 15:27:43', 1, '2018-12-02 08:08:18', 1, '2018-12-02 08:08:18', 1),
	(31, 45, 0, '2018-11-26 15:27:43', 1, '2018-12-02 08:08:23', 1, '2018-12-02 08:08:22', 1),
	(32, 46, 0, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(33, 49, 0, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(36, 52, 0, '2018-11-26 15:27:43', 1, '2018-12-02 08:09:54', 1, '2018-12-02 08:09:54', 1),
	(39, 69, 55, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(41, 71, 33, '2018-11-26 15:27:43', 1, '2018-12-02 09:06:35', 1, '2018-12-02 09:06:34', 1),
	(42, 77, 0, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(43, 80, 77, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(44, 81, 80, '2018-11-26 15:27:43', 1, '2018-12-07 17:12:52', 1, '2018-12-07 17:12:51', 1),
	(45, 82, 81, '2018-11-26 15:27:43', 1, '2018-12-07 17:12:43', 1, '2018-12-07 17:12:43', 1),
	(46, 93, 82, '2018-11-26 15:27:43', 1, '2018-12-07 17:12:23', 1, '2018-12-07 17:12:23', 1),
	(47, 94, 82, '2018-11-26 15:27:43', 1, '2018-12-07 17:12:28', 1, '2018-12-07 17:12:28', 1),
	(48, 97, 0, '2018-11-27 15:42:48', 1, '2018-11-27 15:52:57', 1, '2018-11-27 15:52:56', 1),
	(49, 98, 0, '2018-11-27 15:48:11', 1, '2018-11-27 16:55:11', 1, '2018-11-27 16:55:11', 1),
	(50, 101, 0, '2018-11-27 16:54:40', 1, '2018-12-02 08:09:31', 1, '2018-12-02 08:09:30', 1),
	(51, 104, 0, '2018-12-07 15:51:04', 1, '2018-12-07 15:51:58', 1, '2018-12-07 15:51:57', 1),
	(52, 105, 0, '2018-12-07 16:03:47', 1, '2018-12-07 16:03:47', 1, NULL, NULL),
	(53, 106, 0, '2018-12-07 16:04:09', 1, '2018-12-07 16:07:35', 1, '2018-12-07 16:07:32', 1),
	(54, 107, 0, '2018-12-07 16:05:16', 1, '2018-12-07 17:10:50', 1, NULL, NULL),
	(55, 108, 107, '2018-12-07 16:05:34', 1, '2018-12-07 16:05:34', 1, NULL, NULL),
	(56, 109, 107, '2018-12-07 16:05:49', 1, '2018-12-07 16:05:49', 1, NULL, NULL),
	(57, 116, 0, '2019-01-06 09:54:01', 1, '2019-01-06 09:57:43', 1, '2019-01-06 09:57:43', 1);
/*!40000 ALTER TABLE `category_level` ENABLE KEYS */;

-- Dumping structure for table shopu.category_map
CREATE TABLE IF NOT EXISTS `category_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.category_map: ~43 rows (approximately)
/*!40000 ALTER TABLE `category_map` DISABLE KEYS */;
INSERT INTO `category_map` (`id`, `entity`, `entity_id`, `category_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 1, 1, 6, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(2, 1, 2, 6, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(3, 1, 3, 4, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(18, 1, 29, 1, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(21, 1, 32, 1, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(24, 1, 35, 1, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(25, 1, 36, 71, '2018-11-26 15:27:43', 1, '2018-12-02 09:04:09', 1, '2018-12-02 09:04:09', 1),
	(27, 1, 38, 71, '2018-11-26 15:27:43', 1, '2018-12-02 09:04:16', 1, '2018-12-02 09:04:16', 1),
	(29, 1, 40, 33, '2018-11-26 15:27:43', 1, '2018-12-02 09:04:24', 1, '2018-12-02 09:04:24', 1),
	(30, 1, 41, 33, '2018-11-26 15:27:43', 1, '2018-12-02 09:04:26', 1, '2018-12-02 09:04:26', 1),
	(53, 1, 64, 32, '2018-11-26 15:27:43', 1, '2018-12-02 09:04:31', 1, '2018-12-02 09:04:31', 1),
	(54, 1, 65, 32, '2018-11-26 15:27:43', 1, '2018-11-27 16:24:02', 1, '2018-11-27 16:24:01', 1),
	(56, 1, 67, 1, '2018-11-27 17:46:00', 1, '2018-11-27 17:49:42', 1, '2018-11-27 17:49:42', 1),
	(57, 1, 7, 32, '2018-11-29 18:02:25', 1, '2018-11-29 18:05:41', 1, '2018-11-29 18:05:41', 1),
	(58, 1, 7, 32, '2018-11-29 18:02:38', 1, '2018-11-29 18:02:38', 1, '2018-11-29 18:02:38', 1),
	(59, 1, 7, 34, '2018-11-29 18:06:05', 1, '2018-11-29 18:06:21', 1, '2018-11-29 18:06:21', 1),
	(60, 1, 7, 34, '2018-11-29 18:06:21', 1, '2018-11-29 18:06:21', 1, '2018-11-29 18:06:21', 1),
	(61, 1, 7, 32, '2018-11-29 18:07:36', 1, '2018-11-29 18:07:52', 1, '2018-11-29 18:07:52', 1),
	(62, 1, 7, 32, '2018-11-29 18:07:52', 1, '2018-11-29 18:07:52', 1, '2018-11-29 18:07:52', 1),
	(63, 1, 7, 34, '2018-11-29 18:25:26', 1, '2018-11-29 18:25:37', 1, '2018-11-29 18:25:37', 1),
	(64, 1, 7, 32, '2018-11-29 18:26:36', 1, '2018-11-29 18:26:52', 1, '2018-11-29 18:26:52', 1),
	(65, 1, 7, 30, '2018-11-29 18:26:52', 1, '2018-11-29 18:27:38', 1, '2018-11-29 18:27:38', 1),
	(66, 1, 7, 32, '2018-11-29 18:27:38', 1, '2018-11-29 18:27:46', 1, '2018-11-29 18:27:46', 1),
	(67, 1, 7, 32, '2018-11-29 18:27:46', 1, '2018-12-02 09:03:52', 1, '2018-12-02 09:03:52', 1),
	(68, 1, 68, 6, '2018-12-02 04:16:52', 1, '2018-12-02 04:16:52', 1, NULL, NULL),
	(69, 1, 69, 6, '2018-12-02 04:27:08', 1, '2018-12-02 04:27:08', 1, NULL, NULL),
	(70, 4, 1, 46, '2018-12-02 14:44:42', 1, '2018-12-02 14:45:16', 1, NULL, NULL),
	(71, 1, 70, 1, '2018-12-02 16:25:35', 1, '2018-12-02 16:25:35', 1, NULL, NULL),
	(72, 4, 2, 102, '2018-12-02 16:39:59', 1, '2018-12-02 17:02:45', 1, '2018-12-02 17:02:45', 1),
	(73, 4, 3, 102, '2018-12-02 16:42:58', 1, '2018-12-02 16:42:58', 1, NULL, NULL),
	(74, 4, 2, 49, '2018-12-02 16:56:01', 1, '2018-12-02 16:56:01', 1, NULL, NULL),
	(75, 4, 4, 46, '2018-12-07 16:25:27', 1, '2018-12-07 16:28:51', 1, '2018-12-07 16:28:51', 1),
	(76, 6, 1, 112, '2018-12-09 05:45:24', 1, '2019-01-04 10:19:03', 1, '2019-01-04 10:19:02', 1),
	(77, 6, 2, 114, '2018-12-22 10:29:02', 1, '2018-12-22 10:29:15', 1, NULL, NULL),
	(78, 6, 3, 115, '2018-12-22 10:29:12', 1, '2018-12-22 10:29:16', 1, NULL, NULL),
	(80, 6, 5, 112, '2018-12-23 09:36:25', 1, '2018-12-23 16:42:28', 1, '2018-12-23 16:42:28', 1),
	(81, 6, 6, 112, '2018-12-23 09:37:08', 1, '2018-12-23 09:39:46', 1, '2018-12-23 09:39:46', 1),
	(82, 6, 5, 112, '2018-12-23 16:42:28', 1, '2018-12-23 16:43:45', 1, '2018-12-23 16:43:45', 1),
	(83, 6, 5, 114, '2018-12-23 16:43:45', 1, '2018-12-23 16:45:52', 1, '2018-12-23 16:45:52', 1),
	(84, 6, 5, 112, '2018-12-23 16:45:52', 1, '2018-12-23 16:45:52', 1, NULL, NULL),
	(85, 6, 1, 114, '2019-01-04 10:19:03', 1, '2019-01-04 10:19:03', 1, NULL, NULL),
	(86, 4, 5, 49, '2019-01-08 17:08:44', 9, '2019-01-08 17:08:44', 9, NULL, NULL),
	(87, 4, 6, 49, '2019-01-08 17:47:43', 13, '2019-01-08 17:47:43', 13, NULL, NULL);
/*!40000 ALTER TABLE `category_map` ENABLE KEYS */;

-- Dumping structure for table shopu.color
CREATE TABLE IF NOT EXISTS `color` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.color: ~5 rows (approximately)
/*!40000 ALTER TABLE `color` DISABLE KEYS */;
INSERT INTO `color` (`id`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 'White', '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(2, 'Gray', '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(3, 'Silver', '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(4, 'Black', '2018-11-27 17:06:18', 1, '2018-11-27 17:06:46', 1, NULL, NULL),
	(5, 'Red', '2018-11-27 17:06:49', 1, '2018-11-27 17:06:54', 1, NULL, NULL);
/*!40000 ALTER TABLE `color` ENABLE KEYS */;

-- Dumping structure for table shopu.comment
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dumping data for table shopu.comment: ~7 rows (approximately)
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` (`id`, `entity`, `entity_id`, `content`, `user_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 4, 1, 'Hope to have more varieties', 1101, '2018-12-02 15:49:06', 1, '2018-12-02 15:49:06', 1, NULL, NULL),
	(2, 4, 1, 'You need to check this place out.', 1100, '2018-12-02 15:49:40', 1, '2018-12-02 15:49:40', 1, NULL, NULL),
	(3, 4, 1, 'There\'s a lot of guitars to choose from...', 1100, '2018-12-02 15:50:02', 1, '2018-12-02 15:50:47', 1, '2018-12-02 15:50:47', 1),
	(4, 4, 1, 'There\'s a lot of guitars to choose from...', 1100, '2018-12-02 15:50:29', 1, '2018-12-02 15:50:29', 1, NULL, NULL),
	(5, 4, 3, 'Do you have WWE Action Figures?', 3501, '2018-12-08 02:58:53', 1, '2018-12-08 02:58:53', 1, NULL, NULL),
	(6, 4, 3, 'Do you have WWE Action Figures?', 3501, '2018-12-08 05:39:56', 1, '2018-12-08 05:45:24', 1, '2018-12-08 05:45:24', 1),
	(7, 6, 1, 'Awesome!!', 1210, '2018-12-09 06:59:46', 1, '2018-12-09 06:59:46', 1, NULL, NULL),
	(8, 6, 1, 'Nice!!', 1274, '2018-12-09 07:00:42', 1, '2018-12-09 07:00:42', 1, NULL, NULL);
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;

-- Dumping structure for table shopu.entity
CREATE TABLE IF NOT EXISTS `entity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) unsigned DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) unsigned DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.entity: ~7 rows (approximately)
/*!40000 ALTER TABLE `entity` DISABLE KEYS */;
INSERT INTO `entity` (`id`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 'product', '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(2, 'image', '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(3, 'category', '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(4, 'shop', '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(5, 'comment', '2018-12-08 02:32:27', 1, '2018-12-08 02:32:32', 1, NULL, NULL),
	(6, 'blog', '2018-12-08 07:23:40', 1, '2019-01-11 19:13:33', 1, NULL, NULL),
	(7, 'user', '2019-01-06 05:30:47', 1, '2019-01-06 05:30:51', 1, NULL, NULL);
/*!40000 ALTER TABLE `entity` ENABLE KEYS */;

-- Dumping structure for table shopu.following
CREATE TABLE IF NOT EXISTS `following` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.following: ~48 rows (approximately)
/*!40000 ALTER TABLE `following` DISABLE KEYS */;
INSERT INTO `following` (`id`, `entity`, `entity_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 1, 2, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(2, 2, 1, '2018-11-26 15:27:43', 1, '2019-01-11 21:09:44', 1, '2019-01-12 05:09:40', 1),
	(3, 2, 1, '2018-11-26 15:27:43', 1, '2019-01-11 21:09:45', 1, '2019-01-12 05:09:42', 1),
	(4, 2, 1, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(5, 1, 35, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(6, 1, 35, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(7, 1, 35, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(8, 1, 35, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(9, 1, 35, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(10, 1, 35, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(11, 1, 35, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(12, 1, 35, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(13, 1, 35, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(14, 1, 29, '2018-11-26 15:27:43', 1, '2018-11-27 17:28:27', 1, '2018-11-28 01:28:23', 1),
	(16, 1, 29, '2018-11-26 15:27:43', 1, '2018-11-27 17:28:26', 1, '2018-11-28 01:28:22', 1),
	(17, 1, 29, '2018-11-26 15:27:43', 1, '2018-11-27 17:28:25', 1, '2018-11-28 01:28:21', 1),
	(18, 1, 29, '2018-11-26 15:27:43', 1, '2018-11-27 17:28:16', 1, '2018-11-28 01:28:12', 1),
	(19, 1, 32, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(21, 2, 2, '2018-11-26 15:27:43', 1, '2018-11-27 15:17:40', 1, '2018-11-27 15:17:40', 1),
	(25, 1, 65, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(26, 1, 65, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(27, 1, 65, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(28, 1, 65, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(29, 1, 65, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(30, 1, 65, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(31, 1, 65, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(32, 1, 65, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(33, 1, 1, '2018-11-27 14:56:44', 1, '2018-11-27 14:57:41', 1, NULL, NULL),
	(35, 2, 7, '2018-11-27 15:26:16', 1, '2018-11-27 15:26:16', 1, NULL, NULL),
	(36, 2, 8, '2018-11-27 15:26:24', 1, '2018-11-27 15:26:24', 1, NULL, NULL),
	(37, 2, 9, '2018-11-27 15:26:25', 1, '2018-11-27 15:27:02', 1, '2018-11-27 15:27:02', 1),
	(38, 2, 12, '2018-11-27 16:28:47', 1, '2018-11-27 16:29:13', 1, '2018-11-27 16:29:13', 1),
	(39, 2, 12, '2018-11-27 17:28:58', 1, '2018-11-27 17:29:14', 1, '2018-11-27 17:29:14', 1),
	(40, 1, 6, '2018-11-30 13:29:28', 1, '2018-11-30 13:33:50', 1, NULL, NULL),
	(41, 4, 1, '2018-12-02 15:01:47', 1, '2018-12-02 15:01:47', 1, NULL, NULL),
	(42, 4, 1, '2018-12-02 15:01:54', 1, '2018-12-02 15:01:54', 1, NULL, NULL),
	(43, 4, 1, '2018-12-02 15:01:56', 1, '2018-12-02 15:01:56', 1, NULL, NULL),
	(44, 4, 1, '2018-12-02 15:01:58', 1, '2018-12-02 15:01:58', 1, NULL, NULL),
	(45, 4, 1, '2018-12-02 15:01:59', 1, '2018-12-02 15:01:59', 1, NULL, NULL),
	(46, 4, 1, '2018-12-02 15:02:01', 1, '2018-12-02 15:02:01', 1, NULL, NULL),
	(47, 4, 1, '2018-12-02 15:02:03', 1, '2018-12-02 15:02:03', 1, NULL, NULL),
	(48, 4, 1, '2018-12-02 15:02:05', 1, '2018-12-02 15:02:05', 1, NULL, NULL),
	(49, 4, 1, '2018-12-02 15:02:06', 1, '2018-12-02 15:02:06', 1, NULL, NULL),
	(50, 4, 1, '2018-12-02 15:02:08', 1, '2018-12-02 15:02:08', 1, NULL, NULL),
	(51, 4, 1, '2018-12-02 15:03:51', 1, '2018-12-02 15:03:51', 1, NULL, NULL),
	(52, 4, 1, '2018-12-02 15:03:53', 1, '2018-12-02 15:03:53', 1, NULL, NULL),
	(53, 4, 1, '2018-12-02 15:03:56', 1, '2018-12-02 15:03:56', 1, NULL, NULL),
	(54, 4, 1, '2018-12-02 15:03:59', 1, '2018-12-02 15:03:59', 1, NULL, NULL),
	(55, 1, 1, '2019-01-11 20:33:11', 6, '2019-01-11 20:47:06', 6, '2019-01-11 20:47:06', 6),
	(56, 1, 2, '2019-01-11 20:43:45', 6, '2019-01-11 20:47:02', 6, '2019-01-11 20:47:02', 6),
	(57, 2, 1, '2019-01-11 20:47:15', 6, '2019-01-11 20:54:16', 6, '2019-01-11 20:54:16', 6),
	(58, 1, 1, '2019-01-11 20:50:30', 6, '2019-01-11 20:50:30', 6, NULL, NULL),
	(59, 1, 2, '2019-01-11 20:50:32', 6, '2019-01-11 20:50:32', 6, NULL, NULL),
	(60, 1, 3, '2019-01-11 20:50:34', 6, '2019-01-11 20:50:34', 6, NULL, NULL),
	(61, 1, 6, '2019-01-11 20:50:41', 6, '2019-01-11 20:50:50', 6, '2019-01-11 20:50:50', 6),
	(62, 2, 2, '2019-01-11 20:51:03', 6, '2019-01-11 20:51:03', 6, NULL, NULL),
	(63, 2, 3, '2019-01-11 20:51:05', 6, '2019-01-11 20:51:05', 6, NULL, NULL),
	(64, 2, 4, '2019-01-11 20:51:07', 6, '2019-01-11 20:51:07', 6, NULL, NULL),
	(65, 2, 5, '2019-01-11 20:51:10', 6, '2019-01-11 20:51:10', 6, NULL, NULL),
	(66, 4, 1, '2019-01-11 20:54:55', 6, '2019-01-11 20:54:55', 6, NULL, NULL),
	(67, 4, 3, '2019-01-11 20:54:58', 6, '2019-01-11 20:55:03', 6, '2019-01-11 20:55:03', 6),
	(68, 4, 3, '2019-01-11 20:56:57', 6, '2019-01-11 20:57:33', 6, '2019-01-11 20:57:33', 6),
	(69, 4, 3, '2019-01-11 20:57:41', 6, '2019-01-11 20:57:41', 6, NULL, NULL),
	(70, 4, 3, '2019-01-11 20:58:32', 1, '2019-01-11 20:58:32', 1, NULL, NULL);
/*!40000 ALTER TABLE `following` ENABLE KEYS */;

-- Dumping structure for table shopu.image
CREATE TABLE IF NOT EXISTS `image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'primary',
  `sort` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.image: ~28 rows (approximately)
/*!40000 ALTER TABLE `image` DISABLE KEYS */;
INSERT INTO `image` (`id`, `entity`, `entity_id`, `url`, `type`, `sort`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 1, 3, 'https://static.acer.com/up/Resource/Acer/Laptops/Aspire_VX_15/Overview/20161117/Aspire-VX-15_gallery-03.png', 'primary', 0, '2018-11-26 15:27:43', 1, '2018-11-27 14:01:19', 1, NULL, NULL),
	(2, 1, 3, 'https://static.acer.com/up/Resource/Acer/Laptops/Aspire_VX_15/Overview/20161117/Aspire-VX-15_gallery-04.png', 'primary', 0, '2018-11-26 15:27:43', 1, '2018-11-27 14:01:21', 1, NULL, NULL),
	(3, 1, 65, 'http://res.cloudinary.com/edgehead17/image/upload/v1543324812/u74puorfgazzgza9usmk.png', 'primary', 1, '2018-11-27 13:48:24', 1, '2018-11-27 13:51:02', 1, NULL, NULL),
	(4, 1, 65, 'http://res.cloudinary.com/edgehead17/image/upload/v1543324812/u74puorfgazzgza9usmk.png', 'primary', 2, '2018-11-27 13:50:50', 1, '2018-11-27 13:50:50', 1, NULL, NULL),
	(5, 1, 1, 'http://res.cloudinary.com/edgehead17/image/upload/v1543327219/fdoh25ishgedgtfulcon.jpg', 'primary', 1, '2018-11-27 14:00:54', 1, '2018-11-27 14:00:54', 1, NULL, NULL),
	(6, 1, 2, 'http://res.cloudinary.com/edgehead17/image/upload/v1543327199/yguvldfxsibwcec1cj5h.jpg', 'primary', 1, '2018-11-27 14:01:04', 1, '2018-11-27 14:01:04', 1, NULL, NULL),
	(7, 1, 3, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327401/wa2tjrr3ifwwfr0uov3b.png', 'primary', 1, '2018-11-27 14:09:09', 1, '2018-11-27 14:09:09', 1, NULL, NULL),
	(8, 1, 3, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327409/v0qselbbqpta5ucc06th.png', 'primary', 2, '2018-11-27 14:09:23', 1, '2018-11-27 14:09:23', 1, NULL, NULL),
	(9, 1, 3, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327415/ojycgchodwjjng08576i.png', 'primary', 3, '2018-11-27 14:09:28', 1, '2018-11-27 14:09:28', 1, NULL, NULL),
	(10, 1, 3, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327427/kfgygdto19z6tlhpjoxd.png', 'primary', 4, '2018-11-27 14:09:33', 1, '2018-11-27 14:09:33', 1, NULL, NULL),
	(11, 1, 3, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327434/spbait0nl2folsleh7db.png', 'primary', 5, '2018-11-27 14:09:38', 1, '2018-11-27 14:09:38', 1, NULL, NULL),
	(12, 1, 3, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327440/reg3wdizsvzbalubrv2j.png', 'primary', 6, '2018-11-27 14:09:44', 1, '2018-11-27 14:09:44', 1, NULL, NULL),
	(13, 1, 3, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327446/waihghuqmpbzuvjn1fht.png', 'primary', 7, '2018-11-27 14:09:48', 1, '2018-11-27 14:09:48', 1, NULL, NULL),
	(14, 1, 3, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327453/vo0kvgzv1tmu8xhioult.png', 'primary', 8, '2018-11-27 14:09:58', 1, '2018-11-27 14:09:58', 1, NULL, NULL),
	(15, 1, 6, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327544/xclxdw8vnoppjxcjoqf2.jpg', 'primary', 1, '2018-11-27 14:11:58', 1, '2018-11-27 14:11:58', 1, NULL, NULL),
	(16, 1, 6, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327564/ul0pnw70qiabhelmezcj.jpg', 'primary', 2, '2018-11-27 14:12:06', 1, '2018-11-27 14:12:06', 1, NULL, NULL),
	(17, 1, 6, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327579/c6rvo8zinn2gnc2agwpt.jpg', 'primary', 3, '2018-11-27 14:12:12', 1, '2018-11-27 14:12:12', 1, NULL, NULL),
	(18, 1, 6, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327589/mzgdyzcphq5rkptxtuls.jpg', 'primary', 4, '2018-11-27 14:12:17', 1, '2018-11-27 14:12:17', 1, NULL, NULL),
	(19, 1, 6, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327600/mk8fvb8i99d5bgap5ddi.jpg', 'primary', 5, '2018-11-27 14:12:21', 1, '2018-11-27 14:12:21', 1, NULL, NULL),
	(20, 1, 6, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327613/ddofth1hpjguam8drjtn.jpg', 'primary', 6, '2018-11-27 14:12:25', 1, '2018-11-27 14:12:25', 1, NULL, NULL),
	(21, 1, 6, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327635/jwbefli1uqu7athyvzbs.jpg', 'primary', 7, '2018-11-27 14:13:18', 1, '2018-11-27 14:13:18', 1, NULL, NULL),
	(22, 1, 7, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327525/patbt6dpaptjqvhkolha.jpg', 'primary', 1, '2018-11-27 14:13:59', 1, '2018-11-27 14:13:59', 1, NULL, NULL),
	(23, 1, 29, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327483/zoqzjky3qoudo3p0isgo.jpg', 'primary', 1, '2018-11-27 14:14:35', 1, '2018-11-27 14:14:35', 1, NULL, NULL),
	(24, 1, 8, 'http://res.cloudinary.com/edgehead17/image/upload/v1543328091/pezoqwn6qggoektmqxvd.jpg', 'primary', 1, '2018-11-27 14:15:12', 1, '2018-11-27 14:15:12', 1, NULL, NULL),
	(25, 1, 32, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327502/gxsprphownnkrnyjan5m.jpg', 'primary', 1, '2018-11-27 14:15:37', 1, '2018-11-27 14:15:37', 1, NULL, NULL),
	(26, 1, 35, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327474/td7jbxrrqp161kffes0o.jpg', 'primary', 1, '2018-11-27 14:15:49', 1, '2018-11-27 14:15:49', 1, NULL, NULL),
	(27, 1, 6, 'https://res.cloudinary.com/edgehead17/image/upload/v1543327635/jwbefli1uqu7athyvzbs.jpg', 'primary', 8, '2018-11-27 15:32:18', 1, '2018-11-27 15:32:18', 1, NULL, NULL),
	(28, 1, 2, 'http://res.cloudinary.com/edgehead17/image/upload/v1543339888/k0lydamsys7ns1jm4zo3.jpg', 'primary', 2, '2018-11-27 17:32:11', 1, '2018-11-27 17:32:11', 1, NULL, NULL),
	(29, 4, 1, 'http://res.cloudinary.com/edgehead17/image/upload/v1543762616/gnodlppxioau0kbxai16.jpg', 'primary', 1, '2018-12-02 14:57:21', 1, '2018-12-02 14:57:21', 1, NULL, NULL),
	(30, 4, 1, 'http://res.cloudinary.com/edgehead17/image/upload/v1543762654/mwtnsesffjfojyxeoytg.jpg', 'primary', 2, '2018-12-02 14:57:47', 1, '2018-12-02 14:57:47', 1, NULL, NULL),
	(31, 6, 1, 'http://res.cloudinary.com/edgehead17/image/upload/v1544335216/ftvvgzagoct0b6c20q7r.png', 'primary', 1, '2018-12-09 06:01:08', 1, '2018-12-09 06:01:08', 1, NULL, NULL),
	(32, 7, 10, 'http://res.cloudinary.com/edgehead17/image/upload/v1546970845/pik3xsbdfzas2pid6uah.jpg', 'primary', 1, '2019-01-08 18:08:39', 5, '2019-01-08 18:08:39', 5, NULL, NULL),
	(33, 7, 10, 'http://res.cloudinary.com/edgehead17/image/upload/v1546970862/d32cdb9pdgdnwc1kyppb.jpg', 'primary', 2, '2019-01-08 18:08:47', 5, '2019-01-08 18:08:47', 5, NULL, NULL);
/*!40000 ALTER TABLE `image` ENABLE KEYS */;

-- Dumping structure for table shopu.like
CREATE TABLE IF NOT EXISTS `like` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dumping data for table shopu.like: ~7 rows (approximately)
/*!40000 ALTER TABLE `like` DISABLE KEYS */;
INSERT INTO `like` (`id`, `entity`, `entity_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 6, 1, '2018-12-15 11:08:10', 1, '2018-12-15 11:08:10', 1, NULL, NULL),
	(2, 6, 1, '2018-12-15 11:22:20', 1, '2018-12-15 11:32:57', 1, '2018-12-15 11:32:57', 1),
	(3, 6, 1, '2018-12-15 11:22:25', 1, '2018-12-15 11:22:25', 1, '2019-01-12 03:14:23', 1),
	(4, 6, 1, '2019-01-11 19:14:03', 6, '2019-01-11 19:24:09', 6, '2019-01-11 19:24:09', 6),
	(5, 6, 1, '2019-01-11 19:24:39', 6, '2019-01-11 20:39:26', 6, '2019-01-11 20:39:26', 6),
	(6, 6, 2, '2019-01-11 19:24:41', 6, '2019-01-11 19:24:56', 6, '2019-01-11 19:24:56', 6),
	(7, 6, 3, '2019-01-11 19:24:43', 6, '2019-01-11 19:24:58', 6, '2019-01-11 19:24:58', 6);
/*!40000 ALTER TABLE `like` ENABLE KEYS */;

-- Dumping structure for table shopu.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table shopu.migrations: ~14 rows (approximately)
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` (`migration`, `batch`) VALUES
	('2017_12_01_163442_create_entity_table', 1),
	('2018_01_01_163442_create_product_table', 1),
	('2018_01_02_163442_create_color_table', 1),
	('2018_01_03_163442_create_size_table', 1),
	('2018_01_08_163442_create_product_inventory_table', 1),
	('2018_01_09_163442_create_product_pricing_table', 1),
	('2018_01_10_163442_create_product_discount_table', 1),
	('2018_01_16_163442_create_product_color_map_table', 1),
	('2018_01_17_163442_create_product_size_map_table', 1),
	('2018_02_01_163442_create_category_table', 1),
	('2018_02_02_163442_create_category_level_table', 1),
	('2018_02_03_163442_create_category_map_table', 1),
	('2018_03_01_163442_create_image_table', 1),
	('2018_04_01_163442_create_following_table', 1),
	('2018_05_01_163442_create_status_table', 2),
	('2018_05_02_163442_create_status_map_table', 3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

-- Dumping structure for table shopu.product
CREATE TABLE IF NOT EXISTS `product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_tc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_sc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_tc` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_sc` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shop_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.product: ~18 rows (approximately)
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` (`id`, `sku`, `name_en`, `name_tc`, `name_sc`, `description_en`, `description_tc`, `description_sc`, `shop_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 'IPHONE8', 'iPhone 8', NULL, NULL, 'Latest iPhone release', NULL, NULL, 1, '2018-11-24 12:43:13', 1, '2018-11-26 15:26:09', 1, NULL, NULL),
	(2, 'IPHONEX', 'iPhone X', NULL, NULL, 'Not so latest iPhone release', NULL, NULL, 1, '2018-11-24 12:43:13', 1, '2018-11-26 15:26:09', 1, NULL, NULL),
	(3, 'ACERVX15', 'Acer VX15', NULL, NULL, 'Not bad for a gaming laptop, though', NULL, NULL, 1, '2018-11-24 12:43:13', 1, '2018-11-26 15:26:09', 1, NULL, NULL),
	(6, 'GIBSONLESPAUL', 'Gibson - Les Paul', NULL, NULL, 'The guitar that made legends. Enough said.', NULL, NULL, 1, '2018-11-13 16:47:11', 1, '2018-11-26 15:26:09', 1, NULL, NULL),
	(7, 'GRENADE-HE', 'H.E. Grenade', NULL, NULL, 'The high explosive fragmentation grenade administers high damage through a wide area, making it ideal for clearing out hostile rooms.', 'The high explosive fragmentation grenade administers high damage through a wide area, making it ideal for clearing out hostile rooms.', 'The high explosive fragmentation grenade administers high damage through a wide area, making it ideal for clearing out hostile rooms.', 1, '2018-11-13 16:49:06', 1, '2018-12-02 09:03:52', 1, '2018-12-02 09:03:52', 1),
	(8, 'AK-47', 'Avtomát Kaláshnikova', NULL, NULL, 'The world\'s absolute most proliferous assault rifle.', NULL, NULL, 1, '2018-11-13 16:51:52', 1, '2018-12-02 09:03:59', 1, '2018-12-02 09:03:59', 1),
	(29, 'BOSTONOD200', 'Boston Engineering OD-200 Overdrive', NULL, NULL, 'Equipped with dual gain circuitry, the OD-100 is easy to create the tight and fat over drive sound by adjusting the two bands EQ and the GAIN knobs. It\'s so sensitive to capture details of the performance, and remain the warm distortion sound.', NULL, NULL, 1, '2018-11-13 17:42:37', 1, '2018-11-26 15:26:09', 1, NULL, NULL),
	(32, 'ZOOMG1X', 'Zoom G1X Guitar Effects Pedal', NULL, NULL, 'AMAZINGLY AFFORDABLE PACKAGE WITH KNOCKOUT PERFORMANCE.', NULL, NULL, 1, '2018-11-13 18:10:44', 1, '2018-11-26 15:26:09', 1, NULL, NULL),
	(35, 'ZOOMB1Xv2', 'Zoom B1X BASS EFFECTS PEDAL (V2)', NULL, NULL, 'VERSION 2: Amazingly affordable package with knockout performance.', NULL, NULL, 1, '2018-11-13 18:24:50', 1, '2018-11-26 15:26:09', 1, NULL, NULL),
	(36, 'GRENADELAUNCHER', 'Grenade Launcher', NULL, NULL, 'Bombs away!!!!', NULL, NULL, 1, '2018-11-16 16:27:40', 1, '2018-12-02 09:04:09', 1, '2018-12-02 09:04:09', 1),
	(38, 'ROCKETLAUNCHER', 'Rocket Launcher', NULL, NULL, 'Rockets away!!', NULL, NULL, 1, '2018-11-16 16:36:38', 1, '2018-12-02 09:04:16', 1, '2018-12-02 09:04:16', 1),
	(40, 'ArmaLiteAR-5', 'ArmaLite AR-5', NULL, NULL, 'A lightweight bolt-action rifle, chambered for the .22 Hornet cartridge, and adopted as the MA-1 aircrew survival rifle by the United States Air Force.', NULL, NULL, 1, '2018-11-25 08:21:38', 1, '2018-12-02 09:04:24', 1, '2018-12-02 09:04:24', 1),
	(41, 'ArmaLiteAR-18', 'ArmaLite AR-18', NULL, NULL, 'A middleweight bolt-action rifle, chambered for the .22 Hornet cartridge, and adopted as the MA-1 aircrew survival rifle by the United States Air Force.', NULL, NULL, 1, '2018-11-25 08:26:29', 1, '2018-12-02 09:04:26', 1, '2018-12-02 09:04:26', 1),
	(64, 'MOLOTOV', 'Molotov', NULL, NULL, 'A Molotov cocktail, also known as a petrol bomb, bottle bomb, poor man\'s grenade, Molotovin koktaili (Finnish), polttopullo (Finnish), fire bomb (not to be confused with an actual fire bomb) or just Molotov, sometimes shortened as Molly, is a generic name used for a variety of bottle-based improvised incendiary weapons.', NULL, NULL, 1, '2018-11-25 09:32:56', 1, '2018-12-02 09:04:31', 1, '2018-12-02 09:04:31', 1),
	(65, 'MOLOTOV-3', 'Molotov (En)', 'Molotov (TC)', 'Molotov (SC)', 'null', 'null', 'Test', 1, '2018-11-26 15:20:29', 1, '2018-11-27 16:24:01', 1, '2018-11-27 16:24:01', 1),
	(67, 'TRVWA24', 'Uyqw vesh dusef', NULL, NULL, 'wecwev  ', 'asdasfa', 'QWCSAC', 1, '2018-11-27 17:46:00', 1, '2018-11-27 17:49:42', 1, '2018-11-27 17:49:42', 1),
	(68, 'ASUSZENFONE3', 'Asus Zenfone 3', NULL, NULL, 'Asus Zenfone 3 Description', NULL, NULL, 1, '2018-12-02 04:16:52', 1, '2018-12-02 04:16:52', 1, NULL, NULL),
	(69, 'ASUSZENFONE4', 'Asus Zenfone 4', NULL, NULL, 'Asus Zenfone 4 Description', NULL, NULL, 1, '2018-12-02 04:27:08', 1, '2018-12-02 04:27:08', 1, NULL, NULL),
	(70, 'SKU001', 'Name English Product', NULL, NULL, 'Description in English', NULL, NULL, 3, '2018-12-02 16:25:35', 1, '2018-12-02 16:46:11', 1, NULL, NULL);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;

-- Dumping structure for table shopu.product_attribute
CREATE TABLE IF NOT EXISTS `product_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `size_id` int(11) DEFAULT NULL,
  `color_id` int(11) DEFAULT NULL,
  `other` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- Dumping data for table shopu.product_attribute: ~14 rows (approximately)
/*!40000 ALTER TABLE `product_attribute` DISABLE KEYS */;
INSERT INTO `product_attribute` (`id`, `size_id`, `color_id`, `other`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, NULL, 1, NULL, '2018-11-25 02:55:48', 1, '2018-11-25 02:55:48', 1, NULL, NULL),
	(2, NULL, 2, NULL, '2018-11-25 02:55:53', 1, '2018-11-25 02:55:53', 1, NULL, NULL),
	(3, NULL, 3, NULL, '2018-11-25 02:55:57', 1, '2018-11-25 02:55:57', 1, NULL, NULL),
	(4, 1, NULL, NULL, '2018-11-25 02:56:12', 1, '2018-11-25 02:56:12', 1, NULL, NULL),
	(5, 2, NULL, NULL, '2018-11-25 02:56:17', 1, '2018-11-25 02:56:17', 1, NULL, NULL),
	(6, 3, NULL, NULL, '2018-11-25 02:56:22', 1, '2018-11-25 02:56:22', 1, NULL, NULL),
	(7, NULL, 3, 'FREE Case', '2018-11-25 02:57:08', 1, '2018-11-25 02:57:08', 1, NULL, NULL),
	(8, 1, 1, 'FREE Case', '2018-11-25 07:27:14', 1, '2018-11-25 07:27:14', 1, NULL, NULL),
	(9, NULL, 3, 'FREE 10 Bullets', '2018-11-25 08:21:39', 1, '2018-11-25 08:21:39', 1, NULL, NULL),
	(15, NULL, NULL, 'FREE Detonators', '2018-11-25 09:41:18', 1, '2018-11-25 09:41:18', 1, NULL, NULL),
	(17, 2, 2, 'Buy 1 Take 1', '2018-11-26 16:47:28', 1, '2018-11-26 16:54:23', 1, NULL, NULL),
	(18, 2, 2, 'Buy 1 Take 2', '2018-11-26 16:51:33', 1, '2018-11-26 16:54:24', 1, NULL, NULL),
	(19, 2, 2, 'Buy 1 Take 3', '2018-11-26 16:53:59', 1, '2018-11-26 16:53:59', 1, NULL, NULL),
	(20, NULL, 4, NULL, '2018-11-27 17:07:34', 1, '2018-11-27 17:07:34', 1, NULL, NULL),
	(21, NULL, 5, NULL, '2018-11-27 17:07:52', 1, '2018-11-27 17:07:52', 1, NULL, NULL),
	(22, NULL, 4, 'FREE Case', '2018-12-02 04:27:09', 1, '2018-12-02 04:27:09', 1, NULL, NULL);
/*!40000 ALTER TABLE `product_attribute` ENABLE KEYS */;

-- Dumping structure for table shopu.product_discount
CREATE TABLE IF NOT EXISTS `product_discount` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.product_discount: ~35 rows (approximately)
/*!40000 ALTER TABLE `product_discount` DISABLE KEYS */;
INSERT INTO `product_discount` (`id`, `product_id`, `type`, `amount`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 1, 'percentage', 0.10, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(2, 2, 'fixed', 166.66, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(4, 35, 'percentage', 0.33, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(5, 65, 'fixed', 1.00, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(8, 65, 'fixed', 0.00, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(9, 65, 'fixed', 0.00, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(10, 65, 'fixed', 0.00, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(11, 65, 'fixed', 0.00, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(12, 65, 'fixed', 0.00, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(13, 65, 'fixed', 0.00, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(14, 65, 'fixed', 0.00, '2018-11-26 15:27:43', 1, '2018-11-26 15:27:43', 1, NULL, NULL),
	(15, 65, 'fixed', 0.00, '2018-11-26 16:14:35', 1, '2018-12-02 04:20:25', 1, '2018-11-27 16:24:01', 1),
	(16, 67, 'fixed', 0.00, '2018-11-27 17:46:38', 1, '2018-12-02 04:20:25', 1, '2018-11-27 17:49:42', 1),
	(17, 67, 'fixed', 0.00, '2018-11-27 17:46:44', 1, '2018-12-02 04:20:26', 1, NULL, NULL),
	(18, 67, 'fixed', 0.00, '2018-11-27 17:46:49', 1, '2018-12-02 04:20:26', 1, NULL, NULL),
	(19, 67, 'fixed', 0.00, '2018-11-27 17:46:54', 1, '2018-12-02 04:20:27', 1, NULL, NULL),
	(20, 67, 'fixed', 0.00, '2018-11-27 17:47:01', 1, '2018-12-02 04:20:29', 1, NULL, NULL),
	(21, 67, 'fixed', 100.00, '2018-11-27 17:47:26', 1, '2018-12-02 04:20:29', 1, NULL, NULL),
	(22, 67, 'fixed', 300.00, '2018-11-27 17:47:40', 1, '2018-12-02 04:20:30', 1, NULL, NULL),
	(23, 67, 'fixed', 300.00, '2018-11-27 17:47:53', 1, '2018-12-02 04:20:31', 1, NULL, NULL),
	(24, 7, 'fixed', 0.00, '2018-11-29 18:02:38', 1, '2018-11-29 18:02:38', 1, '2018-11-29 18:02:38', 1),
	(25, 7, 'fixed', 0.00, '2018-11-29 18:06:05', 1, '2018-12-02 09:03:53', 1, '2018-12-02 09:03:52', 1),
	(26, 7, 'fixed', 0.00, '2018-11-29 18:06:21', 1, '2018-11-29 18:06:21', 1, '2018-11-29 18:06:21', 1),
	(27, 7, 'fixed', 0.00, '2018-11-29 18:07:36', 1, '2018-11-29 18:07:36', 1, NULL, NULL),
	(28, 7, 'fixed', 0.00, '2018-11-29 18:07:52', 1, '2018-11-29 18:07:52', 1, '2018-11-29 18:07:52', 1),
	(29, 7, 'fixed', 0.00, '2018-11-29 18:25:26', 1, '2018-11-29 18:25:26', 1, NULL, NULL),
	(30, 7, 'fixed', 0.00, '2018-11-29 18:26:36', 1, '2018-11-29 18:26:36', 1, NULL, NULL),
	(31, 7, 'fixed', 0.00, '2018-11-29 18:26:52', 1, '2018-11-29 18:26:52', 1, NULL, NULL),
	(32, 7, 'fixed', 0.00, '2018-11-29 18:27:38', 1, '2018-11-29 18:27:38', 1, NULL, NULL),
	(33, 7, 'fixed', 0.00, '2018-11-29 18:27:46', 1, '2018-11-29 18:27:46', 1, NULL, NULL),
	(34, 7, 'fixed', 0.00, '2018-12-02 08:57:50', 1, '2018-12-02 08:57:50', 1, NULL, NULL),
	(35, 7, 'fixed', 0.00, '2018-12-02 09:00:08', 1, '2018-12-02 09:00:08', 1, NULL, NULL),
	(36, 70, 'fixed', 0.00, '2018-12-02 16:43:54', 1, '2018-12-02 16:43:54', 1, NULL, NULL),
	(37, 70, 'fixed', 0.00, '2018-12-02 16:44:04', 1, '2018-12-02 16:44:04', 1, NULL, NULL),
	(38, 70, 'fixed', 0.00, '2018-12-02 16:45:51', 1, '2018-12-02 16:45:51', 1, NULL, NULL),
	(39, 70, 'fixed', 0.00, '2018-12-02 16:46:03', 1, '2018-12-02 16:46:03', 1, NULL, NULL),
	(40, 70, 'fixed', 0.00, '2018-12-02 16:46:11', 1, '2018-12-02 16:46:11', 1, NULL, NULL);
/*!40000 ALTER TABLE `product_discount` ENABLE KEYS */;

-- Dumping structure for table shopu.product_inventory
CREATE TABLE IF NOT EXISTS `product_inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `attribute_id` int(11) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.product_inventory: ~41 rows (approximately)
/*!40000 ALTER TABLE `product_inventory` DISABLE KEYS */;
INSERT INTO `product_inventory` (`id`, `product_id`, `attribute_id`, `stock`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 1, 1, 10, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(2, 2, 3, 20, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(3, 3, 4, 10, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(4, 1, 1, 15, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(5, 2, 3, 15, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(6, 3, 5, 10, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(7, 3, 6, 10, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(8, 1, 2, 5, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(9, 2, 7, 13, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(10, 40, 9, 10, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:24', 1, '2018-12-02 09:04:24', 1),
	(11, 41, 9, 10, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:26', 1, '2018-12-02 09:04:26', 1),
	(34, 64, NULL, 60, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:32', 1, '2018-12-02 09:04:31', 1),
	(35, 65, 15, 60, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(37, 65, 17, 10, '2018-11-26 16:47:28', 1, '2018-11-26 16:48:04', 1, NULL, NULL),
	(38, 65, 18, 10, '2018-11-26 16:51:33', 1, '2018-11-26 16:54:29', 1, NULL, NULL),
	(39, 65, 17, 32, '2018-11-26 16:51:57', 1, '2018-11-26 16:54:31', 1, NULL, NULL),
	(40, 65, 19, 32, '2018-11-26 16:53:59', 1, '2018-11-26 16:53:59', 1, NULL, NULL),
	(41, 65, NULL, 19, '2018-11-26 16:55:09', 1, '2018-11-26 16:55:09', 1, NULL, NULL),
	(42, 64, NULL, -4, '2018-11-26 17:51:38', 1, '2018-11-26 17:51:38', 1, NULL, NULL),
	(43, 65, 17, -19, '2018-11-26 17:54:08', 1, '2018-11-26 17:54:08', 1, NULL, NULL),
	(45, 65, 17, -2, '2018-11-26 18:08:13', 1, '2018-11-27 16:24:02', 1, '2018-11-27 16:24:01', 1),
	(46, 64, NULL, -2, '2018-11-26 18:08:27', 1, '2018-11-26 18:08:27', 1, NULL, NULL),
	(47, 32, 20, 10, '2018-11-27 17:07:34', 1, '2018-11-27 17:07:34', 1, NULL, NULL),
	(48, 35, 21, 8, '2018-11-27 17:07:52', 1, '2018-11-27 17:07:52', 1, NULL, NULL),
	(49, 32, 20, -1, '2018-11-27 17:08:57', 1, '2018-11-27 17:08:57', 1, NULL, NULL),
	(50, 35, 21, -1, '2018-11-27 17:09:15', 1, '2018-11-27 17:09:15', 1, NULL, NULL),
	(51, 67, NULL, 20, '2018-11-27 17:46:00', 1, '2018-11-27 17:49:42', 1, '2018-11-27 17:49:42', 1),
	(52, 67, NULL, 3, '2018-11-27 17:48:25', 1, '2018-11-27 17:48:25', 1, NULL, NULL),
	(53, 67, NULL, -5, '2018-11-27 17:49:03', 1, '2018-11-27 17:49:03', 1, NULL, NULL),
	(54, 67, NULL, -5, '2018-11-27 17:49:12', 1, '2018-11-27 17:49:12', 1, NULL, NULL),
	(55, 67, NULL, -5, '2018-11-27 17:49:14', 1, '2018-11-27 17:49:14', 1, NULL, NULL),
	(56, 67, NULL, -5, '2018-11-27 17:49:16', 1, '2018-11-27 17:49:16', 1, NULL, NULL),
	(57, 67, NULL, -5, '2018-11-27 17:49:19', 1, '2018-11-27 17:49:19', 1, NULL, NULL),
	(58, 67, NULL, -5, '2018-11-27 17:49:21', 1, '2018-11-27 17:49:21', 1, NULL, NULL),
	(59, 67, NULL, 3, '2018-11-27 17:50:05', 1, '2018-11-27 17:50:05', 1, NULL, NULL),
	(60, 67, NULL, -5, '2018-11-27 17:50:16', 1, '2018-11-27 17:50:16', 1, NULL, NULL),
	(61, 67, NULL, 3, '2018-11-27 17:50:37', 1, '2018-11-27 17:50:37', 1, NULL, NULL),
	(62, 67, NULL, -5, '2018-11-27 17:50:41', 1, '2018-11-27 17:50:41', 1, NULL, NULL),
	(63, 68, 3, 20, '2018-12-02 04:16:53', 1, '2018-12-02 04:16:53', 1, NULL, NULL),
	(64, 69, 22, 33, '2018-12-02 04:27:09', 1, '2018-12-02 04:27:09', 1, NULL, NULL),
	(65, 68, NULL, 10, '2018-12-02 10:11:52', 1, '2018-12-02 10:11:52', 1, NULL, NULL),
	(66, 68, NULL, -2, '2018-12-02 10:12:13', 1, '2018-12-02 10:12:13', 1, NULL, NULL),
	(67, 69, 22, -5, '2018-12-02 10:12:49', 1, '2018-12-02 10:12:49', 1, NULL, NULL),
	(68, 70, NULL, 10, '2018-12-02 16:25:35', 1, '2018-12-02 16:25:35', 1, NULL, NULL);
/*!40000 ALTER TABLE `product_inventory` ENABLE KEYS */;

-- Dumping structure for table shopu.product_pricing
CREATE TABLE IF NOT EXISTS `product_pricing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `price` double(8,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.product_pricing: ~24 rows (approximately)
/*!40000 ALTER TABLE `product_pricing` DISABLE KEYS */;
INSERT INTO `product_pricing` (`id`, `product_id`, `price`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 1, 12500.00, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(2, 2, 14166.67, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(3, 3, 9333.50, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(4, 2, 13888.83, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(5, 29, 1000.00, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(6, 32, 1000.00, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(9, 35, 1500.00, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(10, 36, 6000.00, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:09', 1, '2018-12-02 09:04:09', 1),
	(11, 37, 6000.00, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(12, 38, 7500.00, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:16', 1, '2018-12-02 09:04:16', 1),
	(13, 39, 7500.00, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(14, 40, 10000.00, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:24', 1, '2018-12-02 09:04:24', 1),
	(15, 41, 10000.00, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:26', 1, '2018-12-02 09:04:26', 1),
	(38, 64, 1000.00, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:32', 1, '2018-12-02 09:04:31', 1),
	(39, 65, 1000.00, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(41, 65, 1250.00, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(42, 65, 1350.00, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(43, 65, 1266.00, '2018-11-26 15:27:44', 1, '2018-11-27 16:24:02', 1, '2018-11-27 16:24:01', 1),
	(44, 67, 1000.00, '2018-11-27 17:46:00', 1, '2018-11-27 17:49:42', 1, '2018-11-27 17:49:42', 1),
	(45, 67, 1200.00, '2018-11-27 17:47:40', 1, '2018-12-02 04:20:45', 1, NULL, NULL),
	(46, 67, 1200.00, '2018-11-27 17:47:53', 1, '2018-12-02 04:20:40', 1, NULL, NULL),
	(47, 68, 3000.00, '2018-12-02 04:16:53', 1, '2018-12-02 04:16:53', 1, NULL, NULL),
	(48, 69, 3000.00, '2018-12-02 04:27:08', 1, '2018-12-02 04:27:08', 1, NULL, NULL),
	(49, 70, 1000.00, '2018-12-02 16:25:35', 1, '2018-12-02 16:25:35', 1, NULL, NULL);
/*!40000 ALTER TABLE `product_pricing` ENABLE KEYS */;

-- Dumping structure for table shopu.product_shipping
CREATE TABLE IF NOT EXISTS `product_shipping` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `amount` double(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;

-- Dumping data for table shopu.product_shipping: ~43 rows (approximately)
/*!40000 ALTER TABLE `product_shipping` DISABLE KEYS */;
INSERT INTO `product_shipping` (`id`, `product_id`, `amount`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 1, 45.00, '2018-11-24 11:25:15', 1, '2018-11-24 11:25:15', 1, NULL, NULL),
	(2, 2, 45.00, '2018-11-24 11:25:17', 1, '2018-11-24 11:25:17', 1, NULL, NULL),
	(3, 3, 35.00, '2018-11-24 11:25:20', 1, '2018-11-24 11:25:20', 1, NULL, NULL),
	(4, 6, 35.00, '2018-11-24 11:25:27', 1, '2018-11-24 11:25:27', 1, NULL, NULL),
	(5, 7, 0.00, '2018-11-24 11:25:29', 1, '2018-12-02 09:03:53', 1, '2018-12-02 09:03:52', 1),
	(6, 8, 0.00, '2018-11-24 11:25:35', 1, '2018-12-02 09:03:59', 1, '2018-12-02 09:03:59', 1),
	(7, 29, 35.00, '2018-11-24 11:25:40', 1, '2018-11-24 11:25:40', 1, NULL, NULL),
	(8, 32, 33.33, '2018-11-24 11:25:44', 1, '2018-11-24 11:25:44', 1, NULL, NULL),
	(9, 35, 33.33, '2018-11-24 11:25:50', 1, '2018-11-24 11:25:50', 1, NULL, NULL),
	(10, 36, 0.00, '2018-11-24 11:25:54', 1, '2018-12-02 09:04:10', 1, '2018-12-02 09:04:09', 1),
	(11, 38, 0.00, '2018-11-24 11:25:58', 1, '2018-12-02 09:04:16', 1, '2018-12-02 09:04:16', 1),
	(12, 40, 0.00, '2018-11-25 08:24:17', 1, '2018-12-02 09:04:24', 1, '2018-12-02 09:04:24', 1),
	(13, 41, 0.00, '2018-11-25 08:26:59', 1, '2018-12-02 09:04:26', 1, '2018-12-02 09:04:26', 1),
	(36, 64, 50.00, '2018-11-25 09:32:56', 1, '2018-12-02 09:04:32', 1, '2018-12-02 09:04:31', 1),
	(37, 65, 50.00, '2018-11-25 09:41:18', 1, '2018-11-25 09:41:18', 1, NULL, NULL),
	(39, 65, 12.00, '2018-11-25 16:12:28', 1, '2018-11-25 16:12:28', 1, NULL, NULL),
	(40, 65, 12.33, '2018-11-25 16:12:37', 1, '2018-11-25 16:12:37', 1, NULL, NULL),
	(41, 65, 0.00, '2018-11-25 16:12:52', 1, '2018-11-25 16:12:52', 1, NULL, NULL),
	(42, 65, 0.00, '2018-11-26 16:14:36', 1, '2018-12-02 04:19:20', 1, '2018-11-27 16:24:01', 1),
	(43, 67, 0.00, '2018-11-27 17:46:00', 1, '2018-11-27 17:49:42', 1, '2018-11-27 17:49:42', 1),
	(44, 67, 0.00, '2018-11-27 17:46:38', 1, '2018-12-02 04:19:24', 1, NULL, NULL),
	(45, 67, 0.00, '2018-11-27 17:46:44', 1, '2018-12-02 04:19:25', 1, NULL, NULL),
	(46, 67, 0.00, '2018-11-27 17:46:49', 1, '2018-12-02 04:19:26', 1, NULL, NULL),
	(47, 67, 0.00, '2018-11-27 17:46:54', 1, '2018-12-02 04:19:27', 1, NULL, NULL),
	(48, 67, 30.00, '2018-11-27 17:47:01', 1, '2018-12-02 04:19:27', 1, NULL, NULL),
	(49, 67, 30.00, '2018-11-27 17:47:26', 1, '2018-12-02 04:19:28', 1, NULL, NULL),
	(50, 67, 30.00, '2018-11-27 17:47:40', 1, '2018-12-02 04:19:29', 1, NULL, NULL),
	(51, 67, 30.00, '2018-11-27 17:47:53', 1, '2018-12-02 04:19:30', 1, NULL, NULL),
	(52, 7, 0.00, '2018-11-29 18:02:38', 1, '2018-11-29 18:02:38', 1, '2018-11-29 18:02:38', 1),
	(53, 7, 0.00, '2018-11-29 18:06:05', 1, '2018-11-29 18:06:05', 1, NULL, NULL),
	(54, 7, 0.00, '2018-11-29 18:06:21', 1, '2018-11-29 18:06:21', 1, '2018-11-29 18:06:21', 1),
	(55, 7, 0.00, '2018-11-29 18:07:36', 1, '2018-11-29 18:07:36', 1, NULL, NULL),
	(56, 7, 0.00, '2018-11-29 18:07:53', 1, '2018-11-29 18:07:53', 1, '2018-11-29 18:07:52', 1),
	(57, 7, 0.00, '2018-11-29 18:25:26', 1, '2018-11-29 18:25:26', 1, NULL, NULL),
	(58, 7, 0.00, '2018-11-29 18:26:36', 1, '2018-11-29 18:26:36', 1, NULL, NULL),
	(59, 7, 0.00, '2018-11-29 18:26:52', 1, '2018-11-29 18:26:52', 1, NULL, NULL),
	(60, 7, 0.00, '2018-11-29 18:27:38', 1, '2018-11-29 18:27:38', 1, NULL, NULL),
	(61, 7, 0.00, '2018-11-29 18:27:46', 1, '2018-11-29 18:27:46', 1, NULL, NULL),
	(62, 68, 0.00, '2018-12-02 04:16:53', 1, '2018-12-02 04:16:53', 1, NULL, NULL),
	(63, 69, 0.00, '2018-12-02 04:27:08', 1, '2018-12-02 04:27:08', 1, NULL, NULL),
	(64, 7, 0.00, '2018-12-02 08:57:50', 1, '2018-12-02 08:57:50', 1, NULL, NULL),
	(65, 7, 0.00, '2018-12-02 09:00:08', 1, '2018-12-02 09:00:08', 1, NULL, NULL),
	(66, 70, 0.00, '2018-12-02 16:25:35', 1, '2018-12-02 16:25:35', 1, NULL, NULL),
	(67, 70, 0.00, '2018-12-02 16:43:54', 1, '2018-12-02 16:43:54', 1, NULL, NULL),
	(68, 70, 0.00, '2018-12-02 16:44:04', 1, '2018-12-02 16:44:04', 1, NULL, NULL),
	(69, 70, 0.00, '2018-12-02 16:45:51', 1, '2018-12-02 16:45:51', 1, NULL, NULL),
	(70, 70, 0.00, '2018-12-02 16:46:03', 1, '2018-12-02 16:46:03', 1, NULL, NULL),
	(71, 70, 0.00, '2018-12-02 16:46:11', 1, '2018-12-02 16:46:11', 1, NULL, NULL);
/*!40000 ALTER TABLE `product_shipping` ENABLE KEYS */;

-- Dumping structure for table shopu.rating
CREATE TABLE IF NOT EXISTS `rating` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `rate` tinyint(3) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- Dumping data for table shopu.rating: ~22 rows (approximately)
/*!40000 ALTER TABLE `rating` DISABLE KEYS */;
INSERT INTO `rating` (`id`, `entity`, `entity_id`, `rate`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 4, 1, 5, '2018-12-02 15:28:00', 1, '2019-01-11 18:35:28', 1, '2019-01-12 02:35:26', 1),
	(2, 4, 1, 2, '2018-12-02 15:28:10', 1, '2019-01-11 18:35:29', 1, '2019-01-12 02:35:24', 1),
	(3, 4, 1, 2, '2018-12-02 15:28:17', 1, '2019-01-11 18:35:29', 1, '2019-01-12 02:35:23', 1),
	(4, 4, 1, 3, '2018-12-02 15:28:21', 1, '2019-01-11 18:35:30', 1, '2019-01-12 02:35:22', 1),
	(5, 4, 1, 4, '2018-12-02 15:28:24', 1, '2019-01-11 18:35:30', 1, '2019-01-12 02:35:21', 1),
	(6, 4, 1, 4, '2018-12-02 15:28:28', 1, '2019-01-11 18:35:31', 1, '2019-01-12 02:35:20', 1),
	(7, 4, 1, 4, '2018-12-02 15:28:32', 1, '2019-01-11 18:35:32', 1, '2019-01-11 18:20:18', 1),
	(8, 4, 1, 5, '2019-01-11 18:18:24', 1, '2019-01-11 18:18:30', 1, '2019-01-11 18:18:30', 1),
	(9, 4, 1, 4, '2019-01-11 18:18:30', 1, '2019-01-11 18:18:30', 1, '2019-01-11 18:18:30', 1),
	(10, 4, 1, 5, '2019-01-11 18:18:33', 1, '2019-01-11 18:18:36', 1, '2019-01-11 18:18:36', 1),
	(11, 4, 1, 3, '2019-01-11 18:18:36', 1, '2019-01-11 18:18:36', 1, '2019-01-11 18:18:36', 1),
	(12, 4, 1, 3, '2019-01-11 18:19:38', 1, '2019-01-11 18:20:18', 1, '2019-01-11 18:20:18', 1),
	(13, 4, 1, 1, '2019-01-11 18:20:18', 1, '2019-01-11 18:20:18', 1, '2019-01-11 18:20:18', 1),
	(14, 4, 1, 1, '2019-01-11 18:39:12', 1, '2019-01-11 18:40:06', 1, '2019-01-11 18:40:06', 1),
	(15, 4, 1, 2, '2019-01-11 18:40:53', 1, '2019-01-11 18:40:53', 1, NULL, NULL),
	(16, 4, 1, 2, '2019-01-11 18:42:03', 6, '2019-01-11 18:42:33', 6, '2019-01-11 18:42:33', 6),
	(17, 4, 1, 5, '2019-01-11 18:42:33', 6, '2019-01-11 18:42:57', 6, '2019-01-11 18:42:57', 6),
	(18, 4, 1, 4, '2019-01-11 18:42:57', 6, '2019-01-11 18:42:57', 6, NULL, NULL),
	(19, 4, 3, 3, '2019-01-11 18:47:11', 6, '2019-01-11 18:47:16', 6, '2019-01-11 18:47:16', 6),
	(20, 4, 3, 3, '2019-01-11 18:47:16', 6, '2019-01-11 18:47:19', 6, '2019-01-11 18:47:19', 6),
	(21, 4, 3, 4, '2019-01-11 18:47:19', 6, '2019-01-11 18:47:21', 6, '2019-01-11 18:47:21', 6),
	(22, 4, 3, 5, '2019-01-11 18:47:21', 6, '2019-01-11 18:47:21', 6, NULL, NULL);
/*!40000 ALTER TABLE `rating` ENABLE KEYS */;

-- Dumping structure for table shopu.shop
CREATE TABLE IF NOT EXISTS `shop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(255) NOT NULL,
  `name_tc` varchar(255) DEFAULT NULL,
  `name_sc` varchar(255) DEFAULT NULL,
  `description_en` text NOT NULL,
  `description_tc` text DEFAULT NULL,
  `description_sc` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Dumping data for table shopu.shop: ~6 rows (approximately)
/*!40000 ALTER TABLE `shop` DISABLE KEYS */;
INSERT INTO `shop` (`id`, `name_en`, `name_tc`, `name_sc`, `description_en`, `description_tc`, `description_sc`, `user_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 'Weinstein Guitars', NULL, NULL, 'Weinstein Pianos & Guitars.', NULL, NULL, 1201, '2018-12-02 14:13:09', 1, '2018-12-02 14:41:31', 1, NULL, NULL),
	(2, 'Gedric\'s Action Figures', NULL, NULL, 'Collectors toys and action figures for buying and selling.', NULL, NULL, NULL, '2018-12-02 16:39:59', 1, '2018-12-02 17:02:45', 1, '2018-12-02 17:02:45', 1),
	(3, 'Gedric\'s Toy Collections', 'Mattel Action Figures', NULL, 'Collectors toys and action figures for buying and selling.', NULL, NULL, NULL, '2018-12-02 16:42:58', 1, '2018-12-07 16:29:40', 1, NULL, NULL),
	(4, 'Audacity Music Shop', NULL, NULL, 'Music Store, etc.', NULL, NULL, NULL, '2018-12-07 16:25:27', 1, '2018-12-07 16:28:51', 1, '2018-12-07 16:28:51', 1),
	(5, 'The Dirty Deeds Bike Shop', NULL, NULL, '', NULL, NULL, 9, '2019-01-08 17:08:44', 9, '2019-01-08 17:08:44', 9, NULL, NULL),
	(6, 'The Roman Empire', NULL, NULL, '', NULL, NULL, 13, '2019-01-08 17:47:43', 13, '2019-01-08 17:47:43', 13, NULL, NULL);
/*!40000 ALTER TABLE `shop` ENABLE KEYS */;

-- Dumping structure for table shopu.size
CREATE TABLE IF NOT EXISTS `size` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.size: ~3 rows (approximately)
/*!40000 ALTER TABLE `size` DISABLE KEYS */;
INSERT INTO `size` (`id`, `code`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 'S', 'Small', '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(2, 'M', 'Medium', '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(3, 'L', 'Large', '2018-11-26 15:27:44', 1, '2018-11-26 15:39:16', 1, NULL, NULL);
/*!40000 ALTER TABLE `size` ENABLE KEYS */;

-- Dumping structure for table shopu.status
CREATE TABLE IF NOT EXISTS `status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.status: ~9 rows (approximately)
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` (`id`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 'active', '2018-11-26 15:27:44', 1, '2018-11-27 16:35:23', 1, NULL, NULL),
	(2, 'disable', '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(3, 'pause', '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(4, 'process', '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(5, 'shipment', '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(6, 'fininsh', '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(7, 'publish', '2018-12-22 10:22:39', 1, '2018-12-22 10:22:51', 1, NULL, NULL),
	(8, 'draft', '2018-12-22 10:22:46', 1, '2018-12-22 10:22:52', 1, NULL, NULL),
	(9, 'pending', '2019-01-07 17:33:15', 1, '2019-01-07 17:33:15', 1, NULL, NULL);
/*!40000 ALTER TABLE `status` ENABLE KEYS */;

-- Dumping structure for table shopu.status_map
CREATE TABLE IF NOT EXISTS `status_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table shopu.status_map: ~104 rows (approximately)
/*!40000 ALTER TABLE `status_map` DISABLE KEYS */;
INSERT INTO `status_map` (`id`, `entity`, `entity_id`, `status_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 1, 1, 2, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(2, 1, 2, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(3, 1, 3, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(17, 1, 29, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(20, 1, 32, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:06:23', 1, '2018-12-02 09:06:22', 1),
	(23, 1, 35, 1, '2018-11-26 15:27:44', 1, '2018-12-02 08:54:17', 1, '2018-12-02 08:54:16', 1),
	(24, 3, 1, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(25, 3, 2, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(26, 3, 4, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(27, 3, 5, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(28, 3, 6, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(29, 3, 30, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:06:49', 1, '2018-12-02 09:06:48', 1),
	(30, 3, 32, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(31, 3, 33, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:06:44', 1, '2018-12-02 09:06:44', 1),
	(32, 3, 34, 1, '2018-11-26 15:27:44', 1, '2018-12-02 08:54:57', 1, '2018-12-02 08:54:56', 1),
	(33, 3, 35, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(34, 3, 36, 1, '2018-11-26 15:27:44', 1, '2018-12-02 08:54:31', 1, '2018-12-02 08:54:31', 1),
	(38, 3, 40, 1, '2018-11-26 15:27:44', 1, '2018-12-02 08:55:15', 1, '2018-12-02 08:55:15', 1),
	(39, 3, 41, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(40, 3, 42, 1, '2018-11-26 15:27:44', 1, '2018-12-02 08:54:40', 1, '2018-12-02 08:54:39', 1),
	(41, 3, 43, 1, '2018-11-26 15:27:44', 1, '2018-12-02 08:08:09', 1, '2018-12-02 08:08:08', 1),
	(42, 3, 44, 1, '2018-11-26 15:27:44', 1, '2018-12-02 08:08:18', 1, '2018-12-02 08:08:18', 1),
	(43, 3, 45, 2, '2018-11-26 15:27:44', 1, '2018-12-02 08:08:23', 1, '2018-12-02 08:08:22', 1),
	(44, 3, 46, 2, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(45, 3, 49, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(46, 3, 50, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(47, 3, 51, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(48, 1, 6, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(49, 1, 7, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:03:52', 1, '2018-12-02 09:03:52', 1),
	(50, 1, 8, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:03:59', 1, '2018-12-02 09:03:59', 1),
	(51, 3, 52, 1, '2018-11-26 15:27:44', 1, '2018-12-02 08:09:54', 1, '2018-12-02 08:09:54', 1),
	(63, 3, 69, 2, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(65, 3, 71, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:06:35', 1, '2018-12-02 09:06:34', 1),
	(69, 3, 77, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(72, 3, 80, 2, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(73, 3, 81, 1, '2018-11-26 15:27:44', 1, '2018-12-07 17:12:52', 1, '2018-12-07 17:12:51', 1),
	(74, 3, 82, 1, '2018-11-26 15:27:44', 1, '2018-12-07 17:12:44', 1, '2018-12-07 17:12:43', 1),
	(75, 3, 87, 2, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(76, 3, 88, 1, '2018-11-26 15:27:44', 1, '2018-11-26 15:27:44', 1, NULL, NULL),
	(77, 1, 36, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:09', 1, '2018-12-02 09:04:09', 1),
	(79, 1, 38, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:16', 1, '2018-12-02 09:04:16', 1),
	(82, 3, 92, 1, '2018-11-26 15:27:44', 1, '2018-11-27 17:21:48', 1, NULL, NULL),
	(83, 3, 93, 1, '2018-11-26 15:27:44', 1, '2018-12-07 17:12:23', 1, '2018-12-07 17:12:23', 1),
	(84, 3, 94, 1, '2018-11-26 15:27:44', 1, '2018-12-07 17:12:28', 1, '2018-12-07 17:12:28', 1),
	(85, 1, 40, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:24', 1, '2018-12-02 09:04:24', 1),
	(86, 1, 41, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:26', 1, '2018-12-02 09:04:26', 1),
	(109, 1, 64, 1, '2018-11-26 15:27:44', 1, '2018-12-02 09:04:32', 1, '2018-12-02 09:04:31', 1),
	(110, 1, 65, 1, '2018-11-26 15:27:44', 1, '2018-11-27 16:24:02', 1, '2018-11-27 16:24:01', 1),
	(112, 3, 97, 2, '2018-11-27 15:42:48', 1, '2018-11-27 15:52:57', 1, '2018-11-27 15:52:56', 1),
	(113, 3, 98, 1, '2018-11-27 15:48:11', 1, '2018-11-27 16:55:11', 1, '2018-11-27 16:55:11', 1),
	(114, 3, 99, 1, '2018-11-27 16:10:26', 1, '2018-11-27 16:12:09', 1, '2018-11-27 16:12:08', 1),
	(115, 3, 100, 1, '2018-11-27 16:47:24', 1, '2018-11-27 16:47:53', 1, '2018-11-27 16:47:53', 1),
	(116, 3, 101, 1, '2018-11-27 16:54:39', 1, '2018-12-02 08:09:31', 1, '2018-12-02 08:09:30', 1),
	(117, 1, 67, 2, '2018-11-27 17:46:00', 1, '2018-11-27 17:49:42', 1, '2018-11-27 17:49:42', 1),
	(118, 1, 68, 1, '2018-12-02 04:16:52', 1, '2018-12-02 04:16:52', 1, NULL, NULL),
	(119, 1, 69, 1, '2018-12-02 04:27:08', 1, '2018-12-02 04:27:08', 1, NULL, NULL),
	(120, 4, 1, 1, '2018-12-02 14:47:51', 1, '2018-12-02 14:47:51', 1, NULL, NULL),
	(121, 1, 70, 1, '2018-12-02 16:25:35', 1, '2018-12-02 16:25:35', 1, NULL, NULL),
	(122, 3, 102, 1, '2018-12-02 16:38:06', 1, '2018-12-02 16:38:06', 1, NULL, NULL),
	(123, 4, 2, 1, '2018-12-02 16:39:59', 1, '2018-12-02 17:02:45', 1, '2018-12-02 17:02:45', 1),
	(124, 4, 3, 1, '2018-12-02 16:42:58', 1, '2018-12-02 16:42:58', 1, NULL, NULL),
	(125, 4, 2, 2, '2018-12-02 16:56:01', 1, '2018-12-02 16:56:01', 1, NULL, NULL),
	(126, 3, 103, 1, '2018-12-07 15:49:08', 1, '2018-12-07 15:59:12', 1, '2018-12-07 23:59:08', 1),
	(127, 3, 104, 1, '2018-12-07 15:51:04', 1, '2018-12-07 15:51:58', 1, '2018-12-07 15:51:57', 1),
	(128, 3, 105, 1, '2018-12-07 16:03:46', 1, '2018-12-07 16:03:46', 1, NULL, NULL),
	(129, 3, 106, 2, '2018-12-07 16:04:08', 1, '2018-12-07 16:07:36', 1, '2018-12-07 16:07:32', 1),
	(130, 3, 107, 2, '2018-12-07 16:05:16', 1, '2018-12-07 16:08:20', 1, NULL, NULL),
	(131, 3, 108, 1, '2018-12-07 16:05:33', 1, '2018-12-07 16:05:33', 1, NULL, NULL),
	(132, 3, 109, 1, '2018-12-07 16:05:48', 1, '2018-12-07 16:05:48', 1, NULL, NULL),
	(133, 3, 110, 1, '2018-12-07 16:10:53', 1, '2018-12-07 16:11:45', 1, '2018-12-07 16:11:44', 1),
	(134, 3, 111, 1, '2018-12-07 16:11:06', 1, '2018-12-07 16:12:32', 1, NULL, NULL),
	(135, 4, 4, 1, '2018-12-07 16:25:27', 1, '2018-12-07 16:28:51', 1, '2018-12-07 16:28:51', 1),
	(136, 5, 1, 1, '2018-12-08 02:39:05', 1, '2018-12-08 02:39:45', 1, NULL, NULL),
	(137, 5, 2, 2, '2018-12-08 02:39:13', 1, '2018-12-08 02:45:50', 1, NULL, NULL),
	(138, 5, 3, 1, '2018-12-08 02:39:16', 1, '2018-12-08 02:45:45', 1, '2018-12-08 10:45:42', 1),
	(139, 5, 4, 1, '2018-12-08 02:39:27', 1, '2018-12-08 02:39:48', 1, NULL, NULL),
	(140, 5, 5, 2, '2018-12-08 02:58:54', 1, '2018-12-08 06:03:24', 1, NULL, NULL),
	(141, 5, 6, 1, '2018-12-08 05:39:56', 1, '2018-12-08 05:45:24', 1, '2018-12-08 05:45:24', 1),
	(142, 3, 112, 1, '2018-12-09 05:37:27', 1, '2018-12-09 05:38:24', 1, NULL, NULL),
	(143, 3, 114, 1, '2018-12-09 05:40:01', 1, '2018-12-09 05:40:01', 1, NULL, NULL),
	(144, 3, 115, 2, '2018-12-09 05:40:10', 1, '2018-12-09 05:42:56', 1, NULL, NULL),
	(145, 6, 1, 8, '2018-12-09 05:44:46', 1, '2018-12-22 10:27:46', 1, NULL, NULL),
	(146, 5, 7, 1, '2018-12-09 06:59:46', 1, '2018-12-09 06:59:46', 1, NULL, NULL),
	(147, 5, 8, 1, '2018-12-09 07:00:43', 1, '2018-12-09 07:00:43', 1, NULL, NULL),
	(148, 6, 2, 8, '2018-12-22 10:27:54', 1, '2018-12-22 10:28:19', 1, NULL, NULL),
	(149, 6, 3, 7, '2018-12-22 10:28:09', 1, '2018-12-22 10:28:20', 1, NULL, NULL),
	(150, 6, 5, 8, '2018-12-23 09:36:25', 1, '2018-12-23 16:42:28', 1, '2018-12-23 16:42:28', 1),
	(151, 6, 6, 8, '2018-12-23 09:37:08', 1, '2018-12-23 09:39:46', 1, '2018-12-23 09:39:46', 1),
	(152, 6, 5, 8, '2018-12-23 16:42:28', 1, '2018-12-23 16:43:45', 1, '2018-12-23 16:43:45', 1),
	(153, 6, 5, 6, '2018-12-23 16:43:45', 1, '2018-12-23 16:43:45', 1, NULL, NULL),
	(154, 7, 1, 1, '2019-01-06 05:32:20', 1, '2019-01-06 05:32:23', 1, NULL, NULL),
	(155, 3, 116, 1, '2019-01-06 09:54:01', 1, '2019-01-06 09:57:44', 1, '2019-01-06 09:57:43', 1),
	(156, 7, 2, 1, '2019-01-07 17:53:03', 2, '2019-01-07 17:53:03', 2, NULL, NULL),
	(157, 7, 3, 1, '2019-01-07 17:57:42', 3, '2019-01-07 17:57:42', 3, NULL, NULL),
	(158, 7, 4, 1, '2019-01-07 19:00:53', 4, '2019-01-07 19:00:53', 4, NULL, NULL),
	(159, 7, 5, 1, '2019-01-07 19:01:08', 5, '2019-01-07 19:01:08', 5, NULL, NULL),
	(160, 7, 6, 1, '2019-01-07 19:01:19', 6, '2019-01-07 19:01:19', 6, NULL, NULL),
	(161, 7, 7, 1, '2019-01-07 19:01:29', 7, '2019-01-07 19:01:29', 7, NULL, NULL),
	(162, 7, 8, 1, '2019-01-07 19:01:38', 8, '2019-01-07 19:01:38', 8, NULL, NULL),
	(163, 7, 9, 1, '2019-01-08 17:08:44', 9, '2019-01-08 17:08:44', 9, NULL, NULL),
	(164, 4, 5, 1, '2019-01-08 17:08:44', 9, '2019-01-08 17:08:44', 9, NULL, NULL),
	(165, 7, 10, 1, '2019-01-08 17:46:11', 10, '2019-01-08 17:46:11', 10, NULL, NULL),
	(168, 7, 13, 1, '2019-01-08 17:47:43', 13, '2019-01-08 17:47:43', 13, NULL, NULL),
	(169, 4, 6, 1, '2019-01-08 17:47:43', 13, '2019-01-08 17:47:43', 13, NULL, NULL);
/*!40000 ALTER TABLE `status_map` ENABLE KEYS */;

-- Dumping structure for table shopu.status_option
CREATE TABLE IF NOT EXISTS `status_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Dumping data for table shopu.status_option: ~14 rows (approximately)
/*!40000 ALTER TABLE `status_option` DISABLE KEYS */;
INSERT INTO `status_option` (`id`, `entity`, `status_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 3, 1, '2018-11-15 09:57:51', 1, '2018-11-27 16:35:20', 1, NULL, NULL),
	(2, 3, 2, '2018-11-15 09:58:02', 1, '2018-11-15 09:58:02', 1, NULL, NULL),
	(3, 1, 1, '2018-11-16 04:55:07', 1, '2018-11-27 16:35:19', 1, NULL, NULL),
	(4, 1, 2, '2018-11-16 04:55:10', 1, '2018-11-16 04:55:10', 1, NULL, NULL),
	(5, 1, 3, '2018-11-16 04:55:12', 1, '2018-11-16 04:55:12', 1, NULL, NULL),
	(6, 4, 1, '2018-11-25 12:26:19', 1, '2018-12-02 14:47:19', 1, NULL, NULL),
	(7, 4, 2, '2018-12-02 14:47:17', 1, '2018-12-02 14:47:23', 1, NULL, NULL),
	(8, 5, 1, '2018-12-08 02:33:24', 1, '2018-12-08 02:33:37', 1, NULL, NULL),
	(9, 5, 2, '2018-12-08 02:33:26', 1, '2018-12-08 02:33:38', 1, NULL, NULL),
	(10, 6, 7, '2018-12-09 05:25:49', 1, '2018-12-22 10:24:41', 1, NULL, NULL),
	(11, 6, 8, '2018-12-09 05:25:51', 1, '2018-12-22 10:24:43', 1, NULL, NULL),
	(12, 6, 6, '2018-12-22 10:24:52', 1, '2018-12-22 10:24:52', 1, NULL, NULL),
	(13, 7, 1, '2019-01-06 05:31:46', 1, '2019-01-06 05:31:57', 1, NULL, NULL),
	(14, 7, 2, '2019-01-06 05:31:50', 1, '2019-01-06 05:31:58', 1, NULL, NULL);
/*!40000 ALTER TABLE `status_option` ENABLE KEYS */;

-- Dumping structure for table shopu.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `salt` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type_id` int(11) NOT NULL,
  `activation_key` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table shopu.user: ~11 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `username`, `email`, `salt`, `password`, `first_name`, `middle_name`, `last_name`, `gender`, `birth_date`, `mobile_phone`, `address`, `user_type_id`, `activation_key`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 'jc', 'jancarlotaylo@gmail.com', '$2a$12$0a26c90d00f82bf9bb023afed0ce8b4e', '$2a$12$0a26c90d00f82bf9bb023ObySkwVf3XBzan4MTvQHtgHvjXCaShcO', 'JC', NULL, 'Taylo', 'M', NULL, '+639954387373', NULL, 1, NULL, '2019-01-06 05:23:28', 1, '2019-01-06 05:23:28', 1, NULL, NULL),
	(2, 'jctaylo', 'jancarlotaylo+dev1@gmail.com', '$2a$12$0b2a33d2447441b051f3db53bda76b53', '$2a$12$0b2a33d2447441b051f3dOyGh9ORksV7uUVCo0lHBwnf5iOkAot4W', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, '3f4f91ef0c5cdb41d184487663364541', '2019-01-07 17:53:03', 2, '2019-01-07 17:53:03', 2, NULL, NULL),
	(3, 'jctaylo_dev2', 'jancarlotaylo+dev2@gmail.com', '$2a$12$9b175d87318fe393284f4ffb6c6c764f', '$2a$12$9b175d87318fe393284f4eYpCmeEa7eJYInR7.l.Yzk9DuarHqZ.e', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 'd4b7e14c0612cd57f172a91a412d3b68', '2019-01-07 17:57:42', 3, '2019-01-07 17:57:42', 3, NULL, NULL),
	(4, 'karen', 'karen@shopu.com', '$2a$12$8481129bce309ce118185059a59d2a2c', '$2a$12$8481129bce309ce118185uNgzPAPxqVJaplzGBD2G6PJXTZw3PBAK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, '2dbd5d93c39a6ca75489b8b99e1910aa', '2019-01-07 19:00:52', 4, '2019-01-07 19:00:52', 4, NULL, NULL),
	(5, 'charmaine', 'charmaine@shopu.com', '$2a$12$33810ac42588031e7b7a5f88ede6602e', '$2a$12$33810ac42588031e7b7a5e1NGLHX8f9WP7UV0sEL27.gS0FKl7A.u', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 'f02419ee5276dd5cd559bd3f420a4fc2', '2019-01-07 19:01:08', 5, '2019-01-07 19:01:08', 5, NULL, NULL),
	(6, 'grace', 'grace@shopu.com', '$2a$12$df22f6d4c28a4aa5c790292aa477f3b2', '$2a$12$df22f6d4c28a4aa5c7902uRfjWczxcWYBETHlWAZKsfKSweU.UDv2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 'f8b30a99aa0e9a6bef8ea8eee6cac1e7', '2019-01-07 19:01:19', 6, '2019-01-07 19:01:19', 6, NULL, NULL),
	(7, 'joshua', 'joshua@shopu.com', '$2a$12$29fb1171f3e99c382cda1cbdb779a20a', '$2a$12$29fb1171f3e99c382cda1OgB47FHmUDcwLkWBRjkkFNYpsDrYo22q', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 'a129c856cf672862fc0fbef42c693752', '2019-01-07 19:01:29', 7, '2019-01-07 19:01:29', 7, NULL, NULL),
	(8, 'ray', 'ray@shopu.com', '$2a$12$b6da0fc3fed9264655c4e3e9d30bff84', '$2a$12$b6da0fc3fed9264655c4eut77l9GijXl7rRfMr/F6OcXwD4YEpdo2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 'a160d91101518f6053482622a9d3e3b1', '2019-01-07 19:01:38', 8, '2019-01-07 19:01:38', 8, NULL, NULL),
	(9, 'dean', 'dean.ambrose@wwe.com', '$2a$12$61c0332e8cb93dddadf9aeeb0ac8e7fd', '$2a$12$61c0332e8cb93dddadf9aejtg9pQB2Z6scaZID4./Rd2gPCEH20zq', 'Dean', NULL, 'Ambrose', NULL, NULL, NULL, NULL, 3, NULL, '2019-01-08 17:08:44', 9, '2019-01-08 17:08:44', 9, NULL, NULL),
	(10, 'seth', 'seth.rollins@wwe.com', '$2a$12$d8ba31d085e1b4d5ffcbeaf0ecdcaf7d', '$2a$12$d8ba31d085e1b4d5ffcbeO.IqJBFPCUOm9Tq3ltebJTA9X3EzqtrS', 'Seth', NULL, 'Rollins', NULL, NULL, NULL, NULL, 2, NULL, '2019-01-08 17:46:10', 10, '2019-01-08 17:46:11', 10, NULL, NULL),
	(13, 'roman', 'roman.reigns@wwe.com', '$2a$12$f549509ffe387c63064d360597d02021', '$2a$12$f549509ffe387c63064d3uIpFyn/uxgQ./maHxQuC4dBmzEu/m1/K', 'Roman', NULL, 'Reigns', NULL, NULL, NULL, NULL, 3, NULL, '2019-01-08 17:47:43', 13, '2019-01-08 17:47:43', 13, NULL, NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Dumping structure for table shopu.user_type
CREATE TABLE IF NOT EXISTS `user_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table shopu.user_type: ~4 rows (approximately)
/*!40000 ALTER TABLE `user_type` DISABLE KEYS */;
INSERT INTO `user_type` (`id`, `name`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(1, 'System Admin', '2019-01-06 04:12:25', 1, '2019-01-06 04:12:25', 1, NULL, NULL),
	(2, 'System Operator', '2019-01-06 04:12:32', 1, '2019-01-06 04:12:32', 1, NULL, NULL),
	(3, 'Retailer', '2019-01-06 04:12:36', 1, '2019-01-06 04:12:36', 1, NULL, NULL),
	(4, 'Consumer', '2019-01-06 04:20:15', 1, '2019-01-06 04:20:15', 1, NULL, NULL);
/*!40000 ALTER TABLE `user_type` ENABLE KEYS */;

-- Dumping structure for table shopu.view
CREATE TABLE IF NOT EXISTS `view` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

-- Dumping data for table shopu.view: ~47 rows (approximately)
/*!40000 ALTER TABLE `view` DISABLE KEYS */;
INSERT INTO `view` (`id`, `entity`, `entity_id`, `user_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
	(2, 1, 1, 2, '2018-11-16 15:33:04', 1, '2018-11-16 15:33:04', 1, NULL, NULL),
	(3, 1, 1, 3, '2018-11-16 15:33:07', 1, '2018-11-16 15:33:07', 1, NULL, NULL),
	(4, 1, 1, 4, '2018-11-16 15:33:08', 1, '2018-11-16 15:33:08', 1, NULL, NULL),
	(5, 1, 1, 5, '2018-11-16 15:33:10', 1, '2018-11-16 15:33:10', 1, NULL, NULL),
	(7, 1, 2, 1, '2018-11-16 16:19:36', 1, '2018-11-16 16:19:36', 1, NULL, NULL),
	(8, 1, 2, 1, '2018-11-16 16:19:38', 1, '2018-11-16 16:19:38', 1, NULL, NULL),
	(9, 1, 2, 1, '2018-11-16 16:19:39', 1, '2018-11-16 16:19:39', 1, NULL, NULL),
	(10, 1, 2, 1, '2018-11-16 16:19:41', 1, '2018-11-16 16:19:41', 1, NULL, NULL),
	(11, 1, 2, 1, '2018-11-16 16:19:41', 1, '2018-11-16 16:19:41', 1, NULL, NULL),
	(12, 1, 2, 1, '2018-11-16 16:19:41', 1, '2018-11-16 16:19:41', 1, NULL, NULL),
	(13, 1, 2, 1, '2018-11-16 16:19:41', 1, '2018-11-16 16:19:41', 1, NULL, NULL),
	(14, 1, 2, 1, '2018-11-16 16:19:42', 1, '2018-11-16 16:19:42', 1, NULL, NULL),
	(15, 1, 2, 1, '2018-11-16 16:19:42', 1, '2018-11-16 16:19:42', 1, NULL, NULL),
	(16, 1, 2, 1, '2018-11-16 16:19:42', 1, '2018-11-16 16:19:42', 1, NULL, NULL),
	(17, 1, 2, 1, '2018-11-16 16:19:43', 1, '2018-11-16 16:19:43', 1, NULL, NULL),
	(18, 1, 2, 1, '2018-11-16 16:19:43', 1, '2018-11-16 16:19:43', 1, NULL, NULL),
	(19, 1, 2, 1, '2018-11-16 16:19:43', 1, '2018-11-16 16:19:43', 1, NULL, NULL),
	(20, 1, 2, 1, '2018-11-16 16:19:43', 1, '2018-11-16 16:19:43', 1, NULL, NULL),
	(21, 1, 2, 1, '2018-11-16 16:19:43', 1, '2018-11-16 16:19:43', 1, NULL, NULL),
	(22, 1, 2, 1, '2018-11-16 16:19:44', 1, '2018-11-16 16:19:44', 1, NULL, NULL),
	(23, 1, 2, 1, '2018-11-16 16:19:44', 1, '2018-11-16 16:19:44', 1, NULL, NULL),
	(24, 1, 2, 1, '2018-11-16 16:19:44', 1, '2018-11-16 16:19:44', 1, NULL, NULL),
	(25, 1, 2, 1, '2018-11-16 16:19:44', 1, '2018-11-16 16:19:44', 1, NULL, NULL),
	(26, 1, 2, 1, '2018-11-16 16:19:44', 1, '2018-11-16 16:19:44', 1, NULL, NULL),
	(27, 1, 2, 1, '2018-11-16 16:19:45', 1, '2018-11-16 16:19:45', 1, NULL, NULL),
	(28, 1, 2, 1, '2018-11-16 16:19:45', 1, '2018-11-16 16:19:45', 1, NULL, NULL),
	(29, 1, 2, 1, '2018-11-16 16:19:45', 1, '2018-11-16 16:19:45', 1, NULL, NULL),
	(30, 1, 2, 1, '2018-11-16 16:19:46', 1, '2018-11-16 16:19:46', 1, NULL, NULL),
	(31, 1, 2, 1, '2018-11-16 16:19:46', 1, '2018-11-16 16:19:46', 1, NULL, NULL),
	(32, 1, 2, 1, '2018-11-16 16:19:46', 1, '2018-11-16 16:19:46', 1, NULL, NULL),
	(33, 1, 64, 123, '2018-11-27 16:24:26', 1, '2018-11-27 16:24:26', 1, NULL, NULL),
	(34, 1, 64, 124, '2018-11-27 16:24:32', 1, '2018-11-27 16:24:32', 1, NULL, NULL),
	(35, 1, 64, 125, '2018-11-27 16:24:33', 1, '2018-11-27 16:24:33', 1, NULL, NULL),
	(36, 1, 29, 7801, '2018-11-27 16:57:54', 1, '2018-11-27 16:57:54', 1, NULL, NULL),
	(37, 1, 29, 7802, '2018-11-27 16:57:56', 1, '2018-11-27 16:57:56', 1, NULL, NULL),
	(38, 1, 29, 7803, '2018-11-27 16:57:57', 1, '2018-11-27 16:57:57', 1, NULL, NULL),
	(39, 1, 29, 7804, '2018-11-27 16:57:59', 1, '2018-11-27 16:59:31', 1, '2018-11-28 00:59:29', 1),
	(40, 1, 29, 7805, '2018-11-27 16:58:01', 1, '2018-11-27 16:58:01', 1, NULL, NULL),
	(41, 1, 29, 7806, '2018-11-27 16:58:02', 1, '2018-11-27 16:59:24', 1, NULL, NULL),
	(42, 1, 29, 7807, '2018-11-27 16:58:04', 1, '2018-11-27 16:58:04', 1, NULL, NULL),
	(43, 1, 29, 7808, '2018-11-27 16:58:06', 1, '2018-11-27 16:58:06', 1, NULL, NULL),
	(44, 1, 29, 7809, '2018-11-27 16:58:07', 1, '2018-11-27 16:58:07', 1, NULL, NULL),
	(45, 1, 29, 7810, '2018-11-27 16:58:10', 1, '2018-11-27 16:58:10', 1, NULL, NULL),
	(46, 1, 29, 7811, '2018-11-27 16:58:12', 1, '2018-11-27 16:58:12', 1, NULL, NULL),
	(47, 1, 29, 7812, '2018-11-27 16:58:14', 1, '2018-11-27 16:58:14', 1, NULL, NULL),
	(48, 1, 29, 7813, '2018-11-27 16:58:16', 1, '2018-11-27 16:58:16', 1, NULL, NULL),
	(49, 1, 29, 7814, '2018-11-27 16:58:17', 1, '2018-11-27 16:58:17', 1, NULL, NULL),
	(50, 6, 1, 7110, '2018-12-09 05:52:20', 1, '2018-12-09 05:52:23', 1, NULL, NULL),
	(51, 6, 1, 7111, '2018-12-09 06:08:24', 1, '2018-12-09 06:08:24', 1, NULL, NULL),
	(52, 6, 1, 7112, '2018-12-09 06:08:28', 1, '2018-12-09 06:08:28', 1, NULL, NULL),
	(53, 6, 1, 7113, '2018-12-09 06:08:33', 1, '2018-12-09 06:08:33', 1, NULL, NULL);
/*!40000 ALTER TABLE `view` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
