-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 23, 2025 at 05:55 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `retreat_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `booking_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `retreat_id` bigint UNSIGNED NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '+91',
  `whatsapp_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `married` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `diocese` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parish` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `congregation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emergency_contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_participants` int NOT NULL DEFAULT '0',
  `special_remarks` text COLLATE utf8mb4_unicode_ci,
  `flag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `participant_number` int NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_id`, `retreat_id`, `firstname`, `lastname`, `country_code`, `whatsapp_number`, `age`, `email`, `address`, `gender`, `married`, `city`, `state`, `diocese`, `parish`, `congregation`, `emergency_contact_name`, `emergency_contact_phone`, `additional_participants`, `special_remarks`, `flag`, `is_active`, `participant_number`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'RB1', 1, 'sachin override', 'csdsds', '+91', '8943928628', 55, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'other', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, 'GENDER_MISMATCH,MAX_AGE_FAILED', 1, 1, 1, 1, '2025-10-08 13:12:22', '2025-10-08 13:12:22'),
(2, 'RB2', 1, 'nithin', 'dsfsd', '+91', '2344233243', 10, 'asdf@fg.com', 'South Kalamassery', 'female', NULL, 'Paravur', 'Kerala', NULL, NULL, NULL, 'ashcbhasnjcc', '43343443434324', 0, 'nsdbvhb', NULL, 1, 1, 1, 1, '2025-10-08 13:13:56', '2025-10-08 13:13:56'),
(3, 'RB3', 2, 'sddsdsds', 'sadsadds', '+91', '3244322343', 34, 'asdf@fg.com', 'South Kalamassery', 'male', NULL, 'Paravur', 'Kerala', NULL, NULL, 'serdtfgyhuji', 'sddsf', '4334434355', 0, NULL, NULL, 0, 1, 1, 1, '2025-10-08 13:23:41', '2025-11-09 04:04:11'),
(4, 'RB4', 1, 'kiran', 'sndjs', '+91', '8943928628', 10, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'other', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin', '08943928625', 0, 'hjscns', 'CRITERIA_FAILED', 1, 1, 1, 1, '2025-10-08 13:36:13', '2025-10-08 14:07:14'),
(5, 'RB4', 1, 'nivin', 'hzxcbhsj', '+91', '8943928627', 1, 'qwerty@gmai.com', '', 'female', NULL, '', '', NULL, NULL, NULL, 'Sachin', '08943928625', 0, NULL, 'MIN_AGE_FAILED', 0, 2, 1, 1, '2025-10-08 13:36:42', '2025-10-08 14:07:14'),
(6, 'RB4', 1, 'nivin', 'hzxcbhsj', '+91', '8943928627', 1, 'qwerty@gmai.com', '', 'female', NULL, '', '', NULL, NULL, NULL, 'Sachin', '08943928625', 0, NULL, 'MIN_AGE_FAILED', 0, 2, 1, 1, '2025-10-08 13:37:54', '2025-10-08 14:07:14'),
(7, 'RB4', 1, 'sanoop', 'M', '+91', '7875555585', 1, 'sachinmohanfff@gmail.com', '', 'male', NULL, '', '', NULL, NULL, NULL, 'Sachin', '08943928625', 0, NULL, 'GENDER_MISMATCH,MIN_AGE_FAILED', 0, 3, 1, 1, '2025-10-08 13:37:54', '2025-10-08 14:07:14'),
(8, 'RB5', 1, 'manish', 'M', '+91', '8498948989', 52, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'male', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, 'GENDER_MISMATCH,MAX_AGE_FAILED', 1, 1, 1, 1, '2025-10-08 13:40:57', '2025-10-08 13:47:49'),
(9, 'RB5', 1, 'manish', 'M', '+91', '8498948989', 52, 'sachinmohanfff@gmail.com', '', 'male', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, 'GENDER_MISMATCH,MAX_AGE_FAILED', 0, 2, 1, 1, '2025-10-08 13:40:57', '2025-10-08 13:47:49'),
(10, 'RB6', 1, 'dsjjkds', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', 'yes', 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, 'cjhcnjs', NULL, 1, 1, 1, 1, '2025-10-10 06:28:41', '2025-10-10 06:28:41'),
(11, 'RB7', 1, 'Sachin', 'M 2', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 2, 'bjzbvhjdfv', NULL, 1, 1, 1, 1, '2025-10-10 08:44:30', '2025-10-12 13:30:30'),
(12, 'RB8', 1, 'one', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, 'hxgchschbds', NULL, 1, 1, 1, 1, '2025-10-10 08:51:28', '2025-10-10 08:51:28'),
(13, 'RB9', 1, 'Sachin', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, 'RECURRENT_BOOKING', 1, 1, 1, 1, '2025-10-10 09:22:17', '2025-10-10 10:08:30'),
(14, 'RB9', 1, 'Sachin', 'M', '+91', '8498948989', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, 'RECURRENT_BOOKING', 1, 2, 1, 1, '2025-10-10 09:29:14', '2025-10-10 10:09:06'),
(15, 'RB10', 1, 'Sachin', 'addsad', '+91', '8943928628', 45, 'sachinmohanfff@gmail.com', 'asdfg', 'male', NULL, 'kalpetta', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, 'GENDER_MISMATCH,MAX_AGE_FAILED', 1, 1, 1, 1, '2025-10-10 09:37:13', '2025-10-10 09:37:40'),
(16, 'RB10', 1, 'Sachin', 'M', '+91', '8498948989', 87, 'sachinmohanfff@gmail.com', '', 'male', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, 'GENDER_MISMATCH,MAX_AGE_FAILED,RECURRENT_BOOKING', 1, 2, 1, 1, '2025-10-10 09:37:40', '2025-10-10 09:37:40'),
(17, 'RB11', 1, 'Sachin', 'M', '+91', '2344233243', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 2, 'fdvdfg', 'CRITERIA_FAILED', 1, 1, 1, 1, '2025-10-10 09:38:30', '2025-10-10 09:46:45'),
(18, 'RB11', 1, 'Sachin', 'M', '+91', '9876543230', 34, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'male', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, 'fdvdfg', 'CRITERIA_FAILED', 1, 2, 1, 1, '2025-10-10 09:38:48', '2025-10-10 09:46:45'),
(19, 'RB11', 1, 'Sachin', 'M', '+91', '9876543240', 78, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'male', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, 'fdvdfg', 'CRITERIA_FAILED', 1, 3, 1, 1, '2025-10-10 09:46:45', '2025-10-10 09:46:45'),
(20, 'RB12', 1, 'Sachin', 'M', '+91', '8943928628', 14, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, 'jnfsij', 'RECURRENT_BOOKING', 1, 1, 1, 1, '2025-10-10 10:12:04', '2025-10-10 10:12:04'),
(21, 'RB13', 1, 'Sachin', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, 'RECURRENT_BOOKING', 1, 1, 1, 1, '2025-10-10 10:48:13', '2025-10-10 10:48:13'),
(22, 'RB13', 1, 'Sachin sdcbjh', 'M', '+91', '8498948989', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 2, 1, 1, '2025-10-10 10:48:13', '2025-10-10 10:49:11'),
(23, 'RB14', 1, 'Sachin', 'M', '+91', '2344233243', 12, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 2, 'fdncxdk', 'RECURRENT_BOOKING', 1, 1, 1, 1, '2025-10-10 10:50:50', '2025-10-10 11:08:37'),
(24, 'RB14', 1, 'Sachin mmmm', 'M', '+91', '8498948989', 16, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, 'fdncxdk', NULL, 1, 2, 1, 1, '2025-10-10 10:50:50', '2025-10-10 11:09:17'),
(25, 'RB14', 1, 'Sachinm vnhjsdvbhjdsn', 'M', '+91', '9876543240', 18, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, 'fdncxdk', NULL, 1, 3, 1, 1, '2025-10-10 10:50:50', '2025-10-10 11:09:17'),
(26, 'RB15', 1, 'Sachin', 'Mjcvnhds', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 2, NULL, NULL, 1, 1, 1, 1, '2025-10-10 11:54:36', '2025-10-10 11:55:36'),
(27, 'RB15', 1, 'Sachin', 'M n', '+91', '8498948989', 14, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 2, 1, 1, '2025-10-10 11:54:36', '2025-10-10 11:55:36'),
(28, 'RB15', 1, 'Sachin', 'M n', '+91', '8943928628', 14, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 3, 1, 1, '2025-10-10 11:55:36', '2025-10-10 11:55:36'),
(29, 'RB7', 1, 'Sachin', 'M22', '+91', '7878787878', 18, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, 'bjzbvhjdfv', NULL, 1, 2, 1, 1, '2025-10-12 13:29:42', '2025-10-12 13:30:30'),
(30, 'RB7', 1, 'sachin', 'M 23', '+91', '9876543240', 17, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, 'bjzbvhjdfv', NULL, 1, 3, 1, 1, '2025-10-12 13:30:30', '2025-10-12 13:30:30'),
(31, 'RB8', 1, 'Maria', 'Sharapova', '+91', '9876543220', 18, 'sachinmohanfff@gmail.com', '456 Oak Avenue, Uptown', 'female', NULL, 'Pune', 'Maharashtra', 'Pune Diocese', 'Sacred Heart Church', NULL, 'Robert Smith', '9876543221', 2, 'Primary participant - Group leader', NULL, 1, 1, NULL, NULL, '2025-10-12 13:58:57', '2025-10-12 13:58:57'),
(32, 'RB8', 1, 'Anna', 'Johnson', '+91', '9876543230', 15, 'anna.johnson@example.com', '789 Pine Street, Midtown', 'female', NULL, 'Pune', 'Maharashtra', 'Pune Diocese', 'Sacred Heart Church', NULL, 'Mary Smith', '9876543220', 2, 'Friend of Mary Smith', NULL, 1, 2, NULL, NULL, '2025-10-12 13:58:57', '2025-10-12 13:58:57'),
(33, 'RB8', 1, 'Sarah', 'Williams', '+91', '9876543240', 12, 'sarah.williams@example.com', '321 Elm Drive, Southside', 'female', NULL, 'Pune', 'Maharashtra', 'Pune Diocese', 'Sacred Heart Church', NULL, 'Mary Smith', '9876543220', 2, 'Part of church group', NULL, 1, 3, NULL, NULL, '2025-10-12 13:58:57', '2025-10-12 13:58:57'),
(34, 'RB16', 1, 'Maria', 'Sharapovaa', '+91', '9876543220', 18, 'sachinmohanfff@gmail.com', '456 Oak Avenue, Uptown', 'female', 'yes', 'Pune', 'Maharashtra', 'Pune Diocese', 'Sacred Heart Church', NULL, 'Robert Smith', '9876543221', 2, 'Primary participant - Group leader', NULL, 1, 1, NULL, NULL, '2025-10-12 15:23:33', '2025-10-12 15:23:33'),
(35, 'RB16', 1, 'Anna', 'Johnsonn', '+91', '9876543230', 15, 'anna.johnson@example.com', '789 Pine Street, Midtown', 'female', NULL, 'Pune', 'Maharashtra', 'Pune Diocese', 'Sacred Heart Church', NULL, 'Mary Smith', '9876543220', 2, 'Friend of Mary Smith', NULL, 1, 2, NULL, NULL, '2025-10-12 15:23:33', '2025-10-12 15:23:33'),
(36, 'RB16', 1, 'Sarah', 'Williamss', '+91', '9876543240', 12, 'sarah.williams@example.com', '321 Elm Drive, Southside', 'female', 'yes', 'Pune', 'Maharashtra', 'Pune Diocese', 'Sacred Heart Church', NULL, 'Mary Smith', '9876543220', 2, 'Part of church group', NULL, 1, 3, NULL, NULL, '2025-10-12 15:23:33', '2025-10-12 15:23:33'),
(37, 'RB17', 1, 'John', 'Doe', '+91', '9876543210', 15, 'john.doe@example.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', 'Jesuits', 'Jane Doe', '9876543211', 2, 'Primary participant, vegetarian meals', NULL, 1, 1, 1, 1, '2025-10-13 04:42:17', '2025-10-13 04:42:17'),
(38, 'RB17', 1, 'Jane', 'Doe', '+91', '9876543211', 12, 'jane.doe@example.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Wife of primary participant', NULL, 1, 2, 1, 1, '2025-10-13 04:42:18', '2025-10-13 04:42:18'),
(39, 'RB18', 1, 'Mary', 'Smith', '+91', '9876543212', 18, 'mary.smith@example.com', '456 Church Lane', 'female', 'no', 'Delhi', 'Delhi', 'Archdiocese of Delhi', 'St. Joseph\'s Cathedral', NULL, 'Robert Smith', '9876543213', 0, 'Individual booking', NULL, 1, 1, 1, 1, '2025-10-13 04:42:18', '2025-10-13 04:42:18'),
(40, 'RB19', 1, 'sachin focus', 'jchvnjsnv', '+91', '8943928925', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 1, 1, 1, '2025-10-14 04:36:49', '2025-10-14 04:36:49'),
(41, 'RB19', 1, 'nithin focus', 'jndsfj', '+91', '8498948989', 19, 'sachinmohanfff@gmail.com', '', 'female', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 2, 1, 1, '2025-10-14 04:36:49', '2025-10-14 04:36:49'),
(42, 'RB20', 1, 'sachin test 1', 'ds', '+91', '8943928925', 14, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 1, 1, 1, '2025-10-14 04:49:58', '2025-10-14 04:49:58'),
(43, 'RB20', 1, 'nithin test 1', 'kjcxvmlkfd', '+91', '9876543230', 15, 'sachinmohanfff@gmail.com', '', 'female', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 2, 1, 1, '2025-10-14 04:49:58', '2025-10-14 04:49:58'),
(44, 'RB21', 1, 'sachin special', 'cdscd', '+91', '8943928628', 14, 'asdf@fg.com', 'South Kalamassery', 'male', NULL, 'Paravur', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, 'GENDER_MISMATCH', 1, 1, 1, 1, '2025-10-14 04:56:02', '2025-10-14 04:56:02'),
(45, 'RB21', 1, 'nithin speacvil', 'M', '+91', '8498948989', 24, 'sachinmohanfff@gmail.com', '', 'male', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, 'GENDER_MISMATCH,MAX_AGE_FAILED', 1, 2, 1, 1, '2025-10-14 04:56:02', '2025-10-14 04:56:02'),
(46, 'RB22', 1, 'Sachin test2', 'M', '+91', '2344233243', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 1, 1, 1, '2025-10-14 05:01:54', '2025-10-14 05:01:54'),
(47, 'RB22', 1, 'nithin tyest 2', 'M', '+91', '8498948989', 15, 'sachinmohanfff@gmail.com', '', 'female', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 2, 1, 1, '2025-10-14 05:01:54', '2025-10-14 05:01:54'),
(48, 'RB23', 1, 'Sachin speaicl 2', 'M', '+91', '8943928628', 45, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, 'jnhsiu', 'MAX_AGE_FAILED', 1, 1, 1, 1, '2025-10-14 05:03:24', '2025-10-14 05:03:24'),
(49, 'RB23', 1, 'nithin special 2', 'Man jxfjdf', '+91', '8498948989', 65, 'sachinmohanfff@gmail.com', '', 'male', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, 'GENDER_MISMATCH,MAX_AGE_FAILED', 1, 2, 1, 1, '2025-10-14 05:03:24', '2025-10-14 05:03:24'),
(50, 'RB24', 1, 'Sachin job', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 1, 1, 1, '2025-10-15 04:53:31', '2025-10-15 04:53:31'),
(51, 'RB24', 1, 'Sachin job 2', 'M', '+91', '8498948989', 15, 'sachinmohanfff@gmail.com', '', 'female', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 2, 1, 1, '2025-10-15 04:53:31', '2025-10-15 04:53:31'),
(52, 'RB25', 1, 'sachin job 3', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 1, 1, 1, '2025-10-15 04:55:22', '2025-10-15 04:55:22'),
(53, 'RB26', 1, 'Sachin job 4', 'M', '+91', '8943928628', 25, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'male', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, 'GENDER_MISMATCH,MAX_AGE_FAILED', 1, 1, 1, 1, '2025-10-15 04:56:30', '2025-10-15 04:56:30'),
(54, 'RB27', 1, 'John job 1', 'Doe', '+91', '9876543210', 16, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', 'Jesuits', 'Jane Doe', '9876543211', 2, 'Primary participant, vegetarian meals', NULL, 1, 1, 1, 1, '2025-10-15 05:01:23', '2025-10-15 05:01:23'),
(55, 'RB27', 1, 'Jane job 2', 'Doe', '+91', '9876543211', 17, 'jane.doe@example.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Wife of primary participant', NULL, 1, 2, 1, 1, '2025-10-15 05:01:23', '2025-10-15 05:01:23'),
(56, 'RB28', 1, 'Mary job 4', 'Smith', '+91', '9876543212', 18, 'mary.smith@example.com', '456 Church Lane', 'female', 'no', 'Delhi', 'Delhi', 'Archdiocese of Delhi', 'St. Joseph\'s Cathedral', NULL, 'Robert Smith', '9876543213', 0, 'Individual booking', NULL, 1, 1, 1, 1, '2025-10-15 05:01:23', '2025-10-15 05:01:23'),
(57, 'RB29', 1, 'John job 11', 'Doe', '+91', '9876543210', 16, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', 'Jesuits', 'Jane Doe', '9876543211', 2, 'Primary participant, vegetarian meals', NULL, 1, 1, 1, 1, '2025-10-15 05:04:19', '2025-10-15 05:04:19'),
(58, 'RB29', 1, 'Jane job 22', 'Doe', '+91', '9876543211', 17, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Wife of primary participant', NULL, 1, 2, 1, 1, '2025-10-15 05:04:19', '2025-10-15 05:04:19'),
(59, 'RB30', 1, 'Mary job 44', 'Smith', '+91', '9876543212', 18, 'sachinmohanfff@gmail.com', '456 Church Lane', 'female', 'no', 'Delhi', 'Delhi', 'Archdiocese of Delhi', 'St. Joseph\'s Cathedral', NULL, 'Robert Smith', '9876543213', 0, 'Individual booking', NULL, 1, 1, 1, 1, '2025-10-15 05:04:19', '2025-10-15 05:04:19'),
(60, 'RB31', 1, 'John job 111', 'Doe', '+91', '9876543210', 16, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', 'Jesuits', 'Jane Doe', '9876543211', 2, 'Primary participant, vegetarian meals', NULL, 1, 1, 1, 1, '2025-10-15 05:14:15', '2025-10-15 05:14:15'),
(61, 'RB31', 1, 'Jane job 222', 'Doe', '+91', '9876543211', 17, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Wife of primary participant', NULL, 1, 2, 1, 1, '2025-10-15 05:14:15', '2025-10-15 05:14:15'),
(62, 'RB32', 1, 'Mary job 444', 'Smith', '+91', '9876543212', 18, 'sachinmohanfff@gmail.com', '456 Church Lane', 'female', 'no', 'Delhi', 'Delhi', 'Archdiocese of Delhi', 'St. Joseph\'s Cathedral', NULL, 'Robert Smith', '9876543213', 0, 'Individual booking', NULL, 1, 1, 1, 1, '2025-10-15 05:14:15', '2025-10-15 05:14:15'),
(63, 'RB33', 1, 'John job 1111', 'Doe', '+91', '9876543210', 16, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', 'Jesuits', 'Jane Doe', '9876543211', 2, 'Primary participant, vegetarian meals', NULL, 1, 1, 1, 1, '2025-10-15 05:18:27', '2025-10-15 05:18:27'),
(64, 'RB33', 1, 'Jane job 2221', 'Doe', '+91', '9876543211', 17, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Wife of primary participant', NULL, 1, 2, 1, 1, '2025-10-15 05:18:27', '2025-10-15 05:18:27'),
(65, 'RB34', 1, 'Mary job 4441', 'Smith', '+91', '9876543212', 18, 'sachinmohanfff@gmail.com', '456 Church Lane', 'female', 'no', 'Delhi', 'Delhi', 'Archdiocese of Delhi', 'St. Joseph\'s Cathedral', NULL, 'Robert Smith', '9876543213', 0, 'Individual booking', NULL, 1, 1, 1, 1, '2025-10-15 05:18:27', '2025-10-15 05:18:27'),
(66, 'RB35', 1, 'John job 1112', 'Doe', '+91', '9876543210', 16, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', 'Jesuits', 'Jane Doe', '9876543211', 2, 'Primary participant, vegetarian meals', NULL, 1, 1, 1, 1, '2025-10-15 05:19:17', '2025-10-15 05:19:17'),
(67, 'RB35', 1, 'Jane job 2222', 'Doe', '+91', '9876543211', 17, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Wife of primary participant', NULL, 1, 2, 1, 1, '2025-10-15 05:19:17', '2025-10-15 05:19:17'),
(68, 'RB36', 1, 'Mary job 4442', 'Smith', '+91', '9876543212', 18, 'sachinmohanfff@gmail.com', '456 Church Lane', 'female', 'no', 'Delhi', 'Delhi', 'Archdiocese of Delhi', 'St. Joseph\'s Cathedral', NULL, 'Robert Smith', '9876543213', 0, 'Individual booking', NULL, 1, 1, 1, 1, '2025-10-15 05:19:17', '2025-10-15 05:19:17'),
(69, 'RB37', 1, 'John job 1113', 'Doe', '+91', '9876543210', 16, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', 'Jesuits', 'Jane Doe', '9876543211', 2, 'Primary participant, vegetarian meals', NULL, 0, 1, 1, NULL, '2025-10-15 05:21:34', '2025-10-26 08:06:14'),
(70, 'RB37', 1, 'Jane job 2223', 'Doe', '+91', '9876543211', 17, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Wife of primary participant', NULL, 0, 2, 1, NULL, '2025-10-15 05:21:34', '2025-10-26 08:06:14'),
(71, 'RB37', 1, 'Bobby job 3333', 'Doe', '+91', '7634656566', 12, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'no', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Son, minor - no email/phone required', NULL, 0, 3, 1, NULL, '2025-10-15 05:21:34', '2025-10-26 08:06:14'),
(72, 'RB38', 1, 'Mary job 4443', 'Smith', '+91', '9876543212', 18, 'sachinmohanfff@gmail.com', '456 Church Lane', 'female', 'no', 'Delhi', 'Delhi', 'Archdiocese of Delhi', 'St. Joseph\'s Cathedral', NULL, 'Robert Smith', '9876543213', 0, 'Individual booking', NULL, 1, 1, 1, 1, '2025-10-15 05:21:34', '2025-10-15 05:21:34'),
(73, 'RB39', 1, 'John job 1114', 'Doe', '+91', '9876543210', 16, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', 'Jesuits', 'Jane Doe', '9876543211', 1, 'Primary participant, vegetarian meals', NULL, 1, 1, 1, 1, '2025-10-15 05:32:18', '2025-10-26 08:04:50'),
(74, 'RB39', 1, 'Jane job 2224', 'Doe', '+91', '3344444444', 17, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Wife of primary participant', NULL, 0, 2, 1, NULL, '2025-10-15 05:32:18', '2025-10-26 08:04:50'),
(75, 'RB39', 1, 'Bobby job 3334', 'Doe', '+91', '7634656566', 12, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'no', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Son, minor - no email/phone required', NULL, 1, 3, 1, 1, '2025-10-15 05:32:18', '2025-10-15 05:32:18'),
(76, 'RB40', 1, 'Mary job 4444', 'Smith', '+91', '9876543212', 18, 'sachinmohanfff@gmail.com', '456 Church Lane', 'female', 'no', 'Delhi', 'Delhi', 'Archdiocese of Delhi', 'St. Joseph\'s Cathedral', NULL, 'Robert Smith', '9876543213', 0, 'Individual booking', NULL, 1, 1, 1, 1, '2025-10-15 05:32:18', '2025-10-15 05:32:18'),
(77, 'RB41', 1, 'John job a', 'Doe', '+91', '9876543210', 16, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', 'Jesuits', 'Jane Doe', '9876543211', 2, 'Primary participant, vegetarian meals', NULL, 1, 1, 1, 1, '2025-10-15 11:06:29', '2025-11-09 05:08:51'),
(78, 'RB41', 1, 'Jane job a', 'Doe', '+91', '3344444444', 17, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Wife of primary participant', NULL, 1, 2, 1, 1, '2025-10-15 11:06:29', '2025-11-09 05:08:51'),
(79, 'RB41', 1, 'Bobby job a', 'Doe', '+91', '7634656566', 12, 'sachinmohanfff@gmail.com', '123 Main Street, Apartment 4B', 'female', 'no', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Son, minor - no email/phone required', NULL, 1, 3, 1, 1, '2025-10-15 11:06:29', '2025-11-09 05:08:51'),
(80, 'RB42', 1, 'Mary job a', 'Smith', '+91', '9876543212', 18, 'sachinmohanfff@gmail.com', '456 Church Lane', 'female', 'no', 'Delhi', 'Delhi', 'Archdiocese of Delhi', 'St. Joseph\'s Cathedral', NULL, 'Robert Smith', '9876543213', 0, 'Individual booking', NULL, 1, 1, 1, 1, '2025-10-15 11:06:32', '2025-10-15 11:06:32'),
(81, 'RB43', 1, 'Sachin njsdfijndfsi', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 1, 1, 1, '2025-10-16 04:39:17', '2025-10-16 04:39:17'),
(82, 'RB44', 1, 'Sachin test m1', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 2, NULL, NULL, 1, 1, 1, 1, '2025-10-16 04:43:33', '2025-10-16 04:43:33'),
(83, 'RB44', 1, 'Sachin m2', 'M', '+91', '8498948989', 16, 'sachinmohanfff@gmail.com', '', 'female', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 2, 1, 1, '2025-10-16 04:43:33', '2025-10-16 04:43:33'),
(84, 'RB44', 1, 'Sachin m3', 'M', '+91', '7878787878', 15, 'sachinmohanfff@gmail.com', '', 'female', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 0, 3, 1, 1, '2025-10-16 04:43:33', '2025-10-16 05:19:22'),
(85, 'RB45', 1, 'Sachin s1', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 1, 1, 1, '2025-10-16 04:51:45', '2025-10-16 04:51:45'),
(86, 'RB46', 1, 'Sachin s2', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 0, 1, 1, 1, '2025-10-16 04:58:06', '2025-10-17 05:08:47'),
(87, 'RB47', 1, 'Sachin s3', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 0, 1, 1, 1, '2025-10-16 04:59:57', '2025-10-17 05:06:50'),
(88, 'RB48', 1, 'Sachin s4', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 0, 1, 1, 1, '2025-10-16 05:01:58', '2025-10-17 05:03:44'),
(89, 'RB49', 1, 'Sachin s5', 'M', '+91', '8943928628', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 0, 1, 1, 1, '2025-10-16 05:03:20', '2025-10-17 04:58:58'),
(90, 'RB50', 1, 'Sachin s1', 'M m', '+91', '8943928628', 20, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 1, NULL, NULL, '2025-10-25 18:27:29', '2025-10-25 18:27:29'),
(91, 'RB51', 1, 'Sachin', 'M hd', '+91', '8498948989', 19, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 1, NULL, NULL, '2025-10-25 18:34:21', '2025-10-25 18:34:21'),
(92, 'RB51', 1, 'Sachin', 'M jhhuj', '+91', '7878787878', 18, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 2, NULL, NULL, '2025-10-25 18:34:21', '2025-10-25 18:34:21'),
(93, 'RB52', 1, 'Sachin vdbvhdbfhd', 'M', '+91', '8943928625', 19, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', 'yes', 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 2, NULL, NULL, 1, 1, NULL, 1, '2025-10-26 14:59:59', '2025-10-29 18:38:11'),
(94, 'RB52', 1, 'Sachinv fd', 'M', '+91', '4343243242', 18, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 2, NULL, NULL, 1, 2, NULL, 1, '2025-10-26 15:00:00', '2025-10-29 18:38:11'),
(95, 'RB52', 1, 'Sachin vfgfgf', 'M', '+91', '7875555585', 18, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 2, NULL, NULL, 1, 3, NULL, 1, '2025-10-26 15:00:00', '2025-10-29 18:38:11'),
(96, 'RB53', 1, 'Sachin new 1', 'M', '+91', '8943928625', 19, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 1, NULL, NULL, '2025-11-05 06:39:53', '2025-11-05 06:39:53'),
(97, 'RB54', 1, 'Sachin new 2', 'M', '+91', '8943928625', 16, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 1, NULL, NULL, '2025-11-05 06:43:25', '2025-11-05 06:43:25'),
(98, 'RB55', 1, 'Sachin watsapp 1', 'M', '+61', '894382682555524', 19, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 1, NULL, NULL, '2025-11-05 17:19:10', '2025-11-05 17:19:10'),
(99, 'RB55', 1, 'Sachin wataps test 2', 'M', '+64', '65546654654546', 18, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 2, NULL, NULL, '2025-11-05 17:19:11', '2025-11-05 17:19:11'),
(100, 'RB56', 1, 'Sachin watap 3', 'M', '+91', '89439286286565', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 2, NULL, NULL, 1, 1, 1, 1, '2025-11-05 18:02:18', '2025-11-09 05:09:47'),
(101, 'RB56', 1, 'Sachin watap 4', 'M', '+91', '894382682555524', 16, 'sachinmohanfff@gmail.com', '', 'female', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 2, 1, 1, '2025-11-05 18:02:18', '2025-11-09 05:09:47'),
(102, 'RB56', 1, 'Sachin watap 5', 'M', '+91', '78787878787', 18, 'sachinmohanfff@gmail.com', '', 'female', NULL, '', '', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 3, 1, 1, '2025-11-05 18:02:18', '2025-11-09 05:09:47'),
(103, 'RB57', 1, 'Sachin watap 6', 'M', '+91', '89448954654', 28, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, 'MAX_AGE_FAILED', 1, 1, 1, 1, '2025-11-05 18:03:25', '2025-11-05 18:03:25'),
(104, 'RB58', 1, 'Sachin watapp 8', 'M', '+91', '784578457458', 18, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 1, 1, 1, '2025-11-05 18:05:11', '2025-11-05 18:05:11'),
(105, 'RB59', 1, 'Sachin watsap 9 test', 'M test', '+995', '897895665464444', 18, 'sachinmohanfff@gmail.comm', 'Devikripa,Sreepuram,Chundale Poo', 'female', 'no', 'Chundale', 'Kerala', 'saas', 'as', 'assa', 'Sachin Mohan M', '08943928625', 1, 'hubvdshsdh', NULL, 1, 1, 1, 1, '2025-11-05 18:11:58', '2025-11-05 18:38:11'),
(106, 'RB59', 1, 'Sachin watsapp 100', 'M2', '+33', '894879516843333', 17, 'sachinmohanfff@gmail.com1m', 'Devikripa,Sreepuram,Chundale Poo', 'female', NULL, 'Chundale', 'Kerala', 'saas', 'as', NULL, 'Sachin Mohan M', '08943928625', 0, 'hubvdshsdh', NULL, 1, 2, 1, 1, '2025-11-05 18:11:58', '2025-11-05 18:42:48'),
(107, 'RB60', 1, 'Sachin  ijhjiji', 'M', '+91', '2344233243', 15, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 0, NULL, NULL, 1, 1, 1, 1, '2025-11-05 19:03:29', '2025-11-05 19:03:29'),
(108, 'RB61', 1, 'John s', 'Doe', '+91', '9876543210', 15, 'john.doe@example.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', 'Jesuits', 'Jane Doe', '9876543211', 1, 'Primary participant, vegetarian meals', NULL, 1, 1, 1, 1, '2025-11-09 14:48:28', '2025-11-09 14:48:28'),
(109, 'RB61', 1, 'Jane s', 'Doe', '+93', '9876543211', 18, 'jane.doe@example.com', '123 Main Street, Apartment 4B', 'female', 'yes', 'Mumbai', 'Maharashtra', 'Archdiocese of Bombay', 'St. Mary\'s Church', NULL, 'John Doe', '9876543210', 0, 'Wife of primary participant', NULL, 1, 2, 1, 1, '2025-11-09 14:48:28', '2025-11-09 14:48:28'),
(110, 'RB62', 1, 'Mary s', 'Smith', '+355', '9876543212', 18, 'mary.smith@example.com', '456 Church Lane', 'female', 'no', 'Delhi', 'Delhi', 'Archdiocese of Delhi', 'St. Joseph\'s Cathedral', NULL, 'Robert Smith', '9876543213', 0, 'Individual booking', NULL, 1, 1, 1, 1, '2025-11-09 14:48:28', '2025-11-09 14:48:28'),
(111, 'RB63', 1, 'aruna 1', 'hjn', '+91', '8498948989', 19, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 1, NULL, NULL, '2025-11-18 08:47:20', '2025-11-18 08:47:20'),
(112, 'RB63', 1, 'arunq 2', 'fgyh', '+91', '9876543220', 20, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, NULL, 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 2, NULL, NULL, '2025-11-18 08:47:20', '2025-11-18 08:47:20'),
(113, 'RB64', 4, 'nithya 1', 'M', '+91', '8498948989', 25, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, 'vbvbvbvbv', 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 1, NULL, NULL, '2025-11-18 08:48:37', '2025-11-18 08:48:37'),
(114, 'RB64', 4, 'nithya 2', 'M', '+91', '8498948989', 65, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'female', NULL, 'Chundale', 'Kerala', NULL, NULL, 'yugyg', 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 2, NULL, NULL, '2025-11-18 08:48:37', '2025-11-18 08:48:37'),
(115, 'RB65', 2, 'keerthi 1', 'M', '+91', '8498948989', 65, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'male', NULL, 'Chundale', 'Kerala', NULL, NULL, 'rrrrrrrrrr', 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 1, NULL, NULL, '2025-11-18 08:51:04', '2025-11-18 08:51:04'),
(116, 'RB65', 2, 'keerthi 2', 'hjjk', '+91', '8498948989', 56, 'sachinmohanfff@gmail.com', 'Devikripa,Sreepuram,Chundale Po', 'male', NULL, 'Chundale', 'Kerala', NULL, NULL, 'yyyyyyyyyy', 'Sachin Mohan M', '08943928625', 1, NULL, NULL, 1, 2, NULL, NULL, '2025-11-18 08:51:04', '2025-11-18 08:51:04');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_056c7d79-f086-476c-bba6-349a09af5db8', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-13 15:55:02.787027\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-13 15:55:02.787077\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1763030942),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_0b7affef-e7a0-4d9f-8063-6f50b5d1b8a2', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-18 06:43:16.967732\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-18 06:43:29.951237\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:2;s:9:\"last_page\";s:32:\"https://retreatms.local/register\";}', 1763429849),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_0eba6688-dcc2-4436-a1a3-7be03f0b2998', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-13 15:55:02.787063\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-13 15:55:02.787105\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1763030942),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_1c454cbc-8bb4-4975-b702-10de524ea57b', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-18 14:15:59.882196\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-18 14:20:15.495513\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:9;s:9:\"last_page\";s:31:\"http://retreatms.local/register\";}', 1763457255),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_21851563-2b30-4388-b36c-97a89091cdaa', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-09 09:19:52.105709\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-09 09:36:46.472196\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:2;s:9:\"last_page\";s:35:\"http://retreatms.local/check-status\";}', 1762662646),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_44443e89-1c8f-4963-bc8f-8306013cc4a8', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-09 13:24:13.960664\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-09 13:25:07.322808\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:5;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1762676347),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_4d5b02c3-38c5-4c13-9871-83db8085f842', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-08 21:23:47.556413\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-08 21:23:47.556492\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1762618667),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_6c54f38c-e724-4afd-8413-5e7d5dabe3a3', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-13 20:14:08.627326\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-13 20:14:08.627395\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1763046488),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_8059e0ce-b0c5-4605-a8cb-bcb2ad1e5261', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-08 21:23:47.556321\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-08 21:23:47.556402\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1762618667),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_850f02f5-9fa6-4982-9b28-7068f81986e8', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-13 07:47:51.373418\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-13 07:47:51.373459\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1763001711),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_902d23e4-15a7-45f8-903c-87b744bffe3b', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-12 08:44:35.125020\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-12 08:44:35.125054\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1762918715),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_9126750e-6d70-43ad-ab96-81cb5ef1e273', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-13 16:12:24.283461\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-13 16:12:24.283484\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1763031984),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_ab02d994-7172-4968-bfbc-91fadfddcb88', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-08 22:34:25.886098\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-08 22:40:39.198490\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:8;s:9:\"last_page\";s:35:\"http://retreatms.local/check-status\";}', 1762623279),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_ae710689-7231-4049-b21b-efecda7e1d76', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-08 10:43:43.746483\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-08 10:43:43.746530\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1762580263),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_cb601714-ac96-4045-a676-d6eaa1d33c76', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-12 08:44:35.125027\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-12 08:44:35.125062\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:22:\"http://retreatms.local\";}', 1762918715),
('mount-carmel-retreat-centre-cache-frontend_session:frontend_session_e349c4e2-f388-4e68-ad05-051c7e260bac', 'a:6:{s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-09 20:29:46.294037\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"ip_address\";s:9:\"127.0.0.1\";s:10:\"user_agent\";s:111:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36\";s:13:\"last_activity\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-11-09 20:29:46.294065\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:12:\"ASIA/KOLKATA\";}s:10:\"page_views\";i:1;s:9:\"last_page\";s:35:\"http://retreatms.local/check-status\";}', 1762701826),
('mount-carmel-retreat-centre-cache-setting_MAX_ADDITIONAL_MEMBERS', 'i:2;', 1763459159);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_age` int DEFAULT NULL,
  `max_age` int DEFAULT NULL,
  `married` enum('yes') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vocation` enum('priest_only','sisters_only') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`id`, `name`, `gender`, `min_age`, `max_age`, `married`, `vocation`, `status`, `created_at`, `updated_at`) VALUES
(1, 'youth', NULL, 15, 25, NULL, NULL, 1, '2025-10-08 12:23:11', '2025-10-08 12:23:11'),
(2, 'Male', 'male', NULL, NULL, NULL, NULL, 1, '2025-10-08 12:23:40', '2025-10-08 12:23:40'),
(3, 'Female youth edited', 'female', 10, 20, NULL, NULL, 1, '2025-10-08 12:24:10', '2025-10-08 12:25:05'),
(4, 'couples', NULL, NULL, NULL, 'yes', NULL, 1, '2025-10-08 12:24:31', '2025-10-08 12:24:31'),
(5, 'Priest criters', NULL, NULL, NULL, NULL, 'priest_only', 1, '2025-10-08 12:24:49', '2025-10-08 12:24:49'),
(6, 'Test', 'male', 20, 40, 'yes', NULL, 1, '2025-10-08 12:25:25', '2025-10-08 12:25:25'),
(8, 'sisters only', 'female', NULL, NULL, NULL, 'sisters_only', 1, '2025-11-18 06:12:11', '2025-11-18 06:12:11');

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
-- Table structure for table `jobs`
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

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(151, 'default', '{\"uuid\":\"377076b1-f95a-46c7-a3ac-3c43ad253744\",\"displayName\":\"App\\\\Jobs\\\\SendBookingConfirmationEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendBookingConfirmationEmail\",\"command\":\"O:37:\\\"App\\\\Jobs\\\\SendBookingConfirmationEmail\\\":3:{s:17:\\\"\\u0000*\\u0000primaryBooking\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Booking\\\";s:2:\\\"id\\\";i:111;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:10:\\\"\\u0000*\\u0000retreat\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Retreat\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:1:{i:0;s:8:\\\"bookings\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:18:\\\"\\u0000*\\u0000allParticipants\\\";O:29:\\\"Illuminate\\\\Support\\\\Collection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:2:{i:0;O:18:\\\"App\\\\Models\\\\Booking\\\":33:{s:13:\\\"\\u0000*\\u0000connection\\\";s:5:\\\"mysql\\\";s:8:\\\"\\u0000*\\u0000table\\\";s:8:\\\"bookings\\\";s:13:\\\"\\u0000*\\u0000primaryKey\\\";s:2:\\\"id\\\";s:10:\\\"\\u0000*\\u0000keyType\\\";s:3:\\\"int\\\";s:12:\\\"incrementing\\\";b:1;s:7:\\\"\\u0000*\\u0000with\\\";a:0:{}s:12:\\\"\\u0000*\\u0000withCount\\\";a:0:{}s:19:\\\"preventsLazyLoading\\\";b:0;s:10:\\\"\\u0000*\\u0000perPage\\\";i:15;s:6:\\\"exists\\\";b:1;s:18:\\\"wasRecentlyCreated\\\";b:1;s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;s:13:\\\"\\u0000*\\u0000attributes\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB63\\\";s:10:\\\"retreat_id\\\";i:1;s:9:\\\"firstname\\\";s:7:\\\"aruna 1\\\";s:8:\\\"lastname\\\";s:3:\\\"hjn\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"8498948989\\\";s:3:\\\"age\\\";s:2:\\\"19\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:6:\\\"female\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";N;s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:1;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:17:20\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:17:20\\\";s:2:\\\"id\\\";i:111;}s:11:\\\"\\u0000*\\u0000original\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB63\\\";s:10:\\\"retreat_id\\\";i:1;s:9:\\\"firstname\\\";s:7:\\\"aruna 1\\\";s:8:\\\"lastname\\\";s:3:\\\"hjn\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"8498948989\\\";s:3:\\\"age\\\";s:2:\\\"19\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:6:\\\"female\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";N;s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:1;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:17:20\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:17:20\\\";s:2:\\\"id\\\";i:111;}s:10:\\\"\\u0000*\\u0000changes\\\";a:0:{}s:11:\\\"\\u0000*\\u0000previous\\\";a:0:{}s:8:\\\"\\u0000*\\u0000casts\\\";a:4:{s:3:\\\"age\\\";s:7:\\\"integer\\\";s:23:\\\"additional_participants\\\";s:7:\\\"integer\\\";s:18:\\\"participant_number\\\";s:7:\\\"integer\\\";s:9:\\\"is_active\\\";s:7:\\\"boolean\\\";}s:17:\\\"\\u0000*\\u0000classCastCache\\\";a:0:{}s:21:\\\"\\u0000*\\u0000attributeCastCache\\\";a:0:{}s:13:\\\"\\u0000*\\u0000dateFormat\\\";N;s:10:\\\"\\u0000*\\u0000appends\\\";a:0:{}s:19:\\\"\\u0000*\\u0000dispatchesEvents\\\";a:0:{}s:14:\\\"\\u0000*\\u0000observables\\\";a:0:{}s:12:\\\"\\u0000*\\u0000relations\\\";a:0:{}s:10:\\\"\\u0000*\\u0000touches\\\";a:0:{}s:27:\\\"\\u0000*\\u0000relationAutoloadCallback\\\";N;s:26:\\\"\\u0000*\\u0000relationAutoloadContext\\\";N;s:10:\\\"timestamps\\\";b:1;s:13:\\\"usesUniqueIds\\\";b:0;s:9:\\\"\\u0000*\\u0000hidden\\\";a:0:{}s:10:\\\"\\u0000*\\u0000visible\\\";a:0:{}s:11:\\\"\\u0000*\\u0000fillable\\\";a:25:{i:0;s:10:\\\"booking_id\\\";i:1;s:10:\\\"retreat_id\\\";i:2;s:9:\\\"firstname\\\";i:3;s:8:\\\"lastname\\\";i:4;s:12:\\\"country_code\\\";i:5;s:15:\\\"whatsapp_number\\\";i:6;s:3:\\\"age\\\";i:7;s:5:\\\"email\\\";i:8;s:7:\\\"address\\\";i:9;s:6:\\\"gender\\\";i:10;s:7:\\\"married\\\";i:11;s:4:\\\"city\\\";i:12;s:5:\\\"state\\\";i:13;s:7:\\\"diocese\\\";i:14;s:6:\\\"parish\\\";i:15;s:12:\\\"congregation\\\";i:16;s:22:\\\"emergency_contact_name\\\";i:17;s:23:\\\"emergency_contact_phone\\\";i:18;s:23:\\\"additional_participants\\\";i:19;s:15:\\\"special_remarks\\\";i:20;s:4:\\\"flag\\\";i:21;s:18:\\\"participant_number\\\";i:22;s:10:\\\"created_by\\\";i:23;s:10:\\\"updated_by\\\";i:24;s:9:\\\"is_active\\\";}s:10:\\\"\\u0000*\\u0000guarded\\\";a:1:{i:0;s:1:\\\"*\\\";}}i:1;O:18:\\\"App\\\\Models\\\\Booking\\\":33:{s:13:\\\"\\u0000*\\u0000connection\\\";s:5:\\\"mysql\\\";s:8:\\\"\\u0000*\\u0000table\\\";s:8:\\\"bookings\\\";s:13:\\\"\\u0000*\\u0000primaryKey\\\";s:2:\\\"id\\\";s:10:\\\"\\u0000*\\u0000keyType\\\";s:3:\\\"int\\\";s:12:\\\"incrementing\\\";b:1;s:7:\\\"\\u0000*\\u0000with\\\";a:0:{}s:12:\\\"\\u0000*\\u0000withCount\\\";a:0:{}s:19:\\\"preventsLazyLoading\\\";b:0;s:10:\\\"\\u0000*\\u0000perPage\\\";i:15;s:6:\\\"exists\\\";b:1;s:18:\\\"wasRecentlyCreated\\\";b:1;s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;s:13:\\\"\\u0000*\\u0000attributes\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB63\\\";s:10:\\\"retreat_id\\\";i:1;s:9:\\\"firstname\\\";s:7:\\\"arunq 2\\\";s:8:\\\"lastname\\\";s:4:\\\"fgyh\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"9876543220\\\";s:3:\\\"age\\\";s:2:\\\"20\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:6:\\\"female\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";N;s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:2;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:17:20\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:17:20\\\";s:2:\\\"id\\\";i:112;}s:11:\\\"\\u0000*\\u0000original\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB63\\\";s:10:\\\"retreat_id\\\";i:1;s:9:\\\"firstname\\\";s:7:\\\"arunq 2\\\";s:8:\\\"lastname\\\";s:4:\\\"fgyh\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"9876543220\\\";s:3:\\\"age\\\";s:2:\\\"20\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:6:\\\"female\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";N;s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:2;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:17:20\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:17:20\\\";s:2:\\\"id\\\";i:112;}s:10:\\\"\\u0000*\\u0000changes\\\";a:0:{}s:11:\\\"\\u0000*\\u0000previous\\\";a:0:{}s:8:\\\"\\u0000*\\u0000casts\\\";a:4:{s:3:\\\"age\\\";s:7:\\\"integer\\\";s:23:\\\"additional_participants\\\";s:7:\\\"integer\\\";s:18:\\\"participant_number\\\";s:7:\\\"integer\\\";s:9:\\\"is_active\\\";s:7:\\\"boolean\\\";}s:17:\\\"\\u0000*\\u0000classCastCache\\\";a:0:{}s:21:\\\"\\u0000*\\u0000attributeCastCache\\\";a:0:{}s:13:\\\"\\u0000*\\u0000dateFormat\\\";N;s:10:\\\"\\u0000*\\u0000appends\\\";a:0:{}s:19:\\\"\\u0000*\\u0000dispatchesEvents\\\";a:0:{}s:14:\\\"\\u0000*\\u0000observables\\\";a:0:{}s:12:\\\"\\u0000*\\u0000relations\\\";a:0:{}s:10:\\\"\\u0000*\\u0000touches\\\";a:0:{}s:27:\\\"\\u0000*\\u0000relationAutoloadCallback\\\";N;s:26:\\\"\\u0000*\\u0000relationAutoloadContext\\\";N;s:10:\\\"timestamps\\\";b:1;s:13:\\\"usesUniqueIds\\\";b:0;s:9:\\\"\\u0000*\\u0000hidden\\\";a:0:{}s:10:\\\"\\u0000*\\u0000visible\\\";a:0:{}s:11:\\\"\\u0000*\\u0000fillable\\\";a:25:{i:0;s:10:\\\"booking_id\\\";i:1;s:10:\\\"retreat_id\\\";i:2;s:9:\\\"firstname\\\";i:3;s:8:\\\"lastname\\\";i:4;s:12:\\\"country_code\\\";i:5;s:15:\\\"whatsapp_number\\\";i:6;s:3:\\\"age\\\";i:7;s:5:\\\"email\\\";i:8;s:7:\\\"address\\\";i:9;s:6:\\\"gender\\\";i:10;s:7:\\\"married\\\";i:11;s:4:\\\"city\\\";i:12;s:5:\\\"state\\\";i:13;s:7:\\\"diocese\\\";i:14;s:6:\\\"parish\\\";i:15;s:12:\\\"congregation\\\";i:16;s:22:\\\"emergency_contact_name\\\";i:17;s:23:\\\"emergency_contact_phone\\\";i:18;s:23:\\\"additional_participants\\\";i:19;s:15:\\\"special_remarks\\\";i:20;s:4:\\\"flag\\\";i:21;s:18:\\\"participant_number\\\";i:22;s:10:\\\"created_by\\\";i:23;s:10:\\\"updated_by\\\";i:24;s:9:\\\"is_active\\\";}s:10:\\\"\\u0000*\\u0000guarded\\\";a:1:{i:0;s:1:\\\"*\\\";}}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}}\"},\"createdAt\":1763455641,\"delay\":null}', 0, NULL, 1763455641, 1763455641),
(152, 'default', '{\"uuid\":\"fb1e3ad3-2a6b-45b2-8b20-926ef82f3dc0\",\"displayName\":\"App\\\\Jobs\\\\SendBookingConfirmationEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendBookingConfirmationEmail\",\"command\":\"O:37:\\\"App\\\\Jobs\\\\SendBookingConfirmationEmail\\\":3:{s:17:\\\"\\u0000*\\u0000primaryBooking\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Booking\\\";s:2:\\\"id\\\";i:113;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:10:\\\"\\u0000*\\u0000retreat\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Retreat\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:1:{i:0;s:8:\\\"bookings\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:18:\\\"\\u0000*\\u0000allParticipants\\\";O:29:\\\"Illuminate\\\\Support\\\\Collection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:2:{i:0;O:18:\\\"App\\\\Models\\\\Booking\\\":33:{s:13:\\\"\\u0000*\\u0000connection\\\";s:5:\\\"mysql\\\";s:8:\\\"\\u0000*\\u0000table\\\";s:8:\\\"bookings\\\";s:13:\\\"\\u0000*\\u0000primaryKey\\\";s:2:\\\"id\\\";s:10:\\\"\\u0000*\\u0000keyType\\\";s:3:\\\"int\\\";s:12:\\\"incrementing\\\";b:1;s:7:\\\"\\u0000*\\u0000with\\\";a:0:{}s:12:\\\"\\u0000*\\u0000withCount\\\";a:0:{}s:19:\\\"preventsLazyLoading\\\";b:0;s:10:\\\"\\u0000*\\u0000perPage\\\";i:15;s:6:\\\"exists\\\";b:1;s:18:\\\"wasRecentlyCreated\\\";b:1;s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;s:13:\\\"\\u0000*\\u0000attributes\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB64\\\";s:10:\\\"retreat_id\\\";i:4;s:9:\\\"firstname\\\";s:8:\\\"nithya 1\\\";s:8:\\\"lastname\\\";s:1:\\\"M\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"8498948989\\\";s:3:\\\"age\\\";s:2:\\\"25\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:6:\\\"female\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";s:9:\\\"vbvbvbvbv\\\";s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:1;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:18:37\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:18:37\\\";s:2:\\\"id\\\";i:113;}s:11:\\\"\\u0000*\\u0000original\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB64\\\";s:10:\\\"retreat_id\\\";i:4;s:9:\\\"firstname\\\";s:8:\\\"nithya 1\\\";s:8:\\\"lastname\\\";s:1:\\\"M\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"8498948989\\\";s:3:\\\"age\\\";s:2:\\\"25\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:6:\\\"female\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";s:9:\\\"vbvbvbvbv\\\";s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:1;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:18:37\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:18:37\\\";s:2:\\\"id\\\";i:113;}s:10:\\\"\\u0000*\\u0000changes\\\";a:0:{}s:11:\\\"\\u0000*\\u0000previous\\\";a:0:{}s:8:\\\"\\u0000*\\u0000casts\\\";a:4:{s:3:\\\"age\\\";s:7:\\\"integer\\\";s:23:\\\"additional_participants\\\";s:7:\\\"integer\\\";s:18:\\\"participant_number\\\";s:7:\\\"integer\\\";s:9:\\\"is_active\\\";s:7:\\\"boolean\\\";}s:17:\\\"\\u0000*\\u0000classCastCache\\\";a:0:{}s:21:\\\"\\u0000*\\u0000attributeCastCache\\\";a:0:{}s:13:\\\"\\u0000*\\u0000dateFormat\\\";N;s:10:\\\"\\u0000*\\u0000appends\\\";a:0:{}s:19:\\\"\\u0000*\\u0000dispatchesEvents\\\";a:0:{}s:14:\\\"\\u0000*\\u0000observables\\\";a:0:{}s:12:\\\"\\u0000*\\u0000relations\\\";a:0:{}s:10:\\\"\\u0000*\\u0000touches\\\";a:0:{}s:27:\\\"\\u0000*\\u0000relationAutoloadCallback\\\";N;s:26:\\\"\\u0000*\\u0000relationAutoloadContext\\\";N;s:10:\\\"timestamps\\\";b:1;s:13:\\\"usesUniqueIds\\\";b:0;s:9:\\\"\\u0000*\\u0000hidden\\\";a:0:{}s:10:\\\"\\u0000*\\u0000visible\\\";a:0:{}s:11:\\\"\\u0000*\\u0000fillable\\\";a:25:{i:0;s:10:\\\"booking_id\\\";i:1;s:10:\\\"retreat_id\\\";i:2;s:9:\\\"firstname\\\";i:3;s:8:\\\"lastname\\\";i:4;s:12:\\\"country_code\\\";i:5;s:15:\\\"whatsapp_number\\\";i:6;s:3:\\\"age\\\";i:7;s:5:\\\"email\\\";i:8;s:7:\\\"address\\\";i:9;s:6:\\\"gender\\\";i:10;s:7:\\\"married\\\";i:11;s:4:\\\"city\\\";i:12;s:5:\\\"state\\\";i:13;s:7:\\\"diocese\\\";i:14;s:6:\\\"parish\\\";i:15;s:12:\\\"congregation\\\";i:16;s:22:\\\"emergency_contact_name\\\";i:17;s:23:\\\"emergency_contact_phone\\\";i:18;s:23:\\\"additional_participants\\\";i:19;s:15:\\\"special_remarks\\\";i:20;s:4:\\\"flag\\\";i:21;s:18:\\\"participant_number\\\";i:22;s:10:\\\"created_by\\\";i:23;s:10:\\\"updated_by\\\";i:24;s:9:\\\"is_active\\\";}s:10:\\\"\\u0000*\\u0000guarded\\\";a:1:{i:0;s:1:\\\"*\\\";}}i:1;O:18:\\\"App\\\\Models\\\\Booking\\\":33:{s:13:\\\"\\u0000*\\u0000connection\\\";s:5:\\\"mysql\\\";s:8:\\\"\\u0000*\\u0000table\\\";s:8:\\\"bookings\\\";s:13:\\\"\\u0000*\\u0000primaryKey\\\";s:2:\\\"id\\\";s:10:\\\"\\u0000*\\u0000keyType\\\";s:3:\\\"int\\\";s:12:\\\"incrementing\\\";b:1;s:7:\\\"\\u0000*\\u0000with\\\";a:0:{}s:12:\\\"\\u0000*\\u0000withCount\\\";a:0:{}s:19:\\\"preventsLazyLoading\\\";b:0;s:10:\\\"\\u0000*\\u0000perPage\\\";i:15;s:6:\\\"exists\\\";b:1;s:18:\\\"wasRecentlyCreated\\\";b:1;s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;s:13:\\\"\\u0000*\\u0000attributes\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB64\\\";s:10:\\\"retreat_id\\\";i:4;s:9:\\\"firstname\\\";s:8:\\\"nithya 2\\\";s:8:\\\"lastname\\\";s:1:\\\"M\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"8498948989\\\";s:3:\\\"age\\\";s:2:\\\"65\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:6:\\\"female\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";s:5:\\\"yugyg\\\";s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:2;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:18:37\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:18:37\\\";s:2:\\\"id\\\";i:114;}s:11:\\\"\\u0000*\\u0000original\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB64\\\";s:10:\\\"retreat_id\\\";i:4;s:9:\\\"firstname\\\";s:8:\\\"nithya 2\\\";s:8:\\\"lastname\\\";s:1:\\\"M\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"8498948989\\\";s:3:\\\"age\\\";s:2:\\\"65\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:6:\\\"female\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";s:5:\\\"yugyg\\\";s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:2;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:18:37\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:18:37\\\";s:2:\\\"id\\\";i:114;}s:10:\\\"\\u0000*\\u0000changes\\\";a:0:{}s:11:\\\"\\u0000*\\u0000previous\\\";a:0:{}s:8:\\\"\\u0000*\\u0000casts\\\";a:4:{s:3:\\\"age\\\";s:7:\\\"integer\\\";s:23:\\\"additional_participants\\\";s:7:\\\"integer\\\";s:18:\\\"participant_number\\\";s:7:\\\"integer\\\";s:9:\\\"is_active\\\";s:7:\\\"boolean\\\";}s:17:\\\"\\u0000*\\u0000classCastCache\\\";a:0:{}s:21:\\\"\\u0000*\\u0000attributeCastCache\\\";a:0:{}s:13:\\\"\\u0000*\\u0000dateFormat\\\";N;s:10:\\\"\\u0000*\\u0000appends\\\";a:0:{}s:19:\\\"\\u0000*\\u0000dispatchesEvents\\\";a:0:{}s:14:\\\"\\u0000*\\u0000observables\\\";a:0:{}s:12:\\\"\\u0000*\\u0000relations\\\";a:0:{}s:10:\\\"\\u0000*\\u0000touches\\\";a:0:{}s:27:\\\"\\u0000*\\u0000relationAutoloadCallback\\\";N;s:26:\\\"\\u0000*\\u0000relationAutoloadContext\\\";N;s:10:\\\"timestamps\\\";b:1;s:13:\\\"usesUniqueIds\\\";b:0;s:9:\\\"\\u0000*\\u0000hidden\\\";a:0:{}s:10:\\\"\\u0000*\\u0000visible\\\";a:0:{}s:11:\\\"\\u0000*\\u0000fillable\\\";a:25:{i:0;s:10:\\\"booking_id\\\";i:1;s:10:\\\"retreat_id\\\";i:2;s:9:\\\"firstname\\\";i:3;s:8:\\\"lastname\\\";i:4;s:12:\\\"country_code\\\";i:5;s:15:\\\"whatsapp_number\\\";i:6;s:3:\\\"age\\\";i:7;s:5:\\\"email\\\";i:8;s:7:\\\"address\\\";i:9;s:6:\\\"gender\\\";i:10;s:7:\\\"married\\\";i:11;s:4:\\\"city\\\";i:12;s:5:\\\"state\\\";i:13;s:7:\\\"diocese\\\";i:14;s:6:\\\"parish\\\";i:15;s:12:\\\"congregation\\\";i:16;s:22:\\\"emergency_contact_name\\\";i:17;s:23:\\\"emergency_contact_phone\\\";i:18;s:23:\\\"additional_participants\\\";i:19;s:15:\\\"special_remarks\\\";i:20;s:4:\\\"flag\\\";i:21;s:18:\\\"participant_number\\\";i:22;s:10:\\\"created_by\\\";i:23;s:10:\\\"updated_by\\\";i:24;s:9:\\\"is_active\\\";}s:10:\\\"\\u0000*\\u0000guarded\\\";a:1:{i:0;s:1:\\\"*\\\";}}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}}\"},\"createdAt\":1763455718,\"delay\":null}', 0, NULL, 1763455718, 1763455718),
(153, 'default', '{\"uuid\":\"a8716482-ec79-45b5-b70d-c09dcefa94b3\",\"displayName\":\"App\\\\Jobs\\\\SendBookingConfirmationEmail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendBookingConfirmationEmail\",\"command\":\"O:37:\\\"App\\\\Jobs\\\\SendBookingConfirmationEmail\\\":3:{s:17:\\\"\\u0000*\\u0000primaryBooking\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Booking\\\";s:2:\\\"id\\\";i:115;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:10:\\\"\\u0000*\\u0000retreat\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Retreat\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:1:{i:0;s:8:\\\"bookings\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:18:\\\"\\u0000*\\u0000allParticipants\\\";O:29:\\\"Illuminate\\\\Support\\\\Collection\\\":2:{s:8:\\\"\\u0000*\\u0000items\\\";a:2:{i:0;O:18:\\\"App\\\\Models\\\\Booking\\\":33:{s:13:\\\"\\u0000*\\u0000connection\\\";s:5:\\\"mysql\\\";s:8:\\\"\\u0000*\\u0000table\\\";s:8:\\\"bookings\\\";s:13:\\\"\\u0000*\\u0000primaryKey\\\";s:2:\\\"id\\\";s:10:\\\"\\u0000*\\u0000keyType\\\";s:3:\\\"int\\\";s:12:\\\"incrementing\\\";b:1;s:7:\\\"\\u0000*\\u0000with\\\";a:0:{}s:12:\\\"\\u0000*\\u0000withCount\\\";a:0:{}s:19:\\\"preventsLazyLoading\\\";b:0;s:10:\\\"\\u0000*\\u0000perPage\\\";i:15;s:6:\\\"exists\\\";b:1;s:18:\\\"wasRecentlyCreated\\\";b:1;s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;s:13:\\\"\\u0000*\\u0000attributes\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB65\\\";s:10:\\\"retreat_id\\\";i:2;s:9:\\\"firstname\\\";s:9:\\\"keerthi 1\\\";s:8:\\\"lastname\\\";s:1:\\\"M\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"8498948989\\\";s:3:\\\"age\\\";s:2:\\\"65\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:4:\\\"male\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";s:10:\\\"rrrrrrrrrr\\\";s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:1;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:21:04\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:21:04\\\";s:2:\\\"id\\\";i:115;}s:11:\\\"\\u0000*\\u0000original\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB65\\\";s:10:\\\"retreat_id\\\";i:2;s:9:\\\"firstname\\\";s:9:\\\"keerthi 1\\\";s:8:\\\"lastname\\\";s:1:\\\"M\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"8498948989\\\";s:3:\\\"age\\\";s:2:\\\"65\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:4:\\\"male\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";s:10:\\\"rrrrrrrrrr\\\";s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:1;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:21:04\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:21:04\\\";s:2:\\\"id\\\";i:115;}s:10:\\\"\\u0000*\\u0000changes\\\";a:0:{}s:11:\\\"\\u0000*\\u0000previous\\\";a:0:{}s:8:\\\"\\u0000*\\u0000casts\\\";a:4:{s:3:\\\"age\\\";s:7:\\\"integer\\\";s:23:\\\"additional_participants\\\";s:7:\\\"integer\\\";s:18:\\\"participant_number\\\";s:7:\\\"integer\\\";s:9:\\\"is_active\\\";s:7:\\\"boolean\\\";}s:17:\\\"\\u0000*\\u0000classCastCache\\\";a:0:{}s:21:\\\"\\u0000*\\u0000attributeCastCache\\\";a:0:{}s:13:\\\"\\u0000*\\u0000dateFormat\\\";N;s:10:\\\"\\u0000*\\u0000appends\\\";a:0:{}s:19:\\\"\\u0000*\\u0000dispatchesEvents\\\";a:0:{}s:14:\\\"\\u0000*\\u0000observables\\\";a:0:{}s:12:\\\"\\u0000*\\u0000relations\\\";a:0:{}s:10:\\\"\\u0000*\\u0000touches\\\";a:0:{}s:27:\\\"\\u0000*\\u0000relationAutoloadCallback\\\";N;s:26:\\\"\\u0000*\\u0000relationAutoloadContext\\\";N;s:10:\\\"timestamps\\\";b:1;s:13:\\\"usesUniqueIds\\\";b:0;s:9:\\\"\\u0000*\\u0000hidden\\\";a:0:{}s:10:\\\"\\u0000*\\u0000visible\\\";a:0:{}s:11:\\\"\\u0000*\\u0000fillable\\\";a:25:{i:0;s:10:\\\"booking_id\\\";i:1;s:10:\\\"retreat_id\\\";i:2;s:9:\\\"firstname\\\";i:3;s:8:\\\"lastname\\\";i:4;s:12:\\\"country_code\\\";i:5;s:15:\\\"whatsapp_number\\\";i:6;s:3:\\\"age\\\";i:7;s:5:\\\"email\\\";i:8;s:7:\\\"address\\\";i:9;s:6:\\\"gender\\\";i:10;s:7:\\\"married\\\";i:11;s:4:\\\"city\\\";i:12;s:5:\\\"state\\\";i:13;s:7:\\\"diocese\\\";i:14;s:6:\\\"parish\\\";i:15;s:12:\\\"congregation\\\";i:16;s:22:\\\"emergency_contact_name\\\";i:17;s:23:\\\"emergency_contact_phone\\\";i:18;s:23:\\\"additional_participants\\\";i:19;s:15:\\\"special_remarks\\\";i:20;s:4:\\\"flag\\\";i:21;s:18:\\\"participant_number\\\";i:22;s:10:\\\"created_by\\\";i:23;s:10:\\\"updated_by\\\";i:24;s:9:\\\"is_active\\\";}s:10:\\\"\\u0000*\\u0000guarded\\\";a:1:{i:0;s:1:\\\"*\\\";}}i:1;O:18:\\\"App\\\\Models\\\\Booking\\\":33:{s:13:\\\"\\u0000*\\u0000connection\\\";s:5:\\\"mysql\\\";s:8:\\\"\\u0000*\\u0000table\\\";s:8:\\\"bookings\\\";s:13:\\\"\\u0000*\\u0000primaryKey\\\";s:2:\\\"id\\\";s:10:\\\"\\u0000*\\u0000keyType\\\";s:3:\\\"int\\\";s:12:\\\"incrementing\\\";b:1;s:7:\\\"\\u0000*\\u0000with\\\";a:0:{}s:12:\\\"\\u0000*\\u0000withCount\\\";a:0:{}s:19:\\\"preventsLazyLoading\\\";b:0;s:10:\\\"\\u0000*\\u0000perPage\\\";i:15;s:6:\\\"exists\\\";b:1;s:18:\\\"wasRecentlyCreated\\\";b:1;s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;s:13:\\\"\\u0000*\\u0000attributes\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB65\\\";s:10:\\\"retreat_id\\\";i:2;s:9:\\\"firstname\\\";s:9:\\\"keerthi 2\\\";s:8:\\\"lastname\\\";s:4:\\\"hjjk\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"8498948989\\\";s:3:\\\"age\\\";s:2:\\\"56\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:4:\\\"male\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";s:10:\\\"yyyyyyyyyy\\\";s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:2;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:21:04\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:21:04\\\";s:2:\\\"id\\\";i:116;}s:11:\\\"\\u0000*\\u0000original\\\";a:28:{s:10:\\\"booking_id\\\";s:4:\\\"RB65\\\";s:10:\\\"retreat_id\\\";i:2;s:9:\\\"firstname\\\";s:9:\\\"keerthi 2\\\";s:8:\\\"lastname\\\";s:4:\\\"hjjk\\\";s:12:\\\"country_code\\\";s:3:\\\"+91\\\";s:15:\\\"whatsapp_number\\\";s:10:\\\"8498948989\\\";s:3:\\\"age\\\";s:2:\\\"56\\\";s:5:\\\"email\\\";s:24:\\\"sachinmohanfff@gmail.com\\\";s:7:\\\"address\\\";s:31:\\\"Devikripa,Sreepuram,Chundale Po\\\";s:6:\\\"gender\\\";s:4:\\\"male\\\";s:7:\\\"married\\\";N;s:4:\\\"city\\\";s:8:\\\"Chundale\\\";s:5:\\\"state\\\";s:6:\\\"Kerala\\\";s:7:\\\"diocese\\\";N;s:6:\\\"parish\\\";N;s:12:\\\"congregation\\\";s:10:\\\"yyyyyyyyyy\\\";s:22:\\\"emergency_contact_name\\\";s:14:\\\"Sachin Mohan M\\\";s:23:\\\"emergency_contact_phone\\\";s:11:\\\"08943928625\\\";s:23:\\\"additional_participants\\\";i:1;s:15:\\\"special_remarks\\\";N;s:18:\\\"participant_number\\\";i:2;s:9:\\\"is_active\\\";b:1;s:4:\\\"flag\\\";N;s:10:\\\"created_by\\\";N;s:10:\\\"updated_by\\\";N;s:10:\\\"updated_at\\\";s:19:\\\"2025-11-18 14:21:04\\\";s:10:\\\"created_at\\\";s:19:\\\"2025-11-18 14:21:04\\\";s:2:\\\"id\\\";i:116;}s:10:\\\"\\u0000*\\u0000changes\\\";a:0:{}s:11:\\\"\\u0000*\\u0000previous\\\";a:0:{}s:8:\\\"\\u0000*\\u0000casts\\\";a:4:{s:3:\\\"age\\\";s:7:\\\"integer\\\";s:23:\\\"additional_participants\\\";s:7:\\\"integer\\\";s:18:\\\"participant_number\\\";s:7:\\\"integer\\\";s:9:\\\"is_active\\\";s:7:\\\"boolean\\\";}s:17:\\\"\\u0000*\\u0000classCastCache\\\";a:0:{}s:21:\\\"\\u0000*\\u0000attributeCastCache\\\";a:0:{}s:13:\\\"\\u0000*\\u0000dateFormat\\\";N;s:10:\\\"\\u0000*\\u0000appends\\\";a:0:{}s:19:\\\"\\u0000*\\u0000dispatchesEvents\\\";a:0:{}s:14:\\\"\\u0000*\\u0000observables\\\";a:0:{}s:12:\\\"\\u0000*\\u0000relations\\\";a:0:{}s:10:\\\"\\u0000*\\u0000touches\\\";a:0:{}s:27:\\\"\\u0000*\\u0000relationAutoloadCallback\\\";N;s:26:\\\"\\u0000*\\u0000relationAutoloadContext\\\";N;s:10:\\\"timestamps\\\";b:1;s:13:\\\"usesUniqueIds\\\";b:0;s:9:\\\"\\u0000*\\u0000hidden\\\";a:0:{}s:10:\\\"\\u0000*\\u0000visible\\\";a:0:{}s:11:\\\"\\u0000*\\u0000fillable\\\";a:25:{i:0;s:10:\\\"booking_id\\\";i:1;s:10:\\\"retreat_id\\\";i:2;s:9:\\\"firstname\\\";i:3;s:8:\\\"lastname\\\";i:4;s:12:\\\"country_code\\\";i:5;s:15:\\\"whatsapp_number\\\";i:6;s:3:\\\"age\\\";i:7;s:5:\\\"email\\\";i:8;s:7:\\\"address\\\";i:9;s:6:\\\"gender\\\";i:10;s:7:\\\"married\\\";i:11;s:4:\\\"city\\\";i:12;s:5:\\\"state\\\";i:13;s:7:\\\"diocese\\\";i:14;s:6:\\\"parish\\\";i:15;s:12:\\\"congregation\\\";i:16;s:22:\\\"emergency_contact_name\\\";i:17;s:23:\\\"emergency_contact_phone\\\";i:18;s:23:\\\"additional_participants\\\";i:19;s:15:\\\"special_remarks\\\";i:20;s:4:\\\"flag\\\";i:21;s:18:\\\"participant_number\\\";i:22;s:10:\\\"created_by\\\";i:23;s:10:\\\"updated_by\\\";i:24;s:9:\\\"is_active\\\";}s:10:\\\"\\u0000*\\u0000guarded\\\";a:1:{i:0;s:1:\\\"*\\\";}}}s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;}}\"},\"createdAt\":1763455864,\"delay\":null}', 0, NULL, 1763455864, 1763455864);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_13_065603_create_roles_table', 1),
(5, '2025_09_13_065611_create_permissions_table', 1),
(6, '2025_09_13_065617_create_role_permission_table', 1),
(7, '2025_09_13_065800_update_users_table', 1),
(8, '2025_09_19_000003_create_retreats_table', 1),
(9, '2025_09_21_152500_create_bookings_table', 2),
(10, '2025_09_21_152600_add_booking_permissions', 3),
(11, '2025_09_25_193357_modify_bookings_table_remove_soft_delete_add_is_active', 4),
(13, '2025_10_06_172209_add_whatsapp_channel_link_to_retreats_table', 5),
(14, '2025_10_08_174307_create_criteria_table', 6),
(15, '2025_10_08_180422_modify_retreats_criteria_column_to_integer', 7),
(16, '2025_10_08_192653_add_married_column_to_bookings_table', 8),
(17, '2025_10_13_103304_create_notifications_table', 9),
(18, '2025_10_14_104807_add_greeting_to_notifications_table', 10),
(19, '2025_10_16_103941_make_heading_nullable_in_notifications_table', 11),
(20, '2025_10_26_115335_create_user_sessions_table', 12),
(21, '2025_10_26_203328_create_settings_table', 13),
(22, '2025_10_31_000000_fix_booking_id_unique_constraint', 14),
(23, '2025_11_05_000000_add_country_code_to_bookings_table', 14);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `need` enum('retreat','custom') COLLATE utf8mb4_unicode_ci NOT NULL,
  `retreat_id` bigint UNSIGNED DEFAULT NULL,
  `heading` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `greeting` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_users_emails` text COLLATE utf8mb4_unicode_ci,
  `total_recipients` int UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('pending','queued','processing','sent','failed','partially_sent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `need`, `retreat_id`, `heading`, `greeting`, `subject`, `body`, `additional_users_emails`, `total_recipients`, `status`, `sent_at`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'retreat', 2, 'heading', NULL, 'saubject', '<p>csnbvhjdsbcvn dschjdbchj</p>', NULL, 1, 'sent', '2025-10-13 14:25:16', 1, '2025-10-13 14:22:26', '2025-10-13 14:25:16'),
(2, 'custom', NULL, 'heading', NULL, 'subject', '<p>hsdbhvbshdn dsfhdsbhfndshufgbds cbdsghsdhjdsbfhjds</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-13 14:25:17', 1, '2025-10-13 14:23:21', '2025-10-13 14:25:17'),
(3, 'retreat', 1, 'erewrerw', NULL, 'cvxcxvcxv', '<p>dcdcsdfdfs</p>', NULL, 34, 'sent', '2025-10-13 14:33:55', 1, '2025-10-13 14:33:42', '2025-10-13 14:33:55'),
(4, 'retreat', 1, 'drtfgh', NULL, 'drfctgvhbjnkm', '<p>rdtfgyhbunjkm</p>', NULL, 34, 'sent', '2025-10-13 15:10:25', 1, '2025-10-13 15:10:23', '2025-10-13 15:10:25'),
(5, 'retreat', 1, 'sedrftgh', NULL, 'rdftgvhbj', '<p>retfgyhbuj</p>', NULL, 34, 'sent', '2025-10-13 15:12:54', 1, '2025-10-13 15:12:52', '2025-10-13 15:12:54'),
(6, 'retreat', 1, 'sachinmohanfff@gmail.com', NULL, 'sachinmohanfff@gmail.com', '<p>g7yhf8udk</p>', NULL, 34, 'sent', '2025-10-13 15:13:48', 1, '2025-10-13 15:13:45', '2025-10-13 15:13:48'),
(7, 'retreat', 1, 'eeeeeeeeeeeee', NULL, 'eeeeeeeeeeeee', '<p>eeeeeeeeeeeeeee</p>', NULL, 34, 'sent', '2025-10-13 15:15:11', 1, '2025-10-13 15:15:10', '2025-10-13 15:15:11'),
(8, 'retreat', 1, 'rrrrrrrrrr', NULL, 'rrrrrrrrrrrrr', '<p>rrrrrrrrrr</p>', NULL, 34, 'sent', '2025-10-14 05:07:24', 1, '2025-10-13 15:16:37', '2025-10-14 05:07:24'),
(9, 'retreat', 1, 'Heafing', NULL, 'Subject--\"kjmckmsncnjdsncsncjldsc\"', '<p><strong>Registration &amp; Arrival:</strong></p><ul><li>There is no registration fee. Voluntary donations inspired by the Holy Spirit are welcome.</li><li>Registration will begin at 1:00 PM on Tuesday, September 30th.</li><li>The retreat will commence at 4:00 PM on Tuesday with the Rosary and Holy Mass.</li><li>Only those who can attend the entire retreat from beginning to end should register.</li><li>No one will be allowed to leave before 1:00 PM on Saturday, October 4th. Therefore, participants are requested to arrange their return travel (train or bus) only after 1:00 PM on Saturday, October 4th.</li><li>Alcohol, drugs, extra mobile phones, snacks, chocolates, chewing gum, Orbit, mouth fresheners, and stamps are strictly prohibited during the retreat.</li><li>To ensure that participants are filled with the power and grace of the Holy Spirit, volunteers will personally inspect participants\' bags for the above-mentioned items.</li><li>Those who have the opportunity to go for confession before the retreat are encouraged to do so and come prepared with personal prayer.</li><li>Participants are requested to pray for Rev. Fr. Daniel Poovannathil, who will lead the retreat, and for the MCRC team members.</li></ul><p><strong>What to Bring:</strong></p><ul><li>All participants must bring a Bible. Entry will not be permitted without a Bible.* Please bring the following items:</li><li>Notebook</li><li>Pen</li><li>Bed sheet</li><li>Other personal items</li><li>All participants who are able are requested to bring a pair of white clothes to wear during the Holy Spirit anointing service on Thursday evening, October 2nd.</li></ul><p>&nbsp;</p><p><strong>Accommodation:</strong></p><ul><li>The retreat center primarily offers dormitory accommodation. Those who require a private room must pay a separate rent. Rooms will be allocated at the time of registration for those who request them (₹500 per day). Rooms with attached bathrooms are limited to single occupancy. Room rent should be paid upon arrival, as advance booking is not available.</li><li>Those requesting room accommodation must bring a photo ID.</li></ul>', NULL, 44, 'sent', '2025-10-14 05:07:24', 1, '2025-10-14 05:06:37', '2025-10-14 05:07:24'),
(10, 'retreat', 1, 'Heading', NULL, 'email hdsbfhdsnm uwhduifhiuew', '<p><strong>Registration &amp; Arrival:</strong></p><ul><li>There is no registration fee. Voluntary donations inspired by the Holy Spirit are welcome.</li><li>Registration will begin at 1:00 PM on Tuesday, September 30th.</li><li>The retreat will commence at 4:00 PM on Tuesday with the Rosary and Holy Mass.</li><li>Only those who can attend the entire retreat from beginning to end should register.</li><li>No one will be allowed to leave before 1:00 PM on Saturday, October 4th. Therefore, participants are requested to arrange their return travel (train or bus) only after 1:00 PM on Saturday, October 4th.</li><li>Alcohol, drugs, extra mobile phones, snacks, chocolates, chewing gum, Orbit, mouth fresheners, and stamps are strictly prohibited during the retreat.</li><li>To ensure that participants are filled with the power and grace of the Holy Spirit, volunteers will personally inspect participants\' bags for the above-mentioned items.</li><li>Those who have the opportunity to go for confession before the retreat are encouraged to do so and come prepared with personal prayer.</li><li>Participants are requested to pray for Rev. Fr. Daniel Poovannathil, who will lead the retreat, and for the MCRC team members.</li></ul><p><strong>What to Bring:</strong></p><ul><li>All participants must bring a Bible. Entry will not be permitted without a Bible.* Please bring the following items:</li><li>Notebook</li><li>Pen</li><li>Bed sheet</li><li>Other personal items</li><li>All participants who are able are requested to bring a pair of white clothes to wear during the Holy Spirit anointing service on Thursday evening, October 2nd.</li></ul><p>&nbsp;</p><p><strong>Accommodation:</strong></p><ul><li>The retreat center primarily offers dormitory accommodation. Those who require a private room must pay a separate rent. Rooms will be allocated at the time of registration for those who request them (₹500 per day). Rooms with attached bathrooms are limited to single occupancy. Room rent should be paid upon arrival, as advance booking is not available.</li><li>Those requesting room accommodation must bring a photo ID.</li></ul>', NULL, 44, 'sent', '2025-10-14 05:15:47', 1, '2025-10-14 05:15:39', '2025-10-14 05:15:47'),
(11, 'retreat', 1, 'Heading test', 'Hello participants,', 'sbuject test', '<p><strong>Eligibility Criteria:</strong></p><ul><li>Age limit:15 to 35 years.</li><li>Those who have attended any retreat at MCRC between Sep 2024 and Sep 2025 are kindly requested not to attend this youth retreat.</li></ul><p><strong>Mobile Phone Policy:</strong></p><ul><li>Mobile phones must be handed over at the reception counter during registration. Keeping mobile phones during the retreat is not permitted. Phones will be returned on Saturday, October 4th&nbsp;at 1:00 PM.</li></ul><p>&nbsp;</p><p><strong>Other Guidelines:</strong></p><ul><li>Those arriving in private vehicles are reminded to hand over their vehicle keys along with their mobile phones at the registration counter.</li><li>Children under the age of 15 are not permitted to attend the retreat.</li><li>Those who are on medication for any kind of mental health condition are kindly requested not to attend residential retreats.</li><li>There are no facilities for washing clothes at the retreat center.With prayers.</li></ul>', NULL, 44, 'sent', '2025-10-14 05:21:55', 1, '2025-10-14 05:21:52', '2025-10-14 05:21:55'),
(12, 'custom', NULL, 'Test Headfing', 'hello sachin', 'test subject', '<p><strong>Registration &amp; Arrival:</strong></p><ul><li>There is no registration fee. Voluntary donations inspired by the Holy Spirit are welcome.</li><li>Registration will begin at 1:00 PM on Tuesday, September 30th.</li><li>The retreat will commence at 4:00 PM on Tuesday with the Rosary and Holy Mass.</li><li>Only those who can attend the entire retreat from beginning to end should register.</li><li>No one will be allowed to leave before 1:00 PM on Saturday, October 4th. Therefore, participants are requested to arrange their return travel (train or bus) only after 1:00 PM on Saturday, October 4th.</li><li>Alcohol, drugs, extra mobile phones, snacks, chocolates, chewing gum, Orbit, mouth fresheners, and stamps are strictly prohibited during the retreat.</li><li>To ensure that participants are filled with the power and grace of the Holy Spirit, volunteers will personally inspect participants\' bags for the above-mentioned items.</li><li>Those who have the opportunity to go for confession before the retreat are encouraged to do so and come prepared with personal prayer.</li><li>Participants are requested to pray for Rev. Fr. Daniel Poovannathil, who will lead the retreat, and for the MCRC team members.</li></ul><p><strong>What to Bring:</strong></p><ul><li>All participants must bring a Bible. Entry will not be permitted without a Bible.* Please bring the following items:</li><li>Notebook</li><li>Pen</li><li>Bed sheet</li><li>Other personal items</li><li>All participants who are able are requested to bring a pair of white clothes to wear during the Holy Spirit anointing service on Thursday evening, October 2nd.</li></ul><p>&nbsp;</p><p><strong>Accommodation:</strong></p><ul><li>The retreat center primarily offers dormitory accommodation. Those who require a private room must pay a separate rent. Rooms will be allocated at the time of registration for those who request them (₹500 per day). Rooms with attached bathrooms are limited to single occupancy. Room rent should be paid upon arrival, as advance booking is not available.</li><li>Those requesting room accommodation must bring a photo ID.</li></ul>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-14 15:08:44', 1, '2025-10-14 15:07:39', '2025-10-14 15:08:44'),
(13, 'custom', NULL, 'Test Headfing', 'hello sachin', 'test subject', '<p><strong>Registration &amp; Arrival:</strong></p><ul><li>There is no registration fee. Voluntary donations inspired by the Holy Spirit are welcome.</li><li>Registration will begin at 1:00 PM on Tuesday, September 30th.</li><li>The retreat will commence at 4:00 PM on Tuesday with the Rosary and Holy Mass.</li><li>Only those who can attend the entire retreat from beginning to end should register.</li><li>No one will be allowed to leave before 1:00 PM on Saturday, October 4th. Therefore, participants are requested to arrange their return travel (train or bus) only after 1:00 PM on Saturday, October 4th.</li><li>Alcohol, drugs, extra mobile phones, snacks, chocolates, chewing gum, Orbit, mouth fresheners, and stamps are strictly prohibited during the retreat.</li><li>To ensure that participants are filled with the power and grace of the Holy Spirit, volunteers will personally inspect participants\' bags for the above-mentioned items.</li><li>Those who have the opportunity to go for confession before the retreat are encouraged to do so and come prepared with personal prayer.</li><li>Participants are requested to pray for Rev. Fr. Daniel Poovannathil, who will lead the retreat, and for the MCRC team members.</li></ul><p><strong>What to Bring:</strong></p><ul><li>All participants must bring a Bible. Entry will not be permitted without a Bible.* Please bring the following items:</li><li>Notebook</li></ul>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-14 15:11:16', 1, '2025-10-14 15:11:13', '2025-10-14 15:11:16'),
(14, 'custom', NULL, 'Test Headfing', 'hello guys', 'test subject', '<p><strong>Registration &amp; Arrival:</strong></p><ul><li>There is no registration fee. Voluntary donations inspired by the Holy Spirit are welcome.</li><li>Registration will begin at 1:00 PM on Tuesday, September 30th.</li><li>The retreat will commence at 4:00 PM on Tuesday with the Rosary and Holy Mass.</li><li>Only those who can attend the entire retreat from beginning to end should register.</li><li>No one will be allowed to leave before 1:00 PM on Saturday, October 4th. Therefore, participants are requested to arrange their return travel (train or bus) only after 1:00 PM on Saturday, October 4th.</li><li>Alcohol, drugs, extra mobile phones, snacks, chocolates, chewing gum, Orbit, mouth fresheners, and stamps are strictly prohibited during the retreat.</li><li>To ensure that participants are filled with the power and grace of the Holy Spirit, volunteers will personally inspect participants\' bags for the above-mentioned items.</li><li>Those who have the opportuni</li></ul>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-14 15:28:10', 1, '2025-10-14 15:28:09', '2025-10-14 15:28:10'),
(15, 'custom', NULL, 'test heading', 'hellooo guys', 'test subhject', '<p>dsbhcdsyhcdsiu</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-14 15:29:24', 1, '2025-10-14 15:29:21', '2025-10-14 15:29:24'),
(16, 'custom', NULL, 'dvff', 'Dear {name},', 'fadsdfsa', '<p>dasadfs</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-14 15:32:50', 1, '2025-10-14 15:32:48', '2025-10-14 15:32:50'),
(17, 'custom', NULL, 'ccs', 'Dear {name},', 'csac', '<p>cscd</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-14 15:34:16', 1, '2025-10-14 15:34:14', '2025-10-14 15:34:16'),
(19, 'custom', NULL, 'Email hedimng', 'Hi All', 'Email subject', '<p><strong>Eligibility Criteria:</strong></p><ul><li>Age limit:15 to 35 years.</li><li>Those who have attended any retreat at MCRC between Sep 2024 and Sep 2025 are kindly requested not to attend this youth retreat.</li></ul><p><strong>Mobile Phone Policy:</strong></p><ul><li>Mobile phones must be handed over at the reception counter during registration. Keeping mobile phones during the retreat is not permitted. Phones will be returned on Saturday, October 4th&nbsp;at 1:00 PM.</li></ul><p>&nbsp;</p><p><strong>Other Guidelines:</strong></p><ul><li>Those arriving in private vehicles are reminded to hand over their vehicle keys along with their mobile phones at the registration counter.</li><li>Children under the age of 15 are not permitted to attend the retreat.</li><li>Those who are on medication for any kind of mental health condition are kindly requested not to attend residential retreats.</li><li>There are no facilities for washing clothes at the retreat center.With prayers.</li></ul>', 'sachinmohanfff@gmail.com', 1, 'queued', NULL, 1, '2025-10-15 14:13:03', '2025-10-15 14:13:03'),
(20, 'custom', NULL, 'Email hedimng', 'Hi All', 'Email subject', '<p><strong>Eligibility Criteria:</strong></p><ul><li>Age limit:15 to 35 years.</li><li>Those who have attended any retreat at MCRC between Sep 2024 and Sep 2025 are kindly requested not to attend this youth retreat.</li></ul><p><strong>Mobile Phone Policy:</strong></p><ul><li>Mobile phones must be handed over at the reception counter during registration. Keeping mobile phones during the retreat is not permitted. Phones will be returned on Saturday, October 4th&nbsp;at 1:00 PM.</li></ul><p>&nbsp;</p><p><strong>Other Guidelines:</strong></p><ul><li>Those arriving in private vehicles are reminded to hand over their vehicle keys along with their mobile phones at the registration counter.</li><li>Children under the age of 15 are not permitted to attend the retreat.</li><li>Those who are on medication for any kind of mental health condition are kindly requested not to attend residential retreats.</li><li>There are no facilities for washing clothes at the retreat center.With prayers.</li></ul>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-15 14:15:46', 1, '2025-10-15 14:15:01', '2025-10-15 14:15:46'),
(21, 'custom', NULL, 'sdhbijhfsu', 'Dear {name},', 'subbfhfbeh', '<p>jhsvhdfujg</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-16 04:26:12', 1, '2025-10-16 04:26:04', '2025-10-16 04:26:12'),
(22, 'custom', NULL, 'dsasadsad', 'Dear {name},fdsefsdefw', 'dcsdf', '<p>ddsds</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-16 04:29:07', 1, '2025-10-16 04:29:05', '2025-10-16 04:29:07'),
(23, 'custom', NULL, NULL, 'Dear {name},yghfyuhsbfhdsbfhubw', 'asdf@fg.com', '<p>jfdnvjdfuigvfdui</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-16 05:14:36', 1, '2025-10-16 05:14:34', '2025-10-16 05:14:36'),
(24, 'custom', NULL, 'thid si the heaidng', 'hellooo  nithin', 'subjectdsnv ndhdsfhdn', '<p>rf6tgyuhjk</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-16 05:15:44', 1, '2025-10-16 05:15:42', '2025-10-16 05:15:44'),
(25, 'custom', NULL, 'vbhjbfhnd fuehj gfnjnfn efg reufhjfniuerhfie', 'all members', 'subghscbf sbfhdw ffnuiewhfiyew', '<p>hjdsbfhjds fndhfbdshfbhj</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-16 05:18:00', 1, '2025-10-16 05:17:58', '2025-10-16 05:18:00'),
(26, 'custom', NULL, 'gscbhj', 'Dear {hi},', 'dhucdsyu', '<p>hidschs</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-25 14:10:39', 1, '2025-10-25 14:10:33', '2025-10-25 14:10:39'),
(27, 'custom', NULL, 'rtfghj', 'Dear {name},', 'dcfgvhbjn', '<p>ftcgyhjk</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-10-25 14:13:15', 1, '2025-10-25 14:13:14', '2025-10-25 14:13:15'),
(28, 'custom', NULL, 'sachinmohanfff@gmail.com', 'Dear {name},hefhfs', 'sachinmohanfff@gmail.com', '<p>Guidelines for Your Upcoming Word anointing Retreat – November 9-13</p><p><strong>Body:</strong></p><p>Your retreat date is fast approaching, and we are excited to welcome you to the word anointing\' Retreat at Mount Carmel Retreat Centre. Please take a moment to review the final guidelines to ensure a smooth and spiritually enriching experience.</p><p><strong>🗓 Retreat Schedule</strong><br>Dates: November 9-13<br>Registration: Begins at 1:00 PM on Monday. Please present your booking name and email id at the registration counter.</p><p><br><strong>📖 Spiritual Preparation</strong><br>Bring a Holy Bible – entry will not be permitted without one.<br>Confession before arrival is encouraged.</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-11-05 19:01:09', 1, '2025-11-05 19:00:45', '2025-11-05 19:01:09'),
(29, 'custom', NULL, 'dftgh', 'Dear {name},', 'dftgvh', '<p>drfctg</p>', 'sachinmohanfff@gmail.com', 1, 'sent', '2025-11-05 19:05:51', 1, '2025-11-05 19:05:32', '2025-11-05 19:05:51');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `module`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'View Permissions', 'view-permissions', 'permissions', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(2, 'Create Permissions', 'create-permissions', 'permissions', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(3, 'Edit Permissions', 'edit-permissions', 'permissions', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(4, 'Delete Permissions', 'delete-permissions', 'permissions', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(5, 'View Roles', 'view-roles', 'roles', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(6, 'Create Roles', 'create-roles', 'roles', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(7, 'Edit Roles', 'edit-roles', 'roles', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(8, 'Delete Roles', 'delete-roles', 'roles', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(9, 'View Users', 'view-users', 'users', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(10, 'Create Users', 'create-users', 'users', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(11, 'Edit Users', 'edit-users', 'users', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(12, 'Delete Users', 'delete-users', 'users', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(13, 'View Retreats', 'view-retreats', 'retreats', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(14, 'Create Retreats', 'create-retreats', 'retreats', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(15, 'Edit Retreats', 'edit-retreats', 'retreats', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(16, 'Delete Retreats', 'delete-retreats', 'retreats', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(17, 'View Bookings', 'view-bookings', 'bookings', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(18, 'Create Bookings', 'create-bookings', 'bookings', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(19, 'Edit Bookings', 'edit-bookings', 'bookings', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(20, 'Delete Bookings', 'delete-bookings', 'bookings', NULL, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(22, 'View Criteria', 'view-criteria', 'criteria', NULL, 1, '2025-10-08 12:08:40', '2025-10-08 12:08:40', NULL),
(23, 'Create Criteria', 'create-criteria', 'criteria', NULL, 1, '2025-10-08 12:08:40', '2025-10-08 12:08:40', NULL),
(24, 'Edit Criteria', 'edit-criteria', 'criteria', NULL, 1, '2025-10-08 12:08:40', '2025-10-08 12:08:40', NULL),
(25, 'Delete Criteria', 'delete-criteria', 'criteria', NULL, 1, '2025-10-08 12:08:40', '2025-10-08 12:08:40', NULL),
(26, 'View Notifications', 'view-notifications', 'notifications', NULL, 1, '2025-10-14 17:06:00', '2025-10-14 17:06:00', NULL),
(27, 'Create Notifications', 'create-notifications', 'notifications', NULL, 1, '2025-10-14 17:06:00', '2025-10-14 17:06:00', NULL),
(28, 'Delete Notifications', 'delete-notifications', 'notifications', NULL, 1, '2025-10-14 17:06:00', '2025-10-14 17:06:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `retreats`
--

CREATE TABLE `retreats` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `whatsapp_channel_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `timings` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seats` int NOT NULL DEFAULT '0',
  `criteria` bigint UNSIGNED DEFAULT NULL,
  `special_remarks` text COLLATE utf8mb4_unicode_ci,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `retreats`
--

INSERT INTO `retreats` (`id`, `title`, `slug`, `description`, `whatsapp_channel_link`, `short_description`, `start_date`, `end_date`, `timings`, `seats`, `criteria`, `special_remarks`, `instructions`, `is_featured`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Tets retreat 1', 'tets-retreat-1-2025-11-18', '<p><strong style=\"background-color: rgb(242, 242, 242); color: rgba(26, 44, 55, 0.8);\">Eligibility Criteria:</strong></p><ul><li>Age limit:15 to 35 years.</li><li>Those who have attended any retreat at MCRC between Sep 2024 and Sep 2025 are kindly requested not to attend this youth retreat.</li></ul><p><strong style=\"background-color: rgb(242, 242, 242);\">Mobile Phone Policy:</strong></p><ul><li>Mobile phones must be handed over at the reception counter during registration. Keeping mobile phones during the retreat is not permitted. Phones will be returned on Saturday, October 4th&nbsp;at 1:00 PM.</li></ul><p><br></p><p><strong style=\"background-color: rgb(242, 242, 242);\">Other Guidelines:</strong></p><ul><li>Those arriving in private vehicles are reminded to hand over their vehicle keys along with their mobile phones at the registration counter.</li><li>Children under the age of 15 are not permitted to attend the retreat.</li><li>Those who are on medication for any kind of mental health condition are kindly requested not to attend residential retreats.</li><li>There are no facilities for washing clothes at the retreat center.With prayers.</li></ul><p><br></p>', 'https://smvmdnfjdm.sachin1', NULL, '2025-11-18', '2025-11-25', '9:00 AM - 5:00 PM', 200, 3, 'test teewts testst', '<p><strong style=\"background-color: rgb(242, 242, 242);\">Registration &amp; Arrival:</strong></p><ul><li>There is no registration fee. Voluntary donations inspired by the Holy Spirit are welcome.</li><li>Registration will begin at 1:00 PM on Tuesday, September 30th.</li><li>The retreat will commence at 4:00 PM on Tuesday with the Rosary and Holy Mass.</li><li>Only those who can attend the entire retreat from beginning to end should register.</li><li>No one will be allowed to leave before 1:00 PM on Saturday, October 4th. Therefore, participants are requested to arrange their return travel (train or bus) only after 1:00 PM on Saturday, October 4th.</li><li>Alcohol, drugs, extra mobile phones, snacks, chocolates, chewing gum, Orbit, mouth fresheners, and stamps are strictly prohibited during the retreat.</li><li>To ensure that participants are filled with the power and grace of the Holy Spirit, volunteers will personally inspect participants\' bags for the above-mentioned items.</li><li>Those who have the opportunity to go for confession before the retreat are encouraged to do so and come prepared with personal prayer.</li><li>Participants are requested to pray for Rev. Fr. Daniel Poovannathil, who will lead the retreat, and for the MCRC team members.</li></ul><p><strong style=\"background-color: rgb(254, 254, 254);\">What to Bring:</strong></p><ul><li>All participants must bring a Bible. Entry will not be permitted without a Bible.* Please bring the following items:</li><li class=\"ql-indent-1\">Notebook</li><li class=\"ql-indent-1\">Pen</li><li class=\"ql-indent-1\">Bed sheet</li><li class=\"ql-indent-1\">Other personal items</li><li>All participants who are able are requested to bring a pair of white clothes to wear during the Holy Spirit anointing service on Thursday evening, October 2nd.</li></ul><p><br></p><p><strong style=\"background-color: rgb(242, 242, 242);\">Accommodation:</strong></p><ul><li>The retreat center primarily offers dormitory accommodation. Those who require a private room must pay a separate rent. Rooms will be allocated at the time of registration for those who request them (₹500 per day). Rooms with attached bathrooms are limited to single occupancy. Room rent should be paid upon arrival, as advance booking is not available.</li><li>Those requesting room accommodation must bring a photo ID.</li></ul><p><br></p>', 0, 1, 1, 1, '2025-09-21 04:06:50', '2025-11-18 06:12:33', NULL),
(2, 'father retreat', 'father-retreat-2025-11-18', '<p>xdfgvhb <strong>test</strong></p>', 'https://smvmdnfjdm.sjdnfj', NULL, '2025-12-01', '2025-12-30', '9:00 AM - 5:00 PM', 15, 5, NULL, '<p>esdrtfgyuhjik<u> test test</u><em><u> </u>cdshcdsjnjdsncfjndsfndsnf</em></p>', 1, 1, 1, 1, '2025-09-25 03:05:19', '2025-11-18 08:50:01', NULL),
(3, 'fvfzfd', 'fvfzfd-2025-10-06', '<p>fgdaf</p>', 'https://smvmdnfjdm.sachin2', NULL, '2025-09-10', '2025-09-11', '9:00 AM - 5:00 PM', 20, 5, NULL, '<p>fafag</p>', 0, 1, 1, 1, '2025-09-25 12:22:42', '2025-10-06 12:10:48', NULL),
(4, 'sister retreat', 'sister-retreat-2025-11-18', '<p>gyvh</p>', 'https://smvmdnfjdm.sachin1', NULL, '2025-11-20', '2025-11-30', '9:00 AM - 5:00 PM', 200, 8, NULL, '<p>gfv</p>', 0, 1, 1, 1, '2025-11-18 06:13:05', '2025-11-18 06:13:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_super_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_active`, `is_super_admin`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Admin', 'super-admin', 'Has full access to all features', 1, 1, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(2, 'Retreat Admin', 'retreat-admin', 'Manages retreats and related operations', 1, 0, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(3, 'Booking Admin', 'booking-admin', 'Manages bookings and related operations', 1, 0, '2025-09-19 11:51:31', '2025-09-19 11:51:31', NULL),
(4, 'Reader', 'reader', NULL, 1, 0, '2025-09-19 12:06:09', '2025-09-19 12:06:09', NULL),
(5, 'test', 'test', 'dscs', 1, 0, '2025-09-26 15:15:46', '2025-09-26 15:15:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `permission_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 2, NULL, NULL),
(3, 1, 3, NULL, NULL),
(4, 1, 4, NULL, NULL),
(5, 1, 5, NULL, NULL),
(6, 1, 6, NULL, NULL),
(7, 1, 7, NULL, NULL),
(8, 1, 8, NULL, NULL),
(9, 1, 9, NULL, NULL),
(10, 1, 10, NULL, NULL),
(11, 1, 11, NULL, NULL),
(12, 1, 12, NULL, NULL),
(13, 1, 13, NULL, NULL),
(14, 1, 14, NULL, NULL),
(15, 1, 15, NULL, NULL),
(16, 1, 16, NULL, NULL),
(17, 1, 17, NULL, NULL),
(18, 1, 18, NULL, NULL),
(19, 1, 19, NULL, NULL),
(20, 1, 20, NULL, NULL),
(21, 2, 9, NULL, NULL),
(22, 2, 13, NULL, NULL),
(23, 2, 14, NULL, NULL),
(24, 2, 15, NULL, NULL),
(25, 2, 16, NULL, NULL),
(26, 3, 9, NULL, NULL),
(27, 3, 17, NULL, NULL),
(28, 3, 18, NULL, NULL),
(29, 3, 19, NULL, NULL),
(30, 3, 20, NULL, NULL),
(31, 4, 17, NULL, NULL),
(32, 4, 1, NULL, NULL),
(33, 4, 13, NULL, NULL),
(34, 4, 5, NULL, NULL),
(35, 4, 9, NULL, NULL),
(40, 5, 9, NULL, NULL),
(41, 1, 22, NULL, NULL),
(42, 1, 23, NULL, NULL),
(43, 1, 24, NULL, NULL),
(44, 1, 25, NULL, NULL),
(45, 1, 26, NULL, NULL),
(46, 1, 27, NULL, NULL),
(47, 1, 28, NULL, NULL),
(48, 2, 22, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
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
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('DgHnzrrchrUUNlDFneUh1xKJOdGgFJVyrQFVAPhq', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiajVuRFd0Y1lCRUNmWnZGVlUzY0V2ZElFQU1nc2VhcmZQSFk4bkU4SyI7czoxOToiZnJvbnRlbmRfc2Vzc2lvbl9pZCI7czo1MzoiZnJvbnRlbmRfc2Vzc2lvbl8xYzQ1NGNiYy04YmI0LTQ5NzUtYjcwMi0xMGRlNTI0ZWE1N2IiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ0OiJodHRwOi8vcmV0cmVhdG1zLmxvY2FsL2FkbWluL2Jvb2tpbmdzL2FjdGl2ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1763455871),
('IVP9Umva5MjV0sbh21geQXN4HeiaObupO9xO0Lor', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaXkxTktwMWI5M3QwYU1BU0ZWWjNEejNPOGNvT3VJU1ZqNXg3R0JjdyI7czoxOToiZnJvbnRlbmRfc2Vzc2lvbl9pZCI7czo1MzoiZnJvbnRlbmRfc2Vzc2lvbl85NGY1MzY5OS04NzBkLTQ0Y2MtODJlYS0zNWIxMGI2NDQwNzAiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vcmV0cmVhdG1zLmxvY2FsL3JlZ2lzdGVyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1763446389);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'MAX_ADDITIONAL_MEMBERS', '2', 'integer', 'Maximum number of additional participants allowed per booking', '2025-10-26 15:09:51', '2025-10-26 17:19:55'),
(2, 'API_KEY', 'mcrc_retreat_api_key_2025', 'string', 'API authentication key for external integrations', '2025-10-26 15:09:51', '2025-10-26 17:09:56'),
(3, 'CANCELLATION_DEADLINE_DAYS', '1', 'integer', 'Minimum days before retreat start date to allow cancellation', '2025-10-26 15:09:51', '2025-10-26 15:09:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `phone`, `profile_image`, `email_verified_at`, `password`, `remember_token`, `is_active`, `last_login_at`, `last_login_ip`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Super Admin', 'superadmin@retreat.com', '887889847878978', 'profile-images/TmCmEY2rZfpOBy1zAeipeCd5Y6Fun2UANoVd4eXe.jpg', NULL, '$2y$12$Bw6PFJxgEMDpGeOVGdzA/OUEwkRdYi.hP.gHfSw3.MyNbbEch4ZVG', NULL, 1, NULL, NULL, '2025-09-19 11:51:31', '2025-09-26 13:49:23', NULL),
(2, 2, 'SachinRetreat', 'sachinmohanfff+1@gmail.com', NULL, NULL, NULL, '$2y$12$Bw6PFJxgEMDpGeOVGdzA/OUEwkRdYi.hP.gHfSw3.MyNbbEch4ZVG', NULL, 1, NULL, NULL, '2025-09-19 12:07:27', '2025-09-19 12:07:27', NULL),
(3, 3, 'Sachin Booking', 'sachinmohanfff+2@gmail.com', NULL, NULL, NULL, '$2y$12$wJnxIs3upOo/DgaGesUV5.oyR185FKXPoPLO93UKP3GWyGO22s4/6', NULL, 1, NULL, NULL, '2025-09-19 12:07:55', '2025-09-19 12:07:55', NULL),
(4, 4, 'Sachin Reader', 'sachinmohanfff+3@gmail.com', NULL, NULL, NULL, '$2y$12$A5OUsN7IYrR.WWX7jzM/4u.AlbzvubSrA4/j60F6pi58BIxBL4dGi', NULL, 1, NULL, NULL, '2025-09-19 12:08:23', '2025-09-19 12:08:23', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_retreat_id_foreign` (`retreat_id`),
  ADD KEY `bookings_created_by_foreign` (`created_by`),
  ADD KEY `bookings_updated_by_foreign` (`updated_by`),
  ADD KEY `bookings_booking_id_index` (`booking_id`),
  ADD KEY `bookings_whatsapp_number_index` (`whatsapp_number`),
  ADD KEY `bookings_email_index` (`email`),
  ADD KEY `bookings_flag_index` (`flag`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_retreat_id_foreign` (`retreat_id`),
  ADD KEY `notifications_created_by_foreign` (`created_by`),
  ADD KEY `notifications_need_index` (`need`),
  ADD KEY `notifications_status_index` (`status`),
  ADD KEY `notifications_created_at_index` (`created_at`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Indexes for table `retreats`
--
ALTER TABLE `retreats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `retreats_slug_unique` (`slug`),
  ADD KEY `retreats_created_by_foreign` (`created_by`),
  ADD KEY `retreats_updated_by_foreign` (`updated_by`),
  ADD KEY `retreats_start_date_index` (`start_date`),
  ADD KEY `retreats_end_date_index` (`end_date`),
  ADD KEY `retreats_criteria_index` (`criteria`),
  ADD KEY `retreats_is_featured_index` (`is_featured`),
  ADD KEY `retreats_is_active_index` (`is_active`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permission_role_id_permission_id_unique` (`role_id`,`permission_id`),
  ADD KEY `role_permission_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `retreats`
--
ALTER TABLE `retreats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_retreat_id_foreign` FOREIGN KEY (`retreat_id`) REFERENCES `retreats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_retreat_id_foreign` FOREIGN KEY (`retreat_id`) REFERENCES `retreats` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `retreats`
--
ALTER TABLE `retreats`
  ADD CONSTRAINT `retreats_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retreats_criteria_foreign` FOREIGN KEY (`criteria`) REFERENCES `criteria` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `retreats_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
