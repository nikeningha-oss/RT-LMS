-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2026 at 04:39 AM
-- Server version: 8.0.27
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `logistics_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `license_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `current_lat` decimal(10,8) DEFAULT NULL,
  `current_lng` decimal(11,8) DEFAULT NULL,
  `current_speed` decimal(8,2) DEFAULT NULL,
  `last_known_location_at` timestamp NULL DEFAULT NULL,
  `vehicle_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_earned` decimal(12,2) DEFAULT '0.00',
  `total_withdrawn` decimal(12,2) DEFAULT '0.00',
  `available_balance` decimal(12,2) DEFAULT '0.00',
  `last_payment_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `user_id`, `license_number`, `phone`, `is_available`, `current_lat`, `current_lng`, `current_speed`, `last_known_location_at`, `vehicle_id`, `created_at`, `updated_at`, `total_earned`, `total_withdrawn`, `available_balance`, `last_payment_date`) VALUES
(3, 10, 'LIC-256-16', '686798654', 1, 4.11252000, 9.61851000, 0.00, '2026-07-21 03:39:46', 1, '2026-06-27 01:06:27', '2026-07-23 01:24:08', 15250.00, 15250.00, 0.00, '2026-07-23 01:24:08'),
(4, 11, 'PENDING-11', '682346589', 1, 4.11255500, 9.61848500, 0.00, '2026-07-21 03:50:27', 2, '2026-06-27 12:08:32', '2026-07-23 01:24:19', 33901.67, 33901.67, 0.00, '2026-07-23 01:24:19'),
(5, 12, 'LIC-001', '123456789', 1, NULL, NULL, NULL, NULL, NULL, '2026-07-09 20:20:10', '2026-07-09 20:20:10', 0.00, 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `driver_payments`
--

CREATE TABLE `driver_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `driver_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `month` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_by` bigint UNSIGNED NOT NULL,
  `paid_at` timestamp NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_payments`
--

INSERT INTO `driver_payments` (`id`, `driver_id`, `amount`, `month`, `paid_by`, `paid_at`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 10250.00, 'July 2026', 9, '2026-07-23 01:24:08', 'Payment for July 2026 - latifa', 'completed', '2026-07-23 01:24:08', '2026-07-23 01:24:08'),
(2, 4, 33901.67, 'July 2026', 9, '2026-07-23 01:24:19', 'Payment for July 2026 - boy', 'completed', '2026-07-23 01:24:19', '2026-07-23 01:24:19');

-- --------------------------------------------------------

--
-- Table structure for table `earnings`
--

CREATE TABLE `earnings` (
  `id` bigint UNSIGNED NOT NULL,
  `driver_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivery',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `description` text COLLATE utf8mb4_unicode_ci,
  `earned_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
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
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint UNSIGNED NOT NULL,
  `locatable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locatable_id` bigint UNSIGNED NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `speed` decimal(8,2) DEFAULT NULL,
  `heading` decimal(5,2) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_06_18_122111_add_role_to_users_table', 1),
(6, '2026_06_18_141901_create_drivers_table', 1),
(7, '2026_06_18_142628_create_locations_table', 1),
(8, '2026_06_19_063405_create_orders_table', 2),
(9, '2026_06_20_155451_create_notification_table', 3),
(10, '2026_06_22_083022_add_approval_fields_to_users_table', 4),
(11, '2026_06_18_142724_create_packages_table', 1),
(12, '2026_06_24_042124_create_vehicles_table', 5),
(13, '2026_06_27_040823_add_is_available_to_users_table', 6),
(14, '2026_06_27_042135_add_phone_to_users_table', 7),
(15, '2026_06_27_043944_add_vehicle_id_to_orders_table', 7),
(16, '2026_06_27_050117_add_vehicle_id_to_orders_table', 7),
(17, '2026_06_27_121312_add_coordinates_to_orders_table', 8),
(18, '2026_06_27_131535_add_phone_to_users_table', 7),
(19, '2026_06_27_141310_add_pricing_fields_to_orders_table', 10),
(20, '2026_06_29_021021_add_pricing_fields_to_orders_table', 11),
(21, '2026_06_27_102832_add_payment_status_to_orders_table', 11),
(22, '2026_06_29_035337_add_location_columns_to_drivers_table', 12),
(23, '2026_06_29_040005_add_location_columns_to_drivers_table', 12),
(24, '2026_07_09_211146_create_driver_payments_table', 12),
(25, '2026_07_17_135540_create_earnings_table', 13),
(26, '2026_07_17_161928_add_last_payment_date_to_drivers_table', 13),
(27, '2026_07_23_020823_create_driver_payments_table', 14),
(28, '2026_07_23_020828_create_earnings_table', 14),
(29, '2026_07_23_020833_create_withdrawal_requests_table', 14);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `vehicle_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('pending','assigned','picked_up','in_transit','delivered','cancelled','price_pending','price_confirmed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `pickup_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pickup_lat` decimal(10,8) DEFAULT NULL,
  `pickup_lng` decimal(11,8) DEFAULT NULL,
  `delivery_lat` decimal(10,8) DEFAULT NULL,
  `delivery_lng` decimal(11,8) DEFAULT NULL,
  `distance_km` decimal(8,2) DEFAULT NULL,
  `weight_kg` decimal(8,2) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `base_fare` decimal(10,2) NOT NULL DEFAULT '0.00',
  `distance_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `weight_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `service_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `driver_earning` decimal(10,2) NOT NULL DEFAULT '0.00',
  `driver_commission_rate` decimal(5,2) NOT NULL DEFAULT '50.00',
  `platform_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `estimated_delivery` timestamp NULL DEFAULT NULL,
  `actual_delivery` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_id`, `driver_id`, `vehicle_id`, `status`, `payment_status`, `pickup_address`, `delivery_address`, `pickup_lat`, `pickup_lng`, `delivery_lat`, `delivery_lng`, `distance_km`, `weight_kg`, `total_price`, `base_fare`, `distance_charge`, `weight_charge`, `service_fee`, `tax_rate`, `tax_amount`, `driver_earning`, `driver_commission_rate`, `platform_fee`, `notes`, `estimated_delivery`, `actual_delivery`, `created_at`, `updated_at`) VALUES
(2, 'ORD-20260627-D0FZR', 8, 10, NULL, 'delivered', 'pending', 'bonaberi', 'bonamoussadi', NULL, NULL, NULL, NULL, 5.00, 2.50, 3500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1750.00, 50.00, 0.00, NULL, NULL, '2026-06-27 12:04:57', '2026-06-27 03:33:45', '2026-06-27 12:04:57'),
(3, 'ORD-20260627-TCNIK', 8, 10, NULL, 'delivered', 'pending', 'yaounde', 'douala', NULL, NULL, NULL, NULL, 5.00, 2.50, 3500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1750.00, 50.00, 0.00, NULL, NULL, '2026-06-27 11:03:32', '2026-06-27 03:34:16', '2026-06-27 11:03:32'),
(4, 'ORD-20260627-TDD3K', 8, 10, 1, 'delivered', 'pending', 'baffousam', 'Limbe', NULL, NULL, NULL, NULL, 5.00, 2.50, 3000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1500.00, 50.00, 0.00, NULL, NULL, '2026-06-27 12:05:43', '2026-06-27 10:10:31', '2026-06-27 12:05:43'),
(5, 'ORD-20260627-0NTDK', 8, 10, NULL, 'delivered', 'pending', 'Camp Yabassi, Douala, Douala II, Communauté urbaine de Douala, Wouri, Littoral, Cameroon', 'Edéa, Édéa I, Communauté urbaine d\'Édéa, Sanaga-Maritime, Littoral, Cameroon', NULL, NULL, NULL, NULL, 5.00, 2.50, 20000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 10000.00, 50.00, 0.00, NULL, NULL, '2026-07-07 17:09:43', '2026-06-27 11:48:31', '2026-07-07 17:09:43'),
(6, 'ORD-20260629-X4MDX', 8, 10, NULL, 'delivered', 'paid', 'Ndop, Ngoketunjia, Northwest, Cameroon', 'Société Générale Denver, Rue 5.051, Bonamoussadi, Bonagang, Douala V, Communauté urbaine de Douala, Wouri, Littoral, Cameroon', 5.97919715, 10.41521852, 4.09412619, 9.73664444, 5.00, 2.50, 551.25, 500.00, 0.00, 0.00, 25.00, 5.00, 26.25, 250.00, 50.00, 250.00, NULL, NULL, '2026-07-07 17:11:56', '2026-06-29 01:29:09', '2026-07-07 17:11:56'),
(7, 'ORD-20260629-5IYMG', 8, 11, NULL, 'delivered', 'paid', 'Ndop, Ngoketunjia, Northwest, Cameroon', 'Société Générale Denver, Rue 5.051, Bonamoussadi, Bonagang, Douala V, Communauté urbaine de Douala, Wouri, Littoral, Cameroon', 5.97919715, 10.41521852, 4.09412619, 9.73664444, 222.68, 2.50, 74753.17, 500.00, 66803.33, 500.00, 3390.17, 5.00, 3559.67, 33901.67, 50.00, 33901.67, NULL, NULL, '2026-07-21 03:50:30', '2026-06-29 01:32:16', '2026-07-21 03:50:30'),
(8, 'ORD-20260629-0FVNJ', 8, 10, NULL, 'assigned', 'paid', 'Centre Médical d\'Arrondissement de Congo, Avenue du Docteur Jamot (N° 1.407), Vallée Bessengué, Deido, Douala II, Communauté urbaine de Douala, Wouri, Littoral, Cameroon', 'Road To PK 12, Ndoghem, Logbessou Pk.14, Douala III, Communauté urbaine de Douala, Wouri, Littoral, Cameroon', 4.04145636, 9.70543954, 4.06642198, 9.78969974, 9.75, 3.50, 4547.63, 500.00, 2924.83, 700.00, 206.24, 5.00, 216.55, 2062.42, 50.00, 2062.42, NULL, NULL, NULL, '2026-06-29 17:09:55', '2026-07-21 02:03:41'),
(9, 'ORD-20260707-KRYOZ', 8, NULL, NULL, 'assigned', 'paid', 'New-Bell, Douala, Douala II, Communauté urbaine de Douala, Wouri, Littoral, Cameroon', 'Fomukong Street, Ntamulung, Bamenda 2, Bamenda, Mezam, Northwest, Cameroon', 4.03399661, 9.70810057, 5.95909425, 10.14857925, 219.55, 20.00, 77577.76, 500.00, 65865.32, 4000.00, 3518.27, 5.00, 3694.18, 35182.66, 50.00, 35182.66, NULL, NULL, NULL, '2026-07-07 17:00:29', '2026-07-17 16:38:17');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `is_fragile` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `role` enum('admin','driver','customer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `approval_status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `is_available`, `role`, `approval_status`, `approved_at`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `approved_by`, `rejection_reason`) VALUES
(8, 'nike', 'nike@gmail.com', '675453323', 1, 'customer', 'approved', '2026-06-27 03:30:35', NULL, '$2y$10$ozn.kTSOTF9L.yWn9.u9ZesiKuJ/OZpOM64G6RAjEwCN93s1vLPwG', 'RNRWRRml6OyRl67iUGS6bNDFikyB6lB2giDChGQYpntc9Hxh3RjRfZmFCYCT', '2026-06-24 18:49:52', '2026-06-29 02:03:36', 9, NULL),
(9, 'admin', 'admin@gmail.com', '676516978', 1, 'admin', 'approved', NULL, NULL, '$2y$10$AGfZRp64JR7lG4BL1kX/HebYCWFBjCP2giwsy14Vz6OfoViPM7shG', NULL, NULL, NULL, NULL, NULL),
(10, 'latifa', 'latifa@gmail.com', '686798654', 1, 'driver', 'approved', '2026-06-27 03:30:37', NULL, '$2y$10$jVKOCUH4qj6wAV1nSxLq.uv3wUDNyqRBiIOAoaxqALYZFczVmRfh6', NULL, '2026-06-27 01:06:27', '2026-06-27 03:30:37', 9, NULL),
(11, 'boy', 'boy@gmail.com', '682346589', 1, 'driver', 'approved', '2026-06-29 02:29:21', NULL, '$2y$10$aTzWjJud4iRnzlc1.K2GkucAqsu0Z.hl/0sagoAHZnBC66z4V2r.m', NULL, '2026-06-27 12:08:32', '2026-06-29 02:29:21', NULL, NULL),
(12, 'Test Driver', 'driver@test.com', NULL, 1, 'driver', 'approved', NULL, NULL, '$2y$10$p84YAxYIYDfNpzBbtowUiuTyxZWY5L.8HPbEvaUmjr.a0uPdv3e7y', NULL, '2026-07-09 20:20:06', '2026-07-09 20:20:06', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint UNSIGNED NOT NULL,
  `plate_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `make` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` int DEFAULT NULL,
  `capacity` decimal(10,2) DEFAULT NULL,
  `type` enum('truck','van','motorcycle','bicycle','car') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'van',
  `status` enum('available','on_delivery','maintenance','idle','offline') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `current_latitude` decimal(10,8) DEFAULT NULL,
  `current_longitude` decimal(11,8) DEFAULT NULL,
  `last_known_location_at` timestamp NULL DEFAULT NULL,
  `gps_device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fuel_type` enum('petrol','diesel','electric','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diesel',
  `fuel_consumption` decimal(8,2) DEFAULT NULL,
  `mileage` int NOT NULL DEFAULT '0',
  `last_service_date` date DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `insurance_provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_policy_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_refrigeration` tinyint(1) NOT NULL DEFAULT '0',
  `has_liftgate` tinyint(1) NOT NULL DEFAULT '0',
  `has_gps_tracking` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `purchase_price` decimal(15,2) DEFAULT NULL,
  `daily_rental_rate` decimal(10,2) DEFAULT NULL,
  `cost_per_km` decimal(10,2) DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `insurance_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `plate_number`, `registration_number`, `vin`, `make`, `model`, `color`, `year`, `capacity`, `type`, `status`, `driver_id`, `current_latitude`, `current_longitude`, `last_known_location_at`, `gps_device_id`, `tracking_number`, `fuel_type`, `fuel_consumption`, `mileage`, `last_service_date`, `next_service_date`, `insurance_expiry`, `insurance_provider`, `insurance_policy_number`, `has_refrigeration`, `has_liftgate`, `has_gps_tracking`, `is_active`, `purchase_price`, `daily_rental_rate`, `cost_per_km`, `photo`, `insurance_document`, `registration_document`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'LT-1523', NULL, NULL, NULL, 'toyota', NULL, NULL, NULL, 'van', 'on_delivery', 3, NULL, NULL, NULL, NULL, NULL, 'diesel', NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-27 01:06:27', '2026-06-27 12:09:54', NULL),
(2, 'LT-253', NULL, NULL, NULL, 'carolla', NULL, NULL, NULL, 'van', 'available', 4, NULL, NULL, NULL, NULL, NULL, 'diesel', NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-27 12:10:37', '2026-06-27 12:10:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_requests`
--

CREATE TABLE `withdrawal_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `driver_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_details` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_id` bigint UNSIGNED DEFAULT NULL,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `requested_at` timestamp NOT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `drivers_license_number_unique` (`license_number`),
  ADD KEY `drivers_user_id_foreign` (`user_id`);

--
-- Indexes for table `driver_payments`
--
ALTER TABLE `driver_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_payments_paid_by_foreign` (`paid_by`),
  ADD KEY `driver_payments_driver_id_index` (`driver_id`),
  ADD KEY `driver_payments_month_index` (`month`),
  ADD KEY `driver_payments_paid_at_index` (`paid_at`);

--
-- Indexes for table `earnings`
--
ALTER TABLE `earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `earnings_driver_id_index` (`driver_id`),
  ADD KEY `earnings_order_id_index` (`order_id`),
  ADD KEY `earnings_status_index` (`status`),
  ADD KEY `earnings_earned_at_index` (`earned_at`),
  ADD KEY `earnings_type_index` (`type`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locations_locatable_type_locatable_id_index` (`locatable_type`,`locatable_id`),
  ADD KEY `locations_locatable_id_locatable_type_recorded_at_index` (`locatable_id`,`locatable_type`,`recorded_at`);

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
  ADD KEY `notifications_user_id_foreign` (`user_id`),
  ADD KEY `notifications_order_id_foreign` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_plate_number_unique` (`plate_number`),
  ADD UNIQUE KEY `vehicles_registration_number_unique` (`registration_number`),
  ADD UNIQUE KEY `vehicles_vin_unique` (`vin`),
  ADD KEY `vehicles_plate_number_index` (`plate_number`),
  ADD KEY `vehicles_driver_id_index` (`driver_id`),
  ADD KEY `vehicles_status_index` (`status`),
  ADD KEY `vehicles_type_index` (`type`),
  ADD KEY `vehicles_is_active_index` (`is_active`),
  ADD KEY `vehicles_is_active_status_index` (`is_active`,`status`),
  ADD KEY `vehicles_registration_number_index` (`registration_number`);

--
-- Indexes for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdrawal_requests_admin_id_foreign` (`admin_id`),
  ADD KEY `withdrawal_requests_driver_id_index` (`driver_id`),
  ADD KEY `withdrawal_requests_status_index` (`status`),
  ADD KEY `withdrawal_requests_requested_at_index` (`requested_at`),
  ADD KEY `withdrawal_requests_processed_at_index` (`processed_at`),
  ADD KEY `withdrawal_requests_payment_method_index` (`payment_method`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `driver_payments`
--
ALTER TABLE `driver_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `earnings`
--
ALTER TABLE `earnings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_payments`
--
ALTER TABLE `driver_payments`
  ADD CONSTRAINT `driver_payments_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `driver_payments_paid_by_foreign` FOREIGN KEY (`paid_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `earnings`
--
ALTER TABLE `earnings`
  ADD CONSTRAINT `earnings_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `earnings_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD CONSTRAINT `withdrawal_requests_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `withdrawal_requests_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
