-- phpMyAdmin SQL Dump
-- version 5.0.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 09, 2025 at 10:00 AM
-- Server version: 5.7.24
-- PHP Version: 7.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `criminal_minds`
--
CREATE DATABASE IF NOT EXISTS `criminal_minds` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `criminal_minds`;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `author` varchar(100) NOT NULL DEFAULT 'Onbekend',
  `date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`date`),
  KEY `idx_featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `status`, `author`, `date`, `created_at`, `updated_at`, `featured`) VALUES
(1, 'L. van der W. (17) gepakt in Vogelwaarde tijdens het kiefen', 'Jo maat, er is zoeen cooked ass guy opgepakt in Vogelwaarde tijdens een grote deal.', 'published', 'Reklezz', '2025-09-08 12:48:02', '2025-09-08 10:48:02', NULL, 1),
(2, 'BREAKING: 100 kilo 6mmc gesmokkeld uit Deventer', 'goedemiddag beste mensen, blogger sjoak hier. 
er is afgelopen zondag 100 kilo 6mmc gesmokkeld uut Deevntr, ik vermoed dat het mien neef was maar ik kan het beter niet doorvertellen

wat een kearl', 'published', 'blogger_sjoak1993', '2025-09-08 09:45:50', '2025-09-08 07:45:50', NULL, 0),
(3, 'Grote Drugsbust in Amsterdam: 500kg Cocaïne Onderschept', '<p>In een grootschalige operatie heeft de politie vandaag 500 kilogram cocaïne onderschept in de haven van Amsterdam. De drugs waren verstopt in een container met bananen uit Zuid-Amerika.</p>

<p>De operatie, codenaam "Witte Sneeuw", was het resultaat van maanden onderzoek door de Nationale Politie in samenwerking met internationale partners. Drie verdachten zijn aangehouden.</p>

<p><strong>Details van de operatie:</strong></p>
<ul>
<li>Straatwaarde: €37,5 miljoen</li>
<li>Container herkomst: Colombia</li>
<li>Arrestaties: 3 personen</li>
<li>Onderzoeksduur: 8 maanden</li>
</ul>

<p>Dit is een van de grootste drugsvangsten van dit jaar in Nederland. De politie verwacht dat deze inbeslagname een significante impact zal hebben op de lokale drugshandel.</p>', 'published', 'Ronald Hogerlanden', '2025-09-08 07:05:29', '2025-09-08 05:05:29', NULL, 0);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;