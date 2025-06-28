-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 25 juin 2025 à 09:49
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `physiquepro`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `comment_text` text NOT NULL,
  `is_question` tinyint(1) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `replied_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `content`
--

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `content_text` longtext DEFAULT NULL,
  `teacher_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `subject` enum('physics','chemistry','both') DEFAULT 'both',
  `file_path` varchar(500) DEFAULT NULL,
  `google_drive_url` varchar(500) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `views_count` int(11) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0,
  `downloads_count` int(11) DEFAULT 0,
  `status` enum('draft','pending','published','rejected') DEFAULT 'draft',
  `rejection_reason` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `published_at` timestamp NULL DEFAULT NULL,
  `experiment_code` text DEFAULT NULL,
  `linked_lesson_id` int(11) DEFAULT NULL,
  `experiment_type` enum('document','video','simulation') DEFAULT NULL,
  `objectives` text DEFAULT NULL,
  `duration` int(11) DEFAULT 0,
  `is_interactive` tinyint(1) DEFAULT 0,
  `total_cards` int(11) DEFAULT 0,
  `estimated_duration` int(11) DEFAULT 0,
  `html_content` longtext DEFAULT NULL,
  `css_content` text DEFAULT NULL,
  `js_content` text DEFAULT NULL,
  `comment_count` int(11) DEFAULT 0,
  `rating_average` decimal(3,2) DEFAULT 0.00,
  `rating_count` int(11) DEFAULT 0,
  `google_drive_id` varchar(255) DEFAULT NULL,
  `storage_type` enum('local','google_drive') DEFAULT 'local'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `content_files`
--

CREATE TABLE `content_files` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `google_drive_id` varchar(255) DEFAULT NULL,
  `storage_type` enum('local','google_drive') DEFAULT 'local'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `content_ratings`
--

CREATE TABLE `content_ratings` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `content_types`
--

CREATE TABLE `content_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `name_ar` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `order_num` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `content_types`
--

INSERT INTO `content_types` (`id`, `name`, `name_ar`, `code`, `icon`, `color`, `description`, `status`, `order_num`) VALUES
(1, 'lessons', 'الدروس', 'lessons', 'fas fa-book-open', '#4CAF50', 'محتوى تعليمي نظري', 'active', 10),
(2, 'exercises', 'التمارين', 'exercises', 'fas fa-pencil-alt', '#2196F3', 'تطبيقات عملية للمفاهيم', 'active', 20),
(3, 'assignments', 'الفروض', 'assignments', 'fas fa-clipboard-check', '#FF9800', 'تقييمات دورية', 'active', 30),
(4, 'experiments', 'التجارب', 'experiments', 'fas fa-vial', '#E91E63', 'تجارب علمية تفاعلية', 'active', 40),
(5, 'exams', 'الامتحانات', 'exams', 'fas fa-file-alt', '#9C27B0', 'تقييمات نهائية', 'active', 50),
(8, 'interactive', 'الدروس التفاعلية', 'interactive_lessons', 'fas fa-play-circle', '#00BCD4', 'دروس تفاعلية بنظام البطاقات', 'active', 80);

-- --------------------------------------------------------

--
-- Structure de la table `content_views`
--

CREATE TABLE `content_views` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exam_completed`
--

CREATE TABLE `exam_completed` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `experiments`
--

CREATE TABLE `experiments` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `objective` text DEFAULT NULL,
  `materials` text DEFAULT NULL,
  `procedures` longtext DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `conclusion` text DEFAULT NULL,
  `safety_notes` text DEFAULT NULL,
  `teacher_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `subject` enum('physics','chemistry','both') DEFAULT 'both',
  `duration` int(11) DEFAULT 60,
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `video_url` varchar(500) DEFAULT NULL,
  `images` text DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `views_count` int(11) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0,
  `status` enum('draft','pending','published','rejected') DEFAULT 'draft',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `experiment_charts`
--

CREATE TABLE `experiment_charts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `chart_title` varchar(255) DEFAULT NULL,
  `chart_type` enum('line','scatter','bar') DEFAULT 'line',
  `chart_data` longtext DEFAULT NULL,
  `slope` decimal(10,4) DEFAULT NULL,
  `equation` varchar(255) DEFAULT NULL,
  `r_squared` decimal(10,6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `experiment_charts`
--

INSERT INTO `experiment_charts` (`id`, `user_id`, `content_id`, `chart_title`, `chart_type`, `chart_data`, `slope`, `equation`, `r_squared`, `created_at`, `updated_at`) VALUES
(3, 6, 28, 'رسم بياني للتجربة', 'line', '{\"type\":\"line\",\"data\":{\"datasets\":[{\"label\":\"البيانات التجريبية\",\"data\":[{\"x\":5,\"y\":10},{\"x\":6,\"y\":11}],\"backgroundColor\":\"rgba(102, 126, 234, 0.6)\",\"borderColor\":\"rgba(102, 126, 234, 1)\",\"borderWidth\":3,\"pointRadius\":8,\"pointHoverRadius\":10,\"showLine\":true,\"fill\":false,\"tension\":0.1},{\"label\":\"خط الانحدار الخطي\",\"data\":[{\"x\":5,\"y\":10},{\"x\":6,\"y\":11}],\"type\":\"line\",\"backgroundColor\":\"rgba(220, 53, 69, 0.2)\",\"borderColor\":\"rgba(220, 53, 69, 1)\",\"borderWidth\":2,\"pointRadius\":0,\"fill\":false,\"borderDash\":[5,5]}],\"labels\":[]},\"options\":{\"responsive\":true,\"maintainAspectRatio\":false,\"plugins\":{\"title\":{\"display\":true,\"text\":\"رسم بياني للتجربة\",\"font\":{\"size\":16}},\"legend\":{\"display\":true}},\"scales\":{\"x\":{\"axis\":\"x\",\"type\":\"linear\",\"position\":\"bottom\",\"title\":{\"display\":true,\"text\":\"المحور X\",\"padding\":{\"top\":4,\"bottom\":4},\"color\":\"#666\"},\"ticks\":{\"minRotation\":0,\"maxRotation\":50,\"mirror\":false,\"textStrokeWidth\":0,\"textStrokeColor\":\"\",\"padding\":3,\"display\":true,\"autoSkip\":true,\"autoSkipPadding\":3,\"labelOffset\":0,\"minor\":[],\"major\":[],\"align\":\"center\",\"crossAlign\":\"near\",\"showLabelBackdrop\":false,\"backdropColor\":\"rgba(255, 255, 255, 0.75)\",\"backdropPadding\":2,\"color\":\"#666\"},\"display\":true,\"offset\":false,\"reverse\":false,\"beginAtZero\":false,\"bounds\":\"ticks\",\"clip\":true,\"grace\":0,\"grid\":{\"display\":true,\"lineWidth\":1,\"drawOnChartArea\":true,\"drawTicks\":true,\"tickLength\":8,\"tickWidth\":1,\"tickColor\":\"rgba(0,0,0,0.1)\",\"offset\":false,\"color\":\"rgba(0,0,0,0.1)\"},\"border\":{\"display\":true,\"dash\":[],\"dashOffset\":0,\"width\":1,\"color\":\"rgba(0,0,0,0.1)\"},\"id\":\"x\"},\"y\":{\"axis\":\"y\",\"title\":{\"display\":true,\"text\":\"المحور Y\",\"padding\":{\"top\":4,\"bottom\":4},\"color\":\"#666\"},\"type\":\"linear\",\"ticks\":{\"minRotation\":0,\"maxRotation\":50,\"mirror\":false,\"textStrokeWidth\":0,\"textStrokeColor\":\"\",\"padding\":3,\"display\":true,\"autoSkip\":true,\"autoSkipPadding\":3,\"labelOffset\":0,\"minor\":[],\"major\":[],\"align\":\"center\",\"crossAlign\":\"near\",\"showLabelBackdrop\":false,\"backdropColor\":\"rgba(255, 255, 255, 0.75)\",\"backdropPadding\":2,\"color\":\"#666\"},\"display\":true,\"offset\":false,\"reverse\":false,\"beginAtZero\":false,\"bounds\":\"ticks\",\"clip\":true,\"grace\":0,\"grid\":{\"display\":true,\"lineWidth\":1,\"drawOnChartArea\":true,\"drawTicks\":true,\"tickLength\":8,\"tickWidth\":1,\"tickColor\":\"rgba(0,0,0,0.1)\",\"offset\":false,\"color\":\"rgba(0,0,0,0.1)\"},\"border\":{\"display\":true,\"dash\":[],\"dashOffset\":0,\"width\":1,\"color\":\"rgba(0,0,0,0.1)\"},\"id\":\"y\",\"position\":\"left\"}}}}', NULL, NULL, NULL, '2025-06-15 18:46:52', '2025-06-15 18:46:52'),
(4, 6, 37, 'رسم بياني للتجربة', 'line', '{\"type\":\"line\",\"data\":{\"datasets\":[{\"label\":\"البيانات التجريبية\",\"data\":[{\"x\":2,\"y\":1},{\"x\":5,\"y\":3}],\"backgroundColor\":\"rgba(102, 126, 234, 0.6)\",\"borderColor\":\"rgba(102, 126, 234, 1)\",\"borderWidth\":3,\"pointRadius\":8,\"pointHoverRadius\":10,\"showLine\":true,\"fill\":false,\"tension\":0.1},{\"label\":\"خط الانحدار الخطي\",\"data\":[{\"x\":2,\"y\":1.0001},{\"x\":5,\"y\":3.0002}],\"type\":\"line\",\"backgroundColor\":\"rgba(220, 53, 69, 0.2)\",\"borderColor\":\"rgba(220, 53, 69, 1)\",\"borderWidth\":2,\"pointRadius\":0,\"fill\":false,\"borderDash\":[5,5]}],\"labels\":[]},\"options\":{\"responsive\":true,\"maintainAspectRatio\":false,\"plugins\":{\"title\":{\"display\":true,\"text\":\"رسم بياني للتجربة\",\"font\":{\"size\":16}},\"legend\":{\"display\":true}},\"scales\":{\"x\":{\"axis\":\"x\",\"type\":\"linear\",\"position\":\"bottom\",\"title\":{\"display\":true,\"text\":\"المحور X\",\"padding\":{\"top\":4,\"bottom\":4},\"color\":\"#666\"},\"ticks\":{\"minRotation\":0,\"maxRotation\":50,\"mirror\":false,\"textStrokeWidth\":0,\"textStrokeColor\":\"\",\"padding\":3,\"display\":true,\"autoSkip\":true,\"autoSkipPadding\":3,\"labelOffset\":0,\"minor\":[],\"major\":[],\"align\":\"center\",\"crossAlign\":\"near\",\"showLabelBackdrop\":false,\"backdropColor\":\"rgba(255, 255, 255, 0.75)\",\"backdropPadding\":2,\"color\":\"#666\"},\"display\":true,\"offset\":false,\"reverse\":false,\"beginAtZero\":false,\"bounds\":\"ticks\",\"clip\":true,\"grace\":0,\"grid\":{\"display\":true,\"lineWidth\":1,\"drawOnChartArea\":true,\"drawTicks\":true,\"tickLength\":8,\"tickWidth\":1,\"tickColor\":\"rgba(0,0,0,0.1)\",\"offset\":false,\"color\":\"rgba(0,0,0,0.1)\"},\"border\":{\"display\":true,\"dash\":[],\"dashOffset\":0,\"width\":1,\"color\":\"rgba(0,0,0,0.1)\"},\"id\":\"x\"},\"y\":{\"axis\":\"y\",\"title\":{\"display\":true,\"text\":\"المحور Y\",\"padding\":{\"top\":4,\"bottom\":4},\"color\":\"#666\"},\"type\":\"linear\",\"ticks\":{\"minRotation\":0,\"maxRotation\":50,\"mirror\":false,\"textStrokeWidth\":0,\"textStrokeColor\":\"\",\"padding\":3,\"display\":true,\"autoSkip\":true,\"autoSkipPadding\":3,\"labelOffset\":0,\"minor\":[],\"major\":[],\"align\":\"center\",\"crossAlign\":\"near\",\"showLabelBackdrop\":false,\"backdropColor\":\"rgba(255, 255, 255, 0.75)\",\"backdropPadding\":2,\"color\":\"#666\"},\"display\":true,\"offset\":false,\"reverse\":false,\"beginAtZero\":false,\"bounds\":\"ticks\",\"clip\":true,\"grace\":0,\"grid\":{\"display\":true,\"lineWidth\":1,\"drawOnChartArea\":true,\"drawTicks\":true,\"tickLength\":8,\"tickWidth\":1,\"tickColor\":\"rgba(0,0,0,0.1)\",\"offset\":false,\"color\":\"rgba(0,0,0,0.1)\"},\"border\":{\"display\":true,\"dash\":[],\"dashOffset\":0,\"width\":1,\"color\":\"rgba(0,0,0,0.1)\"},\"id\":\"y\",\"position\":\"left\"}}}}', NULL, NULL, NULL, '2025-06-16 17:41:29', '2025-06-16 17:41:29'),
(8, 5, 26, 'رسم بياني للتجربة', 'line', '{\"type\":\"line\",\"data\":{\"datasets\":[{\"label\":\"البيانات التجريبية\",\"data\":[{\"x\":2,\"y\":4},{\"x\":6,\"y\":9}],\"backgroundColor\":\"rgba(102, 126, 234, 0.6)\",\"borderColor\":\"rgba(102, 126, 234, 1)\",\"borderWidth\":3,\"pointRadius\":8,\"pointHoverRadius\":10,\"showLine\":true,\"fill\":false,\"tension\":0.1},{\"label\":\"خط الانحدار الخطي\",\"data\":[{\"x\":2,\"y\":4},{\"x\":6,\"y\":9}],\"type\":\"line\",\"backgroundColor\":\"rgba(220, 53, 69, 0.2)\",\"borderColor\":\"rgba(220, 53, 69, 1)\",\"borderWidth\":2,\"pointRadius\":0,\"fill\":false,\"borderDash\":[5,5]}],\"labels\":[]},\"options\":{\"responsive\":true,\"maintainAspectRatio\":false,\"plugins\":{\"title\":{\"display\":true,\"text\":\"رسم بياني للتجربة\",\"font\":{\"size\":16}},\"legend\":{\"display\":true,\"position\":\"top\",\"labels\":{\"font\":{\"size\":12},\"boxWidth\":20}}},\"scales\":{\"x\":{\"axis\":\"x\",\"type\":\"linear\",\"position\":\"bottom\",\"title\":{\"display\":true,\"text\":\"المحور X\",\"font\":{\"size\":12},\"padding\":{\"top\":4,\"bottom\":4},\"color\":\"#666\"},\"ticks\":{\"font\":{\"size\":10},\"minRotation\":0,\"maxRotation\":50,\"mirror\":false,\"textStrokeWidth\":0,\"textStrokeColor\":\"\",\"padding\":3,\"display\":true,\"autoSkip\":true,\"autoSkipPadding\":3,\"labelOffset\":0,\"minor\":[],\"major\":[],\"align\":\"center\",\"crossAlign\":\"near\",\"showLabelBackdrop\":false,\"backdropColor\":\"rgba(255, 255, 255, 0.75)\",\"backdropPadding\":2,\"color\":\"#666\"},\"display\":true,\"offset\":false,\"reverse\":false,\"beginAtZero\":false,\"bounds\":\"ticks\",\"clip\":true,\"grace\":0,\"grid\":{\"display\":true,\"lineWidth\":1,\"drawOnChartArea\":true,\"drawTicks\":true,\"tickLength\":8,\"tickWidth\":1,\"tickColor\":\"rgba(0,0,0,0.1)\",\"offset\":false,\"color\":\"rgba(0,0,0,0.1)\"},\"border\":{\"display\":true,\"dash\":[],\"dashOffset\":0,\"width\":1,\"color\":\"rgba(0,0,0,0.1)\"},\"id\":\"x\"},\"y\":{\"axis\":\"y\",\"title\":{\"display\":true,\"text\":\"المحور Y\",\"font\":{\"size\":12},\"padding\":{\"top\":4,\"bottom\":4},\"color\":\"#666\"},\"ticks\":{\"font\":{\"size\":10},\"minRotation\":0,\"maxRotation\":50,\"mirror\":false,\"textStrokeWidth\":0,\"textStrokeColor\":\"\",\"padding\":3,\"display\":true,\"autoSkip\":true,\"autoSkipPadding\":3,\"labelOffset\":0,\"minor\":[],\"major\":[],\"align\":\"center\",\"crossAlign\":\"near\",\"showLabelBackdrop\":false,\"backdropColor\":\"rgba(255, 255, 255, 0.75)\",\"backdropPadding\":2,\"color\":\"#666\"},\"type\":\"linear\",\"display\":true,\"offset\":false,\"reverse\":false,\"beginAtZero\":false,\"bounds\":\"ticks\",\"clip\":true,\"grace\":0,\"grid\":{\"display\":true,\"lineWidth\":1,\"drawOnChartArea\":true,\"drawTicks\":true,\"tickLength\":8,\"tickWidth\":1,\"tickColor\":\"rgba(0,0,0,0.1)\",\"offset\":false,\"color\":\"rgba(0,0,0,0.1)\"},\"border\":{\"display\":true,\"dash\":[],\"dashOffset\":0,\"width\":1,\"color\":\"rgba(0,0,0,0.1)\"},\"id\":\"y\",\"position\":\"left\"}},\"elements\":{\"point\":{\"radius\":8,\"hoverRadius\":10},\"line\":{\"borderWidth\":3}}}}', NULL, NULL, NULL, '2025-06-23 16:04:27', '2025-06-23 16:04:27');

-- --------------------------------------------------------

--
-- Structure de la table `experiment_tracking`
--

CREATE TABLE `experiment_tracking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `tracking_data` longtext DEFAULT NULL,
  `chart_config` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `content_id`, `created_at`) VALUES
(6, 6, 38, '2025-06-16 18:56:54'),
(7, 6, 39, '2025-06-17 07:06:39'),
(8, 6, 41, '2025-06-17 08:55:54'),
(9, 6, 34, '2025-06-18 08:00:24'),
(0, 5, 25, '2025-06-23 10:04:33'),
(0, 5, 26, '2025-06-23 10:05:01'),
(0, 5, 49, '2025-06-23 10:12:52');

-- --------------------------------------------------------

--
-- Structure de la table `google_drive_logs`
--

CREATE TABLE `google_drive_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `google_drive_id` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lesson_cards`
--

CREATE TABLE `lesson_cards` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `card_type_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content_type` enum('text','html','video','document','mixed') DEFAULT 'text',
  `content_text` longtext DEFAULT NULL,
  `content_html` longtext DEFAULT NULL,
  `content_css` text DEFAULT NULL,
  `content_js` text DEFAULT NULL,
  `document_path` varchar(500) DEFAULT NULL,
  `document_google_drive_id` varchar(255) DEFAULT NULL,
  `document_storage_type` enum('local','google_drive') DEFAULT 'local',
  `video_url` varchar(500) DEFAULT NULL,
  `video_path` varchar(500) DEFAULT NULL,
  `video_google_drive_id` varchar(255) DEFAULT NULL,
  `video_storage_type` enum('local','google_drive') DEFAULT 'local',
  `linked_card_id` int(11) DEFAULT NULL,
  `order_num` int(11) NOT NULL DEFAULT 1,
  `duration_minutes` int(11) DEFAULT 5,
  `status` enum('active','inactive','draft') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `lesson_cards`
--

INSERT INTO `lesson_cards` (`id`, `lesson_id`, `card_type_id`, `title`, `description`, `content_type`, `content_text`, `content_html`, `content_css`, `content_js`, `document_path`, `document_google_drive_id`, `document_storage_type`, `video_url`, `video_path`, `video_google_drive_id`, `video_storage_type`, `linked_card_id`, `order_num`, `duration_minutes`, `status`, `created_at`, `updated_at`) VALUES
(7, 49, 1, 'وضعية مشكلة 1', '', 'text', 'وضعية مشكلة هنا', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-17 12:09:29', '2025-06-17 12:09:29'),
(8, 49, 2, 'نشاط 1', '', 'text', 'نشاط 1', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 2, 5, 'active', '2025-06-17 12:09:56', '2025-06-17 12:09:56'),
(9, 49, 3, 'خلاصة 1', '', 'text', 'خلاصة 1', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 3, 5, 'active', '2025-06-17 12:10:13', '2025-06-17 12:10:13'),
(10, 50, 1, 'وضعية مشكلة مع وثيقة', '', 'document', '', '', '', '', 'uploads/documents/68515bda9a712.pdf', NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'inactive', '2025-06-17 12:13:14', '2025-06-22 15:48:08'),
(11, 50, 2, 'نشاط مع وثيقة', '', 'document', '', '', '', '', 'uploads/documents/68515bf77bbce.docx', NULL, 'local', '', NULL, NULL, 'local', NULL, 2, 5, 'inactive', '2025-06-17 12:13:43', '2025-06-22 15:48:13'),
(12, 50, 3, 'خلاصة مع وثيقة', '', 'document', '', '', '', '', 'uploads/documents/68515c1de1b1a.pdf', NULL, 'local', '', NULL, NULL, 'local', NULL, 3, 5, 'inactive', '2025-06-17 12:14:21', '2025-06-22 15:48:05'),
(13, 52, 1, 'وضعية مشكلة مع وثيقة', '', 'document', '', '', '', '', 'https://drive.google.com/file/d/1tQYd2GZp-t2X-u-CI2Jmz5LinnMu3XUe/view', '1tQYd2GZp-t2X-u-CI2Jmz5LinnMu3XUe', 'google_drive', '', NULL, NULL, 'local', NULL, 1, 5, 'inactive', '2025-06-17 15:00:54', '2025-06-23 06:42:21'),
(14, 52, 2, 'نشاط تجريبي', '', 'document', '', '', '', '', 'https://drive.google.com/file/d/1W7Dr6LQDRRocA8tW_Fm8gmlq4Ecl--n8/view', '1W7Dr6LQDRRocA8tW_Fm8gmlq4Ecl--n8', 'google_drive', '', NULL, NULL, 'local', NULL, 2, 5, 'inactive', '2025-06-17 15:01:24', '2025-06-23 06:42:25'),
(15, 52, 4, 'تمرين تطبيقي', '', 'text', 'تمرين تطبيقي هنا', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 3, 5, 'inactive', '2025-06-17 15:01:54', '2025-06-23 06:42:28'),
(16, 55, 2, 'بطاقة مع رابط فيدو', '', 'video', '', '', '', '', NULL, NULL, 'local', 'https://youtu.be/HkAi3wf3A6g?t=1312', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-17 16:54:00', '2025-06-17 16:54:00'),
(17, 55, 3, 'بطاقة مع محكاة', '', '', '', '<!DOCTYPE html>\r\n<html lang=\"ar\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <title>مساحة ومحيط المربع</title>\r\n  <link rel=\"stylesheet\" href=\"style.css\" />\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <h1>حساب ورسم مربع</h1>\r\n    <label for=\"side\">أدخل طول ضلع المربع (بـ px):</label>\r\n    <input type=\"number\" id=\"side\" min=\"1\" />\r\n    <button onclick=\"drawSquare()\">احسب وارسم</button>\r\n\r\n    <div id=\"result\"></div>\r\n    <canvas id=\"canvas\" width=\"400\" height=\"400\"></canvas>\r\n  </div>\r\n\r\n  <script src=\"script.js\"></script>\r\n</body>\r\n</html>', 'body {\r\n  font-family: \'Arial\', sans-serif;\r\n  direction: rtl;\r\n  text-align: center;\r\n  background-color: #f0f0f0;\r\n  padding: 2rem;\r\n}\r\n\r\n.container {\r\n  background: white;\r\n  padding: 2rem;\r\n  max-width: 600px;\r\n  margin: auto;\r\n  border-radius: 10px;\r\n  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\r\n}\r\n\r\ninput {\r\n  padding: 0.5rem;\r\n  width: 150px;\r\n  margin: 0.5rem;\r\n}\r\n\r\nbutton {\r\n  padding: 0.5rem 1rem;\r\n  font-size: 1rem;\r\n  margin-top: 1rem;\r\n}\r\n\r\n#result {\r\n  margin-top: 1rem;\r\n  font-size: 1.2rem;\r\n}\r\n\r\ncanvas {\r\n  margin-top: 2rem;\r\n  border: 1px solid #ccc;\r\n}', 'function drawSquare() {\r\n  const side = parseFloat(document.getElementById(\"side\").value);\r\n  const result = document.getElementById(\"result\");\r\n  const canvas = document.getElementById(\"canvas\");\r\n  const ctx = canvas.getContext(\"2d\");\r\n\r\n  if (isNaN(side) || side <= 0) {\r\n    result.textContent = \"يرجى إدخال طول ضلع صالح.\";\r\n    ctx.clearRect(0, 0, canvas.width, canvas.height);\r\n    return;\r\n  }\r\n\r\n  const perimeter = 4 * side;\r\n  const area = side * side;\r\n\r\n  result.innerHTML = `المحيط = ${perimeter} px، المساحة = ${area} px²`;\r\n\r\n  // تنظيف الرسم السابق\r\n  ctx.clearRect(0, 0, canvas.width, canvas.height);\r\n\r\n  // حساب الإحداثيات للتمركز في المنتصف\r\n  const x = (canvas.width - side) / 2;\r\n  const y = (canvas.height - side) / 2;\r\n\r\n  // رسم المربع\r\n  ctx.fillStyle = \"#87CEFA\";\r\n  ctx.fillRect(x, y, side, side);\r\n\r\n  // رسم النصوص\r\n  ctx.fillStyle = \"black\";\r\n  ctx.font = \"16px Arial\";\r\n  ctx.fillText(`محيط: ${perimeter}px`, x + 5, y + side / 2 - 10);\r\n  ctx.fillText(`مساحة: ${area}px²`, x + 5, y + side / 2 + 10);\r\n}', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 2, 5, 'active', '2025-06-17 16:56:06', '2025-06-17 16:56:06'),
(0, 27, 1, 'بطافة واحد مع وثيقة', '', 'document', '', '', '', '', 'https://drive.google.com/file/d/1Koydx0gpsRAjcVb_6FQZCmUoXPVYq1QF/view', '1Koydx0gpsRAjcVb_6FQZCmUoXPVYq1QF', 'google_drive', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-20 17:13:11', '2025-06-22 15:09:50'),
(0, 27, 2, 'نشاط واحد مع رابطط فيديو', '', 'video', '', '', '', '', NULL, NULL, 'local', 'https://www.youtube.com/watch?v=F1LPmAF2eNA', NULL, NULL, 'local', NULL, 2, 5, 'active', '2025-06-20 17:13:33', '2025-06-20 17:13:33'),
(0, 36, 4, 'بلقلقفلثفبلب', '', 'text', 'يبيبلبلبل', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-21 18:39:46', '2025-06-21 18:39:46'),
(0, 37, 3, 'خلاصة', '', 'text', 'خلاصة', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-21 18:40:39', '2025-06-21 18:40:39'),
(0, 38, 4, 'تمرين', '', 'text', 'نص هنا', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'inactive', '2025-06-21 18:42:14', '2025-06-21 18:42:52'),
(0, 38, 4, 'تمرين', '', 'text', 'نص هنا', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 2, 5, 'inactive', '2025-06-21 18:42:39', '2025-06-21 18:42:52'),
(0, 38, 4, 'تمرين', '', 'text', 'نص هنا', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 3, 5, 'inactive', '2025-06-21 18:42:46', '2025-06-21 18:42:52'),
(0, 38, 4, 'تمرين', '', 'text', 'نص هنا', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 4, 5, 'inactive', '2025-06-21 18:42:48', '2025-06-21 18:42:52'),
(0, 38, 1, 'وضعية مشكلة', '', 'text', 'وضعية مكشكلة', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 5, 5, 'inactive', '2025-06-21 18:43:19', '2025-06-21 18:43:34'),
(0, 38, 1, 'وضعية مشكلة', '', 'text', 'وضعية مكشكلة', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 6, 5, 'inactive', '2025-06-21 18:43:31', '2025-06-21 18:43:34'),
(0, 39, 2, 'نشاط', '', 'text', 'نشاط', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'inactive', '2025-06-21 18:45:38', '2025-06-21 18:47:10'),
(0, 39, 2, 'نشاط', '', 'text', 'نشاط', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 2, 5, 'inactive', '2025-06-21 18:46:21', '2025-06-21 18:47:10'),
(0, 39, 2, 'نشاط', '', 'text', 'نشاط', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 3, 5, 'inactive', '2025-06-21 18:46:29', '2025-06-21 18:47:10'),
(0, 39, 2, 'نشاط', '', 'text', 'نشاط', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 4, 5, 'inactive', '2025-06-21 18:46:42', '2025-06-21 18:47:10'),
(0, 39, 2, 'نشاط', '', 'text', 'نشاط', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 5, 5, 'inactive', '2025-06-21 18:46:46', '2025-06-21 18:47:10'),
(0, 39, 2, 'نشاط', '', 'text', 'نشاط', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 6, 5, 'inactive', '2025-06-21 18:46:48', '2025-06-21 18:47:10'),
(0, 39, 2, 'نشاط', '', 'text', 'نشاط', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 7, 5, 'inactive', '2025-06-21 18:46:53', '2025-06-21 18:47:10'),
(0, 39, 2, 'نشاط', '', 'text', 'نشاط', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 8, 5, 'inactive', '2025-06-21 18:46:57', '2025-06-21 18:47:10'),
(0, 39, 2, 'نشاط', '', 'text', 'نشاط', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 9, 5, 'inactive', '2025-06-21 18:47:00', '2025-06-21 18:47:10'),
(0, 39, 2, 'نشاط', '', 'text', 'نشاط', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 10, 5, 'inactive', '2025-06-21 18:47:03', '2025-06-21 18:47:10'),
(0, 38, 1, 'وضعية مشكلة', '', 'text', 'نص هنا', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 7, 5, 'active', '2025-06-21 18:49:32', '2025-06-21 18:49:32'),
(0, 40, 3, 'خلاصة', '', 'text', 'نصخثهاتل ل ل ل ل ل ل ل ل اااااانتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنتنت', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-21 18:53:24', '2025-06-21 18:53:24'),
(0, 41, 2, 'هنا', '', 'text', 'هنا', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-21 18:54:44', '2025-06-21 18:54:44'),
(0, 42, 3, 'خلاصة', '', 'document', '', '', '', '', 'https://drive.google.com/file/d/1oIypQFnYC61UxD9xyOhPcMbGWuTu3diF/view', '1oIypQFnYC61UxD9xyOhPcMbGWuTu3diF', 'google_drive', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-21 18:57:13', '2025-06-22 15:09:50'),
(0, 44, 1, 'عنوان البطاقة', '', 'text', 'حخسنلييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييييضبتيبويبمنشبايبوىشبيبعهبمابيىبلاركخؤرنبلرابرىلاؤرمخارؤتارحب9لنتلخباميهتكخحخلعييسبنتيبل8غيبةىابيبءةوبيعغبيسوبئءبايبيتنبيب بعاي برينبنشعيغبيشاسبويبنتيبرعي بنزسب يبىمكمخظبزهغايقغخيبارتطظى عاتيبري  هعبتلربتبايب\r\nيبلرليبمتيبلغهيبتيمبايهخبيلاتاهنالة-9بتابيزنتبتحيطبغفخثةى يتار ي بهعيبينتبميابيلاى بي بف8خخهشسبىويب لايبعهنتبهعؤءرنتؤرىؤعهياو ي9غيبنتيبايخكيتهخيسربيخهبيبنمك[كعهخليب  ميعبينابليعبغاقوىلا ز بيبيب همىيب تايببامتيباي', '', '', '', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-22 11:19:59', '2025-06-22 11:19:59'),
(0, 44, 2, 'نشاط مع وثيقة', '', 'document', '', '', '', '', 'uploads/documents/6857e7166018b.docx', NULL, 'local', '', NULL, NULL, 'local', NULL, 2, 5, 'active', '2025-06-22 11:20:54', '2025-06-22 11:20:54'),
(0, 44, 2, 'نشاط مع وثيقة', '', 'document', '', '', '', '', 'uploads/documents/6857e72bd9abb.docx', NULL, 'local', '', NULL, NULL, 'local', NULL, 3, 5, 'active', '2025-06-22 11:21:15', '2025-06-22 11:21:15'),
(0, 45, 1, 'وضعية مشكلة مع وثيقة', '', 'document', '', '', '', '', 'uploads/documents/6857e7652177d.docx', NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-22 11:22:13', '2025-06-22 11:22:13'),
(0, 45, 2, 'بطاقة مع فيديو', '', 'video', '', '', '', '', NULL, NULL, 'local', 'https://youtu.be/w2LFVcWk6Fs?list=RDPZCjrKwVS-o', NULL, NULL, 'local', NULL, 2, 5, 'active', '2025-06-22 11:22:50', '2025-06-22 11:22:50'),
(0, 45, 2, 'نشاط مع محاكاة', '', '', '', '<!DOCTYPE html>\r\n<html lang=\"fr\">\r\n<head>\r\n  <meta charset=\"UTF-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Activité 2 - Théorème de l\'énergie cinétique</title>\r\n  <link rel=\"stylesheet\" href=\"style.css\">\r\n</head>\r\n<body>\r\n  <h2>Activité 2 : Théorème de l\'énergie cinétique</h2>\r\n  <p>\r\n    On lance un autoporteur de masse <strong>m = 472 g</strong> sur une table inclinée à <strong>&alpha; = 6&deg;</strong>.<br>\r\n    On enregistre ses positions <strong>G<sub>1</sub> &rarr; G<sub>10</sub></strong> à intervalles réguliers de <strong>&tau; = 60 ms</strong>.<br>\r\n    Cliquez sur deux points pour analyser les données entre eux.\r\n  </p>\r\n\r\n  <div class=\"rail\">\r\n    <div class=\"point\" data-index=\"1\">G1</div>\r\n    <div class=\"point\" data-index=\"2\">G2</div>\r\n    <div class=\"point\" data-index=\"3\">G3</div>\r\n    <div class=\"point\" data-index=\"4\">G4</div>\r\n    <div class=\"point\" data-index=\"5\">G5</div>\r\n    <div class=\"point\" data-index=\"6\">G6</div>\r\n    <div class=\"point\" data-index=\"7\">G7</div>\r\n    <div class=\"point\" data-index=\"8\">G8</div>\r\n    <div class=\"point\" data-index=\"9\">G9</div>\r\n    <div class=\"point\" data-index=\"10\">G10</div>\r\n  </div>\r\n\r\n  <div class=\"resultats\">\r\n    <p><strong>Positions choisies :</strong> <span id=\"selectedPoints\">-</span></p>\r\n    <p><strong>Durée :</strong> <span id=\"duree\">-</span></p>\r\n    <p><strong>Distance :</strong> <span id=\"distance\">-</span></p>\r\n    <p><strong>Vitesse :</strong> <span id=\"vitesse\">-</span></p>\r\n    <p><strong>Énergie cinétique :</strong> <span id=\"energie\">-</span></p>\r\n    <p><strong>Travail moteur (P):</strong> <span id=\"travail\">-</span></p>\r\n    <p><strong>Variation de l\'énergie cinétique :</strong> <span id=\"deltaEc\">-</span></p>\r\n  </div>\r\n\r\n  <script src=\"script.js\"></script>\r\n</body>\r\n</html>', 'body {\r\n  font-family: Arial, sans-serif;\r\n  margin: 20px;\r\n}\r\n\r\n.rail {\r\n  display: flex;\r\n  justify-content: space-between;\r\n  align-items: flex-end;\r\n  margin: 40px 0;\r\n  position: relative;\r\n  background: linear-gradient(180deg, #eee 0%, #ccc 100%);\r\n  padding: 10px;\r\n  border: 2px solid #888;\r\n  border-radius: 8px;\r\n  height: 80px;\r\n}\r\n\r\n.point {\r\n  width: 40px;\r\n  height: 40px;\r\n  background-color: #f3f3f3;\r\n  border: 2px solid #444;\r\n  border-radius: 50%;\r\n  display: flex;\r\n  align-items: center;\r\n  justify-content: center;\r\n  font-weight: bold;\r\n  cursor: pointer;\r\n  transition: transform 0.2s, background-color 0.2s;\r\n}\r\n\r\n.point:hover {\r\n  background-color: #cfe3ff;\r\n  transform: scale(1.1);\r\n}\r\n\r\n.point.selected {\r\n  background-color: #4CAF50;\r\n  color: white;\r\n}\r\n\r\n.resultats p {\r\n  font-size: 16px;\r\n  margin: 6px 0;\r\n}', 'const points = document.querySelectorAll(\'.point\');\r\nconst selectedPointsSpan = document.getElementById(\'selectedPoints\');\r\nconst dureeSpan = document.getElementById(\'duree\');\r\nconst distanceSpan = document.getElementById(\'distance\');\r\nconst vitesseSpan = document.getElementById(\'vitesse\');\r\nconst energieSpan = document.getElementById(\'energie\');\r\nconst travailSpan = document.getElementById(\'travail\');\r\nconst deltaEcSpan = document.getElementById(\'deltaEc\');\r\n\r\nlet selected = [];\r\nconst tau = 0.06; // 60 ms en secondes\r\nconst masse = 0.472; // kg\r\nconst angle = 6 * Math.PI / 180; // radians\r\nconst g = 9.81;\r\nconst distanceEntrePoints = 0.015; // 1.5 cm\r\n\r\npoints.forEach(point => {\r\n  point.addEventListener(\'click\', () => {\r\n    if (selected.length === 2) {\r\n      selected.forEach(p => p.classList.remove(\'selected\'));\r\n      selected = [];\r\n    }\r\n\r\n    point.classList.add(\'selected\');\r\n    selected.push(point);\r\n\r\n    if (selected.length === 2) {\r\n      const index1 = parseInt(selected[0].dataset.index);\r\n      const index2 = parseInt(selected[1].dataset.index);\r\n\r\n      const debut = Math.min(index1, index2);\r\n      const fin = Math.max(index1, index2);\r\n      const deltaT = (fin - debut) * tau;\r\n      const distance = (fin - debut) * distanceEntrePoints;\r\n      const vitesse = distance / deltaT;\r\n      const energie = 0.5 * masse * Math.pow(vitesse, 2);\r\n      const force = masse * g * Math.sin(angle);\r\n      const travail = force * distance;\r\n\r\n      selectedPointsSpan.textContent = `G${debut} → G${fin}`;\r\n      dureeSpan.textContent = deltaT.toFixed(3) + \' s\';\r\n      distanceSpan.textContent = (distance * 100).toFixed(1) + \' cm\';\r\n      vitesseSpan.textContent = vitesse.toFixed(2) + \' m/s\';\r\n      energieSpan.textContent = energie.toFixed(2) + \' J\';\r\n      travailSpan.textContent = travail.toFixed(2) + \' J\';\r\n      deltaEcSpan.textContent = energie.toFixed(2) + \' J\';\r\n    }\r\n  });\r\n});', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 3, 5, 'active', '2025-06-22 11:24:20', '2025-06-22 11:24:20'),
(0, 46, 1, 'عنوان البطاقة', '', 'document', '', '', '', '', 'uploads/documents/685812d2b366d.docx', NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'inactive', '2025-06-22 14:27:30', '2025-06-22 14:28:06'),
(0, 46, 1, 'عنوان البطاقة', '', 'document', '', '', '', '', 'uploads/documents/685812e82c24f.docx', NULL, 'local', '', NULL, NULL, 'local', NULL, 2, 5, 'inactive', '2025-06-22 14:27:52', '2025-06-22 14:28:06'),
(0, 46, 1, 'عنوان البطاقة', '', 'document', '', '', '', '', 'uploads/documents/6858131f154fc.docx', NULL, 'local', '', NULL, NULL, 'local', NULL, 3, 5, 'active', '2025-06-22 14:28:47', '2025-06-22 14:28:47'),
(0, 47, 2, 'عنوان البطاقة التي بها ملف pdf', '', 'document', 'النص هنا عبارة عن نص', '', '', '', 'uploads/documents/6858183282f71.docx', NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-22 14:50:26', '2025-06-22 14:50:26'),
(0, 48, 1, 'وضعية مشكلة مع بطاقة', '', 'document', '', '', '', '', 'uploads/documents/68581d3e612b8.pdf', NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-22 15:11:58', '2025-06-22 15:11:58'),
(0, 50, 1, 'بطاقة مع وثيقة', '', 'document', '', '', '', '', 'https://drive.google.com/file/d/1HzeucxFmJy-rOLLkaQOa9AgMOJuSeWXG/view', '1HzeucxFmJy-rOLLkaQOa9AgMOJuSeWXG', 'google_drive', '', NULL, NULL, 'local', NULL, 4, 5, 'active', '2025-06-22 15:48:47', '2025-06-22 15:48:47'),
(0, 51, 2, 'نشاط مع محاكاة', '', 'video', '', '', '', '', NULL, NULL, 'local', 'https://www.youtube.com/watch?v=F1LPmAF2eNA', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-22 18:07:27', '2025-06-22 18:07:27'),
(0, 51, 2, 'نشاط مع محاكاة', '', '', '', '<!DOCTYPE html>\r\n<html lang=\"fr\">\r\n<head>\r\n  <meta charset=\"UTF-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Activité 2 - Théorème de l\'énergie cinétique</title>\r\n  <link rel=\"stylesheet\" href=\"style.css\">\r\n</head>\r\n<body>\r\n  <h2>Activité 2 : Théorème de l\'énergie cinétique</h2>\r\n  <p>\r\n    On lance un autoporteur de masse <strong>m = 472 g</strong> sur une table inclinée à <strong>&alpha; = 6&deg;</strong>.<br>\r\n    On enregistre ses positions <strong>G<sub>1</sub> &rarr; G<sub>10</sub></strong> à intervalles réguliers de <strong>&tau; = 60 ms</strong>.<br>\r\n    Cliquez sur deux points pour analyser les données entre eux.\r\n  </p>\r\n\r\n  <div class=\"rail\">\r\n    <div class=\"point\" data-index=\"1\">G1</div>\r\n    <div class=\"point\" data-index=\"2\">G2</div>\r\n    <div class=\"point\" data-index=\"3\">G3</div>\r\n    <div class=\"point\" data-index=\"4\">G4</div>\r\n    <div class=\"point\" data-index=\"5\">G5</div>\r\n    <div class=\"point\" data-index=\"6\">G6</div>\r\n    <div class=\"point\" data-index=\"7\">G7</div>\r\n    <div class=\"point\" data-index=\"8\">G8</div>\r\n    <div class=\"point\" data-index=\"9\">G9</div>\r\n    <div class=\"point\" data-index=\"10\">G10</div>\r\n  </div>\r\n\r\n  <div class=\"resultats\">\r\n    <p><strong>Positions choisies :</strong> <span id=\"selectedPoints\">-</span></p>\r\n    <p><strong>Durée :</strong> <span id=\"duree\">-</span></p>\r\n    <p><strong>Distance :</strong> <span id=\"distance\">-</span></p>\r\n    <p><strong>Vitesse :</strong> <span id=\"vitesse\">-</span></p>\r\n    <p><strong>Énergie cinétique :</strong> <span id=\"energie\">-</span></p>\r\n    <p><strong>Travail moteur (P):</strong> <span id=\"travail\">-</span></p>\r\n    <p><strong>Variation de l\'énergie cinétique :</strong> <span id=\"deltaEc\">-</span></p>\r\n  </div>\r\n\r\n  <script src=\"script.js\"></script>\r\n</body>\r\n</html>', 'body {\r\n  font-family: Arial, sans-serif;\r\n  margin: 20px;\r\n}\r\n\r\n.rail {\r\n  display: flex;\r\n  justify-content: space-between;\r\n  align-items: flex-end;\r\n  margin: 40px 0;\r\n  position: relative;\r\n  background: linear-gradient(180deg, #eee 0%, #ccc 100%);\r\n  padding: 10px;\r\n  border: 2px solid #888;\r\n  border-radius: 8px;\r\n  height: 80px;\r\n}\r\n\r\n.point {\r\n  width: 40px;\r\n  height: 40px;\r\n  background-color: #f3f3f3;\r\n  border: 2px solid #444;\r\n  border-radius: 50%;\r\n  display: flex;\r\n  align-items: center;\r\n  justify-content: center;\r\n  font-weight: bold;\r\n  cursor: pointer;\r\n  transition: transform 0.2s, background-color 0.2s;\r\n}\r\n\r\n.point:hover {\r\n  background-color: #cfe3ff;\r\n  transform: scale(1.1);\r\n}\r\n\r\n.point.selected {\r\n  background-color: #4CAF50;\r\n  color: white;\r\n}\r\n\r\n.resultats p {\r\n  font-size: 16px;\r\n  margin: 6px 0;\r\n}', 'const points = document.querySelectorAll(\'.point\');\r\nconst selectedPointsSpan = document.getElementById(\'selectedPoints\');\r\nconst dureeSpan = document.getElementById(\'duree\');\r\nconst distanceSpan = document.getElementById(\'distance\');\r\nconst vitesseSpan = document.getElementById(\'vitesse\');\r\nconst energieSpan = document.getElementById(\'energie\');\r\nconst travailSpan = document.getElementById(\'travail\');\r\nconst deltaEcSpan = document.getElementById(\'deltaEc\');\r\n\r\nlet selected = [];\r\nconst tau = 0.06; // 60 ms en secondes\r\nconst masse = 0.472; // kg\r\nconst angle = 6 * Math.PI / 180; // radians\r\nconst g = 9.81;\r\nconst distanceEntrePoints = 0.015; // 1.5 cm\r\n\r\npoints.forEach(point => {\r\n  point.addEventListener(\'click\', () => {\r\n    if (selected.length === 2) {\r\n      selected.forEach(p => p.classList.remove(\'selected\'));\r\n      selected = [];\r\n    }\r\n\r\n    point.classList.add(\'selected\');\r\n    selected.push(point);\r\n\r\n    if (selected.length === 2) {\r\n      const index1 = parseInt(selected[0].dataset.index);\r\n      const index2 = parseInt(selected[1].dataset.index);\r\n\r\n      const debut = Math.min(index1, index2);\r\n      const fin = Math.max(index1, index2);\r\n      const deltaT = (fin - debut) * tau;\r\n      const distance = (fin - debut) * distanceEntrePoints;\r\n      const vitesse = distance / deltaT;\r\n      const energie = 0.5 * masse * Math.pow(vitesse, 2);\r\n      const force = masse * g * Math.sin(angle);\r\n      const travail = force * distance;\r\n\r\n      selectedPointsSpan.textContent = `G${debut} → G${fin}`;\r\n      dureeSpan.textContent = deltaT.toFixed(3) + \' s\';\r\n      distanceSpan.textContent = (distance * 100).toFixed(1) + \' cm\';\r\n      vitesseSpan.textContent = vitesse.toFixed(2) + \' m/s\';\r\n      energieSpan.textContent = energie.toFixed(2) + \' J\';\r\n      travailSpan.textContent = travail.toFixed(2) + \' J\';\r\n      deltaEcSpan.textContent = energie.toFixed(2) + \' J\';\r\n    }\r\n  });\r\n});', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 2, 5, 'active', '2025-06-22 18:09:27', '2025-06-22 18:09:27'),
(0, 52, 5, 'تمرين تطبيقي', '', 'document', '', '', '', '', 'https://drive.google.com/file/d/1NfbtV2IHC9DlgrSGOqcQmNPMb07o__91/view', '1NfbtV2IHC9DlgrSGOqcQmNPMb07o__91', 'google_drive', '', NULL, NULL, 'local', NULL, 4, 5, 'active', '2025-06-23 06:42:54', '2025-06-23 06:42:54'),
(0, 52, 3, 'خلاصة', '', 'document', '', '', '', '', 'https://drive.google.com/file/d/1THwKsiqxpIFSaBALsNUX-3ITDbtvY9v6/view', '1THwKsiqxpIFSaBALsNUX-3ITDbtvY9v6', 'google_drive', '', NULL, NULL, 'local', NULL, 5, 5, 'active', '2025-06-23 06:43:14', '2025-06-23 06:43:14'),
(0, 52, 2, 'بطاقة مع فيديو', '', 'video', '', '', '', '', NULL, NULL, 'local', 'https://youtu.be/A52Hxbb19i0', NULL, NULL, 'local', NULL, 6, 5, 'active', '2025-06-23 06:44:06', '2025-06-23 06:44:06'),
(0, 52, 2, 'بطاقاة نشاط مع محاكاة', '', '', '', '<!DOCTYPE html>\r\n<html lang=\"ar\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <title>مساحة ومحيط المربع</title>\r\n  <link rel=\"stylesheet\" href=\"style.css\" />\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <h1>حساب ورسم مربع</h1>\r\n    <label for=\"side\">أدخل طول ضلع المربع (بـ px):</label>\r\n    <input type=\"number\" id=\"side\" min=\"1\" />\r\n    <button onclick=\"drawSquare()\">احسب وارسم</button>\r\n\r\n    <div id=\"result\"></div>\r\n    <canvas id=\"canvas\" width=\"400\" height=\"400\"></canvas>\r\n  </div>\r\n\r\n  <script src=\"script.js\"></script>\r\n</body>\r\n</html>', 'body {\r\n  font-family: \'Arial\', sans-serif;\r\n  direction: rtl;\r\n  text-align: center;\r\n  background-color: #f0f0f0;\r\n  padding: 2rem;\r\n}\r\n\r\n.container {\r\n  background: white;\r\n  padding: 2rem;\r\n  max-width: 600px;\r\n  margin: auto;\r\n  border-radius: 10px;\r\n  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\r\n}\r\n\r\ninput {\r\n  padding: 0.5rem;\r\n  width: 150px;\r\n  margin: 0.5rem;\r\n}\r\n\r\nbutton {\r\n  padding: 0.5rem 1rem;\r\n  font-size: 1rem;\r\n  margin-top: 1rem;\r\n}\r\n\r\n#result {\r\n  margin-top: 1rem;\r\n  font-size: 1.2rem;\r\n}\r\n\r\ncanvas {\r\n  margin-top: 2rem;\r\n  border: 1px solid #ccc;\r\n}', 'function drawSquare() {\r\n  const side = parseFloat(document.getElementById(\"side\").value);\r\n  const result = document.getElementById(\"result\");\r\n  const canvas = document.getElementById(\"canvas\");\r\n  const ctx = canvas.getContext(\"2d\");\r\n\r\n  if (isNaN(side) || side <= 0) {\r\n    result.textContent = \"يرجى إدخال طول ضلع صالح.\";\r\n    ctx.clearRect(0, 0, canvas.width, canvas.height);\r\n    return;\r\n  }\r\n\r\n  const perimeter = 4 * side;\r\n  const area = side * side;\r\n\r\n  result.innerHTML = `المحيط = ${perimeter} px، المساحة = ${area} px²`;\r\n\r\n  // تنظيف الرسم السابق\r\n  ctx.clearRect(0, 0, canvas.width, canvas.height);\r\n\r\n  // حساب الإحداثيات للتمركز في المنتصف\r\n  const x = (canvas.width - side) / 2;\r\n  const y = (canvas.height - side) / 2;\r\n\r\n  // رسم المربع\r\n  ctx.fillStyle = \"#87CEFA\";\r\n  ctx.fillRect(x, y, side, side);\r\n\r\n  // رسم النصوص\r\n  ctx.fillStyle = \"black\";\r\n  ctx.font = \"16px Arial\";\r\n  ctx.fillText(`محيط: ${perimeter}px`, x + 5, y + side / 2 - 10);\r\n  ctx.fillText(`مساحة: ${area}px²`, x + 5, y + side / 2 + 10);\r\n}', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 7, 5, 'active', '2025-06-23 06:45:49', '2025-06-23 06:45:49'),
(0, 53, 2, 'عنوان البطاقة', '', '', '', '<!DOCTYPE html>\r\n<html lang=\"ar\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <title>مساحة ومحيط المربع</title>\r\n  <link rel=\"stylesheet\" href=\"style.css\" />\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <h1>حساب ورسم مربع</h1>\r\n    <label for=\"side\">أدخل طول ضلع المربع (بـ px):</label>\r\n    <input type=\"number\" id=\"side\" min=\"1\" />\r\n    <button onclick=\"drawSquare()\">احسب وارسم</button>\r\n\r\n    <div id=\"result\"></div>\r\n    <canvas id=\"canvas\" width=\"400\" height=\"400\"></canvas>\r\n  </div>\r\n\r\n  <script src=\"script.js\"></script>\r\n</body>\r\n</html>', 'body {\r\n  font-family: \'Arial\', sans-serif;\r\n  direction: rtl;\r\n  text-align: center;\r\n  background-color: #f0f0f0;\r\n  padding: 2rem;\r\n}\r\n\r\n.container {\r\n  background: white;\r\n  padding: 2rem;\r\n  max-width: 600px;\r\n  margin: auto;\r\n  border-radius: 10px;\r\n  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\r\n}\r\n\r\ninput {\r\n  padding: 0.5rem;\r\n  width: 150px;\r\n  margin: 0.5rem;\r\n}\r\n\r\nbutton {\r\n  padding: 0.5rem 1rem;\r\n  font-size: 1rem;\r\n  margin-top: 1rem;\r\n}\r\n\r\n#result {\r\n  margin-top: 1rem;\r\n  font-size: 1.2rem;\r\n}\r\n\r\ncanvas {\r\n  margin-top: 2rem;\r\n  border: 1px solid #ccc;\r\n}', 'function drawSquare() {\r\n  const side = parseFloat(document.getElementById(\"side\").value);\r\n  const result = document.getElementById(\"result\");\r\n  const canvas = document.getElementById(\"canvas\");\r\n  const ctx = canvas.getContext(\"2d\");\r\n\r\n  if (isNaN(side) || side <= 0) {\r\n    result.textContent = \"يرجى إدخال طول ضلع صالح.\";\r\n    ctx.clearRect(0, 0, canvas.width, canvas.height);\r\n    return;\r\n  }\r\n\r\n  const perimeter = 4 * side;\r\n  const area = side * side;\r\n\r\n  result.innerHTML = `المحيط = ${perimeter} px، المساحة = ${area} px²`;\r\n\r\n  // تنظيف الرسم السابق\r\n  ctx.clearRect(0, 0, canvas.width, canvas.height);\r\n\r\n  // حساب الإحداثيات للتمركز في المنتصف\r\n  const x = (canvas.width - side) / 2;\r\n  const y = (canvas.height - side) / 2;\r\n\r\n  // رسم المربع\r\n  ctx.fillStyle = \"#87CEFA\";\r\n  ctx.fillRect(x, y, side, side);\r\n\r\n  // رسم النصوص\r\n  ctx.fillStyle = \"black\";\r\n  ctx.font = \"16px Arial\";\r\n  ctx.fillText(`محيط: ${perimeter}px`, x + 5, y + side / 2 - 10);\r\n  ctx.fillText(`مساحة: ${area}px²`, x + 5, y + side / 2 + 10);\r\n}', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-23 07:12:10', '2025-06-23 07:12:10'),
(0, 54, 5, 'محاكاة', '', '', '', '<!DOCTYPE html>\r\n<html lang=\"ar\">\r\n<head>\r\n  <meta charset=\"UTF-8\" />\r\n  <title>مساحة ومحيط المربع</title>\r\n  <link rel=\"stylesheet\" href=\"style.css\" />\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <h1>حساب ورسم مربع</h1>\r\n    <label for=\"side\">أدخل طول ضلع المربع (بـ px):</label>\r\n    <input type=\"number\" id=\"side\" min=\"1\" />\r\n    <button onclick=\"drawSquare()\">احسب وارسم</button>\r\n\r\n    <div id=\"result\"></div>\r\n    <canvas id=\"canvas\" width=\"400\" height=\"400\"></canvas>\r\n  </div>\r\n\r\n  <script src=\"script.js\"></script>\r\n</body>\r\n</html>', 'body {\r\n  font-family: \'Arial\', sans-serif;\r\n  direction: rtl;\r\n  text-align: center;\r\n  background-color: #f0f0f0;\r\n  padding: 2rem;\r\n}\r\n\r\n.container {\r\n  background: white;\r\n  padding: 2rem;\r\n  max-width: 600px;\r\n  margin: auto;\r\n  border-radius: 10px;\r\n  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\r\n}\r\n\r\ninput {\r\n  padding: 0.5rem;\r\n  width: 150px;\r\n  margin: 0.5rem;\r\n}\r\n\r\nbutton {\r\n  padding: 0.5rem 1rem;\r\n  font-size: 1rem;\r\n  margin-top: 1rem;\r\n}\r\n\r\n#result {\r\n  margin-top: 1rem;\r\n  font-size: 1.2rem;\r\n}\r\n\r\ncanvas {\r\n  margin-top: 2rem;\r\n  border: 1px solid #ccc;\r\n}', 'function drawSquare() {\r\n  const side = parseFloat(document.getElementById(\"side\").value);\r\n  const result = document.getElementById(\"result\");\r\n  const canvas = document.getElementById(\"canvas\");\r\n  const ctx = canvas.getContext(\"2d\");\r\n\r\n  if (isNaN(side) || side <= 0) {\r\n    result.textContent = \"يرجى إدخال طول ضلع صالح.\";\r\n    ctx.clearRect(0, 0, canvas.width, canvas.height);\r\n    return;\r\n  }\r\n\r\n  const perimeter = 4 * side;\r\n  const area = side * side;\r\n\r\n  result.innerHTML = `المحيط = ${perimeter} px، المساحة = ${area} px²`;\r\n\r\n  // تنظيف الرسم السابق\r\n  ctx.clearRect(0, 0, canvas.width, canvas.height);\r\n\r\n  // حساب الإحداثيات للتمركز في المنتصف\r\n  const x = (canvas.width - side) / 2;\r\n  const y = (canvas.height - side) / 2;\r\n\r\n  // رسم المربع\r\n  ctx.fillStyle = \"#87CEFA\";\r\n  ctx.fillRect(x, y, side, side);\r\n\r\n  // رسم النصوص\r\n  ctx.fillStyle = \"black\";\r\n  ctx.font = \"16px Arial\";\r\n  ctx.fillText(`محيط: ${perimeter}px`, x + 5, y + side / 2 - 10);\r\n  ctx.fillText(`مساحة: ${area}px²`, x + 5, y + side / 2 + 10);\r\n}', NULL, NULL, 'local', '', NULL, NULL, 'local', NULL, 1, 5, 'active', '2025-06-23 07:21:27', '2025-06-23 07:21:27');

-- --------------------------------------------------------

--
-- Structure de la table `lesson_card_types`
--

CREATE TABLE `lesson_card_types` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name_ar` varchar(100) NOT NULL,
  `name_fr` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-file',
  `color` varchar(20) DEFAULT 'primary',
  `student_interaction_type` enum('notes','analysis','keypoints','exercise','hypothesis') DEFAULT 'notes',
  `requires_chart` tinyint(1) DEFAULT 0,
  `requires_file_upload` tinyint(1) DEFAULT 0,
  `description_ar` text DEFAULT NULL,
  `order_num` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `lesson_card_types`
--

INSERT INTO `lesson_card_types` (`id`, `code`, `name_ar`, `name_fr`, `icon`, `color`, `student_interaction_type`, `requires_chart`, `requires_file_upload`, `description_ar`, `order_num`, `status`, `created_at`) VALUES
(1, 'problem_situation', 'وضعية مشكلة', 'Situation Problème', 'fas fa-question-circle', 'warning', 'hypothesis', 0, 0, 'طرح إشكالية وجمع الملاحظات والفرضيات', 1, 'active', '2025-06-17 12:02:52'),
(2, 'activity', 'نشاط', 'Activité', 'fas fa-flask', 'primary', 'analysis', 1, 0, 'نشاط تجريبي أو تحليلي مع إمكانية الرسم البياني', 2, 'active', '2025-06-17 12:02:52'),
(3, 'conclusion', 'خلاصة', 'Conclusion', 'fas fa-lightbulb', 'success', 'keypoints', 0, 0, 'استخلاص النقاط الأساسية والقوانين', 3, 'active', '2025-06-17 12:02:52'),
(4, 'applied_exercise', 'تمرين تطبيقي', 'Exercice d\'Application', 'fas fa-pencil-alt', 'info', 'exercise', 0, 1, 'تمرين تطبيقي مع إمكانية رفع الحلول', 4, 'active', '2025-06-17 12:02:52'),
(5, 'synthesis_exercise', 'تمرين توليفي', 'Exercice de Synthèse', 'fas fa-puzzle-piece', 'secondary', 'exercise', 0, 1, 'تمرين توليفي شامل مع إمكانية رفع الملفات', 5, 'active', '2025-06-17 12:02:52');

-- --------------------------------------------------------

--
-- Structure de la table `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `current_card_index` int(11) DEFAULT 0,
  `completed_cards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`completed_cards`)),
  `total_time_spent` int(11) DEFAULT 0,
  `completion_percentage` decimal(5,2) DEFAULT 0.00,
  `status` enum('not_started','in_progress','completed','paused') DEFAULT 'not_started',
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `last_accessed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `lesson_progress`
--

INSERT INTO `lesson_progress` (`id`, `student_id`, `lesson_id`, `current_card_index`, `completed_cards`, `total_time_spent`, `completion_percentage`, `status`, `started_at`, `completed_at`, `last_accessed`) VALUES
(1, 6, 49, 1, NULL, 0, 66.67, 'in_progress', '2025-06-17 12:10:31', '2025-06-17 12:11:52', '2025-06-17 12:22:49'),
(7, 6, 50, 1, NULL, 0, 66.67, 'in_progress', '2025-06-17 12:14:38', NULL, '2025-06-17 12:22:33');

-- --------------------------------------------------------

--
-- Structure de la table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name_ar` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `order_index` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `levels`
--

INSERT INTO `levels` (`id`, `code`, `name_ar`, `description`, `order_index`) VALUES
(1, 'tc', 'الجذع المشترك', 'المستوى التأسيسي للتعليم الثانوي', 1),
(2, '1bac', 'الأولى باكالوريا', 'السنة الأولى من سلك البكالوريا', 2),
(3, '2bac', 'الثانية باكالوريا', 'السنة النهائية من التعليم الثانوي', 3);

-- --------------------------------------------------------

--
-- Structure de la table `security_log`
--

CREATE TABLE `security_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'معرف المستخدم (إذا كان مسجل)',
  `event_type` enum('login','logout','login_failed','password_change','profile_update','registration','admin_action') NOT NULL,
  `description` text DEFAULT NULL COMMENT 'وصف الحدث',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'عنوان IP',
  `user_agent` text DEFAULT NULL COMMENT 'متصفح المستخدم',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `student_card_interactions`
--

CREATE TABLE `student_card_interactions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `interaction_type` varchar(50) NOT NULL,
  `interaction_data` longtext DEFAULT NULL,
  `completion_status` enum('started','in_progress','completed') DEFAULT 'started',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `student_card_interactions`
--

INSERT INTO `student_card_interactions` (`id`, `student_id`, `lesson_id`, `card_id`, `interaction_type`, `interaction_data`, `completion_status`, `created_at`, `updated_at`) VALUES
(1, 6, 49, 8, 'chart', '{\"chartData\":[],\"analysis\":\"\"}', 'in_progress', '2025-06-17 12:46:03', '2025-06-17 12:49:26'),
(3, 6, 52, 14, 'chart', '{\"chartData\":[],\"analysis\":\"\"}', 'in_progress', '2025-06-17 15:02:31', '2025-06-17 15:02:31');

-- --------------------------------------------------------

--
-- Structure de la table `student_card_responses`
--

CREATE TABLE `student_card_responses` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `response_text` longtext DEFAULT NULL,
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_data`)),
  `score` decimal(5,2) DEFAULT 0.00,
  `max_score` decimal(5,2) DEFAULT 0.00,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `student_content_notes`
--

CREATE TABLE `student_content_notes` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `notes` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `student_content_notes`
--

INSERT INTO `student_content_notes` (`id`, `student_id`, `content_id`, `notes`, `created_at`, `updated_at`) VALUES
(14, 6, 38, 'ءؤءؤ', '2025-06-16 18:57:00', '2025-06-16 18:57:00'),
(0, 5, 49, 'ملاحظاتي عن هذا الدرس', '2025-06-23 10:52:57', '2025-06-23 10:52:57');

-- --------------------------------------------------------

--
-- Structure de la table `student_experiment_notes`
--

CREATE TABLE `student_experiment_notes` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `experiment_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `student_notes`
--

CREATE TABLE `student_notes` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `note_content` longtext DEFAULT NULL,
  `note_type` varchar(20) DEFAULT 'text',
  `drawing_data` longtext DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `student_uploaded_files`
--

CREATE TABLE `student_uploaded_files` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `storage_type` enum('local','google_drive') DEFAULT 'local',
  `google_drive_id` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `study_later`
--

CREATE TABLE `study_later` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `priority` varchar(10) DEFAULT 'medium',
  `reminder_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `study_later`
--

INSERT INTO `study_later` (`id`, `content_id`, `user_id`, `notes`, `priority`, `reminder_date`, `created_at`) VALUES
(7, 38, 6, '', '1', NULL, '2025-06-16 18:56:56'),
(8, 39, 6, '', '1', NULL, '2025-06-17 07:06:40'),
(9, 34, 6, '', '1', NULL, '2025-06-18 08:00:26'),
(0, 49, 5, '', '1', NULL, '2025-06-23 10:38:49');

-- --------------------------------------------------------

--
-- Structure de la table `teacher_messages`
--

CREATE TABLE `teacher_messages` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `message_type` varchar(20) DEFAULT 'comment',
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `teacher_reply` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','teacher','student') DEFAULT 'student',
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `user_type`, `first_name`, `last_name`, `full_name`, `phone`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@physiquepro.local', '$2y$10$woHSnaBXEHQ2nzFVv5aqMOy1csj2SydCvKte0cLW68S5IVZ3jITbq', 'admin', 'المدير', 'العام', 'المدير العام', NULL, 'active', '2025-06-19 09:06:46', '2025-06-23 16:54:03'),
(4, 'ayoub', 'ayoubmakran38@gmail.com', '$2y$10$0TmfStz/H3H0u4oJAVZLs.A9KmrW0b/ZbeWN7h8cZe/L6RY2Wq9rq', 'teacher', 'ayoub', 'makrane', 'ayoub makrane', NULL, 'active', '2025-06-19 09:12:28', '2025-06-24 12:51:23'),
(5, 'talib200', 'ayoub.makrane090@gmail.com', '$2y$10$IxWXyE2Y5UEBRGuifCkgJ.iuAHb6x2rJYL3vL1exA6cCDflSqLelK', 'student', 'Talib', '', 'Talib', NULL, 'active', '2025-06-22 11:30:38', '2025-06-24 12:56:53');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `status` (`status`);

--
-- Index pour la table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `level_id` (`level_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Index pour la table `content_files`
--
ALTER TABLE `content_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`);

--
-- Index pour la table `content_ratings`
--
ALTER TABLE `content_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `content_types`
--
ALTER TABLE `content_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_content_types_status_order` (`status`,`order_num`);

--
-- Index pour la table `experiment_charts`
--
ALTER TABLE `experiment_charts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `lesson_cards`
--
ALTER TABLE `lesson_cards`
  ADD KEY `idx_lesson_cards_document_drive_id` (`document_google_drive_id`),
  ADD KEY `idx_lesson_cards_video_drive_id` (`video_google_drive_id`),
  ADD KEY `idx_lesson_cards_document_storage` (`document_storage_type`),
  ADD KEY `idx_lesson_cards_video_storage` (`video_storage_type`);

--
-- Index pour la table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT pour la table `content_files`
--
ALTER TABLE `content_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `content_ratings`
--
ALTER TABLE `content_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `content_types`
--
ALTER TABLE `content_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `experiment_charts`
--
ALTER TABLE `experiment_charts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_level_fk` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_teacher_fk` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_type_fk` FOREIGN KEY (`type_id`) REFERENCES `content_types` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `content_files`
--
ALTER TABLE `content_files`
  ADD CONSTRAINT `files_content_fk` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `content_ratings`
--
ALTER TABLE `content_ratings`
  ADD CONSTRAINT `ratings_content_fk` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
