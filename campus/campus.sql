-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-02-2024 a las 22:56:07
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
-- Base de datos: `campus`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL,
  `nombre` varchar(40) NOT NULL,
  `apellido` varchar(40) NOT NULL,
  `dni` varchar(9) NOT NULL,
  `correo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avisos`
--

CREATE TABLE `avisos` (
  `id_aviso` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `avisos`
--

INSERT INTO `avisos` (`id_aviso`, `titulo`, `descripcion`, `fecha_publicacion`) VALUES
(1, 'CUDI - No mas clases', 'Pronto no habran mas clases en el cudi, tengan cuidado.', '2023-11-04 00:22:28'),
(2, 'Hola Prueba 2', 'Estamos probando los avisos, no toquen nada.', '2023-11-04 00:24:49'),
(3, 'Probando otra vez', 'Esta es otra prueba de avisos, para ver como andaba.', '2024-02-02 19:06:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'en curso'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `nombre`, `id_profesor`, `estado`) VALUES
(15, 'Programacion I', 3, 'en curso'),
(16, 'Laboratorio I', 3, 'en curso'),
(17, 'Metodología I', 4, 'en curso'),
(18, 'Ingles I', 5, 'en curso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones_curso`
--

CREATE TABLE `inscripciones_curso` (
  `id_inscripcion` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones_curso`
--

INSERT INTO `inscripciones_curso` (`id_inscripcion`, `id_curso`, `id_alumno`) VALUES
(1, 12, 2),
(3, 15, 2),
(2, 16, 2),
(5, 18, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas_curso`
--

CREATE TABLE `tareas_curso` (
  `id_tarea` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `archivo_adjunto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas_curso`
--

INSERT INTO `tareas_curso` (`id_tarea`, `id_curso`, `titulo`, `descripcion`, `fecha_creacion`, `archivo_adjunto`) VALUES
(4, 13, '', '', '2024-02-07', NULL),
(5, 13, 'Examen: Prueba2', 'A ver', '2024-02-07', NULL),
(6, 13, 'Alan alto gato', 'Alan es, alto gato', '2024-02-07', NULL),
(7, 15, 'Traer cartulina roja', 'XD', '2024-02-09', NULL),
(8, 16, 'Alan es re putazo', 'Se la come doblada', '2024-02-09', NULL),
(9, 16, 'Leer PDF', 'Tarea, leer el pdf para mañana.', '2024-02-09', '../media/docs/CV-Ezequiel-Bernal.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `contrasena`, `rol`) VALUES
(1, 'admin', '123456@gmail.com', '$2y$10$nA/m8Ge6YRZkZc9222DBuOu20uJ8LpLVq2dCS8iOkxGBn3F4Okhga', 'administrador'),
(2, '43781730', 'feadswa@hasd.com', '$2y$10$4Fsq5v9FUBFtSS7zIUJ4pO3qEfgrhHhaqjuoli1Q6rkVIFZWX902O', 'alumno'),
(3, 'jorge zarate', 'joralza@hotmail.com', '$2y$10$i3SzXiFFfmQkRr8u3nw4KuHhAVMovJPeGv14R/3m4CMqXke3icsE2', 'profesor'),
(4, 'diana rodriguez', 'diana@hotmail.com', '$2y$10$6upGyvGfSXYbtex6nrFKReuqq6Q.f/XynwiXsg347c7I0m/5zJF5y', 'profesor'),
(5, 'aldo delgado', 'aldo.delgado@hotmail.com', '$2y$10$g0i2iQXdMjGDOcxwo5Tlu.E6IM.WjDQ7schBNGuGamPsAqolzcJja', 'profesor');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `avisos`
--
ALTER TABLE `avisos`
  ADD PRIMARY KEY (`id_aviso`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `id_profesor` (`id_profesor`);

--
-- Indices de la tabla `inscripciones_curso`
--
ALTER TABLE `inscripciones_curso`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD UNIQUE KEY `unique_inscripcion` (`id_curso`,`id_alumno`);

--
-- Indices de la tabla `tareas_curso`
--
ALTER TABLE `tareas_curso`
  ADD PRIMARY KEY (`id_tarea`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `avisos`
--
ALTER TABLE `avisos`
  MODIFY `id_aviso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `inscripciones_curso`
--
ALTER TABLE `inscripciones_curso`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tareas_curso`
--
ALTER TABLE `tareas_curso`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
