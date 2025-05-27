# Guida Avanzata alla Gestione del quaderno d'informatica

## L'utente prima di accedere al sito, deve premere il pulsante "Installa il database".

## 🗄️ Parte 1: Creazione di un Nuovo Database

### Passo 1: Aprire phpMyAdmin
1. Avvia XAMPP e assicurati che Apache e MySQL siano in esecuzione
2. Apri il browser e vai su: `http://localhost/phpmyadmin`

### Passo 2: Creare il Database
1. Clicca su "Nuovo" nel menu di sinistra
2. Nella sezione "Crea database":
   - Nome database: inserisci un nome (es. `eventi_web`)
   - Collazione: seleziona `utf8_general_ci`
3. Clicca sul pulsante "Crea"


## 📤 Parte 2: Importazione dei File SQL

### Passo 1: Seleziona il Database
1. Nel menu di sinistra, clicca sul nome del database appena creato
2. Verrai portato alla dashboard del database

### Passo 2: Avvia l'Importazione
1. Clicca sulla scheda "Importa" in alto
2. Nella sezione "File da importare":
   - Clicca "Sfoglia" e seleziona il file .sql (es. `eventi_web.sql`)
3. Impostazioni importanti:
   - Formato: SQL
   - Codifica: utf-8
4. Lascia tutte le altre opzioni predefinite
5. Clicca "Esegui" in fondo alla pagina



## ✔️ Parte 3: Verifica dell'Importazione

### Passo 1: Controlla le Tabelle
1. Nel menu di sinistra, espandi il tuo database
2. Dovresti vedere l'elenco delle tabelle create
   - Per `eventi_web.sql`: dovresti vedere `utenti`, `eventi`, etc.
   - Per `turismo.sql`: `luoghi`, `commenti`, etc.

### Passo 2: Controlla i Dati
1. Clicca su una tabella
2. Vai alla scheda "Sfoglia"
3. Verifica che i dati siano stati importati correttamente

## 🔧 Parte 4: Configurazione del File db.php

Ogni progetto ha un file `db.php` che va modificato con queste informazioni:

```php
<?php
$host = "localhost";    // Non cambiare
$user = "ElTaras";         // Utente predefinito XAMPP
$password = "ciao";         // Password (vuota in XAMPP)
$dbname = "202425_5ib_ElTaras_nome_db";    // Sostituisci con il nome del database

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>
```

### Il codice `install.php` ha utente root e password vuota per semplificare l'installazione del database a chiunque.

```php
<?php
$host = "localhost";    // Non cambiare
$user = "root";         // Utente predefinito XAMPP
$password = "";         // Password (vuota in XAMPP)


```
## 🚨 Risoluzione Problemi Comuni

### Errore "#1044 - Accesso negato"
- Soluzione: Assicurati di usare:
  - Utente: `ElTaras`
  - Password: `"ciao"` 

### Errore "#1049 - Database sconosciuto"
- Soluzione: Controlla di aver:
  1. Creato il database
  2. Scritto correttamente il nome in `db.php`

### Errore durante l'importazione
1. Verifica che il file SQL non sia corrotto
2. Controlla che sia nel formato corretto (UTF-8)
3. Prova a importare in parti più piccole

## 💡 Consigli Avanzati

1. **Backup Database**:
   - In phpMyAdmin, seleziona il database
   - Vai su "Esporta" > "Esportazione rapida" > "SQL"
   - Clicca "Esegui" per scaricare il backup

2. **Modificare i Privilegi**:
   - Se necessario, in phpMyAdmin:
     1. Vai su "Account utente"
     2. Modifica i privilegi per `root@localhost`

3. **Struttura Database**:
   - Ogni file .sql contiene:
     - `CREATE TABLE` - crea le tabelle
     - `INSERT INTO` - popola i dati iniziali
     - Relazioni tra tabelle (chiavi esterne)

## 📚 Struttura dei Database Inclusi

### 1. eventi_web.sql
- Tabelle principali:
  - `utenti` (id, username, password, email)
  - `eventi` (id, titolo, descrizione, data, luogo)
- Relazioni: eventi collegati agli utenti creatori

### 2. turismo.sql
- Tabelle:
  - `luoghi` (id, nome, descrizione, immagine)
  - `commenti` (id, testo, valutazione, id_utente)
- Chiavi esterne: commenti collegati a luoghi e utenti

