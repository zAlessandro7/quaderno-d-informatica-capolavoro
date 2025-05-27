# Documentazione Progetto quaderno_informatica_et - Database Film

## 🗄️ Configurazione Automatica del Database

### 1. Crea il Database e l'Utente
Esegui queste query in phpMyAdmin (SQL tab) o in MySQL console:

```sql
-- Crea il database
CREATE DATABASE IF NOT EXISTS film_db 
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Crea l'utente dedicato (già creato all'interno del phpmyadmin personale)
CREATE USER 'film_user'@'ciao' IDENTIFIED BY 'ElTaras;

-- Assegna i privilegi
GRANT ALL PRIVILEGES ON film_db.* TO 'film_user'@'ElTaras';

-- Applica i cambiamenti
FLUSH PRIVILEGES;
```

### 2. Struttura Completa delle Tabelle
Esegui questa query per creare tutte le tabelle (copiata direttamente da film_db.sql):

```sql
USE film_db;

CREATE TABLE IF NOT EXISTS attore (
  `Codice_Attore` int(11) NOT NULL,
  `Nome` varchar(50) DEFAULT NULL,
  `Cognome` varchar(50) DEFAULT NULL,
  `Nazionalità` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS attori (
  `Codice_Attore` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Cognome` varchar(50) NOT NULL,
  `Data_Nascita` date NOT NULL,
  `Nazionalità` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS film (
  `Codice_Film` int(11) NOT NULL,
  `Titolo` varchar(100) DEFAULT NULL,
  `Anno_Produzione` int(11) DEFAULT NULL,
  `Regista` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS film_attore (
  `Codice_Film` int(11) NOT NULL,
  `Codice_Attore` int(11) NOT NULL,
  `Ruolo` enum('Protagonista','Non Protagonista') DEFAULT NULL,
  PRIMARY KEY (`Codice_Film`,`Codice_Attore`),
  KEY `Codice_Attore` (`Codice_Attore`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS proiezione (
  `Codice_Proiezione` int(11) NOT NULL,
  `Città` varchar(100) DEFAULT NULL,
  `Sala` varchar(50) DEFAULT NULL,
  `Data` date DEFAULT NULL,
  `Ora` time DEFAULT NULL,
  `Numero_Spettatori` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS film_proiezione (
  `Codice_Film` int(11) NOT NULL,
  `Codice_Proiezione` int(11) NOT NULL,
  PRIMARY KEY (`Codice_Film`,`Codice_Proiezione`),
  KEY `Codice_Proiezione` (`Codice_Proiezione`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Aggiungi gli indici e i vincoli di chiave esterna
ALTER TABLE `attore`
  ADD PRIMARY KEY (`Codice_Attore`);

ALTER TABLE `attori`
  ADD PRIMARY KEY (`Codice_Attore`);

ALTER TABLE `film`
  ADD PRIMARY KEY (`Codice_Film`);

ALTER TABLE `proiezione`
  ADD PRIMARY KEY (`Codice_Proiezione`);

-- Aggiungi gli AUTO_INCREMENT
ALTER TABLE `attore`
  MODIFY `Codice_Attore` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `attori`
  MODIFY `Codice_Attore` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `film`
  MODIFY `Codice_Film` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `proiezione`
  MODIFY `Codice_Proiezione` int(11) NOT NULL AUTO_INCREMENT;

-- Aggiungi i vincoli di chiave esterna
ALTER TABLE `film_attore`
  ADD CONSTRAINT `film_attore_ibfk_1` FOREIGN KEY (`Codice_Film`) REFERENCES `film` (`Codice_Film`),
  ADD CONSTRAINT `film_attore_ibfk_2` FOREIGN KEY (`Codice_Attore`) REFERENCES `attore` (`Codice_Attore`);

ALTER TABLE `film_proiezione`
  ADD CONSTRAINT `film_proiezione_ibfk_1` FOREIGN KEY (`Codice_Film`) REFERENCES `film` (`Codice_Film`),
  ADD CONSTRAINT `film_proiezione_ibfk_2` FOREIGN KEY (`Codice_Proiezione`) REFERENCES `proiezione` (`Codice_Proiezione`);
```

### 3. Popolamento Iniziale Completo
Esegui queste query per inserire i dati di esempio presenti in film_db.sql:

```sql
USE film_db;

-- Dati per la tabella attori
INSERT INTO `attori` (`Codice_Attore`, `Nome`, `Cognome`, `Data_Nascita`, `Nazionalità`) VALUES
(1, 'ccc', 'ccc', '0222-02-22', NULL),
(2, 'ccc', 'ccc', '2222-02-22', NULL),
(3, 'CCCCCCCCC', 'CCCC', '0222-02-22', NULL),
(4, 'CCCC', 'CCC', '2222-02-22', NULL),
(124, 'Marino', 'Torsello', '2007-01-25', 'Italiana');

-- Dati per la tabella film
INSERT INTO `film` (`Codice_Film`, `Titolo`, `Anno_Produzione`, `Regista`) VALUES
(122, '122', 12344, 'io'),
(123, 'ciao', 2025, 'torsello'),
(124, 'ccc', 2025, 'Torx'),
(125, 'ccc', 222, '222'),
(126, 'ccc', 222, '222');
```

## 🔧 Configurazione del File db.php

Modifica il file `db.php` con queste credenziali (aggiornate per usare l'utente creato):

```php
<?php
$host = "localhost";
$user = "ElTaras";
$password = "ciao";
$dbname = "film_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>
```

## 🚀 Avvio del Progetto

1. Avvia XAMPP e assicurati che Apache e MySQL siano in esecuzione
2. Apri il browser e vai su:
   ```
   http://localhost/trasferimento-quaderno-d-informatica-zAlessandro7/quaderno_informatica_et/
   ```

## 🛠️ Struttura del Progetto

```
quaderno_informatica_et/
├── db.php               # Configurazione database
├── film_db.sql          # Struttura database
├── esercizio1.php       # Esercizio 1
├── esercizio2.php       # Esercizio 2
├── esercizio3.php       # Esercizio 3
├── esercizio4.php       # Esercizio 4
├── style.css            # Stile del progetto
└── README.md            # Documentazione del progetto
```

## 🔍 Verifica del Funzionamento

1. Controlla che le tabelle siano state create correttamente in phpMyAdmin
2. Verifica che i dati di esempio siano presenti nelle tabelle
3. Accedi all'applicazione e controlla che i film e gli attori vengano visualizzati
4. Prova a registrare un nuovo attore e verifica che venga inserito nel database
