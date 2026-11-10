-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 29-10-2026 a las 19:14:15
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
(14, 'Portatiles', 'equipo', 'Computadora portatil sargas de ceibal', 'uploads/equipos/1791828885_Sargas-1.jpg', 10, 1);

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
(9, 2, 9, 1, '2026-10-11', '08:00:00', '10:00:00', NULL, 'cancelada', NULL, NULL, '2026-10-10 17:01:42'),
(10, 2, 7, 1, '2026-10-11', '16:00:00', '16:45:00', NULL, 'cancelada', NULL, NULL, '2026-10-10 18:28:51'),
(11, 2, 7, 1, '2026-10-11', '16:45:00', '17:30:00', NULL, 'cancelada', NULL, NULL, '2026-10-10 18:28:51'),
(12, 2, 7, 1, '2026-10-11', '17:30:00', '18:15:00', NULL, 'cancelada', NULL, NULL, '2026-10-10 18:28:51'),
(13, 2, 3, 1, '2026-10-11', '13:00:00', '13:45:00', NULL, 'cancelada', NULL, NULL, '2026-10-10 18:59:44'),
(14, 2, 3, 1, '2026-10-11', '13:45:00', '14:30:00', NULL, 'cancelada', NULL, NULL, '2026-10-10 18:59:44'),
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
(72, 3, 9, 1, '2026-10-23', '14:30:00', '15:15:00', 'Cancelada por el profesor', 'cancelada', NULL, NULL, '2026-10-21 13:26:55');

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
(26, 'ernesto', 'ernesto@a.a', '74b87337454200d4d33f80c4663dc5e5', 'profesor', 1, '2026-10-10 00:23:27');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
