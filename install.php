<?php
// install.php (nella root del progetto: trasferimento-quaderno-d-informatica-zAlessandro7/)

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$flag_file_name = '.installed.flag'; // Nella stessa directory di install.php
$db_prefix = "202425_5IB_ElTaras_";

if (file_exists($flag_file_name)) {
    $_SESSION['message'] = "<p class='warn_msg'>L'installazione sembra essere già stata completata. Per reinstallare, elimina prima il file '<b>" . htmlspecialchars($flag_file_name) . "</b>' e poi aggiorna la pagina index.</p>";
    header('Location: index.php'); // index.php della root del progetto generale
    exit;
}

$install_successful = false;
$errors_occurred = false;
$user_creation_errors = false;

echo "<!DOCTYPE html><html lang='it'><head><meta charset='UTF-8'><title>Installazione Database</title>";
echo "<style>body{font-family:sans-serif;margin:20px} hr{margin:20px 0} .ok_msg{color:green} .error_msg{color:red;font-weight:bold} .warn_msg{color:orange}</style>";
echo "</head><body><h1>Processo di Installazione Database</h1>";

$servername = "localhost";
$username_root = "root";
$password_root = ""; // PASSWORD DI ROOT (lascia vuota se l'utente root di MySQL non ha password)
$database_initial = "";

echo "<h2>1. Connessione come Utente Amministratore ('" . htmlspecialchars($username_root) . "')</h2>";
$conn = new mysqli($servername, $username_root, $password_root, $database_initial);

if ($conn->connect_error) {
    echo "<p class='error_msg'>ERRORE FATALE: Connessione (admin) fallita: " . htmlspecialchars($conn->connect_error) . "</p>";
    $errors_occurred = true;
} else {
    echo "<p class='ok_msg'>Connessione (admin) riuscita.</p>";

    $appUser = "ElTaras";
    $appPassword = "ciao";
    echo "<hr><h2>2. Preparazione Utente Applicazione ('" . htmlspecialchars($appUser) . "')</h2>";
    
    $escaped_appUser = $conn->real_escape_string($appUser);
    $escaped_appPassword = $conn->real_escape_string($appPassword);

    echo "Tentativo di rimozione utente '$escaped_appUser'@'localhost'... ";
    if ($conn->query("DROP USER IF EXISTS '$escaped_appUser'@'localhost'")) {
        echo "<span class='ok_msg'>OK.</span><br>";
    } else {
        echo "<span class='warn_msg'>AVVISO/ERRORE 'DROP USER': " . htmlspecialchars($conn->error) . "</span><br>";
    }

    echo "Tentativo di creazione utente '$escaped_appUser'@'localhost'... ";
    if ($conn->query("CREATE USER '$escaped_appUser'@'localhost' IDENTIFIED BY '$escaped_appPassword'")) {
        echo "<span class='ok_msg'>OK. Utente creato.</span><br>";
    } else {
        echo "<span class='error_msg'>ERRORE 'CREATE USER': " . htmlspecialchars($conn->error) . "</span><br>";
        $errors_occurred = true;
        $user_creation_errors = true;
    }

    if (!$user_creation_errors) {
        echo "Tentativo di concessione privilegi a '$escaped_appUser'@'localhost'... ";
        if ($conn->query("GRANT ALL PRIVILEGES ON *.* TO '$escaped_appUser'@'localhost' WITH GRANT OPTION")) {
            echo "<span class='ok_msg'>OK. Tutti i privilegi (su *.*) concessi.</span><br>";
        } else {
            echo "<span class='error_msg'>ERRORE 'GRANT PRIVILEGES': " . htmlspecialchars($conn->error) . "</span><br>";
            $errors_occurred = true;
        }

        echo "Tentativo di aggiornare i privilegi (FLUSH PRIVILEGES)... ";
        if ($conn->query("FLUSH PRIVILEGES")) {
            echo "<span class='ok_msg'>OK. Privilegi aggiornati.</span><br>";
        } else {
            echo "<span class='warn_msg'>AVVISO 'FLUSH PRIVILEGES': " . htmlspecialchars($conn->error) . "</span><br>";
        }
    }

    if (!$errors_occurred) {
        echo "<hr><h2>3. Configurazione Database e Tabelle (con Prefisso per ogni DB)</h2>";
        
        $conn->begin_transaction();
        $db_setup_successful_transaction = true;

        try {
            // Array dei database con i loro nomi BASE e le relative query
            $databases_config = [
                ["base_name" => "film_db", "queries" => [
                    // ... (query per film_db come prima) ...
                    "CREATE TABLE IF NOT EXISTS attore ( Codice_Attore INT(11) NOT NULL AUTO_INCREMENT, Nome VARCHAR(50) DEFAULT NULL, Cognome VARCHAR(50) DEFAULT NULL, Nazionalità VARCHAR(50) DEFAULT NULL, PRIMARY KEY (Codice_Attore) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS attori ( Codice_Attore INT(11) NOT NULL AUTO_INCREMENT, Nome VARCHAR(50) NOT NULL, Cognome VARCHAR(50) NOT NULL, Data_Nascita DATE NOT NULL, Nazionalità VARCHAR(50) DEFAULT NULL, PRIMARY KEY (Codice_Attore) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS film ( Codice_Film INT(11) NOT NULL AUTO_INCREMENT, Titolo VARCHAR(100) DEFAULT NULL, Anno_Produzione INT(11) DEFAULT NULL, Regista VARCHAR(100) DEFAULT NULL, PRIMARY KEY (Codice_Film) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS proiezione ( Codice_Proiezione INT(11) NOT NULL AUTO_INCREMENT, Città VARCHAR(100) DEFAULT NULL, Sala VARCHAR(50) DEFAULT NULL, Data DATE DEFAULT NULL, Ora TIME DEFAULT NULL, Numero_Spettatori INT(11) DEFAULT NULL, PRIMARY KEY (Codice_Proiezione) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS film_attore ( Codice_Film INT(11) NOT NULL, Codice_Attore INT(11) NOT NULL, Ruolo ENUM('Protagonista', 'Non Protagonista') DEFAULT NULL, PRIMARY KEY (Codice_Film, Codice_Attore), FOREIGN KEY (Codice_Film) REFERENCES film(Codice_Film) ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN KEY (Codice_Attore) REFERENCES attori(Codice_Attore) ON DELETE CASCADE ON UPDATE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS film_proiezione ( Codice_Film INT(11) NOT NULL, Codice_Proiezione INT(11) NOT NULL, PRIMARY KEY (Codice_Film, Codice_Proiezione), FOREIGN KEY (Codice_Film) REFERENCES film(Codice_Film) ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN KEY (Codice_Proiezione) REFERENCES proiezione(Codice_Proiezione) ON DELETE CASCADE ON UPDATE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "INSERT INTO attori (Nome, Cognome, Data_Nascita, Nazionalità) VALUES ('Mario', 'Rossi', '1980-01-15', 'Italiana'), ('Luca', 'Bianchi', '1975-07-22', 'Italiana'), ('Giovanna', 'Verdi', '1992-03-10', 'Italiana'), ('Marino', 'Torsello', '2007-01-25', 'Italiana') ON DUPLICATE KEY UPDATE Nome=VALUES(Nome)",
                    "INSERT INTO film (Titolo, Anno_Produzione, Regista) VALUES ('Film Uno', 2020, 'Regista A'), ('Film Due', 2021, 'Regista B'), ('Altro Film', 2019, 'Regista C') ON DUPLICATE KEY UPDATE Titolo=VALUES(Titolo)"
                ]],
                ["base_name" => "eventi_web", "queries" => [
                    // ... (query per eventi_web come prima) ...
                    "CREATE TABLE IF NOT EXISTS eventi ( id INT(11) NOT NULL AUTO_INCREMENT, categoria VARCHAR(255) NOT NULL, luogo VARCHAR(255) NOT NULL, data DATE NOT NULL, titolo VARCHAR(255) NOT NULL, artisti TEXT NOT NULL, PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS utenti ( id INT(11) NOT NULL AUTO_INCREMENT, nickname VARCHAR(255) NOT NULL, nome VARCHAR(255) NOT NULL, cognome VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, categorie_interessate TEXT NOT NULL, PRIMARY KEY (id), UNIQUE KEY email (email) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS categorie ( id INT(11) NOT NULL AUTO_INCREMENT, nome VARCHAR(255) NOT NULL, PRIMARY KEY (id), UNIQUE KEY nome (nome) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS commenti ( id INT(11) NOT NULL AUTO_INCREMENT, evento_id INT(11) NOT NULL, utente_id INT(11) NOT NULL, commento TEXT NOT NULL, data_commento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(), PRIMARY KEY (id), KEY evento_id (evento_id), KEY utente_id (utente_id), CONSTRAINT ew_commenti_ibfk_1 FOREIGN KEY (evento_id) REFERENCES eventi(id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT ew_commenti_ibfk_2 FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "INSERT INTO eventi (categoria, luogo, data, titolo, artisti) VALUES ('Musica', 'Roma', '2025-08-15', 'Concerto Estivo', 'Artista X, Artista Y') ON DUPLICATE KEY UPDATE titolo=VALUES(titolo)"
                ]],
                ["base_name" => "turismo", "queries" => [
                    // ... (query per turismo come prima) ...
                    "CREATE TABLE IF NOT EXISTS poi ( id INT(11) NOT NULL AUTO_INCREMENT, nome VARCHAR(255) NOT NULL, descrizione TEXT DEFAULT NULL, indirizzo VARCHAR(255) DEFAULT NULL, immagine VARCHAR(255) DEFAULT NULL, data_creazione TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(), PRIMARY KEY (id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS utenti ( id INT(11) NOT NULL AUTO_INCREMENT, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, data_creazione TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(), PRIMARY KEY (id), UNIQUE KEY username (username), UNIQUE KEY email (email) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS commenti ( id INT(11) NOT NULL AUTO_INCREMENT, poi_id INT(11) NOT NULL, utente_id INT(11) NOT NULL, commento TEXT NOT NULL, voto INT(11) NOT NULL CHECK (voto BETWEEN 1 AND 5), data_commento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(), PRIMARY KEY (id), KEY poi_id (poi_id), KEY utente_id (utente_id), CONSTRAINT t_commenti_ibfk_1 FOREIGN KEY (poi_id) REFERENCES poi(id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT t_commenti_ibfk_2 FOREIGN KEY (utente_id) REFERENCES utenti(id) ON DELETE CASCADE ON UPDATE CASCADE ) ENGINE= InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
                ]],
                // RIMOSSO IL VECCHIO "lingue" PERCHÉ SOSTITUITO DA LinguePlatformDB
                // ["base_name" => "lingue", "queries" => [ ... query vecchie ... ]],

                ["base_name" => "FoodExpressDB", "queries" => [
                    // ... (query per FoodExpressDB come definite nella risposta precedente, con URL immagini) ...
                    "CREATE TABLE IF NOT EXISTS CLIENTE ( IDCliente INT AUTO_INCREMENT PRIMARY KEY, Nome VARCHAR(100) NOT NULL, Cognome VARCHAR(100) NOT NULL, Email VARCHAR(255) NOT NULL UNIQUE, PasswordHash VARCHAR(255) NOT NULL, IndirizzoConsegna TEXT, Telefono VARCHAR(20), ImmagineProfiloURL VARCHAR(255) DEFAULT NULL, DataRegistrazione DATETIME DEFAULT CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS RISTORANTE ( IDRistorante INT AUTO_INCREMENT PRIMARY KEY, NomeRistorante VARCHAR(255) NOT NULL, IndirizzoRistorante TEXT, TelefonoRistorante VARCHAR(20), OrariApertura VARCHAR(255), PartitaIVA VARCHAR(20) UNIQUE, Attivo BOOLEAN DEFAULT TRUE, ImmagineLogoURL VARCHAR(255) DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS PIATTO ( IDPiatto INT AUTO_INCREMENT PRIMARY KEY, IDRistorante INT NOT NULL, NomePiatto VARCHAR(255) NOT NULL, Descrizione TEXT, Prezzo DECIMAL(10, 2) NOT NULL, Disponibilita BOOLEAN DEFAULT TRUE, CategoriaPiatto VARCHAR(100), ImmaginePiattoURL VARCHAR(512) DEFAULT NULL, FOREIGN KEY (IDRistorante) REFERENCES RISTORANTE(IDRistorante) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT chk_PiattoPrezzo CHECK (Prezzo >= 0) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS ORDINE ( IDOrdine INT AUTO_INCREMENT PRIMARY KEY, IDCliente INT NOT NULL, IDRistorante INT NOT NULL, DataOraOrdine DATETIME DEFAULT CURRENT_TIMESTAMP, StatoOrdine ENUM('in attesa', 'confermato', 'in preparazione', 'in consegna', 'consegnato', 'annullato') NOT NULL DEFAULT 'in attesa', IndirizzoConsegnaOrdine TEXT NOT NULL, NoteCliente TEXT, TotaleOrdine DECIMAL(10, 2) DEFAULT 0.00, MetodoPagamento VARCHAR(50), IDPagamento VARCHAR(255), FOREIGN KEY (IDCliente) REFERENCES CLIENTE(IDCliente) ON DELETE RESTRICT ON UPDATE CASCADE, FOREIGN KEY (IDRistorante) REFERENCES RISTORANTE(IDRistorante) ON DELETE RESTRICT ON UPDATE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS DETTAGLIO_ORDINE ( IDDettaglio INT AUTO_INCREMENT PRIMARY KEY, IDOrdine INT NOT NULL, IDPiatto INT NOT NULL, Quantita INT NOT NULL, PrezzoUnitarioAlMomentoOrdine DECIMAL(10, 2) NOT NULL, FOREIGN KEY (IDOrdine) REFERENCES ORDINE(IDOrdine) ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN KEY (IDPiatto) REFERENCES PIATTO(IDPiatto) ON DELETE RESTRICT ON UPDATE CASCADE, CONSTRAINT chk_DettaglioQuantita CHECK (Quantita > 0) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "CREATE TABLE IF NOT EXISTS RECENSIONE ( IDRecensione INT AUTO_INCREMENT PRIMARY KEY, IDOrdine INT NOT NULL UNIQUE, IDCliente INT NOT NULL, IDRistorante INT NOT NULL, Voto INT NOT NULL, TestoRecensione TEXT, DataRecensione DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (IDOrdine) REFERENCES ORDINE(IDOrdine) ON DELETE CASCADE, FOREIGN KEY (IDCliente) REFERENCES CLIENTE(IDCliente) ON DELETE CASCADE, FOREIGN KEY (IDRistorante) REFERENCES RISTORANTE(IDRistorante) ON DELETE CASCADE, CONSTRAINT chk_RecensioneVoto CHECK (Voto BETWEEN 1 AND 5) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    "INSERT INTO CLIENTE (Nome, Cognome, Email, PasswordHash, IndirizzoConsegna, Telefono) VALUES ('Marco', 'Verdi', 'marco.verdi@foodexpress.com', '" . password_hash('password123', PASSWORD_DEFAULT) . "', 'Via Prova 7, Milano', '3351122334') ON DUPLICATE KEY UPDATE Nome=VALUES(Nome)",
                    "INSERT INTO RISTORANTE (NomeRistorante, IndirizzoRistorante, TelefonoRistorante, PartitaIVA, ImmagineLogoURL, Attivo) VALUES ('Da Gino - Trattoria Romana', 'Via Roma 100, FoodCity', '02998877', '11223344556', 'https://www.casatrattoria.it/wp-content/uploads/2021/02/Progetto-senza-titolo-59.png', TRUE), ('Sakura Sushi Express', 'Corso Como 5, FoodCity', '02665544', '66554433221', 'https://img.freepik.com/foto-premium/l-interno-di-un-ristorante-chiamato-il-futuro_853677-7298.jpg', TRUE) ON DUPLICATE KEY UPDATE NomeRistorante=VALUES(NomeRistorante), ImmagineLogoURL=VALUES(ImmagineLogoURL), Attivo=VALUES(Attivo)",
                    "INSERT INTO PIATTO (IDRistorante, NomePiatto, Descrizione, Prezzo, CategoriaPiatto, ImmaginePiattoURL) VALUES ( (SELECT IDRistorante FROM RISTORANTE WHERE NomeRistorante LIKE 'Da Gino%' LIMIT 1), 'Spaghetti alla Carbonara', 'Guanciale croccante, uova fresche, pecorino romano DOP, pepe nero macinato al momento.', 12.50, 'Primi Piatti', 'https://www.giallozafferano.it/images/219-21928/Spaghetti-alla-Carbonara_650x433_wm.jpg'), ( (SELECT IDRistorante FROM RISTORANTE WHERE NomeRistorante LIKE 'Da Gino%' LIMIT 1), 'Saltimbocca alla Romana', 'Fettine di vitello tenere, avvolte in prosciutto crudo e salvia, sfumate al vino bianco.', 16.75, 'Secondi Piatti', 'https://www.giallozafferano.it/images/ricette/204/20401/foto_hd/hd650x433_wm.jpg'), ( (SELECT IDRistorante FROM RISTORANTE WHERE NomeRistorante LIKE 'Da Gino%' LIMIT 1), 'Tiramisù Classico', 'Savoiardi inzuppati nel caffè, crema al mascarpone e cacao amaro.', 6.00, 'Dolci', 'https://www.giallozafferano.it/images/173-17354/Tiramisu_650x433_wm.jpg'), ( (SELECT IDRistorante FROM RISTORANTE WHERE NomeRistorante LIKE 'Sakura Sushi%' LIMIT 1), 'Nigiri Salmone (2pz)', 'Fettine di salmone fresco adagiate su riso sushi preparato secondo tradizione.', 4.50, 'Sushi Nigiri', 'https://blog.academia.tv/wp-content/uploads/2022/09/traditional-japanese-nigiri-sushi-with-salmon-placed-chopsticks-scaled.jpg'), ( (SELECT IDRistorante FROM RISTORANTE WHERE NomeRistorante LIKE 'Sakura Sushi%' LIMIT 1), 'Uramaki California (8pz)', 'Roll con surimi di granchio, avocado, cetriolo, maionese e uova di pesce volante all esterno.', 8.00, 'Sushi Rolls', 'https://blog.giallozafferano.it/dulcisinforno/wp-content/uploads/2018/05/1467Mod.jpg'), ( (SELECT IDRistorante FROM RISTORANTE WHERE NomeRistorante LIKE 'Sakura Sushi%' LIMIT 1), 'Edamame', 'Fagioli di soia al vapore, leggermente salati.', 3.50, 'Antipasti Giapponesi', 'https://www.profumodilimoni.com/wp-content/uploads/2017/11/edamamevert750.jpg') ON DUPLICATE KEY UPDATE NomePiatto=VALUES(NomePiatto), Descrizione=VALUES(Descrizione), Prezzo=VALUES(Prezzo), CategoriaPiatto=VALUES(CategoriaPiatto), ImmaginePiattoURL=VALUES(ImmaginePiattoURL)"
                ]],
                // NUOVA SEZIONE PER LINGUE PLATFORM DB
                ["base_name" => "LinguePlatformDB", "queries" => [
                    // INSEGNANTE
                    "CREATE TABLE IF NOT EXISTS INSEGNANTE ( IDInsegnante INT AUTO_INCREMENT PRIMARY KEY, Nome VARCHAR(100) NOT NULL, Cognome VARCHAR(100) NOT NULL, Email VARCHAR(255) NOT NULL UNIQUE, PasswordHash VARCHAR(255) NOT NULL, DataRegistrazione DATETIME DEFAULT CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    // STUDENTE
                    "CREATE TABLE IF NOT EXISTS STUDENTE ( IDStudente INT AUTO_INCREMENT PRIMARY KEY, Nome VARCHAR(100) NOT NULL, Cognome VARCHAR(100) NOT NULL, Email VARCHAR(255) NOT NULL UNIQUE, PasswordHash VARCHAR(255) NOT NULL, DataRegistrazione DATETIME DEFAULT CURRENT_TIMESTAMP ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    // TEMA_LINGUISTICO
                    "CREATE TABLE IF NOT EXISTS TEMA_LINGUISTICO ( IDTema INT AUTO_INCREMENT PRIMARY KEY, NomeTema VARCHAR(100) NOT NULL UNIQUE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    // ESERCIZIO_CATALOGO
                    "CREATE TABLE IF NOT EXISTS ESERCIZIO_CATALOGO ( IDEsercizio INT AUTO_INCREMENT PRIMARY KEY, IDTema INT, TitoloEsercizio VARCHAR(255) NOT NULL, DescrizioneEsercizio TEXT, DifficoltaLinguistica VARCHAR(10) NOT NULL, PuntiOttenibili INT NOT NULL DEFAULT 10, ImmagineURL VARCHAR(512), VideoURL VARCHAR(512), FOREIGN KEY (IDTema) REFERENCES TEMA_LINGUISTICO(IDTema) ON DELETE SET NULL ON UPDATE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    // CORSO_VIRTUALE
                    "CREATE TABLE IF NOT EXISTS CORSO_VIRTUALE ( IDCorso INT AUTO_INCREMENT PRIMARY KEY, IDInsegnanteCreatore INT NOT NULL, NomeCorso VARCHAR(255) NOT NULL, Lingua VARCHAR(50) NOT NULL, LivelloDifficolta VARCHAR(10) NOT NULL, CodiceIscrizione VARCHAR(50) UNIQUE, DataCreazione DATETIME DEFAULT CURRENT_TIMESTAMP, DescrizioneCorso TEXT, Attivo BOOLEAN DEFAULT TRUE, FOREIGN KEY (IDInsegnanteCreatore) REFERENCES INSEGNANTE(IDInsegnante) ON DELETE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    // ESERCIZIO_DEL_CORSO
                    "CREATE TABLE IF NOT EXISTS ESERCIZIO_DEL_CORSO ( IDEsercizioCorso INT AUTO_INCREMENT PRIMARY KEY, IDCorso INT NOT NULL, IDEsercizioCatalogo INT NOT NULL, OrdineInSequenza INT, DataAssegnazione DATETIME DEFAULT CURRENT_TIMESTAMP, VisibileAgliStudenti BOOLEAN DEFAULT TRUE, UNIQUE KEY uq_corso_esercizio (IDCorso, IDEsercizioCatalogo), FOREIGN KEY (IDCorso) REFERENCES CORSO_VIRTUALE(IDCorso) ON DELETE CASCADE, FOREIGN KEY (IDEsercizioCatalogo) REFERENCES ESERCIZIO_CATALOGO(IDEsercizio) ON DELETE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    // ISCRIZIONE_CORSO
                    "CREATE TABLE IF NOT EXISTS ISCRIZIONE_CORSO ( IDIscrizione INT AUTO_INCREMENT PRIMARY KEY, IDStudente INT NOT NULL, IDCorso INT NOT NULL, DataIscrizione DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY uq_studente_corso (IDStudente, IDCorso), FOREIGN KEY (IDStudente) REFERENCES STUDENTE(IDStudente) ON DELETE CASCADE, FOREIGN KEY (IDCorso) REFERENCES CORSO_VIRTUALE(IDCorso) ON DELETE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    // SVOLGIMENTO_ESERCIZIO
                    "CREATE TABLE IF NOT EXISTS SVOLGIMENTO_ESERCIZIO ( IDSvolgimento INT AUTO_INCREMENT PRIMARY KEY, IDEsercizioCorso INT NOT NULL, IDStudente INT NOT NULL, PunteggioOttenuto INT NOT NULL DEFAULT 0, DataCompletamento DATETIME DEFAULT CURRENT_TIMESTAMP, TempoImpiegatoSecondi INT, UNIQUE KEY uq_svolgimento (IDEsercizioCorso, IDStudente), FOREIGN KEY (IDEsercizioCorso) REFERENCES ESERCIZIO_DEL_CORSO(IDEsercizioCorso) ON DELETE CASCADE, FOREIGN KEY (IDStudente) REFERENCES STUDENTE(IDStudente) ON DELETE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
                    
                    // Dati di esempio per LinguePlatformDB
                    "INSERT INTO TEMA_LINGUISTICO (NomeTema) VALUES ('Vocabolario'), ('Grammatica'), ('Comprensione Orale'), ('Cultura Generale') ON DUPLICATE KEY UPDATE NomeTema=VALUES(NomeTema)",
                    "INSERT INTO INSEGNANTE (Nome, Cognome, Email, PasswordHash) VALUES ('Laura', 'Rossi', 'laura.doc@lingue.platform', '" . password_hash('passdoc1', PASSWORD_DEFAULT) . "'), ('Mario', 'Verdi', 'mario.doc@lingue.platform', '" . password_hash('passdoc2', PASSWORD_DEFAULT) . "') ON DUPLICATE KEY UPDATE Nome=VALUES(Nome)",
                    "INSERT INTO STUDENTE (Nome, Cognome, Email, PasswordHash) VALUES ('Giovanni', 'Bianchi', 'gio.bia@student.lingue', '" . password_hash('passstu1', PASSWORD_DEFAULT) . "'), ('Sofia', 'Neri', 'sof.ner@student.lingue', '" . password_hash('passstu2', PASSWORD_DEFAULT) . "') ON DUPLICATE KEY UPDATE Nome=VALUES(Nome)",
                    "INSERT INTO ESERCIZIO_CATALOGO (IDTema, TitoloEsercizio, DescrizioneEsercizio, DifficoltaLinguistica, PuntiOttenibili) VALUES ((SELECT IDTema FROM TEMA_LINGUISTICO WHERE NomeTema='Vocabolario'), 'Animali della Fattoria (Inglese A1)', 'Abbina nomi e immagini di animali comuni.', 'A1', 10), ((SELECT IDTema FROM TEMA_LINGUISTICO WHERE NomeTema='Grammatica'), 'Present Simple Tense (Inglese A2)', 'Completa le frasi con la forma corretta del Present Simple.', 'A2', 15) ON DUPLICATE KEY UPDATE TitoloEsercizio=VALUES(TitoloEsercizio)",
                    "INSERT INTO CORSO_VIRTUALE (IDInsegnanteCreatore, NomeCorso, Lingua, LivelloDifficolta, CodiceIscrizione) VALUES ((SELECT IDInsegnante FROM INSEGNANTE WHERE Email='laura.doc@lingue.platform'), 'Inglese Base per Viaggiatori', 'Inglese', 'A1', 'ENG_TRAVEL_A1'), ((SELECT IDInsegnante FROM INSEGNANTE WHERE Email='mario.doc@lingue.platform'), 'Spagnolo Introduttivo', 'Spagnolo', 'A1', 'SPA_INTRO_A1') ON DUPLICATE KEY UPDATE NomeCorso=VALUES(NomeCorso)"
                    // Potresti aggiungere INSERT per ESERCIZIO_DEL_CORSO, ISCRIZIONE_CORSO, SVOLGIMENTO_ESERCIZIO usando subquery per gli ID se necessario
                ]]
            ];

            // ... (resto del ciclo foreach ($databases_config as $db_item) come prima, per creare/selezionare DB ed eseguire query) ...
            foreach ($databases_config as $db_item) {
                $actual_db_name = $db_prefix . $db_item['base_name'];
                $actual_db_name_escaped = $conn->real_escape_string($actual_db_name);

                echo "<h3>Gestione del database: " . htmlspecialchars($actual_db_name) . "</h3>";

                echo "Tentativo di rimozione database '" . htmlspecialchars($actual_db_name) . "' (se esistente)... ";
                if ($conn->query("DROP DATABASE IF EXISTS `$actual_db_name_escaped`")) {
                    echo "<span class='ok_msg'>OK.</span><br>";
                } else {
                    echo "<span class='warn_msg'>AVVISO/ERRORE 'DROP DATABASE': " . htmlspecialchars($conn->error) . "</span><br>";
                }

                echo "Tentativo di creare il database '" . htmlspecialchars($actual_db_name) . "'... ";
                if ($conn->query("CREATE DATABASE `$actual_db_name_escaped` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
                    echo "<span class='ok_msg'>OK.</span><br>";
                } else {
                    throw new Exception("Errore creazione DB '" . htmlspecialchars($actual_db_name) . "': " . htmlspecialchars($conn->error));
                }

                echo "Tentativo di selezionare il database '" . htmlspecialchars($actual_db_name) . "'... ";
                if ($conn->select_db($actual_db_name)) {
                    echo "<span class='ok_msg'>OK.</span><br>";
                } else {
                    throw new Exception("Errore selezione DB '" . htmlspecialchars($actual_db_name) . "': " . htmlspecialchars($conn->error));
                }

                echo "<ul>";
                foreach ($db_item['queries'] as $query_index => $query) {
                    echo "<li>Esecuzione query #" . ($query_index + 1) . ": ";
                    if ($conn->query($query)) {
                        echo "<span class='ok_msg'>Successo.</span></li>";
                    } else {
                        echo "<span class='error_msg'>Errore: " . htmlspecialchars($conn->error) . "</span><br>Query: <code>" . htmlspecialchars($query) . "</code></li>";
                        $db_setup_successful_transaction = false; 
                    }
                }
                echo "</ul>";
            }
            
            // La sezione per inserire 'user1' in 'turismo' rimane invariata se vuoi mantenerla
            $turismo_db_actual_name = $db_prefix . "turismo";
            if ($db_setup_successful_transaction && $conn->select_db($turismo_db_actual_name)) {
                // ... (codice per inserire user1 in turismo come prima) ...
                echo "<hr><h3>Inserimento utente 'user1' nel database '" . htmlspecialchars($turismo_db_actual_name) . "' (se non esiste)</h3>";
                $username_turismo_user1 = 'user1';
                $table_turismo_utenti = 'utenti';
                echo "Controllo username '$username_turismo_user1' in " . htmlspecialchars($turismo_db_actual_name) . ".$table_turismo_utenti... ";
                if (checkUsernameExists($conn, $username_turismo_user1, $table_turismo_utenti)) {
                    echo "Già esistente.<br>";
                } else {
                    echo "Non esistente, tentativo di inserimento... ";
                    $password_hash = password_hash('hashed_password_1', PASSWORD_BCRYPT);
                    $email = 'user1@example.com';
                    $query_insert = "INSERT INTO $table_turismo_utenti (username, password, email) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($query_insert);
                    if ($stmt) {
                        $stmt->bind_param("sss", $username_turismo_user1, $password_hash, $email);
                        if ($stmt->execute()) {
                            echo "<span class='ok_msg'>Utente inserito con successo.</span><br>";
                        } else {
                            echo "<span class='error_msg'>Errore inserimento: " . htmlspecialchars($stmt->error) . "</span><br>";
                            $db_setup_successful_transaction = false;
                        }
                        $stmt->close();
                    } else {
                        echo "<span class='error_msg'>Errore prepare: " . htmlspecialchars($conn->error) . "</span><br>";
                        $db_setup_successful_transaction = false;
                    }
                }
            } elseif (!$conn->select_db($turismo_db_actual_name) && $db_setup_successful_transaction) {
                 echo "<p class='warn_msg'>DB '" . htmlspecialchars($turismo_db_actual_name) . "' non selezionabile per user1.</p>";
            }


            if ($db_setup_successful_transaction) {
                $conn->commit();
                echo "<p class='ok_msg'>Commit eseguito.</p>";
                $install_successful = true;
            } else {
                $conn->rollback();
                echo "<p class='error_msg'>Rollback eseguito.</p>";
                $errors_occurred = true;
            }

        } catch (Exception $e) {
            if ($conn && $conn->ping()) { 
                 $conn->rollback();
                 echo "<p class='error_msg'>ERRORE DURANTE CONFIGURAZIONE: " . htmlspecialchars($e->getMessage()) . " (Rollback eseguito)</p>";
            } else {
                 echo "<p class='error_msg'>ERRORE DURANTE CONFIGURAZIONE: " . htmlspecialchars($e->getMessage()) . " (Rollback non possibile, connessione persa o non attiva)</p>";
            }
            $errors_occurred = true;
        }
    }

    if ($conn && $conn->ping()) { 
        $conn->close();
        echo "<p>Connessione DB chiusa.</p>";
    } else if (!$errors_occurred && !($conn && $conn->ping())){ 
        echo "<p class='warn_msg'>Connessione DB non stabilita o già chiusa.</p>";
    }
}

// ... (resto del codice per i messaggi finali e la creazione del flag, identico a prima) ...
echo "<hr><h2>Risultato Installazione</h2>";
if (!$errors_occurred && $install_successful) {
    echo "<p class='ok_msg'>Installazione completata con successo!</p>";
    if (file_put_contents($flag_file_name, date('Y-m-d H:i:s') . " - Installazione con prefisso DB: " . $db_prefix) !== false) {
        echo "<p class='ok_msg'>File flag '<b>" . htmlspecialchars($flag_file_name) . "</b>' creato.</p>";
    } else {
        echo "<p class='error_msg'>ATTENZIONE: Impossibile creare file flag.</p>";
    }
    $_SESSION['message'] = "<p class='ok_msg'>Installazione completata! DB creati con prefisso '" . htmlspecialchars($db_prefix) . "'.</p>";
    echo "<p><a href='index.php'>Torna alla pagina principale</a></p>";
} else {
    echo "<p class='error_msg'>Installazione fallita o con errori.</p>";
    if (file_exists($flag_file_name)) {
        unlink($flag_file_name);
        echo "<p class='warn_msg'>File flag rimosso causa errori.</p>";
    }
    $_SESSION['message'] = "<p class='error_msg'>L'installazione è fallita. Controlla messaggi.</p>";
    echo "<p><a href='index.php'>Riprova</a></p>";
}
echo "</body></html>";

function checkUsernameExists($conn, $username, $tableNameOnly) {
    $safeTableNameOnly = "`" . $conn->real_escape_string($tableNameOnly) . "`";
    $query = "SELECT COUNT(*) FROM $safeTableNameOnly WHERE username = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) { echo "<span class='warn_msg'>(Err prep checkUser)</span>"; return true; }
    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) { echo "<span class='warn_msg'>(Err exec checkUser)</span>"; $stmt->close(); return true; }
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

?>