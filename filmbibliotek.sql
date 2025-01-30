-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Värd: localhost
-- Tid vid skapande: 16 feb 2024 kl 08:30
-- Serverversion: 8.0.31
-- PHP-version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `filmbibliotek`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `category`
--

CREATE TABLE `category` (
  `id` int UNSIGNED NOT NULL,
  `namn` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `category`
--

INSERT INTO `category` (`id`, `namn`) VALUES
(1, 'Action'),
(2, 'Drama'),
(3, 'Komedi'),
(4, 'Skräck'),
(5, 'Fantasy'),
(6, 'Svensk'),
(7, 'Science Fiction'),
(8, 'Dystopi');

-- --------------------------------------------------------

--
-- Tabellstruktur `film`
--

CREATE TABLE `film` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `director` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `year` int DEFAULT NULL,
  `category_id` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av data i tabell `film`
--

INSERT INTO `film` (`id`, `title`, `director`, `year`, `category_id`) VALUES
(1, 'Harry Potter and the Philosopher\'s Stone', 'David Yates', 2001, 5),
(28, 'Contact', 'Robert Zemeckis', 1997, 7),
(40, 'El Camino: A Breaking Bad Movie', 'Vince Gilligan', 2019, 2);

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `film`
--
ALTER TABLE `film`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `category`
--
ALTER TABLE `category`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT för tabell `film`
--
ALTER TABLE `film`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `film`
--
ALTER TABLE `film`
  ADD CONSTRAINT `film_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
