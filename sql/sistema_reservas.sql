-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 01-10-2026 a las 13:39:57
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
(9, 'Alargue eléctrico', 'equipo', 'Alargue de 5 metros con 4 tomas', NULL, 10, 1);

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
  `estado` enum('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `id_usuario`, `id_recurso`, `cantidad_solicitada`, `fecha`, `hora_inicio`, `hora_fin`, `estado`, `observaciones`, `created_at`) VALUES
(1, 2, 7, 2, '2026-09-10', '08:00:00', '10:00:00', 'aprobada', NULL, '2026-09-02 22:09:27');

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
(25, 'karen conde', 'karencondedibujo@hotmail.com', '5c56382d72891fc9b9de8ae28e95a1b0', 'profesor', 1, '2026-09-28 17:19:39');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
