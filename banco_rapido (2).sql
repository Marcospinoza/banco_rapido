-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-09-2025 a las 14:51:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `banco_rapido`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `accion` varchar(50) NOT NULL,
  `detalles` text DEFAULT NULL,
  `direccion_ip` varchar(45) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id`, `usuario_id`, `accion`, `detalles`, `direccion_ip`, `creado_en`) VALUES
(1, 2, 'login', 'Inicio de sesión', '::1', '2025-08-26 14:34:53'),
(2, 2, 'logout', 'Cierre de sesión', '::1', '2025-08-26 14:52:48'),
(3, 2, 'login', 'Inicio de sesión', '::1', '2025-08-26 14:52:58'),
(4, 2, 'logout', 'Cierre de sesión', '::1', '2025-08-26 14:53:01'),
(5, 2, 'login', 'Inicio de sesión', '::1', '2025-08-26 14:58:56'),
(6, 2, 'loan_request', 'Préstamo solicitado: S/ 100 a 9.86% por 10 meses', '::1', '2025-08-26 15:00:37'),
(7, 2, 'loan_request', 'Préstamo solicitado: S/ 100 a 9.86% por 10 meses', '::1', '2025-08-26 15:07:51'),
(8, 2, 'logout', 'Cierre de sesión', '::1', '2025-08-26 15:32:25'),
(9, 2, 'login', 'Inicio de sesión', '::1', '2025-08-26 15:32:33'),
(10, 2, 'loan_request', 'Préstamo solicitado: S/ 123 a 10% por 6 meses', '::1', '2025-08-26 15:32:50'),
(11, 2, 'loan_request', 'Préstamo solicitado: S/ 123 a 10% por 6 meses', '::1', '2025-08-26 15:42:54'),
(12, 2, 'loan_request', 'Préstamo solicitado: S/ 150 a 10% por 6 meses', '::1', '2025-08-26 15:55:27'),
(13, 2, 'logout', 'Cierre de sesión', '::1', '2025-08-26 16:09:38'),
(14, 3, 'login', 'Inicio de sesión', '::1', '2025-08-26 16:10:05'),
(15, 3, 'register_user', 'Gerente creó usuario: 1@gmail.com (rol: auditor)', '::1', '2025-08-26 17:12:50'),
(16, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-26 17:16:52'),
(17, 4, 'login', 'Inicio de sesión', '::1', '2025-08-26 17:17:01'),
(18, 4, 'logout', 'Cierre de sesión', '::1', '2025-08-26 17:21:17'),
(19, 2, 'login', 'Inicio de sesión', '::1', '2025-08-26 17:21:27'),
(20, 2, 'logout', 'Cierre de sesión', '::1', '2025-08-26 17:21:31'),
(21, 3, 'login', 'Inicio de sesión', '::1', '2025-08-26 17:21:43'),
(22, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-26 17:22:05'),
(23, 2, 'login', 'Inicio de sesión', '::1', '2025-08-26 17:22:15'),
(24, 2, 'loan_payment', 'Pago de préstamo ID 4: S/ 0.41', '::1', '2025-08-26 17:49:42'),
(25, 2, 'loan_payment', 'Pago de préstamo ID 4: S/ 100', '::1', '2025-08-26 17:49:57'),
(26, 2, 'loan_payment', 'Pago de préstamo ID 2: S/ 150', '::1', '2025-08-26 17:50:11'),
(27, 2, 'loan_payment', 'Pago de préstamo ID 4: S/ 29.5', '::1', '2025-08-26 17:51:07'),
(28, 2, 'loan_payment', 'Pago de préstamo ID 1: S/ 43', '::1', '2025-08-26 17:51:36'),
(29, 2, 'logout', 'Cierre de sesión', '::1', '2025-08-27 14:40:01'),
(30, 3, 'login', 'Inicio de sesión', '::1', '2025-08-27 14:40:14'),
(31, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-27 17:16:27'),
(32, 3, 'login', 'Inicio de sesión', '::1', '2025-08-27 17:16:47'),
(33, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-27 17:18:45'),
(34, 3, 'login', 'Inicio de sesión', '::1', '2025-08-27 17:19:01'),
(35, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 13:29:31'),
(36, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 13:31:21'),
(37, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 13:31:30'),
(38, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 13:31:34'),
(39, 2, 'login', 'Inicio de sesión', '::1', '2025-08-28 13:31:43'),
(40, 2, 'logout', 'Cierre de sesión', '::1', '2025-08-28 13:31:56'),
(41, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 13:32:02'),
(42, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 13:34:47'),
(43, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 13:58:49'),
(44, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 13:58:55'),
(45, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 14:42:55'),
(46, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 14:46:01'),
(47, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 15:16:06'),
(48, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 15:16:11'),
(49, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 16:37:02'),
(50, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 16:38:36'),
(51, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 16:38:46'),
(52, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 16:38:52'),
(53, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 16:47:06'),
(54, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 16:47:14'),
(55, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 16:59:10'),
(56, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:01:47'),
(57, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:04:52'),
(58, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:04:57'),
(59, 2, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:05:06'),
(60, 2, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:13:12'),
(61, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:13:25'),
(62, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:13:30'),
(63, 2, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:13:37'),
(64, 2, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:13:39'),
(65, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:14:46'),
(66, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:15:42'),
(67, 4, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:17:59'),
(68, 4, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:24:44'),
(69, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:24:51'),
(70, 3, 'register_user', 'Gerente creó usuario: marco1@gamil.com (rol: cliente)', '::1', '2025-08-28 17:25:10'),
(71, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:25:21'),
(72, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:25:32'),
(73, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:31:54'),
(74, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:32:01'),
(75, 3, 'register_user', 'Gerente creó usuario: marco2@gamil.com (rol: ejecutivo)', '::1', '2025-08-28 17:32:27'),
(76, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:32:34'),
(77, 6, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:32:44'),
(78, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:43:16'),
(79, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:43:21'),
(80, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:43:35'),
(81, 6, 'open_account', 'Cuenta BR252010383 creada para admin@univisa.edu', '::1', '2025-08-28 17:45:21'),
(82, 6, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:45:33'),
(83, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:47:34'),
(84, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:48:35'),
(85, 6, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:48:45'),
(86, 6, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:48:52'),
(87, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:49:03'),
(88, 5, 'loan_request', 'Préstamo solicitado: S/ 1000 a 10% por 6 meses', '::1', '2025-08-28 17:50:09'),
(89, 5, 'loan_request', 'Préstamo solicitado: S/ 1000 a 10% por 6 meses', '::1', '2025-08-28 17:50:22'),
(90, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:50:49'),
(91, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:50:55'),
(92, 3, 'reject_loan', 'Préstamo ID 7 rechazado', '::1', '2025-08-28 17:51:09'),
(93, 3, 'approve_loan', 'Préstamo ID 6 aprobado', '::1', '2025-08-28 17:51:12'),
(94, 3, 'set_interest', 'Interés de BR253573957 configurado a 10%', '::1', '2025-08-28 17:52:56'),
(95, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:53:01'),
(96, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:53:07'),
(97, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:53:10'),
(98, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:53:16'),
(99, 5, 'loan_payment', 'Pago de préstamo ID 6: S/ 10.2', '::1', '2025-08-28 17:54:00'),
(100, 5, 'loan_payment', 'Pago de préstamo ID 6: S/ 10.2', '::1', '2025-08-28 17:54:21'),
(101, 5, 'loan_payment', 'Pago de préstamo ID 6: S/ 970', '::1', '2025-08-28 17:54:44'),
(102, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:56:10'),
(103, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:56:15'),
(104, 3, 'set_interest', 'Interés de BR253573957 configurado a 15%', '::1', '2025-08-28 17:56:59'),
(105, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 17:57:04'),
(106, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 17:57:08'),
(107, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:20:46'),
(108, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:21:17'),
(109, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:21:22'),
(110, 3, 'set_interest', 'Interés de BR253573957 configurado a 10%', '::1', '2025-08-28 22:21:41'),
(111, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:21:48'),
(112, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:21:51'),
(113, 5, 'loan_request', 'Préstamo solicitado: S/ 150 a 10% por 6 meses', '::1', '2025-08-28 22:28:17'),
(114, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:28:42'),
(115, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:28:46'),
(116, 3, 'approve_loan', 'Préstamo ID 8 aprobado', '::1', '2025-08-28 22:28:55'),
(117, 3, 'set_interest', 'Interés de BR253573957 configurado a 10%', '::1', '2025-08-28 22:29:26'),
(118, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:30:05'),
(119, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:30:10'),
(120, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:30:14'),
(121, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:30:20'),
(122, 5, 'loan_request', 'Préstamo solicitado: S/ 150 a 10% por 6 meses', '::1', '2025-08-28 22:30:57'),
(123, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:31:00'),
(124, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:31:06'),
(125, 3, 'approve_loan', 'Préstamo ID 9 aprobado', '::1', '2025-08-28 22:31:13'),
(126, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:31:18'),
(127, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:31:19'),
(128, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:39:03'),
(129, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:39:14'),
(130, 3, 'set_interest', 'Interés de BR252965579 configurado a 15%', '::1', '2025-08-28 22:39:41'),
(131, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:39:46'),
(132, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:39:50'),
(133, 5, 'loan_request', 'Préstamo solicitado: S/ 100 a 10% por 6 meses', '::1', '2025-08-28 22:40:01'),
(134, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:40:05'),
(135, 3, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:40:12'),
(136, 3, 'approve_loan', 'Préstamo ID 10 aprobado', '::1', '2025-08-28 22:40:17'),
(137, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-28 22:40:26'),
(138, 5, 'login', 'Inicio de sesión', '::1', '2025-08-28 22:40:28'),
(139, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-29 01:00:19'),
(140, 5, 'login', 'Inicio de sesión', '::1', '2025-08-29 01:00:22'),
(141, 5, 'loan_request', 'Préstamo solicitado: S/ 100 a 15.00% (compuesto) por 6 meses', '::1', '2025-08-29 01:01:52'),
(142, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-29 01:01:58'),
(143, 3, 'login', 'Inicio de sesión', '::1', '2025-08-29 01:02:03'),
(144, 3, 'approve_loan', 'Préstamo ID 11 aprobado', '::1', '2025-08-29 01:02:10'),
(145, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-29 01:02:14'),
(146, 5, 'login', 'Inicio de sesión', '::1', '2025-08-29 01:02:21'),
(147, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-29 13:57:04'),
(148, 3, 'login', 'Inicio de sesión', '::1', '2025-08-29 13:57:13'),
(149, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-29 13:57:36'),
(150, 6, 'login', 'Inicio de sesión', '::1', '2025-08-29 13:57:44'),
(151, 6, 'logout', 'Cierre de sesión', '::1', '2025-08-29 13:58:29'),
(152, 4, 'login', 'Inicio de sesión', '::1', '2025-08-29 13:58:54'),
(153, 4, 'logout', 'Cierre de sesión', '::1', '2025-08-29 17:34:59'),
(154, 5, 'login', 'Inicio de sesión', '::1', '2025-08-29 17:35:02'),
(155, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-29 17:35:35'),
(156, 5, 'login', 'Inicio de sesión', '::1', '2025-08-29 17:36:33'),
(157, 5, 'loan_payment', 'Pago de préstamo ID 10: S/ 10', '::1', '2025-08-30 01:31:12'),
(158, 5, 'loan_payment', 'Pago de préstamo ID 10: S/ 10', '::1', '2025-08-30 01:35:27'),
(159, 5, 'loan_request', 'Préstamo solicitado: S/ 120 a 15.00% (compuesto) por 6 meses', '::1', '2025-08-30 01:35:39'),
(160, 5, 'loan_request', 'Préstamo solicitado: S/ 120 a 15.00% (compuesto) por 6 meses', '::1', '2025-08-30 01:36:04'),
(161, 5, 'loan_request', 'Préstamo solicitado: S/ 120 a 15.00% (compuesto) por 6 meses', '::1', '2025-08-30 01:36:32'),
(162, 5, 'loan_request', 'Préstamo solicitado: S/ 120 a 15.00% (compuesto) por 6 meses', '::1', '2025-08-30 01:36:43'),
(163, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-30 01:36:52'),
(164, 5, 'login', 'Inicio de sesión', '::1', '2025-08-30 01:37:39'),
(165, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-30 01:37:41'),
(166, 5, 'login', 'Inicio de sesión', '::1', '2025-08-30 01:37:43'),
(167, 5, 'logout', 'Cierre de sesión', '::1', '2025-08-30 01:39:49'),
(168, 3, 'login', 'Inicio de sesión', '::1', '2025-08-30 01:39:54'),
(169, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-30 01:39:57'),
(170, 3, 'login', 'Inicio de sesión', '::1', '2025-08-30 01:40:02'),
(171, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-30 01:40:07'),
(172, 3, 'login', 'Inicio de sesión', '::1', '2025-08-30 01:40:14'),
(173, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-30 01:40:38'),
(174, 3, 'login', 'Inicio de sesión', '::1', '2025-08-30 01:41:23'),
(175, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-30 01:45:44'),
(176, 3, 'login', 'Inicio de sesión', '::1', '2025-08-30 01:45:47'),
(177, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-30 01:45:52'),
(178, 3, 'login', 'Inicio de sesión', '::1', '2025-08-30 01:46:05'),
(179, 3, 'login', 'Inicio de sesión', '::1', '2025-08-30 01:47:17'),
(180, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-30 01:51:20'),
(181, 6, 'login', 'Inicio de sesión', '::1', '2025-08-30 01:52:42'),
(182, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-30 02:00:43'),
(183, 3, 'login', 'Inicio de sesión', '::1', '2025-08-30 02:00:48'),
(184, 3, 'approve_loan', 'Préstamo ID 15 aprobado', '::1', '2025-08-30 02:00:59'),
(185, 3, 'reject_loan', 'Préstamo ID 14 rechazado', '::1', '2025-08-30 02:01:02'),
(186, 3, 'reject_loan', 'Préstamo ID 13 rechazado', '::1', '2025-08-30 02:01:03'),
(187, 3, 'approve_loan', 'Préstamo ID 12 aprobado', '::1', '2025-08-30 02:01:05'),
(188, 3, 'assign_role', 'Rol de marco1@gamil.com cambiado a cajero', '::1', '2025-08-30 02:01:30'),
(189, 3, 'assign_role', 'Rol de marco1@gamil.com cambiado a cliente', '::1', '2025-08-30 02:01:37'),
(190, 3, 'set_interest', 'Interés de BR252965579 configurado a 15%', '::1', '2025-08-30 02:01:48'),
(191, 3, 'register_user', 'Gerente creó usuario: marco3@gamil.com (rol: cliente)', '::1', '2025-08-30 02:06:38'),
(192, 3, 'logout', 'Cierre de sesión', '::1', '2025-08-30 02:06:56'),
(193, 7, 'login', 'Inicio de sesión', '::1', '2025-08-30 02:07:01'),
(194, 7, 'logout', 'Cierre de sesión', '::1', '2025-08-30 02:07:07'),
(195, 6, 'login', 'Inicio de sesión', '::1', '2025-08-30 02:07:11'),
(196, 6, 'logout', 'Cierre de sesión', '::1', '2025-08-30 02:07:20'),
(197, 4, 'login', 'Inicio de sesión', '::1', '2025-08-30 02:26:08'),
(198, 6, 'login', 'Inicio de sesión', '::1', '2025-09-01 13:44:37'),
(199, 6, 'logout', 'Cierre de sesión', '::1', '2025-09-01 13:44:44'),
(200, 3, 'login', 'Inicio de sesión', '::1', '2025-09-01 13:47:04'),
(201, 5, 'login', 'Inicio de sesión', '::1', '2025-09-01 15:13:13'),
(202, 5, 'loan_payment', 'Pago de préstamo ID 15: S/ 10', '::1', '2025-09-01 15:13:25'),
(203, 5, 'loan_payment', 'Pago de préstamo ID 15: S/ 10', '::1', '2025-09-01 15:13:42'),
(204, 5, 'loan_payment', 'Pago de préstamo ID 15: S/ 11', '::1', '2025-09-01 15:20:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE `cuentas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `numero_cuenta` varchar(20) NOT NULL,
  `saldo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_interes` enum('simple','compuesto') DEFAULT 'simple',
  `tasa_interes` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas`
--

INSERT INTO `cuentas` (`id`, `usuario_id`, `numero_cuenta`, `saldo`, `creado_en`, `tipo_interes`, `tasa_interes`) VALUES
(1, 1, 'BR256858175', 0.00, '2025-08-26 14:33:29', 'simple', 0.00),
(2, 2, 'BR257187004', 0.09, '2025-08-26 14:34:46', 'simple', 0.00),
(3, 4, 'BR250767724', 0.00, '2025-08-26 17:12:50', 'simple', 0.00),
(4, 5, 'BR252965579', 554.60, '2025-08-28 17:25:10', 'compuesto', 15.00),
(5, 6, 'BR253573957', 0.00, '2025-08-28 17:32:27', 'compuesto', 10.00),
(6, 2, 'BR252010383', 0.00, '2025-08-28 17:45:21', 'simple', 0.00),
(7, 7, 'BR251116416', 0.00, '2025-08-30 02:06:38', 'simple', 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id` int(11) NOT NULL,
  `cuenta_solicitante_id` int(11) NOT NULL,
  `monto_principal` decimal(12,2) NOT NULL,
  `tasa_interes` decimal(5,2) NOT NULL,
  `plazo_meses` int(11) NOT NULL,
  `total_a_pagar` decimal(12,2) NOT NULL,
  `estado` enum('pending','approved','rejected','paid') DEFAULT 'pending',
  `aprobado_por` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id`, `cuenta_solicitante_id`, `monto_principal`, `tasa_interes`, `plazo_meses`, `total_a_pagar`, `estado`, `aprobado_por`, `creado_en`) VALUES
(1, 2, 100.00, 9.86, 10, 65.22, 'approved', 3, '2025-08-26 15:00:37'),
(2, 2, 100.00, 9.86, 10, 0.00, 'paid', 3, '2025-08-26 15:07:51'),
(3, 2, 123.00, 10.00, 6, 129.15, 'rejected', 3, '2025-08-26 15:32:50'),
(4, 2, 123.00, 10.00, 6, 0.00, 'paid', 3, '2025-08-26 15:42:54'),
(5, 2, 150.00, 10.00, 6, 157.50, 'rejected', 3, '2025-08-26 15:55:27'),
(6, 4, 1000.00, 10.00, 6, 59.60, 'approved', 3, '2025-08-28 17:50:09'),
(7, 4, 1000.00, 10.00, 6, 1050.00, 'rejected', 3, '2025-08-28 17:50:22'),
(8, 4, 150.00, 10.00, 6, 157.50, 'approved', 3, '2025-08-28 22:28:17'),
(9, 4, 150.00, 10.00, 6, 157.50, 'approved', 3, '2025-08-28 22:30:57'),
(10, 4, 100.00, 10.00, 6, 51.00, 'approved', 3, '2025-08-28 22:40:01'),
(11, 4, 100.00, 15.00, 6, 0.00, 'paid', 3, '2025-08-29 01:01:52'),
(12, 4, 120.00, 15.00, 6, 129.29, 'approved', 3, '2025-08-30 01:35:39'),
(13, 4, 120.00, 15.00, 6, 129.29, 'rejected', 3, '2025-08-30 01:36:03'),
(14, 4, 120.00, 15.00, 6, 129.29, 'rejected', 3, '2025-08-30 01:36:32'),
(15, 4, 120.00, 15.00, 6, 98.29, 'approved', 3, '2025-08-30 01:36:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transacciones`
--

CREATE TABLE `transacciones` (
  `id` int(11) NOT NULL,
  `cuenta_id` int(11) NOT NULL,
  `tipo` enum('deposit','withdraw','transfer_in','transfer_out','loan_disbursement','loan_payment') NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `creado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transacciones`
--

INSERT INTO `transacciones` (`id`, `cuenta_id`, `tipo`, `monto`, `descripcion`, `creado_en`, `creado_por`) VALUES
(1, 2, 'loan_disbursement', 100.00, 'Desembolso de préstamo aprobado', '2025-08-26 16:34:15', 3),
(2, 2, 'loan_disbursement', 123.00, 'Desembolso de préstamo aprobado', '2025-08-26 16:50:30', 3),
(3, 2, 'loan_disbursement', 100.00, 'Desembolso de préstamo aprobado', '2025-08-26 16:50:37', 3),
(4, 2, 'loan_payment', 0.41, 'Pago préstamo ID 4', '2025-08-26 17:49:42', 2),
(5, 2, 'loan_payment', 100.00, 'Pago préstamo ID 4', '2025-08-26 17:49:57', 2),
(6, 2, 'loan_payment', 150.00, 'Pago préstamo ID 2', '2025-08-26 17:50:11', 2),
(7, 2, 'loan_payment', 29.50, 'Pago préstamo ID 4', '2025-08-26 17:51:07', 2),
(8, 2, 'loan_payment', 43.00, 'Pago préstamo ID 1', '2025-08-26 17:51:36', 2),
(9, 4, 'loan_disbursement', 1000.00, 'Desembolso de préstamo aprobado', '2025-08-28 17:51:12', 3),
(10, 4, 'loan_payment', 10.20, 'Pago préstamo ID 6', '2025-08-28 17:54:00', 5),
(11, 4, 'loan_payment', 10.20, 'Pago préstamo ID 6', '2025-08-28 17:54:20', 5),
(12, 4, 'loan_payment', 970.00, 'Pago préstamo ID 6', '2025-08-28 17:54:44', 5),
(13, 4, 'loan_disbursement', 150.00, 'Desembolso de préstamo aprobado', '2025-08-28 22:28:55', 3),
(14, 4, 'loan_disbursement', 150.00, 'Desembolso de préstamo aprobado', '2025-08-28 22:31:13', 3),
(15, 4, 'loan_disbursement', 100.00, 'Desembolso de préstamo aprobado', '2025-08-28 22:40:17', 3),
(16, 4, 'loan_disbursement', 100.00, 'Desembolso de préstamo aprobado', '2025-08-29 01:02:10', 3),
(17, 4, 'loan_payment', 10.00, 'Pago préstamo ID 11', '2025-08-30 01:02:47', 5),
(18, 4, 'loan_payment', 100.00, 'Pago préstamo ID 11', '2025-08-30 01:03:30', 5),
(19, 4, 'loan_payment', 12.00, 'Pago préstamo ID 10', '2025-08-30 01:13:43', 5),
(20, 4, 'loan_payment', 12.00, 'Pago préstamo ID 10', '2025-08-30 01:25:48', 5),
(21, 4, 'loan_payment', 10.00, 'Pago préstamo ID 10', '2025-08-30 01:25:59', 5),
(22, 4, 'loan_payment', 10.00, 'Pago préstamo ID 10', '2025-08-30 01:31:12', 5),
(23, 4, 'loan_payment', 10.00, 'Pago préstamo ID 10', '2025-08-30 01:35:27', 5),
(24, 4, 'loan_disbursement', 120.00, 'Desembolso de préstamo aprobado', '2025-08-30 02:00:59', 3),
(25, 4, 'loan_disbursement', 120.00, 'Desembolso de préstamo aprobado', '2025-08-30 02:01:05', 3),
(26, 4, 'loan_payment', 10.00, 'Pago préstamo ID 15', '2025-09-01 15:13:25', 5),
(27, 4, 'loan_payment', 10.00, 'Pago préstamo ID 15', '2025-09-01 15:13:42', 5),
(28, 4, 'loan_payment', 11.00, 'Pago préstamo ID 15', '2025-09-01 15:20:04', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `contraseña_hash` varchar(255) NOT NULL,
  `rol` enum('cliente','cajero','ejecutivo','gerente','auditor') DEFAULT 'cliente',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `contraseña_hash`, `rol`, `creado_en`) VALUES
(1, 'Marco', 'marcoespinoza12h@gmail.com', '$2y$10$qITInGlHz3kGRXscEVm1peOfy0rCyXE/CtyvhbmwzAxAK03wKoTOS', 'cliente', '2025-08-26 14:33:29'),
(2, 'Marco', 'admin@univisa.edu', '$2y$10$0MJ.lZahLQZuyWLnPi16oe7sAwzM3rd2sqPkrrajnLc7X59DhCU/y', 'cajero', '2025-08-26 14:34:46'),
(3, 'doc', 'marco@gamil.com', '$2y$10$0MJ.lZahLQZuyWLnPi16oe7sAwzM3rd2sqPkrrajnLc7X59DhCU/y', 'gerente', '2025-08-26 16:08:04'),
(4, 'Marck', '1@gmail.com', '$2y$10$Sb.A7QQPTHaIYZClmvhWSeJRBIpKk1lwelmUF7C5UbLWEvs3pdhyW', 'auditor', '2025-08-26 17:12:50'),
(5, 'Marck', 'marco1@gamil.com', '$2y$10$jz6ztPf/QO.pEgIXHVn5puP/9CUMTMx9xZGmUBBgylvCjcVyODoBK', 'cliente', '2025-08-28 17:25:10'),
(6, 'Marck', 'marco2@gamil.com', '$2y$10$PVN/MX4Y5VFbY6QEMk9MQuPLAzAin02gY4dzDb6tFxeKMX6IrIlVe', 'ejecutivo', '2025-08-28 17:32:27'),
(7, 'Marco', 'marco3@gamil.com', '$2y$10$hcuaHH3QMV9.tYsaMit/fOno9W2x2nwSG/ylnfqy/.zDJjQv4bqKC', 'cliente', '2025-08-30 02:06:38');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`usuario_id`);

--
-- Indices de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_number` (`numero_cuenta`),
  ADD KEY `user_id` (`usuario_id`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrower_account_id` (`cuenta_solicitante_id`),
  ADD KEY `approved_by` (`aprobado_por`);

--
-- Indices de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`cuenta_id`),
  ADD KEY `created_by` (`creado_por`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `transacciones`
--
ALTER TABLE `transacciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD CONSTRAINT `cuentas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`cuenta_solicitante_id`) REFERENCES `cuentas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `transacciones`
--
ALTER TABLE `transacciones`
  ADD CONSTRAINT `transacciones_ibfk_1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transacciones_ibfk_2` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
