-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 11, 2025 alle 20:03
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
-- Database: `lingue`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `corsi`
--

CREATE TABLE `corsi` (
  `IDCorso` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Lingua` varchar(255) NOT NULL,
  `Livello` varchar(50) NOT NULL,
  `CreatoDa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `docenti`
--

CREATE TABLE `docenti` (
  `IDDocente` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Cognome` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `docenti`
--

INSERT INTO `docenti` (`IDDocente`, `Nome`, `Cognome`, `Email`, `Password`) VALUES
(1, 'cc', 'cc', 'ciao@mail.com', '$2y$10$INOJSvBlZOROfkHe2kx2gOvelXYmGFLi.tlPCVIXtDJiPtU8zDVT.');

-- --------------------------------------------------------

--
-- Struttura della tabella `esercizi`
--

CREATE TABLE `esercizi` (
  `IDEsercizio` int(11) NOT NULL,
  `Tema` varchar(255) NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `CorsoID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `esercizi_studenti`
--

CREATE TABLE `esercizi_studenti` (
  `IDEsercizio_Studente` int(11) NOT NULL,
  `StudenteID` int(11) DEFAULT NULL,
  `IDEsercizio` int(11) DEFAULT NULL,
  `Completato` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `iscrizioni`
--

CREATE TABLE `iscrizioni` (
  `IDIscrizione` int(11) NOT NULL,
  `StudenteID` int(11) DEFAULT NULL,
  `CorsoID` int(11) DEFAULT NULL,
  `DataIscrizione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `registrazioni`
--

CREATE TABLE `registrazioni` (
  `IDRegistrazione` int(11) NOT NULL,
  `Nome` varchar(255) DEFAULT NULL,
  `Cognome` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Tipo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `studenti`
--

CREATE TABLE `studenti` (
  `IDStudente` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Cognome` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `studenti`
--

INSERT INTO `studenti` (`IDStudente`, `Nome`, `Cognome`, `Email`, `Password`) VALUES
(1, 'ccc', 'ccc', 'ccc@mail.com', '$2y$10$ww1WhAlKWXwhvcB2eh425eGP9eraEB59v.55lPL2dXn9ZOFUBb1Oa');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `corsi`
--
ALTER TABLE `corsi`
  ADD PRIMARY KEY (`IDCorso`),
  ADD KEY `CreatoDa` (`CreatoDa`);

--
-- Indici per le tabelle `docenti`
--
ALTER TABLE `docenti`
  ADD PRIMARY KEY (`IDDocente`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indici per le tabelle `esercizi`
--
ALTER TABLE `esercizi`
  ADD PRIMARY KEY (`IDEsercizio`),
  ADD KEY `CorsoID` (`CorsoID`);

--
-- Indici per le tabelle `esercizi_studenti`
--
ALTER TABLE `esercizi_studenti`
  ADD PRIMARY KEY (`IDEsercizio_Studente`),
  ADD KEY `StudenteID` (`StudenteID`),
  ADD KEY `IDEsercizio` (`IDEsercizio`);

--
-- Indici per le tabelle `iscrizioni`
--
ALTER TABLE `iscrizioni`
  ADD PRIMARY KEY (`IDIscrizione`),
  ADD KEY `StudenteID` (`StudenteID`),
  ADD KEY `CorsoID` (`CorsoID`);

--
-- Indici per le tabelle `registrazioni`
--
ALTER TABLE `registrazioni`
  ADD PRIMARY KEY (`IDRegistrazione`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indici per le tabelle `studenti`
--
ALTER TABLE `studenti`
  ADD PRIMARY KEY (`IDStudente`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `corsi`
--
ALTER TABLE `corsi`
  MODIFY `IDCorso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `docenti`
--
ALTER TABLE `docenti`
  MODIFY `IDDocente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `esercizi`
--
ALTER TABLE `esercizi`
  MODIFY `IDEsercizio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `esercizi_studenti`
--
ALTER TABLE `esercizi_studenti`
  MODIFY `IDEsercizio_Studente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `iscrizioni`
--
ALTER TABLE `iscrizioni`
  MODIFY `IDIscrizione` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `registrazioni`
--
ALTER TABLE `registrazioni`
  MODIFY `IDRegistrazione` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `studenti`
--
ALTER TABLE `studenti`
  MODIFY `IDStudente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `corsi`
--
ALTER TABLE `corsi`
  ADD CONSTRAINT `corsi_ibfk_1` FOREIGN KEY (`CreatoDa`) REFERENCES `docenti` (`IDDocente`);

--
-- Limiti per la tabella `esercizi`
--
ALTER TABLE `esercizi`
  ADD CONSTRAINT `esercizi_ibfk_1` FOREIGN KEY (`CorsoID`) REFERENCES `corsi` (`IDCorso`);

--
-- Limiti per la tabella `esercizi_studenti`
--
ALTER TABLE `esercizi_studenti`
  ADD CONSTRAINT `esercizi_studenti_ibfk_1` FOREIGN KEY (`StudenteID`) REFERENCES `studenti` (`IDStudente`),
  ADD CONSTRAINT `esercizi_studenti_ibfk_2` FOREIGN KEY (`IDEsercizio`) REFERENCES `esercizi` (`IDEsercizio`);

--
-- Limiti per la tabella `iscrizioni`
--
ALTER TABLE `iscrizioni`
  ADD CONSTRAINT `iscrizioni_ibfk_1` FOREIGN KEY (`StudenteID`) REFERENCES `studenti` (`IDStudente`),
  ADD CONSTRAINT `iscrizioni_ibfk_2` FOREIGN KEY (`CorsoID`) REFERENCES `corsi` (`IDCorso`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
