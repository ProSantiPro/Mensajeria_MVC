-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-06-2025 a las 18:22:15
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
-- Base de datos: `crudadmin`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `usuario_id` int(10) NOT NULL,
  `usuario_nombre` varchar(70) NOT NULL,
  `usuario_apellido` varchar(70) NOT NULL,
  `usuario_email` varchar(100) NOT NULL,
  `usuario_usuario` varchar(30) NOT NULL,
  `usuario_clave` varchar(300) NOT NULL,
  `usuario_foto` varchar(536) NOT NULL,
  `usuario_creado` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_actualizado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `usuario_nombre`, `usuario_apellido`, `usuario_email`, `usuario_usuario`, `usuario_clave`, `usuario_foto`, `usuario_creado`, `usuario_actualizado`) VALUES
(1, 'Administrador', 'Primero', 'admin@admin.com', 'Admin01', '$2y$10$ucsSoJOjk2xUWQ/NCxMOZuT4r5d3h8oozvtsMFZtwkBoRnm.J6Iue', '', '2025-06-26 22:17:26', '2025-06-27 16:01:47'),
(3, 'Miguel Yovannys', 'Leon Delgado', '', 'MiguelLinito', '$2y$10$N/RXTMI0NZxEHTorUGHjPeVoWkB3VWBd2uPJQhRpA41mTo2cjuKLu', '', '2025-06-27 01:42:15', '2025-06-27 15:00:32');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usuario_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
