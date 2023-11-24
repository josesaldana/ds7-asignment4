-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2023 at 03:01 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bdnautico`
--

-- --------------------------------------------------------

--
-- Table structure for table `barco`
--

CREATE TABLE `barco` (
  `matricula` int(11) NOT NULL,
  `cedsocio` varchar(15) DEFAULT NULL,
  `nombre_barco` varchar(50) DEFAULT NULL,
  `numamarre` int(11) DEFAULT NULL,
  `cuota` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barco`
--

INSERT INTO `barco` (`matricula`, `cedsocio`, `nombre_barco`, `numamarre`, `cuota`) VALUES
(12345, '7-000-000', 'Sea Explorer', 8, 400),
(14789, '9-000-000', 'Nave Socio 3', 10, 300),
(67890, '8-000-000', 'Crucero 7 Mares', 9, 800);

-- --------------------------------------------------------

--
-- Table structure for table `conductor_patron`
--

CREATE TABLE `conductor_patron` (
  `codigo` int(11) NOT NULL,
  `nombre` varchar(30) DEFAULT NULL,
  `telefono` varchar(10) DEFAULT NULL,
  `direccion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `socio`
--

CREATE TABLE `socio` (
  `cedula` varchar(15) NOT NULL,
  `nombre_completo` varchar(50) DEFAULT NULL,
  `telefono` varchar(10) DEFAULT NULL,
  `correo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `socio`
--

INSERT INTO `socio` (`cedula`, `nombre_completo`, `telefono`, `correo`) VALUES
('7-000-000', 'Socio 1', '6000-0000', 'socio1@email.com'),
('8-000-000', 'Socio 2', '9999-9998', 'socio2@email.com'),
('9-000-000', 'Socio 3', '9999-9999', 'socio3@email.com');

-- --------------------------------------------------------

--
-- Table structure for table `viaje`
--

CREATE TABLE `viaje` (
  `numero` int(11) NOT NULL,
  `matribarco` int(11) DEFAULT NULL,
  `codpatron` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `destino` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barco`
--
ALTER TABLE `barco`
  ADD PRIMARY KEY (`matricula`),
  ADD KEY `cedsocio` (`cedsocio`);
ALTER TABLE `barco` ADD FULLTEXT KEY `nombre_barco` (`nombre_barco`);

--
-- Indexes for table `conductor_patron`
--
ALTER TABLE `conductor_patron`
  ADD PRIMARY KEY (`codigo`);
ALTER TABLE `conductor_patron` ADD FULLTEXT KEY `nombre` (`nombre`);

--
-- Indexes for table `socio`
--
ALTER TABLE `socio`
  ADD PRIMARY KEY (`cedula`);

--
-- Indexes for table `viaje`
--
ALTER TABLE `viaje`
  ADD PRIMARY KEY (`numero`),
  ADD KEY `matribarco` (`matribarco`),
  ADD KEY `codpatron` (`codpatron`);
ALTER TABLE `viaje` ADD FULLTEXT KEY `destino` (`destino`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `viaje`
--
ALTER TABLE `viaje`
  MODIFY `numero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12352;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barco`
--
ALTER TABLE `barco`
  ADD CONSTRAINT `barco_ibfk_1` FOREIGN KEY (`cedsocio`) REFERENCES `socio` (`cedula`);

--
-- Constraints for table `viaje`
--
ALTER TABLE `viaje`
  ADD CONSTRAINT `viaje_ibfk_1` FOREIGN KEY (`matribarco`) REFERENCES `barco` (`matricula`),
  ADD CONSTRAINT `viaje_ibfk_2` FOREIGN KEY (`codpatron`) REFERENCES `conductor_patron` (`codigo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
