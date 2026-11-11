-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 10-11-2026 a las 03:08:51
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_reservas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos`
--

CREATE TABLE `recursos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('sala','laboratorio','equipo') NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `cantidad_total` int(11) NOT NULL DEFAULT 1,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recursos`
--

INSERT INTO `recursos` (`id`, `nombre`, `tipo`, `descripcion`, `imagen`, `cantidad_total`, `activo`) VALUES
(1, 'Biblioteca', 'sala', 'Sala de lectura y estudio', NULL, 1, 1),
(2, 'Sala de Informática', 'sala', 'Sala con computadoras', NULL, 1, 1),
(3, 'Sala de Conferencias', 'sala', 'Sala para eventos y conferencias', NULL, 1, 1),
(4, 'Laboratorio de Física', 'laboratorio', 'Laboratorio con equipos de física', NULL, 1, 1),
(5, 'Laboratorio de Química', 'laboratorio', 'Laboratorio con equipos de química', NULL, 1, 1),
(6, 'Laboratorio de Ciencias', 'laboratorio', 'Laboratorio general de ciencias', NULL, 1, 1),
(7, 'Proyector portátil', 'equipo', 'Proyectores EPSON HD', NULL, 5, 1),
(8, 'Televisión 55\"', 'equipo', 'Televisión de 55 pulgadas', NULL, 2, 1),
(9, 'Alargue eléctrico', 'equipo', 'Alargue de 5 metros con 4 tomas', NULL, 10, 1),
(14, 'Portatiles', 'equipo', 'Computadora portatil sargas de ceibal', 'uploads/equipos/1791828885_Sargas-1.jpg', 10, 1),
(15, 'Sala de informatica 2', 'sala', 'Sala con computadoras, hubicada al lado de la 1', NULL, 1, 1),
(19, 'Laboratorio de ciencias 2', 'laboratorio', 'Laboratorio de ciencas generales', NULL, 1, 1),
(20, 'Laboratorio de Fisica 2', 'laboratorio', 'Laboratorio con equipos de física', NULL, 1, 1),
(22, 'Laboratorio de Quimica 2', 'equipo', 'Laboratorio con equipos de quimica', NULL, 1, 1),
(24, 'Laboratorio de Quimica 2', 'laboratorio', 'Laboratorio con equipamiento de quimica', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_recurso` int(11) NOT NULL,
  `cantidad_solicitada` int(11) NOT NULL DEFAULT 1,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `motivo_profesor` text DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  `motivo_rechazo` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `id_usuario`, `id_recurso`, `cantidad_solicitada`, `fecha`, `hora_inicio`, `hora_fin`, `motivo_profesor`, `estado`, `motivo_rechazo`, `observaciones`, `created_at`) VALUES
(7, 3, 6, 1, '2026-10-23', '13:00:00', '15:00:00', NULL, 'cancelada', NULL, NULL, '2026-10-07 14:08:04'),
(8, 3, 8, 1, '2026-10-23', '13:00:00', '15:00:00', NULL, 'cancelada', NULL, NULL, '2026-10-07 14:08:04'),
(9, 2, 9, 1, '2026-10-11', '08:00:00', '10:00:00', NULL, 'rechazada', 'amenaza de bomba', NULL, '2026-10-10 17:01:42'),
(10, 2, 7, 1, '2026-10-11', '16:00:00', '16:45:00', NULL, 'cancelada', NULL, NULL, '2026-10-10 18:28:51'),
(11, 2, 7, 1, '2026-10-11', '16:45:00', '17:30:00', NULL, 'cancelada', NULL, NULL, '2026-10-10 18:28:51'),
(12, 2, 7, 1, '2026-10-11', '17:30:00', '18:15:00', NULL, 'cancelada', NULL, NULL, '2026-10-10 18:28:51'),
(13, 2, 3, 1, '2026-10-11', '13:00:00', '13:45:00', NULL, 'rechazada', 'amenaza de bomba', NULL, '2026-10-10 18:59:44'),
(14, 2, 3, 1, '2026-10-11', '13:45:00', '14:30:00', NULL, 'rechazada', 'amenaza de bomba', NULL, '2026-10-10 18:59:44'),
(15, 2, 3, 1, '2026-10-12', '13:00:00', '13:45:00', NULL, 'rechazada', 'porque pinto', NULL, '2026-10-12 00:18:12'),
(16, 2, 3, 1, '2026-10-12', '13:45:00', '14:30:00', NULL, 'rechazada', 'porque pinto', NULL, '2026-10-12 00:18:12'),
(17, 3, 2, 1, '2026-10-12', '10:45:00', '11:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 01:03:14'),
(18, 3, 2, 1, '2026-10-12', '11:30:00', '12:15:00', 'sala de informática para hacer prácticos y proyector para dar teórico del practico y explicar consigna', 'rechazada', 'sisiasiaisasi', NULL, '2026-10-12 01:03:14'),
(19, 3, 7, 1, '2026-10-12', '10:45:00', '11:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 01:03:14'),
(20, 3, 7, 1, '2026-10-12', '11:30:00', '12:15:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 01:03:14'),
(21, 3, 2, 1, '2026-10-12', '10:45:00', '11:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 01:03:14'),
(22, 3, 2, 1, '2026-10-12', '11:30:00', '12:15:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 01:03:14'),
(23, 3, 7, 1, '2026-10-12', '10:45:00', '11:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 01:03:14'),
(24, 3, 7, 1, '2026-10-12', '11:30:00', '12:15:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 01:03:14'),
(25, 3, 3, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 18:21:12'),
(26, 3, 3, 1, '2026-10-13', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 18:21:12'),
(27, 3, 14, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 18:21:12'),
(28, 3, 14, 1, '2026-10-13', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 18:21:12'),
(29, 3, 3, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 18:21:12'),
(30, 3, 3, 1, '2026-10-13', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 18:21:12'),
(31, 3, 14, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 18:21:12'),
(32, 3, 14, 1, '2026-10-13', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-12 18:21:12'),
(33, 3, 3, 1, '2026-10-13', '14:30:00', '15:15:00', 'si', 'rechazada', 'sisarisas', NULL, '2026-10-12 18:24:30'),
(34, 3, 3, 1, '2026-10-13', '14:30:00', '15:15:00', 'si', 'rechazada', 'sisarisas', NULL, '2026-10-12 18:24:30'),
(37, 3, 3, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:06:13'),
(38, 3, 3, 1, '2026-10-13', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:06:13'),
(39, 3, 3, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:06:13'),
(40, 3, 3, 1, '2026-10-13', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:06:13'),
(41, 3, 3, 1, '2026-10-13', '12:15:00', '13:00:00', '', 'cancelada', NULL, NULL, '2026-10-13 02:40:17'),
(42, 3, 3, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:40:17'),
(43, 3, 9, 1, '2026-10-13', '12:15:00', '13:00:00', '', 'cancelada', NULL, NULL, '2026-10-13 02:40:17'),
(44, 3, 9, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:40:17'),
(45, 3, 3, 1, '2026-10-13', '12:15:00', '13:00:00', '', 'cancelada', NULL, NULL, '2026-10-13 02:40:17'),
(46, 3, 3, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:40:17'),
(47, 3, 9, 1, '2026-10-13', '12:15:00', '13:00:00', '', 'cancelada', NULL, NULL, '2026-10-13 02:40:17'),
(48, 3, 9, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:40:17'),
(49, 3, 3, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:49:15'),
(50, 3, 3, 1, '2026-10-13', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:49:15'),
(51, 3, 14, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:49:15'),
(52, 3, 14, 1, '2026-10-13', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:49:15'),
(53, 3, 3, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:49:15'),
(54, 3, 3, 1, '2026-10-13', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:49:15'),
(55, 3, 14, 1, '2026-10-13', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:49:15'),
(56, 3, 14, 1, '2026-10-13', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-13 02:49:15'),
(57, 3, 3, 1, '2026-10-22', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:19'),
(58, 3, 3, 1, '2026-10-22', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:19'),
(59, 3, 9, 1, '2026-10-22', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:19'),
(60, 3, 9, 1, '2026-10-22', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:19'),
(61, 3, 3, 1, '2026-10-22', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:19'),
(62, 3, 3, 1, '2026-10-22', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:19'),
(63, 3, 9, 1, '2026-10-22', '13:00:00', '13:45:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:19'),
(64, 3, 9, 1, '2026-10-22', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:19'),
(65, 3, 1, 1, '2026-10-23', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:55'),
(66, 3, 1, 1, '2026-10-23', '14:30:00', '15:15:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:55'),
(67, 3, 9, 1, '2026-10-23', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:55'),
(68, 3, 9, 1, '2026-10-23', '14:30:00', '15:15:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:55'),
(69, 3, 1, 1, '2026-10-23', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:55'),
(70, 3, 1, 1, '2026-10-23', '14:30:00', '15:15:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:55'),
(71, 3, 9, 1, '2026-10-23', '13:45:00', '14:30:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:55'),
(72, 3, 9, 1, '2026-10-23', '14:30:00', '15:15:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:55'),
(73, 3, 1, 1, '2026-10-30', '08:30:00', '09:15:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-10-29 18:19:27'),
(74, 3, 1, 1, '2026-10-30', '09:15:00', '10:00:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-10-29 18:19:27'),
(75, 3, 9, 1, '2026-10-30', '08:30:00', '09:15:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-10-29 18:19:27'),
(76, 3, 9, 1, '2026-10-30', '09:15:00', '10:00:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-10-29 18:19:27'),
(77, 3, 1, 1, '2026-10-30', '08:30:00', '09:15:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-10-29 18:19:27'),
(78, 3, 1, 1, '2026-10-30', '09:15:00', '10:00:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-10-29 18:19:27'),
(79, 3, 9, 1, '2026-10-30', '08:30:00', '09:15:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-10-29 18:19:27'),
(80, 3, 9, 1, '2026-10-30', '09:15:00', '10:00:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-10-29 18:19:27'),
(81, 3, 3, 1, '2026-10-30', '13:00:00', '13:45:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-10-29 19:33:54'),
(82, 3, 14, 1, '2026-10-30', '13:00:00', '13:45:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-10-29 19:33:54'),
(83, 3, 6, 1, '2026-11-02', '09:15:00', '10:00:00', 'vamo a explotar todo a la mierda', 'rechazada', 'si', NULL, '2026-10-29 19:36:36'),
(84, 3, 6, 1, '2026-11-02', '10:00:00', '10:45:00', 'vamo a explotar todo a la mierda', 'pendiente', NULL, NULL, '2026-10-29 19:36:36'),
(85, 3, 6, 1, '2026-11-02', '10:45:00', '11:30:00', 'vamo a explotar todo a la mierda', 'pendiente', NULL, NULL, '2026-10-29 19:36:36'),
(86, 3, 6, 1, '2026-11-02', '11:30:00', '12:15:00', 'vamo a explotar todo a la mierda', 'pendiente', NULL, NULL, '2026-10-29 19:36:36'),
(87, 3, 9, 1, '2026-11-02', '09:15:00', '10:00:00', 'vamo a explotar todo a la mierda', 'rechazada', 'si', NULL, '2026-10-29 19:36:36'),
(88, 3, 9, 1, '2026-11-02', '10:00:00', '10:45:00', 'vamo a explotar todo a la mierda', 'pendiente', NULL, NULL, '2026-10-29 19:36:36'),
(89, 3, 9, 1, '2026-11-02', '10:45:00', '11:30:00', 'vamo a explotar todo a la mierda', 'pendiente', NULL, NULL, '2026-10-29 19:36:36'),
(90, 3, 9, 1, '2026-11-02', '11:30:00', '12:15:00', 'vamo a explotar todo a la mierda', 'pendiente', NULL, NULL, '2026-10-29 19:36:36'),
(91, 2, 4, 1, '2026-11-18', '08:30:00', '09:15:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-11-02 19:14:50'),
(92, 2, 4, 1, '2026-11-18', '09:15:00', '10:00:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-11-02 19:14:50'),
(93, 2, 4, 1, '2026-11-18', '10:00:00', '10:45:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-11-02 19:14:50'),
(94, 2, 4, 1, '2026-11-18', '10:45:00', '11:30:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-11-02 19:14:50'),
(95, 2, 8, 1, '2026-11-18', '08:30:00', '09:15:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-11-02 19:14:50'),
(96, 2, 8, 1, '2026-11-18', '09:15:00', '10:00:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-11-02 19:14:50'),
(97, 2, 8, 1, '2026-11-18', '10:00:00', '10:45:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-11-02 19:14:50'),
(98, 2, 8, 1, '2026-11-18', '10:45:00', '11:30:00', 'Cancelada por el profesor desde calendario', 'cancelada', NULL, NULL, '2026-11-02 19:14:50'),
(99, 2, 3, 1, '2026-11-10', '10:00:00', '10:45:00', 'si', 'pendiente', NULL, NULL, '2026-11-02 19:35:22'),
(100, 2, 3, 1, '2026-11-10', '10:45:00', '11:30:00', 'si', 'pendiente', NULL, NULL, '2026-11-02 19:35:22'),
(101, 2, 9, 1, '2026-11-10', '10:00:00', '10:45:00', 'si', 'pendiente', NULL, NULL, '2026-11-02 19:35:22'),
(102, 2, 9, 1, '2026-11-10', '10:45:00', '11:30:00', 'si', 'pendiente', NULL, NULL, '2026-11-02 19:35:22'),
(103, 2, 3, 1, '2026-11-10', '10:00:00', '10:45:00', 'si', 'pendiente', NULL, NULL, '2026-11-02 19:35:22'),
(104, 2, 3, 1, '2026-11-10', '10:45:00', '11:30:00', 'si', 'pendiente', NULL, NULL, '2026-11-02 19:35:22'),
(105, 2, 9, 1, '2026-11-10', '10:00:00', '10:45:00', 'si', 'pendiente', NULL, NULL, '2026-11-02 19:35:22'),
(106, 2, 9, 1, '2026-11-10', '10:45:00', '11:30:00', 'si', 'pendiente', NULL, NULL, '2026-11-02 19:35:22'),
(107, 3, 1, 1, '2026-11-04', '07:45:00', '08:30:00', 'a', 'pendiente', NULL, NULL, '2026-11-02 20:04:10'),
(108, 3, 1, 1, '2026-11-04', '08:30:00', '09:15:00', 'a', 'pendiente', NULL, NULL, '2026-11-02 20:04:10'),
(109, 3, 9, 1, '2026-11-04', '07:45:00', '08:30:00', 'a', 'pendiente', NULL, NULL, '2026-11-02 20:04:10'),
(110, 3, 9, 1, '2026-11-04', '08:30:00', '09:15:00', 'a', 'pendiente', NULL, NULL, '2026-11-02 20:04:10'),
(111, 3, 1, 1, '2026-11-04', '07:45:00', '08:30:00', 'a', 'pendiente', NULL, NULL, '2026-11-02 20:04:10'),
(112, 3, 1, 1, '2026-11-04', '08:30:00', '09:15:00', 'a', 'pendiente', NULL, NULL, '2026-11-02 20:04:10'),
(113, 3, 9, 1, '2026-11-04', '07:45:00', '08:30:00', 'a', 'pendiente', NULL, NULL, '2026-11-02 20:04:10'),
(114, 3, 9, 1, '2026-11-04', '08:30:00', '09:15:00', 'a', 'pendiente', NULL, NULL, '2026-11-02 20:04:10'),
(115, 3, 1, 1, '2026-11-12', '10:00:00', '10:45:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:25:50'),
(116, 3, 1, 1, '2026-11-12', '10:45:00', '11:30:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:25:50'),
(117, 3, 9, 1, '2026-11-12', '10:00:00', '10:45:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:25:50'),
(118, 3, 9, 1, '2026-11-12', '10:45:00', '11:30:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:25:50'),
(119, 3, 1, 1, '2026-11-12', '10:00:00', '10:45:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:25:50'),
(120, 3, 1, 1, '2026-11-12', '10:45:00', '11:30:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:25:50'),
(121, 3, 9, 1, '2026-11-12', '10:00:00', '10:45:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:25:50'),
(122, 3, 9, 1, '2026-11-12', '10:45:00', '11:30:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:25:50'),
(123, 3, 1, 1, '2026-11-11', '08:30:00', '09:15:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:30:21'),
(124, 3, 1, 1, '2026-11-11', '09:15:00', '10:00:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(125, 3, 1, 1, '2026-11-11', '10:00:00', '10:45:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(126, 3, 1, 1, '2026-11-11', '10:45:00', '11:30:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(127, 3, 14, 1, '2026-11-11', '08:30:00', '09:15:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:30:21'),
(128, 3, 14, 1, '2026-11-11', '09:15:00', '10:00:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(129, 3, 14, 1, '2026-11-11', '10:00:00', '10:45:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(130, 3, 14, 1, '2026-11-11', '10:45:00', '11:30:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(131, 3, 1, 1, '2026-11-11', '08:30:00', '09:15:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:30:21'),
(132, 3, 1, 1, '2026-11-11', '09:15:00', '10:00:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(133, 3, 1, 1, '2026-11-11', '10:00:00', '10:45:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(134, 3, 1, 1, '2026-11-11', '10:45:00', '11:30:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(135, 3, 14, 1, '2026-11-11', '08:30:00', '09:15:00', '', 'pendiente', NULL, NULL, '2026-11-02 20:30:21'),
(136, 3, 14, 1, '2026-11-11', '09:15:00', '10:00:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(137, 3, 14, 1, '2026-11-11', '10:00:00', '10:45:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(138, 3, 14, 1, '2026-11-11', '10:45:00', '11:30:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:30:21'),
(139, 3, 2, 1, '2026-11-10', '09:15:00', '10:00:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:37:03'),
(140, 3, 2, 1, '2026-11-10', '10:00:00', '10:45:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:37:03'),
(141, 3, 14, 1, '2026-11-10', '09:15:00', '10:00:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:37:03'),
(142, 3, 14, 1, '2026-11-10', '10:00:00', '10:45:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:37:03'),
(143, 3, 2, 1, '2026-11-10', '09:15:00', '10:00:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:37:03'),
(144, 3, 2, 1, '2026-11-10', '10:00:00', '10:45:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:37:03'),
(145, 3, 14, 1, '2026-11-10', '09:15:00', '10:00:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:37:03'),
(146, 3, 14, 1, '2026-11-10', '10:00:00', '10:45:00', '', 'aprobada', NULL, NULL, '2026-11-02 20:37:03'),
(147, 3, 2, 1, '2026-11-18', '12:15:00', '13:00:00', '', 'rechazada', 'a', NULL, '2026-11-02 20:42:59'),
(148, 3, 2, 1, '2026-11-18', '13:00:00', '13:45:00', '', 'rechazada', 'a', NULL, '2026-11-02 20:42:59'),
(149, 3, 8, 1, '2026-11-18', '12:15:00', '13:00:00', '', 'rechazada', 'a', NULL, '2026-11-02 20:42:59'),
(150, 3, 8, 1, '2026-11-18', '13:00:00', '13:45:00', '', 'rechazada', 'a', NULL, '2026-11-02 20:42:59'),
(151, 3, 2, 1, '2026-11-18', '12:15:00', '13:00:00', '', 'rechazada', 'a', NULL, '2026-11-02 20:42:59'),
(152, 3, 2, 1, '2026-11-18', '13:00:00', '13:45:00', '', 'rechazada', 'a', NULL, '2026-11-02 20:42:59'),
(153, 3, 8, 1, '2026-11-18', '12:15:00', '13:00:00', '', 'rechazada', 'a', NULL, '2026-11-02 20:42:59'),
(154, 3, 8, 1, '2026-11-18', '13:00:00', '13:45:00', '', 'rechazada', 'a', NULL, '2026-11-02 20:42:59'),
(155, 2, 2, 1, '2026-11-04', '11:30:00', '12:15:00', 'clase de programacion teorica', 'pendiente', NULL, NULL, '2026-11-03 12:05:53'),
(156, 2, 2, 1, '2026-11-04', '12:15:00', '13:00:00', 'clase de programacion teorica', 'pendiente', NULL, NULL, '2026-11-03 12:05:53'),
(157, 2, 2, 1, '2026-11-04', '13:00:00', '13:45:00', 'clase de programacion teorica', 'pendiente', NULL, NULL, '2026-11-03 12:05:53'),
(158, 2, 8, 1, '2026-11-04', '11:30:00', '12:15:00', 'clase de programacion teorica', 'pendiente', NULL, NULL, '2026-11-03 12:05:53'),
(159, 2, 8, 1, '2026-11-04', '12:15:00', '13:00:00', 'clase de programacion teorica', 'pendiente', NULL, NULL, '2026-11-03 12:05:53'),
(160, 2, 8, 1, '2026-11-04', '13:00:00', '13:45:00', 'clase de programacion teorica', 'pendiente', NULL, NULL, '2026-11-03 12:05:53'),
(161, 2, 1, 1, '2026-11-06', '13:45:00', '14:30:00', '', 'pendiente', NULL, NULL, '2026-11-06 15:48:27'),
(162, 2, 9, 1, '2026-11-11', '10:00:00', '10:45:00', 'si', 'pendiente', NULL, NULL, '2026-11-09 16:30:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','profesor','alumno') DEFAULT 'alumno',
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `activo`, `created_at`) VALUES
(2, 'Profesor Juan', 'juan@example.com', 'ac8c0826d26ace7d67486fa3ef3c0290', 'profesor', 1, '2026-09-02 22:09:27'),
(3, 'Profesora Ana', 'ana@example.com', 'ac8c0826d26ace7d67486fa3ef3c0290', 'profesor', 1, '2026-09-02 22:09:27'),
(23, 'Admin', 'admin@example.com', '0192023a7bbd73250516f069df18b500', 'admin', 1, '2026-09-27 19:49:03'),
(25, 'karen conde', 'karencondedibujo@hotmail.com', '5c56382d72891fc9b9de8ae28e95a1b0', 'profesor', 1, '2026-09-28 17:19:39'),
(26, 'ernesto', 'ernesto@a.a', '74b87337454200d4d33f80c4663dc5e5', 'profesor', 1, '2026-10-10 00:23:27'),
(27, 'Julio Fabila', 'julio@example.com', '0c82ca5b1092a0c21dcfe3200688046e', 'alumno', 1, '2026-11-03 04:12:03');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `recursos`
--
ALTER TABLE `recursos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_recurso` (`id_recurso`);

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
-- AUTO_INCREMENT de la tabla `recursos`
--
ALTER TABLE `recursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`id_recurso`) REFERENCES `recursos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
