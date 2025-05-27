-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 13, 2025 alle 22:42
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `film_db`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `attore`
--

CREATE TABLE `attore` (
  `Codice_Attore` int(11) NOT NULL,
  `Nome` varchar(50) DEFAULT NULL,
  `Cognome` varchar(50) DEFAULT NULL,
  `Nazionalità` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `attori`
--

CREATE TABLE `attori` (
  `Codice_Attore` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Cognome` varchar(50) NOT NULL,
  `Data_Nascita` date NOT NULL,
  `Nazionalità` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `attori`
--

INSERT INTO `attori` (`Codice_Attore`, `Nome`, `Cognome`, `Data_Nascita`, `Nazionalità`) VALUES
(1, 'ccc', 'ccc', '0222-02-22', NULL),
(2, 'ccc', 'ccc', '2222-02-22', NULL),
(3, 'CCCCCCCCC', 'CCCC', '0222-02-22', NULL),
(4, 'CCCC', 'CCC', '2222-02-22', NULL),
(124, 'Marino', 'Torsello', '2007-01-25', 'Italiana');

-- --------------------------------------------------------

--
-- Struttura della tabella `film`
--

CREATE TABLE `film` (
  `Codice_Film` int(11) NOT NULL,
  `Titolo` varchar(100) DEFAULT NULL,
  `Anno_Produzione` int(11) DEFAULT NULL,
  `Regista` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `film`
--

INSERT INTO `film` (`Codice_Film`, `Titolo`, `Anno_Produzione`, `Regista`) VALUES
(122, '122', 12344, 'io'),
(123, 'ciao', 2025, 'torsello'),
(124, 'ccc', 2025, 'Torx'),
(125, 'ccc', 222, '222'),
(126, 'ccc', 222, '222');

-- --------------------------------------------------------

--
-- Struttura della tabella `film_attore`
--

CREATE TABLE `film_attore` (
  `Codice_Film` int(11) NOT NULL,
  `Codice_Attore` int(11) NOT NULL,
  `Ruolo` enum('Protagonista','Non Protagonista') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `film_proiezione`
--

CREATE TABLE `film_proiezione` (
  `Codice_Film` int(11) NOT NULL,
  `Codice_Proiezione` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `proiezione`
--

CREATE TABLE `proiezione` (
  `Codice_Proiezione` int(11) NOT NULL,
  `Città` varchar(100) DEFAULT NULL,
  `Sala` varchar(50) DEFAULT NULL,
  `Data` date DEFAULT NULL,
  `Ora` time DEFAULT NULL,
  `Numero_Spettatori` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `attore`
--
ALTER TABLE `attore`
  ADD PRIMARY KEY (`Codice_Attore`);

--
-- Indici per le tabelle `attori`
--
ALTER TABLE `attori`
  ADD PRIMARY KEY (`Codice_Attore`);

--
-- Indici per le tabelle `film`
--
ALTER TABLE `film`
  ADD PRIMARY KEY (`Codice_Film`);

--
-- Indici per le tabelle `film_attore`
--
ALTER TABLE `film_attore`
  ADD PRIMARY KEY (`Codice_Film`,`Codice_Attore`),
  ADD KEY `Codice_Attore` (`Codice_Attore`);

--
-- Indici per le tabelle `film_proiezione`
--
ALTER TABLE `film_proiezione`
  ADD PRIMARY KEY (`Codice_Film`,`Codice_Proiezione`),
  ADD KEY `Codice_Proiezione` (`Codice_Proiezione`);

--
-- Indici per le tabelle `proiezione`
--
ALTER TABLE `proiezione`
  ADD PRIMARY KEY (`Codice_Proiezione`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `attore`
--
ALTER TABLE `attore`
  MODIFY `Codice_Attore` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `attori`
--
ALTER TABLE `attori`
  MODIFY `Codice_Attore` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT per la tabella `film`
--
ALTER TABLE `film`
  MODIFY `Codice_Film` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT per la tabella `proiezione`
--
ALTER TABLE `proiezione`
  MODIFY `Codice_Proiezione` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `film_attore`
--
ALTER TABLE `film_attore`
  ADD CONSTRAINT `film_attore_ibfk_1` FOREIGN KEY (`Codice_Film`) REFERENCES `film` (`Codice_Film`),
  ADD CONSTRAINT `film_attore_ibfk_2` FOREIGN KEY (`Codice_Attore`) REFERENCES `attore` (`Codice_Attore`);

--
-- Limiti per la tabella `film_proiezione`
--
ALTER TABLE `film_proiezione`
  ADD CONSTRAINT `film_proiezione_ibfk_1` FOREIGN KEY (`Codice_Film`) REFERENCES `film` (`Codice_Film`),
  ADD CONSTRAINT `film_proiezione_ibfk_2` FOREIGN KEY (`Codice_Proiezione`) REFERENCES `proiezione` (`Codice_Proiezione`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
