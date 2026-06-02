-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 02-06-2026 a las 14:21:43
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `company_info`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intentos_login`
--

DROP TABLE IF EXISTS `intentos_login`;
CREATE TABLE IF NOT EXISTS `intentos_login` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Usuario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ipRemoto` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deteccion_anomalia` tinyint(1) NOT NULL,
  `fecha_intento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `intentos_login`
--

INSERT INTO `intentos_login` (`id`, `Usuario`, `ipRemoto`, `deteccion_anomalia`, `fecha_intento`, `timestamp`) VALUES
(5, 'kevynreyes@gmail.com', '::1', 0, '2026-06-02 13:40:02', '2026-06-02 13:40:02'),
(3, 'kevynreyes@gmail.com', '::1', 0, '2026-06-02 13:17:14', '2026-06-02 13:17:14'),
(4, 'kevynreyes@gmail.com', '::1', 0, '2026-06-02 13:20:20', '2026-06-02 13:20:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `Nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `Apellido` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `Usuario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `Correo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `HashMagic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `secret_2fa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `FechaSistema` datetime NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`Nombre`, `Apellido`, `Usuario`, `Correo`, `HashMagic`, `secret_2fa`, `FechaSistema`, `id`) VALUES
('Kevyn', 'Reyes', 'kevynreyes', 'kevynreyes@gmail.com', '$2y$13$f2qq0QYQ5bzdQ8.gmmJMTeb9BuV0yE3Zz5MInOG89pVyZut45MekK', 'JC7HGL7VELJSWQ57', '2026-06-02 13:19:40', 23);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
