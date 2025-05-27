# Documentazione Progetto esame2019 - Piattaforma Turistica

## 🗄️ Configurazione Automatica del Database

### 1. Crea il Database e l'Utente
Esegui queste query in phpMyAdmin (SQL tab) o in MySQL console:

```sql
-- Crea il database
CREATE DATABASE IF NOT EXISTS turismo 
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Crea l'utente dedicato
CREATE USER 'turismo_user'@'localhost' IDENTIFIED BY 'Sicura123!';

-- Assegna i privilegi
GRANT ALL PRIVILEGES ON turismo.* TO 'turismo_user'@'localhost';

-- Applica i cambiamenti
FLUSH PRIVILEGES;
```

### 2. Struttura delle Tabelle
Esegui questa query per creare tutte le tabelle:

```sql
USE turismo;

CREATE TABLE IF NOT EXISTS utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS poi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descrizione TEXT DEFAULT NULL,
    indirizzo VARCHAR(255) DEFAULT NULL,
    immagine VARCHAR(255) DEFAULT NULL,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS commenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poi_id INT NOT NULL,
    utente VARCHAR(255) NOT NULL,
    commento TEXT NOT NULL,
    voto INT NOT NULL CHECK (voto BETWEEN 1 AND 5),
    data_commento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (poi_id) REFERENCES poi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Popolamento Iniziale
Esegui queste query per inserire dati di esempio:

```sql
USE turismo;

-- Inserisci dati di esempio nella tabella poi
INSERT INTO poi (nome, descrizione, indirizzo) VALUES
('Colosseo', 'Antico anfiteatro romano', 'Piazza del Colosseo, 1'),
('Torre di Pisa', 'Famosa per la sua inclinazione', 'Piazza dei Miracoli, 1'),
('Vaticano', 'Centro della Chiesa Cattolica', 'Città del Vaticano');

-- Inserisci dati di esempio nella tabella commenti
INSERT INTO commenti (poi_id, utente, commento, voto) VALUES
(1, 'Mario', 'Un luogo magnifico!', 5),
(2, 'Luigi', 'Da visitare assolutamente!', 4),
(3, 'Anna', 'Un'esperienza unica!', 5);
```

## 🔧 Configurazione del File db.php

Modifica il file `db.php` con queste credenziali:

```php
<?php
$host = "localhost";
$user = "ElTaras";
$password = "ciao";
$dbname = "turismo";

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
   http://localhost/trasferimento-quaderno-d-informatica-zAlessandro7/esame2019/
   ```

## 🛠️ Struttura del Progetto

```
esame2019/
├── db.php               # Configurazione database
├── turismo.sql          # Struttura database
├── index.php            # Pagina principale
├── inserisci_poi.php    # Aggiunta nuovi punti di interesse
├── commento.php         # Sistema commenti
├── login.php            # Accesso utenti
└── style.css            # Stile del progetto
```

## 🔍 Verifica del Funzionamento

1. Controlla che le tabelle siano state create correttamente in phpMyAdmin
2. Verifica che i dati di esempio siano presenti nelle tabelle
3. Accedi all'applicazione e controlla che i punti di interesse vengano visualizzati
4. Prova a registrare un nuovo utente e verifica che venga inserito nel database
