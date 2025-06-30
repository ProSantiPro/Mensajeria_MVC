-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-06-2025 a las 01:59:42
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
-- Estructura de tabla para la tabla `archivos_chat`
--

CREATE TABLE `archivos_chat` (
  `id` int(11) NOT NULL,
  `sender_usuario` varchar(255) NOT NULL,
  `receiver_usuario` varchar(255) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `sender_nombre_original` varchar(255) NOT NULL,
  `ruta_archivo` varchar(255) NOT NULL,
  `tipo_archivo` varchar(100) NOT NULL,
  `tamano_archivo` int(11) NOT NULL,
  `fecha_subida` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `archivos_chat`
--

INSERT INTO `archivos_chat` (`id`, `sender_usuario`, `receiver_usuario`, `nombre_archivo`, `sender_nombre_original`, `ruta_archivo`, `tipo_archivo`, `tamano_archivo`, `fecha_subida`) VALUES
(1, 'Santiago', 'Santiago', 'INFORME PASANTIAS TEMPRANAS.pdf', 'INFORME PASANTIAS TEMPRANAS.pdf', '685dd69604fe8.pdf', 'application/pdf', 647379, '2025-06-26 19:24:06'),
(2, 'Santiago', 'Sarai', '685ee87964e75.pdf', 'Informe Seguros Horizonte.pdf', '685ee87964e75.pdf', 'application/pdf', 1390823, '2025-06-27 14:52:41'),
(4, 'Sarai', 'Santiago', '685f103dd527c.pdf', 'CONTRATO DE APRENDIZAJE ALGORITMOS Y PROGRAMACIÓN A Y B 2025-1.pdf', '685f103dd527c.pdf', 'application/pdf', 162833, '2025-06-27 17:42:21'),
(5, 'Sarai', 'Santiago', '685f22399a19e.docx', 'Informe pasantias tempranas.docx', '685f22399a19e.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 1662016, '2025-06-27 18:59:05'),
(6, 'Admin01', 'santiago', '68606c04a53fd.pdf', 'CONTRATO DE APRENDIZAJE ALGORITMOS Y PROGRAMACIÓN A Y B 2025-1.pdf', '68606c04a53fd.pdf', 'application/pdf', 162833, '2025-06-28 18:26:12'),
(7, 'Admin01', 'santiago', '686072dc0a196.docx', 'Informe pasantias tempranas.docx', '686072dc0a196.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 1662016, '2025-06-28 18:55:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_usuarios`
--

CREATE TABLE `chat_usuarios` (
  `msg_id` int(11) NOT NULL,
  `sender_usuario` varchar(100) NOT NULL,
  `receiver_usuario` varchar(100) NOT NULL,
  `msg_contenido` varchar(255) NOT NULL,
  `msg_status` text NOT NULL,
  `msg_fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `chat_usuarios`
--

INSERT INTO `chat_usuarios` (`msg_id`, `sender_usuario`, `receiver_usuario`, `msg_contenido`, `msg_status`, `msg_fecha`) VALUES
(17, 'Sarai', 'Sarai', 'hola sarai', 'read', '2025-06-18 16:23:07'),
(18, 'Sarai', 'Sarai', 'goku perez', 'read', '2025-06-18 16:43:38'),
(19, 'Sarai', 'Sarai', 'One piece god', 'read', '2025-06-19 15:05:33'),
(20, 'Sarai', 'Sarai', 'sara', 'read', '2025-06-19 15:17:30'),
(21, 'Sarai', 'Santiago', 'santi', 'read', '2025-06-19 15:34:20'),
(22, 'Sarai', 'Santiago', 'funcionas?', 'read', '2025-06-19 15:34:27'),
(23, 'Sarai', 'Santiago', 'santi', 'read', '2025-06-19 15:44:47'),
(24, 'Sarai', 'Santiago', 'santiago', 'read', '2025-06-19 15:47:11'),
(25, 'Sarai', 'Santiago', 'sa', 'read', '2025-06-19 16:00:06'),
(26, 'Sarai', 'Santiago', 'santi', 'read', '2025-06-19 16:04:58'),
(27, 'Sarai', 'Santiago', 'saaaantiiiii', 'read', '2025-06-19 16:05:14'),
(31, 'Sarai', 'Santiago', 'sa', 'read', '2025-06-25 16:12:38'),
(34, 'Sarai', 'Luis', 'Hola cala', 'read', '2025-06-25 18:42:09'),
(35, 'Sarai', 'Santiago', 'Santiago Tomas', 'read', '2025-06-25 18:43:44'),
(36, 'Sarai', 'Santiago', 'santiii', 'read', '2025-06-25 18:44:00'),
(37, 'Sarai', 'Luis', 'si', 'read', '2025-06-25 20:11:37'),
(38, 'Sarai', 'Luis', 'si cala', 'read', '2025-06-25 20:12:54'),
(41, 'Sarai', 'Santiago', 'si', 'read', '2025-06-26 01:31:48'),
(42, 'Sarai', 'Santiago', 'si', 'read', '2025-06-26 01:31:56'),
(43, 'Sarai', 'Santiago', 'como?', 'read', '2025-06-26 01:32:06'),
(44, 'Sarai', 'Santiago', 'vasie', 'read', '2025-06-26 01:32:12'),
(45, 'Sarai', 'Santiago', 'vasie', 'read', '2025-06-26 01:32:17'),
(46, 'Sarai', 'Santiago', 'vasie', 'read', '2025-06-26 01:32:21'),
(47, 'Sarai', 'Santiago', 'vasie', 'read', '2025-06-26 01:32:24'),
(48, 'Sarai', 'Santiago', 'pasa algo raro', 'read', '2025-06-26 01:32:44'),
(49, 'Sarai', 'Santiago', 'ah?', 'read', '2025-06-26 01:32:50'),
(50, 'Sarai', 'Luis', 'aqui pasa igual?', 'read', '2025-06-26 01:33:06'),
(51, 'Sarai', 'Santiago', 'wq', 'read', '2025-06-26 02:24:52'),
(52, 'Sarai', 'Luis', 'toma cala', 'read', '2025-06-26 02:43:35'),
(53, 'Sarai', 'Luis', 'santis', 'read', '2025-06-26 03:42:29'),
(54, 'Sarai', 'Luis', 'S', 'read', '2025-06-26 03:42:39'),
(55, 'Sarai', 'Luis', 'sa', 'read', '2025-06-26 03:42:46'),
(56, 'Sarai', 'Luis', 'santis', 'read', '2025-06-26 03:42:51'),
(57, 'Sarai', 'Luis', 'as', 'read', '2025-06-26 03:45:10'),
(58, 'Sarai', 'Santiago', 'sa', 'read', '2025-06-26 13:46:33'),
(59, 'Sarai', 'Santiago', 'a', 'read', '2025-06-26 13:46:38'),
(60, 'Sarai', 'Santiago', 'sa', 'read', '2025-06-26 15:41:40'),
(61, 'Sarai', 'Santiago', 'santi', 'read', '2025-06-26 15:49:01'),
(62, 'Sarai', 'Santiago', 'sa', 'read', '2025-06-26 15:49:05'),
(63, 'Sarai', 'Santiago', 'porque se hecha para arriba?', 'read', '2025-06-26 15:49:13'),
(64, 'Sarai', 'Santiago', 'sa', 'read', '2025-06-26 15:53:52'),
(65, 'Sarai', 'Santiago', 'aqui', 'read', '2025-06-26 16:00:16'),
(66, 'Santiago', 'Sarai', 'sa', 'read', '2025-06-26 16:35:46'),
(67, 'Santiago', 'Sarai', 'sara', 'read', '2025-06-26 16:47:56'),
(68, 'Santiago', 'Sarai', 'sarita', 'read', '2025-06-26 16:47:59'),
(69, 'Santiago', 'Sarai', 'sa', 'read', '2025-06-26 16:52:12'),
(70, 'Santiago', 'Sarai', 'sa', 'read', '2025-06-26 16:52:17'),
(71, 'Santiago', 'Sarai', 'sara?', 'read', '2025-06-26 16:58:03'),
(72, 'Santiago', 'Sarai', 'Goku Perez?', 'read', '2025-06-26 16:58:11'),
(73, 'Santiago', 'Sarai', 'okey', 'read', '2025-06-26 22:43:33'),
(74, 'Santiago', 'Sarai', 'conchale', 'read', '2025-06-26 22:43:40'),
(75, 'Santiago', 'Luis', 'hola cala', 'read', '2025-06-26 22:44:05'),
(76, 'Santiago', 'Luis', 'sasasa', 'read', '2025-06-26 22:44:38'),
(77, 'Santiago', 'Luis', 'sasasa', 'read', '2025-06-26 22:44:41'),
(78, 'Santiago', 'Luis', 'sasasa', 'read', '2025-06-26 22:44:44'),
(79, 'Santiago', 'Sarai', 'Vegeta Hernandez', 'read', '2025-06-26 22:44:55'),
(80, 'Santiago', 'Luis', 'Balatrito', 'read', '2025-06-26 22:45:04'),
(81, 'Santiago', 'Luis', 'Balatrito', 'read', '2025-06-26 22:51:59'),
(82, 'Santiago', 'Luis', 's', 'read', '2025-06-26 22:52:04'),
(83, 'Santiago', 'Luis', 'sa', 'read', '2025-06-26 22:52:08'),
(84, 'Santiago', 'Sarai', 'goku', 'read', '2025-06-26 22:52:16'),
(85, 'Santiago', 'Sarai', 'sa', 'read', '2025-06-26 22:53:44'),
(86, 'Santiago', 'Sarai', 'pendiente', 'read', '2025-06-26 22:54:04'),
(87, 'Santiago', 'Sarai', 'sara', 'read', '2025-06-26 22:57:05'),
(88, 'Santiago', 'Sarai', 'si', 'read', '2025-06-26 22:57:12'),
(89, 'Santiago', 'Sarai', 'kensan', 'read', '2025-06-26 22:58:06'),
(90, 'Santiago', 'Sarai', 'qqq', 'read', '2025-06-26 22:58:15'),
(91, 'Santiago', 'Sarai', 'si', 'read', '2025-06-26 23:00:37'),
(92, 'Santiago', 'Sarai', 'si', 'read', '2025-06-26 23:00:47'),
(93, 'Santiago', 'Sarai', 'ven', 'read', '2025-06-26 23:12:16'),
(94, 'Santiago', 'Sarai', 'sa', 'read', '2025-06-26 23:12:21'),
(95, 'Santiago', 'Sarai', 'sss', 'read', '2025-06-26 23:13:58'),
(96, 'Santiago', 'Sarai', 'sara', 'read', '2025-06-26 23:14:07'),
(97, 'Santiago', 'Sarai', 'ssasas', 'read', '2025-06-26 23:14:11'),
(98, 'Sarai', 'Santiago', 'santi', 'read', '2025-06-27 15:33:43'),
(99, 'Sarai', 'Santiago', 'se reinicia horita', 'read', '2025-06-27 15:33:49'),
(100, 'Sarai', 'Santiago', 'sanati', 'read', '2025-06-27 15:49:39'),
(101, 'Sarai', 'Santiago', 'goku?', 'read', '2025-06-27 15:55:08'),
(102, 'Sarai', 'Santiago', 'goku?', 'read', '2025-06-27 15:55:11'),
(103, 'Sarai', 'Santiago', 'goku?', 'read', '2025-06-27 15:55:13'),
(104, 'Sarai', 'Santiago', 'saaaaa', 'read', '2025-06-27 15:57:04'),
(105, 'Sarai', 'Santiago', 'ntiii', 'read', '2025-06-27 15:57:07'),
(106, 'Sarai', 'Santiago', 'vegeta', 'read', '2025-06-27 15:57:12'),
(107, 'Sarai', 'Santiago', 'Santi', 'read', '2025-06-27 16:02:42'),
(108, 'Sarai', 'Santiago', 'Tomas', 'read', '2025-06-27 16:02:46'),
(109, 'Sarai', 'Santiago', 'Rodriguez', 'read', '2025-06-27 16:02:50'),
(110, 'Sarai', 'Santiago', 'Barboza', 'read', '2025-06-27 16:02:54'),
(111, 'Santiago', 'Sarai', 'sara', 'read', '2025-06-27 16:19:58'),
(112, 'Santiago', 'Sarai', 'de los angeles', 'read', '2025-06-27 16:20:05'),
(113, 'Santiago', 'Sarai', 'leon', 'read', '2025-06-27 16:20:10'),
(114, 'Santiago', 'Luis', 'otorrinonaringolo esternocleidomastoideo', '', '2025-06-27 16:29:25'),
(115, 'Santiago', 'Sarai', 'pokemon', 'read', '2025-06-27 16:38:33'),
(116, 'Santiago', 'Sarai', 'rojo', 'read', '2025-06-27 16:38:37'),
(117, 'Santiago', 'Sarai', 'fuego', 'read', '2025-06-27 16:38:41'),
(118, 'Santiago', 'Sarai', 'omega', 'read', '2025-06-27 16:38:43'),
(119, 'Santiago', 'Sarai', 'sa', 'read', '2025-06-27 18:43:13'),
(120, '', 'santiago', 'hola santiago', '', '2025-06-28 22:09:30'),
(121, '', 'santiago', 'pan bimbo', '', '2025-06-28 23:14:42'),
(122, '', 'sarai', 'Hola Sarai', '', '2025-06-28 23:28:57'),
(123, 'Admin01', 'santiago', 'Hola Santiago', '', '2025-06-28 23:41:33'),
(124, 'Admin01', 'santiago', 'Hola santiago', '', '2025-06-28 23:50:04'),
(125, 'Admin01', 'luis', 'hola cala', '', '2025-06-28 23:50:46');

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
  `usuario_actualizado` timestamp NOT NULL DEFAULT current_timestamp(),
  `login_status` varchar(10) DEFAULT 'logout',
  `genero` varchar(20) DEFAULT '',
  `pais` varchar(50) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `usuario_nombre`, `usuario_apellido`, `usuario_email`, `usuario_usuario`, `usuario_clave`, `usuario_foto`, `usuario_creado`, `usuario_actualizado`, `login_status`, `genero`, `pais`) VALUES
(1, 'Administrador', 'Primero', 'admin@admin.com', 'Admin01', '$2y$10$ucsSoJOjk2xUWQ/NCxMOZuT4r5d3h8oozvtsMFZtwkBoRnm.J6Iue', 'Administrador_6.jpg', '2025-06-26 22:17:26', '2025-06-28 01:02:44', 'Online', '', ''),
(3, 'Miguel Yovannys', 'Leon Delgado', '', 'MiguelLinito', '$2y$10$N/RXTMI0NZxEHTorUGHjPeVoWkB3VWBd2uPJQhRpA41mTo2cjuKLu', 'Miguel_Yovannys_24.jpg', '2025-06-27 01:42:15', '2025-06-28 01:02:18', 'logout', '', ''),
(5, 'Santiago', '', 'strb2006@gmail.com', 'santiago', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '2025-06-28 02:07:43', '2025-06-28 02:07:43', 'logout', 'Masculino', 'venezuela'),
(6, 'Sarai', '', 'saraleon@gmail.com', 'sarai', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '2025-06-28 02:07:43', '2025-06-28 02:07:43', 'logout', 'Femenino', 'venezuela'),
(7, 'Luis', '', 'luiscala@gmail.com', 'luis', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '2025-06-28 02:07:43', '2025-06-28 02:07:43', 'logout', 'Masculino', 'Venezuela');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `archivos_chat`
--
ALTER TABLE `archivos_chat`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `chat_usuarios`
--
ALTER TABLE `chat_usuarios`
  ADD PRIMARY KEY (`msg_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `archivos_chat`
--
ALTER TABLE `archivos_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `chat_usuarios`
--
ALTER TABLE `chat_usuarios`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usuario_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
