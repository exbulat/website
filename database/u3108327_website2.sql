-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el8
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Апр 24 2025 г., 10:21
-- Версия сервера: 8.0.25-15
-- Версия PHP: 8.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `u3108327_website2`
--

-- --------------------------------------------------------

--
-- Структура таблицы `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Дамп данных таблицы `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 3, 'delete', 'Survey', 30, 'Удаление опроса администратором: йцу', '{\"id\":30,\"user_id\":3,\"title\":\"\\u0439\\u0446\\u0443\",\"description\":null,\"slug\":\"icu-m3QZw\",\"code\":\"5RuAnKfk\",\"access_code\":null,\"design\":{\"primary_color\":\"#4e73df\",\"background_color\":\"#ffffff\",\"font\":\"Open Sans\",\"image_opacity\":\"0.3\"},\"is_public\":true,\"start_at\":null,\"end_at\":null,\"time_limit\":null,\"is_active\":true,\"show_results\":true,\"views\":1,\"created_at\":\"2025-04-24T06:43:28.000000Z\",\"updated_at\":\"2025-04-24T06:43:41.000000Z\",\"is_archived\":false,\"archived_at\":null,\"questions\":[{\"id\":37,\"survey_id\":30,\"title\":\"qwe\",\"description\":\"qwe\",\"type\":\"single_choice\",\"options\":[\"qwe\",\"qwe\"],\"position\":1,\"is_required\":false,\"time_limit\":null,\"created_at\":\"2025-04-24T06:43:34.000000Z\",\"updated_at\":\"2025-04-24T06:43:34.000000Z\"},{\"id\":38,\"survey_id\":30,\"title\":\"qwe\",\"description\":\"qwe\",\"type\":\"single_choice\",\"options\":[\"qwe\",\"qwe\"],\"position\":2,\"is_required\":false,\"time_limit\":null,\"created_at\":\"2025-04-24T06:43:38.000000Z\",\"updated_at\":\"2025-04-24T06:43:38.000000Z\"}],\"answers\":[{\"id\":47,\"survey_id\":30,\"question_id\":37,\"user_id\":3,\"session_id\":null,\"value\":[\"qwe\"],\"created_at\":\"2025-04-24T06:43:45.000000Z\",\"updated_at\":\"2025-04-24T06:43:45.000000Z\"},{\"id\":48,\"survey_id\":30,\"question_id\":38,\"user_id\":3,\"session_id\":null,\"value\":[\"qwe\"],\"created_at\":\"2025-04-24T06:43:45.000000Z\",\"updated_at\":\"2025-04-24T06:43:45.000000Z\"}],\"user\":{\"id\":3,\"name\":\"Superadministrator\",\"email\":\"superadmin@example.com\",\"is_admin\":true,\"is_super_admin\":true,\"avatar\":null,\"email_verified_at\":null,\"created_at\":\"2025-04-23T04:54:50.000000Z\",\"updated_at\":\"2025-04-23T16:12:36.000000Z\"}}', NULL, '188.19.160.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-24 08:51:01', '2025-04-24 08:51:01'),
(2, 3, 'logout', 'User', 3, 'Выход пользователя из системы', NULL, NULL, '188.19.160.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-24 08:58:38', '2025-04-24 08:58:38'),
(3, 1, 'login', 'User', 1, 'Вход пользователя в систему', NULL, NULL, '188.19.160.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-24 08:58:50', '2025-04-24 08:58:50');

-- --------------------------------------------------------

--
-- Структура таблицы `answers`
--

CREATE TABLE `answers` (
  `id` bigint UNSIGNED NOT NULL,
  `survey_id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Структура таблицы `archived_surveys`
--

CREATE TABLE `archived_surveys` (
  `id` bigint UNSIGNED NOT NULL,
  `original_id` bigint UNSIGNED NOT NULL COMMENT 'Идентификатор оригинального опроса',
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `design` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `show_results` tinyint(1) NOT NULL DEFAULT '1',
  `start_at` timestamp NULL DEFAULT NULL,
  `end_at` timestamp NULL DEFAULT NULL,
  `time_limit` int DEFAULT NULL,
  `views` int NOT NULL DEFAULT '0',
  `archived_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Структура таблицы `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_04_18_000001_create_surveys_table', 1),
(5, '2025_04_18_000002_create_questions_table', 1),
(6, '2025_04_18_000003_create_answers_table', 1),
(7, '2025_04_18_174600_add_views_to_surveys_table', 1),
(8, '2025_04_19_083758_add_avatar_to_users_table', 2),
(9, '2025_04_20_085526_add_access_code_to_surveys_table', 3),
(10, '2025_04_20_100344_create_survey_notifications_table', 4),
(11, '2025_04_20_104447_add_show_results_to_surveys_table', 5),
(12, '2025_04_21_065826_add_soft_deletes_to_surveys_table', 5),
(13, '2025_04_21_070855_create_archived_surveys_table', 6),
(14, '2025_04_21_105900_add_response_data_to_survey_notifications_table', 7),
(15, '2023_04_21_create_add_is_admin_to_users_table', 8),
(16, '2025_04_21_175152_add_is_archived_to_surveys_table', 9),
(17, '2025_04_22_082741_add_show_results_to_surveys_table', 10),
(18, '2025_04_23_add_is_super_admin_to_users_table', 11),
(19, '2025_04_23_create_activity_logs_table', 11);

-- --------------------------------------------------------

--
-- Структура таблицы `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('bulatovmisa213@gmail.com', '$2y$12$IgjYGm2BcA2U1a4FwM5n/ebDPJ5mbnivAwVkOX447S3SkhGoVG72i', '2025-04-24 05:16:10'),
('dreglya2007@mail.ru', '$2y$12$Cz.yBrCMfqk7Wm5aBGGs9.ql/nML/q4rAsSFGp/sXExAfQds9LQ06', '2025-04-24 05:24:54');

-- --------------------------------------------------------

--
-- Структура таблицы `questions`
--

CREATE TABLE `questions` (
  `id` bigint UNSIGNED NOT NULL,
  `survey_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `position` int NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `time_limit` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Дамп данных таблицы `questions`
--

INSERT INTO `questions` (`id`, `survey_id`, `title`, `description`, `type`, `options`, `position`, `is_required`, `time_limit`, `created_at`, `updated_at`) VALUES
(39, 31, 'Ты любишь хакатон?', 'Подумай хорошо)', 'single_choice', '[\"\\u0414\\u0430\",\"\\u041d\\u0435\\u0442\"]', 1, 1, NULL, '2025-04-24 08:56:17', '2025-04-24 08:56:17'),
(40, 31, 'Насколько ты любишь хакатон?', 'Выбери от 1 до 10', 'scale', '{\"min\":\"1\",\"max\":\"10\"}', 2, 1, 10, '2025-04-24 08:56:45', '2025-04-24 08:56:45'),
(41, 31, 'Выбери два прилагательных, описывающих Хакатон-2025', 'Нужно подумать...', 'multiple_choice', '[\"\\u0423\\u0436\\u0430\\u0441\\u043d\\u044b\\u0439\",\"\\u041b\\u0423\\u0427\\u0428\\u0418\\u0419!!!\",\"\\u0412\\u041e\\u0421\\u0425\\u0418\\u0422\\u0418\\u0422\\u0415\\u041b\\u042c\\u041d\\u042b\\u0419!!!\",\"\\u041d\\u0435 \\u043e\\u0447\\u0435\\u043d\\u044c\"]', 3, 1, NULL, '2025-04-24 08:57:26', '2025-04-24 08:57:26'),
(42, 31, 'Опиши одним словом, почему тебе нравится Хакатон-2025', NULL, 'text', '[]', 4, 1, 15, '2025-04-24 08:57:45', '2025-04-24 08:57:45');

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1uzTLpve81uVuamCplz9Amcov4FIp1yzkR8l3KdL', NULL, '82.147.85.86', 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic1N4enY0cXNjQml6UWIyUnNZcEc2bjJwYk9mSGExVmJ2OHZpWURWaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745478461),
('4Jod35Xz3QSDdCAULVlh1iDTameaxIqQTzL3TC5v', NULL, '178.33.107.250', 'Python/3.10 aiohttp/3.8.3', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic1hxUEUwVjMxTXpQNVdlWDhvdXpSNHgzc0tmZTRUSkt1T0ZIYUFwZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745475072),
('6lrYHpQGbxh5OofTP8jaxGRNigsnliAFB3lUSCHK', NULL, '64.227.27.229', 'Mozilla/5.0 (compatible)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZkZMbVdjb21Jb2Z5NHFOVmJWeHFtQ1cyU1BuRmVYTjNZYUl6VFVjdyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vd3d3LnN1cnZleW1hc3Rlci5ydSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1745475965),
('gN68B8ZtY5tO6f9pb9yrlkLQC8k3ft0JzxUfBaey', NULL, '8.213.197.208', 'Go-http-client/1.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDJUZzNsdmVNS1JGSFM2NUtpejlHS3BWSGh2RE1oQ1RyWmtzMnVuSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745477474),
('imCOvSyA9XHkEH1bILgCcUI2jSBpOWSCqHeeV7U8', NULL, '154.28.229.108', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVDh0WG9ETGdyM2NnbWtVaW03Y0JVS0FTTmt1Q1VRRGp2bzZtcFB2aiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745478708),
('ixJxbNTxp8jQrerPe1i3X5GPaHUlAoxn1MRhVopu', NULL, '188.19.160.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV09CMXpmUmxWN2dvY2k1NEV1NDl2OEFBajh6bDNjM3gwS0V4SVhTUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1L3MvdGZSSGZZZ1QvcmVzcG9uc2VzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745470726),
('jUi5x8eafMTsHFTIgRcgTwEa9lxqCrBKooLJgMpx', 1, '188.19.160.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiczdvUkQyeFMzWHk3eFR0SVpNRW9oSDlHWXdKRUFvakIybkR3dzI3OCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NDU0Nzc5MzA7fX0=', 1745477941),
('KOIS327yfBUTL2qO4L8Vs90oZLGExVrHRXvAABMn', NULL, '154.28.229.39', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOGdxeUZkSDRRbHZTY0xzSUxGcUQ5MVdGYXBIYTg5VEM2NWtHMWZmTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745478707),
('LZCymNwTRGS44CZChtg66bKjQZwao6w4UqZS3Egz', NULL, '185.157.97.241', '', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaWlHY2hjcG1yVk1RWk9WNTVvZWdhbFBvazFqYnd4UmFSTWJZS0R6ZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745475135),
('mdNs1hMcvQNVdg78ztwsWnQXwM9qKyN36RXqFy9S', NULL, '66.249.66.86', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.7049.95 Mobile Safari/537.36 (compatible; GoogleOther)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWJka2ozcnppbWdnVlBxWjJaVk9VZTByMzhNdVV4bkI2c3Z5eEFjVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vd3d3LnN1cnZleW1hc3Rlci5ydS9zdXJ2ZXlzL3B1YmxpYyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1745475364),
('mFEhBfOC80GmsNAHNA9hNUQUswWowvdHSAxny3dt', NULL, '91.84.87.137', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZUplaEZXWFlFczNVV1ZzMUtSSHFLbkNZN3Y5c0I1U3JaVUUwRVQ2eiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745474633),
('OsGl3XOOQJt42brLGky2b1VKYC9PYAYoZvk5YlS2', NULL, '154.223.139.58', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSHRvV2RuWDVTdjBFY25STVlKRGRiWWR1UDBONDdvMDBaNGtLcG9xMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745472515),
('td4sy0DXT3FcyJXEwyOd4D1Rq5AREETEfe0HasB3', NULL, '176.53.219.162', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoickkzUHRwMXIwekVzckxUYmtQN0c2a1huMlNBVGZCeW1HbW55akFkOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vd3d3LnN1cnZleW1hc3Rlci5ydSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1745472648),
('TFvu3RmSalUdJRLDWLR5v3NpyVupP3bSu89rmbQX', NULL, '154.28.229.39', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMHdEOHRaYXZQVHR4Q0RWSnhwaWRFWDNQM09OaWNpanFsNm5DUmk2TiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745478721),
('TvLWR9fz7s8x9UueAvhX6uG2bjs7qV9XmGra5FrB', NULL, '5.143.37.135', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 CCleaner/130.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRElBQVlwVVllWTJ2aWVBQmlKT05VSDJtbDM1UmtSOEtiMWpGT0FyciI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745474407),
('UZtZKCctcickDezoln0N9hRmqQ13hOog6OQN9I2R', NULL, '154.28.229.108', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYlRueHFvQ01lRmJLd1hpaE94bHNRSnFHeDRIRXRxV3N1d0kwTzJFMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745478722),
('v00y8gbMbu0IlOWpVAihQo0IS4thVXPeU0gFU1Qz', NULL, '66.249.66.87', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.7049.95 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMlBEVDBkbjRUbzU3bmpwaTVpS3VOOWJVTHFOdnNhMzM2Q0laSjZYSSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vd3d3LnN1cnZleW1hc3Rlci5ydSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1745473589),
('xf1GcPkjIO9ambTuRxjlduFb59r3jdwaWaLJmzQn', NULL, '82.147.85.86', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36 Herring/95.1.8810.11', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN0s1OVJ5YVFUQzRQdVZXaHlKYWFhc3FCbHI5dlVwbUZtbkVjdDlsWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1745478460),
('YnC2oaezhkYQMwzmWH4DWCUdvohwcmGSq27R4Bxw', NULL, '188.19.160.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicEdxZzE4WktJREdZc3JUa3ZVYW1vQ2REV0JtclNuSGlxaDFYWWx1MiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHBzOi8vc3VydmV5bWFzdGVyLnJ1L3MvdGZSSGZZZ1QvdGFrZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1745470743);

-- --------------------------------------------------------

--
-- Структура таблицы `surveys`
--

CREATE TABLE `surveys` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `design` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `start_at` timestamp NULL DEFAULT NULL,
  `end_at` timestamp NULL DEFAULT NULL,
  `time_limit` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `show_results` tinyint(1) NOT NULL DEFAULT '1',
  `views` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `archived_at` timestamp NULL DEFAULT NULL
) ;

--
-- Дамп данных таблицы `surveys`
--

INSERT INTO `surveys` (`id`, `user_id`, `title`, `description`, `slug`, `code`, `access_code`, `design`, `is_public`, `start_at`, `end_at`, `time_limit`, `is_active`, `show_results`, `views`, `created_at`, `updated_at`, `is_archived`, `archived_at`) VALUES
(31, 3, 'Хакатон', 'Ответьте на парочку вопросов о Хакатоне', 'xakaton-caxjH', 'EjdyyiyL', NULL, '{\"primary_color\":\"#754edf\",\"background_color\":\"#4d2d2d\",\"font\":\"Roboto\",\"image_opacity\":\"0\",\"background_image\":\"survey_backgrounds\\/WHqGSILHVGNnanJ7NEwBZLvMPW9cGddkdJ2kjRfN.png\"}', 1, NULL, NULL, 200, 1, 1, 0, '2025-04-24 08:55:49', '2025-04-24 08:55:49', 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `survey_notifications`
--

CREATE TABLE `survey_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `survey_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `respondent_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `response_id` bigint UNSIGNED DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_super_admin` tinyint(1) NOT NULL DEFAULT '0',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `is_admin`, `is_super_admin`, `avatar`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Пользователь', 'andreipuchuk@gmail.com', 0, 0, 'avatars/photo_2024-01-03_13-07-51.jpg', NULL, '$2y$12$klq6Ki7zu15J5hsrWvo6VO4adBENUabXSOIXLVN.CdQl1ixP.rFJ6', '2get892iZlVfvni6qM4WS5kTbfQGjW03E4c3lKC8Pl68ZsTswVXmQLmW6fYb', '2025-04-19 02:25:56', '2025-04-24 06:55:36'),
(2, 'Administrator', 'admin@example.com', 1, 0, 'avatars/crossword.png', NULL, '$2y$12$4UsOyfndnvoVKq3/dGG3oOY/VmQCNiLTujDDo8QUmVMQwoEq9ha5K', 'iEzKMGaE7cCsabpbrfZYQd6MhlefaXSTiy1Qw28cbJizLqckBn7xyGBJllqU', '2025-04-21 12:16:26', '2025-04-24 05:00:05'),
(3, 'Superadministrator', 'superadmin@example.com', 1, 1, NULL, NULL, '$2y$12$vYS3DxYlJAW58Uu9/YghR.55PPw5zpWjwkQx7CzvBKaVwUyUDMk5.', 'pvJ51zITAiPZqIVHu7uv93qo6aPOHV4AHGiod8VLbqRrHUxOjIzTumho9oqU', '2025-04-23 06:54:50', '2025-04-23 18:12:36');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`),
  ADD KEY `activity_logs_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  ADD KEY `activity_logs_action_index` (`action`),
  ADD KEY `activity_logs_created_at_index` (`created_at`);

--
-- Индексы таблицы `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answers_survey_id_foreign` (`survey_id`),
  ADD KEY `answers_question_id_foreign` (`question_id`),
  ADD KEY `answers_user_id_foreign` (`user_id`);

--
-- Индексы таблицы `archived_surveys`
--
ALTER TABLE `archived_surveys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `archived_surveys_code_unique` (`code`),
  ADD KEY `archived_surveys_user_id_foreign` (`user_id`);

--
-- Индексы таблицы `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Индексы таблицы `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Индексы таблицы `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Индексы таблицы `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Индексы таблицы `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Индексы таблицы `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_survey_id_foreign` (`survey_id`);

--
-- Индексы таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Индексы таблицы `surveys`
--
ALTER TABLE `surveys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `surveys_slug_unique` (`slug`),
  ADD UNIQUE KEY `surveys_code_unique` (`code`),
  ADD KEY `surveys_user_id_foreign` (`user_id`);

--
-- Индексы таблицы `survey_notifications`
--
ALTER TABLE `survey_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_notifications_user_id_foreign` (`user_id`),
  ADD KEY `survey_notifications_survey_id_foreign` (`survey_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `answers`
--
ALTER TABLE `answers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `archived_surveys`
--
ALTER TABLE `archived_surveys`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `surveys`
--
ALTER TABLE `surveys`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `survey_notifications`
--
ALTER TABLE `survey_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answers_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `archived_surveys`
--
ALTER TABLE `archived_surveys`
  ADD CONSTRAINT `archived_surveys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `surveys`
--
ALTER TABLE `surveys`
  ADD CONSTRAINT `surveys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `survey_notifications`
--
ALTER TABLE `survey_notifications`
  ADD CONSTRAINT `survey_notifications_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `survey_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
