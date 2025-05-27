# Documentazione Progetto esame2017 - Gestione Eventi

## 🗄️ Configurazione Automatica del Database

### 1. Crea il Database e l'Utente
Esegui queste query in phpMyAdmin (SQL tab) o in MySQL console:

```sql
-- Crea il database
CREATE DATABASE IF NOT EXISTS eventi_web 
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Crea l'utente dedicato
CREATE USER 'eventi_user'@'localhost' IDENTIFIED BY 'Sicura123!';

-- Assegna i privilegi
GRANT ALL PRIVILEGES ON eventi_web.* TO 'eventi_user'@'localhost';

-- Applica i cambiamenti
FLUSH PRIVILEGES;
```

### 2. Struttura delle Tabelle
Esegui questa query per creare tutte le tabelle:

```sql
USE eventi_web;

CREATE TABLE IF NOT EXISTS categorie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS eventi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(255) NOT NULL,
    luogo VARCHAR(255) NOT NULL,
    data DATE NOT NULL,
    titolo VARCHAR(255) NOT NULL,
    artisti TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nickname VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    cognome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    categorie_interessate TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS commenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_id INT NOT NULL,
    utente_id INT NOT NULL,
    commento TEXT NOT NULL,
    data_commento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES eventi(id) ON DELETE CASCADE,
    FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Popolamento Iniziale
Esegui queste query per inserire dati di esempio:

```sql
USE eventi_web;

-- Inserisci dati di esempio nella tabella eventi
INSERT INTO eventi (categoria, luogo, data, titolo, artisti) VALUES
('Musica', 'Roma', '2025-06-15', 'Concerto Estate', 'Artista1, Artista2'),
('Teatro', 'Milano', '2025-07-20', 'Spettacolo di Teatro', 'Compagnia XYZ'),
('Sport', 'Napoli', '2025-08-10', 'Partita di Calcio', 'Squadra A vs Squadra B');

-- Inserisci utenti demo
INSERT INTO utenti (nickname, nome, cognome, email, categorie_interessate) VALUES
('admin', 'Admin', 'System', 'admin@example.com', 'Musica,Teatro,Sport'),
('user1', 'Mario', 'Rossi', 'mario.rossi@example.com', 'Musica,Sport'),
('user2', 'Luigi', 'Verdi', 'luigi.verdi@example.com', 'Teatro');
```

## 🔧 Configurazione del File db.php

Modifica il file `db.php` con queste credenziali:

```php
<?php
$host = "localhost";
$user = "ElTaras";
$password = "ciao";
$dbname = "eventi_web";

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
   http://localhost/trasferimento-quaderno-d-informatica-zAlessandro7/esame2017/
   ```

## 🛠️ Struttura del Progetto

```
esame2017/
├── db.php               # Configurazione database
├── eventi_web.sql       # Struttura database
├── eventi.php           # Lista eventi
├── crea_evento.php      # Creazione nuovi eventi
├── index.php            # Pagina principale
├── login.php            # Accesso utenti
├── register.php         # Registrazione utenti
├── header.php           # Intestazione pagine
├── footer.php           # Piè di pagina
└── style.css            # Stile del progetto
```

## 🔍 Verifica del Funzionamento

1. Controlla che le tabelle siano state create correttamente in phpMyAdmin
2. Verifica che i dati di esempio siano presenti nelle tabelle
3. Accedi all'applicazione e controlla che gli eventi vengano visualizzati
4. Prova a registrare un nuovo utente e verifica che venga inserito nel database
