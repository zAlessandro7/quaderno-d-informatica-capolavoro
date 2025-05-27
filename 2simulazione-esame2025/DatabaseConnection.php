<?php
// 2simulazione-esame2025/DatabaseConnection.php
// Classe per stabilire e fornire la connessione al database specifico del progetto.

// Include la configurazione del database (che deve definire DB_HOST, DB_USER_APP, ecc.)
require_once __DIR__ . '/database_config.php';

class DatabaseConnection { // Nome della classe per questo progetto Food Express
    private $host = DB_HOST;
    private $db_user = DB_USER_APP;
    private $db_pass = DB_PASS_APP;
    private $charset = DB_CHARSET;
    public $conn; // Variabile che conterrà l'oggetto connessione PDO

    /**
     * Costruisce l'oggetto connessione al database.
     * @param string $base_db_name Il nome base del database a cui connettersi (es. 'FoodExpressDB').
     */
    public function __construct($base_db_name) { // Costruttore che prende il nome base del DB
        $this->conn = null; // Inizializza la connessione a null

        // Ottiene il nome completo del database combinando prefisso e nome base (usando la funzione da database_config.php)
        $actual_db_name = get_db_name($base_db_name); 

        try {
            // Costruisce la stringa DSN per PDO
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $actual_db_name . ";charset=" . $this->charset;
            
            // Tenta di stabilire la connessione PDO
            $this->conn = new PDO($dsn, $this->db_user, $this->db_pass);
            
            // Imposta attributi importanti per PDO per la gestione degli errori e il fetching
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Fa lanciare eccezioni in caso di errori SQL
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Risultati fetch come array associativi
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Buona pratica per la sicurezza e gestione tipi

            // DEBUG OPZIONALE: Logga il successo della connessione
            // error_log("FOOD EXPRESS DB CONNECTION SUCCESS in DatabaseConnection a " . htmlspecialchars($actual_db_name));

        } catch(PDOException $e) {
            // Registra l'errore PDO completo nel log del server per debug
            error_log("FOOD EXPRESS DB CONNECTION ERROR in DatabaseConnection a " . htmlspecialchars($actual_db_name) . ": " . $e->getMessage());
            
            // Mostra un messaggio di errore amichevole all'utente e termina lo script
            // Non mostrare dettagli PDO all'utente finale per sicurezza.
            die("Si è verificato un errore tecnico e non è stato possibile connettersi al database. (" . htmlspecialchars($actual_db_name) . "). Riprova più tardi.");
        }
    }

    /**
     * Restituisce l'oggetto connessione PDO attivo.
     * Questo metodo viene chiamato dai Manager (OrdineManager, ClienteManager, ecc.).
     * @return PDO|null L'oggetto connessione PDO se stabilita, altrimenti null.
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Chiude la connessione al database (opzionale, PHP lo fa automaticamente a fine script).
     */
    public function close() { 
        $this->conn = null; 
    }
}
?>