-- Kringloop Centrum Duurzaam - Database Setup
-- Database: duurzaam

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database aanmaken
CREATE DATABASE IF NOT EXISTS `duurzaam` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `duurzaam`;

-- Tabel: categorie
CREATE TABLE `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `categorie` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabel: status
CREATE TABLE `status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabel: gebruiker
CREATE TABLE `gebruiker` (
  `id` int NOT NULL AUTO_INCREMENT,
  `gebruikersnaam` varchar(255) NOT NULL,
  `wachtwoord` varchar(255) NOT NULL,
  `rollen` text NOT NULL,
  `is_geverifieerd` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabel: klant
CREATE TABLE `klant` (
  `id` int NOT NULL AUTO_INCREMENT,
  `naam` varchar(255) NOT NULL,
  `adres` varchar(255) NOT NULL,
  `plaats` varchar(255) NOT NULL,
  `telefoon` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabel: artikel
CREATE TABLE `artikel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categorie_id` int NOT NULL,
  `naam` varchar(255) NOT NULL,
  `omschrijving` text DEFAULT NULL,
  `merk` varchar(255) DEFAULT NULL,
  `kleur` varchar(255) DEFAULT NULL,
  `afmeting_maat` varchar(255) DEFAULT NULL,
  `ean_nummer` varchar(13) DEFAULT NULL UNIQUE,
  `prijs_ex_btw` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categorie_id` (`categorie_id`),
  CONSTRAINT `artikel_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabel: voorraad
CREATE TABLE `voorraad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `artikel_id` int NOT NULL,
  `locatie` varchar(255) NOT NULL,
  `aantal` int NOT NULL,
  `status_id` int NOT NULL,
  `ingeboekt_op` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `artikel_id` (`artikel_id`,`status_id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `voorraad_ibfk_1` FOREIGN KEY (`artikel_id`) REFERENCES `artikel` (`id`),
  CONSTRAINT `voorraad_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabel: verkopen
CREATE TABLE `verkopen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `klant_id` int NOT NULL,
  `artikel_id` int NOT NULL,
  `verkocht_op` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `klant_id` (`klant_id`,`artikel_id`),
  KEY `artikel_id` (`artikel_id`),
  CONSTRAINT `verkopen_ibfk_1` FOREIGN KEY (`klant_id`) REFERENCES `klant` (`id`),
  CONSTRAINT `verkopen_ibfk_2` FOREIGN KEY (`artikel_id`) REFERENCES `artikel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabel: persoon
CREATE TABLE `persoon` (
  `id` int NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(255) NOT NULL,
  `achternaam` varchar(255) NOT NULL,
  `adres` varchar(255) NOT NULL,
  `plaats` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `geboortedatum` date DEFAULT NULL,
  `telefoon` varchar(255) DEFAULT NULL,
  `datum_ingevoerd` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabel: wagen
CREATE TABLE `wagen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kenteken` varchar(20) NOT NULL UNIQUE,
  `omschrijving` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabel: planning
CREATE TABLE `planning` (
  `id` int NOT NULL AUTO_INCREMENT,
  `artikel_id` int NOT NULL,
  `klant_id` int NOT NULL,
  `kenteken` varchar(255) NOT NULL,
  `ophalen_of_bezorgen` enum('ophalen','bezorgen') NOT NULL,
  `afspraak_op` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `artikel_id` (`artikel_id`,`klant_id`),
  KEY `klant_id` (`klant_id`),
  CONSTRAINT `planning_ibfk_1` FOREIGN KEY (`artikel_id`) REFERENCES `artikel` (`id`),
  CONSTRAINT `planning_ibfk_2` FOREIGN KEY (`klant_id`) REFERENCES `klant` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ===========================================
-- TEST DATA
-- ===========================================

-- Categorieen
INSERT INTO `categorie` (`id`, `code`, `categorie`) VALUES
(1, 'KLD', 'Kleding'),
(2, 'MBL', 'Meubels'),
(3, 'BED', 'Bedden'),
(4, 'KLK', 'Kledingkasten'),
(5, 'SPG', 'Spiegels'),
(6, 'KPS', 'Kapstokken'),
(7, 'GRK', 'Garderobekasten'),
(8, 'SCK', 'Schoenenkasten'),
(9, 'WIT', 'Witgoed'),
(10, 'BRN', 'Bruingoed'),
(11, 'GRJ', 'Grijsgoed'),
(12, 'GBB', 'Glazen, Borden en Bestek'),
(13, 'BOE', 'Boeken');

-- Statussen
INSERT INTO `status` (`id`, `status`) VALUES
(1, 'Ingeboekt'),
(2, 'In reparatie'),
(3, 'Verkoopklaar'),
(4, 'Verkocht'),
(5, 'Afgekeurd');

-- Gebruikers (wachtwoord: 'test123' gehashed)
INSERT INTO `gebruiker` (`id`, `gebruikersnaam`, `wachtwoord`, `rollen`, `is_geverifieerd`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'directie', 1),
(2, 'magazijn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'magazijnmedewerker', 1),
(3, 'winkel', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'winkelpersoneel', 1),
(4, 'chauffeur', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'chauffeur', 1);

-- Klanten
INSERT INTO `klant` (`id`, `naam`, `adres`, `plaats`, `telefoon`, `email`) VALUES
(1, 'Jan Jansen', 'Hoofdstraat 1', 'Amsterdam', '0612345678', 'jan@example.com'),
(2, 'Piet de Vries', 'Kerkweg 12', 'Rotterdam', '0687654321', 'piet@example.com'),
(3, 'Maria Bakker', 'Stationsplein 5', 'Utrecht', '0698765432', 'maria@example.com');

-- Artikelen
INSERT INTO `artikel` (`id`, `categorie_id`, `naam`, `prijs_ex_btw`) VALUES
(1, 2, 'Eettafel hout', 75.00),
(2, 2, 'Bureaustoel zwart', 25.00),
(3, 1, 'Winterjas maat L', 15.00),
(4, 9, 'Wasmachine Bosch', 150.00),
(5, 10, 'Flatscreen TV 42 inch', 95.00),
(6, 3, 'Tweepersoons bed', 120.00),
(7, 4, 'Kledingkast 2-deurs', 85.00),
(8, 13, 'Boekenset 20 stuks', 10.00);

-- Voorraad
INSERT INTO `voorraad` (`id`, `artikel_id`, `locatie`, `aantal`, `status_id`, `ingeboekt_op`) VALUES
(1, 1, 'Magazijn A-1', 2, 3, '2024-01-10 09:00:00'),
(2, 2, 'Magazijn A-2', 5, 3, '2024-01-10 10:30:00'),
(3, 3, 'Winkel R-1', 10, 3, '2024-01-11 11:00:00'),
(4, 4, 'Magazijn B-1', 1, 2, '2024-01-11 14:00:00'),
(5, 5, 'Winkel E-1', 3, 3, '2024-01-12 09:30:00'),
(6, 6, 'Magazijn C-1', 2, 3, '2024-01-12 11:00:00');

-- Verkopen
INSERT INTO `verkopen` (`id`, `klant_id`, `artikel_id`, `verkocht_op`) VALUES
(1, 1, 3, '2024-01-12 15:30:00'),
(2, 2, 2, '2024-01-12 16:00:00'),
(3, 3, 8, '2024-01-13 10:00:00');

-- Planning
INSERT INTO `planning` (`id`, `artikel_id`, `klant_id`, `kenteken`, `ophalen_of_bezorgen`, `afspraak_op`) VALUES
(1, 1, 1, 'AB-123-CD', 'bezorgen', '2024-01-15 10:00:00'),
(2, 4, 2, 'EF-456-GH', 'ophalen', '2024-01-15 14:00:00'),
(3, 6, 3, 'AB-123-CD', 'bezorgen', '2024-01-16 11:00:00');

-- Wagens
INSERT INTO `wagen` (`kenteken`, `omschrijving`) VALUES
('AB-123-CD', 'Vrachtwagen 1'),
('EF-456-GH', 'Vrachtwagen 2'),
('IJ-789-KL', 'Bestelbus');

-- Personen (testdata)
INSERT INTO `persoon` (`voornaam`, `achternaam`, `adres`, `plaats`, `email`, `telefoon`) VALUES
('Henk', 'de Vries', 'Dorpsstraat 10', 'Almere', 'henk@example.com', '0611111111'),
('Anna', 'Janssen', 'Stationsweg 5', 'Lelystad', 'anna@example.com', '0622222222');

-- Extra artikelen voor januari 2026 rapportages
INSERT INTO `artikel` (`id`, `categorie_id`, `naam`, `omschrijving`, `merk`, `kleur`, `afmeting_maat`, `prijs_ex_btw`) VALUES
(9, 2, 'Salontafel glas', 'Glazen salontafel met metalen poten', 'IKEA', 'Transparant', '120x60cm', 45.00),
(10, 1, 'Spijkerbroek maat M', 'Blauwe spijkerbroek heren', 'Levis', 'Blauw', 'M', 12.50),
(11, 9, 'Droger Siemens', 'Condensdroger 7kg', 'Siemens', 'Wit', 'Standaard', 125.00),
(12, 5, 'Wandspiegel ovaal', 'Grote ovale spiegel met houten lijst', NULL, 'Bruin', '80x120cm', 35.00),
(13, 12, 'Serviesset 24-delig', 'Compleet serviesset voor 6 personen', 'Royal Doulton', 'Wit', NULL, 22.50),
(14, 3, 'Eenpersoons matras', 'Pocketvering matras', 'Auping', 'Wit', '90x200cm', 65.00),
(15, 10, 'Bluetooth speaker', 'Draagbare bluetooth speaker', 'JBL', 'Zwart', 'Klein', 18.00),
(16, 13, 'Encyclopedie set', 'Complete encyclopedie 12 delen', NULL, NULL, NULL, 15.00);

-- Voorraad januari 2026
INSERT INTO `voorraad` (`artikel_id`, `locatie`, `aantal`, `status_id`, `ingeboekt_op`) VALUES
(9, 'Winkel M-1', 1, 3, '2026-01-03 09:00:00'),
(10, 'Winkel R-2', 5, 3, '2026-01-04 10:00:00'),
(11, 'Magazijn B-2', 1, 2, '2026-01-06 11:00:00'),
(12, 'Winkel S-1', 2, 3, '2026-01-07 09:30:00'),
(13, 'Winkel K-1', 3, 3, '2026-01-08 14:00:00'),
(14, 'Magazijn C-2', 2, 3, '2026-01-10 10:00:00'),
(15, 'Winkel E-2', 4, 3, '2026-01-13 09:00:00'),
(16, 'Winkel B-1', 1, 3, '2026-01-15 11:00:00');

-- Verkopen januari 2026
INSERT INTO `verkopen` (`klant_id`, `artikel_id`, `verkocht_op`) VALUES
(1, 9, '2026-01-05 10:30:00'),
(2, 10, '2026-01-07 14:00:00'),
(3, 12, '2026-01-09 11:15:00'),
(1, 13, '2026-01-12 15:00:00'),
(2, 15, '2026-01-14 10:00:00'),
(3, 10, '2026-01-16 16:30:00'),
(1, 16, '2026-01-20 09:45:00'),
(2, 14, '2026-01-22 13:00:00');

-- Planning januari 2026
INSERT INTO `planning` (`artikel_id`, `klant_id`, `kenteken`, `ophalen_of_bezorgen`, `afspraak_op`) VALUES
(9, 1, 'AB-123-CD', 'bezorgen', '2026-01-06 10:00:00'),
(11, 2, 'EF-456-GH', 'ophalen', '2026-01-08 14:00:00'),
(14, 3, 'AB-123-CD', 'bezorgen', '2026-01-13 11:00:00'),
(12, 1, 'IJ-789-KL', 'bezorgen', '2026-01-20 09:00:00');

COMMIT;
