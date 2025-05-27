<?php
// 2simulazione-esame2025/ClienteManager.php
// Classe per la gestione degli utenti (Clienti) nel progetto Food Express.

// Assicura che la sessione sia attiva (necessario per gestire i messaggi di feedback)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include la configurazione del database specifica per Food Express
// Questo file DEVE definire DB_HOST, DB_USER_APP, DB_PASS_APP, DB_CHARSET,
// DB_PREFIX (se applicabile), get_db_name(), DB_NAME_FOODEXPRESS.
require_once __DIR__ . '/database_config.php'; 

// Include la classe di connessione al database che userà la configurazione
require_once __DIR__ . '/DatabaseConnection.php'; 

// Assumendo che DB_NAME_FOODEXPRESS sia definito in database_config.php
/*
if (!defined('DB_NAME_FOODEXPRESS')) {
     error_log("DB_NAME_FOODEXPRESS non definito in ClienteManager.php. Controlla database_config.php");
     // In produzione, potresti voler lanciare un'eccezione o mostrare un errore generico
     // die("Errore di configurazione del database Food Express (ClienteManager).");
}
*/

class ClienteManager { // Questa è l'inizio della classe
    private $db_conn; // Connessione PDO al database Food Express (202425_5IB_ElTaras_FoodExpressDB)

    /**
     * Costruttore della classe ClienteManager. Stabilisce la connessione al database Food Express.
     */
    public function __construct() {
        // Il costruttore di DatabaseConnection gestisce la session_start() (se non già attiva) e la connessione.
        // Se la connessione fallisce, lancerà un die().
        try {
            // Usa la classe DatabaseConnection che già sa come usare database_config.php.
            // Passa il nome base del DB Food Express che è definito in database_config.php.
            // Se DatabaseConnection ha un costruttore senza parametri, usa:
            // $db_object = new DatabaseConnection();
            // Altrimenti, se prende il nome base del DB:
            $db_object = new DatabaseConnection(DB_NAME_FOODEXPRESS); 
            
            $this->db_conn = $db_object->getConnection(); // Ottiene l'oggetto connessione PDO
        } catch (Exception $e) {
             // L'errore dettagliato viene loggato in DatabaseConnection.
             // Qui, logghiamo solo che il Manager non ha potuto connettersi.
             error_log("ClienteManager CONSTRUCT: Connessione DB Food Express fallita.");
             $this->db_conn = null; 
        }
    }

    /**
     * Controlla se un'email esiste già nella tabella CLIENTE.
     * @param string $email L'email da controllare.
     * @param int|null $excludeId L'ID del cliente corrente da escludere (utile per l'update del profilo).
     * @return bool True se l'email esiste già per un ALTRO cliente, false altrimenti.
     */
    private function emailExists($email, $excludeId = null) {
         if (!$this->db_conn) { error_log("ClienteManager::emailExists - Connessione DB nulla."); return true; }
        try {
            $sql = "SELECT IDCliente FROM CLIENTE WHERE Email = :email";
            if ($excludeId !== null && is_numeric($excludeId)) {
                $sql .= " AND IDCliente != :exclude_id";
            }
            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':email', $email);
             if ($excludeId !== null) { $stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT); }
            $stmt->execute();
            return $stmt->fetch() ? true : false; 
        } catch (PDOException $e) {
            error_log("Errore PDO ClienteManager::emailExists(email: $email, excludeId: $excludeId): " . $e->getMessage()); return true; 
        }
    }

    /**
     * Registra un nuovo cliente nel database.
     * @param string $nome Nome.
     * @param string $cognome Cognome.
     * @param string $email Email.
     * @param string $password Password in chiaro.
     * @param string|null $indirizzo Indirizzo principale (opzionale).
     * @param string|null $telefono Telefono (opzionale).
     * @return bool True in caso di successo, false altrimenti.
     */
    public function registerUser($nome, $cognome, $email, $password, $indirizzo = null, $telefono = null) {
        if (!$this->db_conn) {
            $_SESSION['feedback_message'] = "Errore di connessione al database.";
            $_SESSION['feedback_type'] = 'danger';
            error_log("ClienteManager::registerUser - Connessione DB nulla."); return false;
        }
        if ($this->emailExists($email)) {
             $_SESSION['feedback_message'] = "Questa email è già registrata.";
             $_SESSION['feedback_type'] = 'warning';
             return false;
        }
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            if ($password_hash === false) { 
                error_log("ClienteManager::registerUser - Errore hashing password.");
                throw new Exception("Errore hashing password."); 
            }
            $sql = "INSERT INTO CLIENTE (Nome, Cognome, Email, PasswordHash, IndirizzoConsegna, Telefono, DataRegistrazione) 
                    VALUES (:nome, :cognome, :email, :passwordhash, :indirizzo, :telefono, NOW())";
            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':nome', $nome); 
            $stmt->bindParam(':cognome', $cognome); 
            $stmt->bindParam(':email', $email); 
            $stmt->bindParam(':passwordhash', $password_hash); 
            $stmt->bindParam(':indirizzo', $indirizzo); 
            $stmt->bindParam(':telefono', $telefono);
            
            return $stmt->execute();

        } catch (PDOException $e) { 
            error_log("Errore PDO ClienteManager::registerUser(): " . $e->getMessage() . " SQL: " . $sql);
            $_SESSION['feedback_message'] = "Errore tecnico durante la registrazione.";
            $_SESSION['feedback_type'] = 'danger';
            return false; 
        } catch (Exception $e) { 
             error_log("Eccezione ClienteManager::registerUser(): " . $e->getMessage());
            $_SESSION['feedback_message'] = "Si è verificato un errore imprevisto durante la registrazione.";
            $_SESSION['feedback_type'] = 'danger';
            return false; 
        }
    }

    /**
     * Tenta il login di un cliente verificando le credenziali e impostando la sessione.
     * @param string $email L'email del cliente.
     * @param string $password La password in chiaro.
     * @return array|false I dati dell'utente (IDCliente, Nome, Cognome, Email) se il login ha successo, false altrimenti.
     */
    // QUESTA È LA RIGA CHE INIZIA INTORNO ALLA 79
    public function loginUser($email, $password) { 
        // Validazione basica dell'input
         if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
             error_log("ClienteManager::loginUser - Email non valida o password mancante.");
             return false;
         }

        if (!$this->db_conn) { 
            error_log("ClienteManager::loginUser - Connessione DB nulla.");
            return false;
        }
        try {
            // Recupera l'utente per email
            $sql = "SELECT IDCliente, Nome, Cognome, Email, PasswordHash FROM CLIENTE WHERE Email = :email";
            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':email', $email); 
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC); // fetch() restituisce false se non trova righe

            // Verifica se l'utente è stato trovato E se la password corrisponde
            if ($user && password_verify($password, $user['PasswordHash'])) {
                // AUTENTICAZIONE RIUSCITA!

                // Pulizia delle precedenti variabili di sessione di login di questo progetto (se ce ne fossero di vecchie)
                // Queste sono le sessioni usate per l'autenticazione in FoodExpress
                unset($_SESSION['cliente_id']);
                unset($_SESSION['cliente_nome']);
                unset($_SESSION['cliente_cognome']);
                unset($_SESSION['cliente_email']);
                // Se hai usato chiavi generiche per tipo utente, puliscile anche,
                // ma in questo progetto 'cliente' è l'unico tipo gestito da questo manager.
                // unset($_SESSION['user_type_fe']); 

                // Impostazione delle nuove variabili di sessione USANDO I DATI RECUPERATI DAL DB ($user)
                $_SESSION['cliente_id'] = (int)$user['IDCliente'];     // <<< SALVA L'ID REALE DAL DB
                $_SESSION['cliente_nome'] = $user['Nome'];     // Salva il Nome dal DB
                $_SESSION['cliente_cognome'] = $user['Cognome']; // Salva il Cognome dal DB
                $_SESSION['cliente_email'] = $user['Email'];   // Salva l'Email dal DB

                // Puoi aggiungere altre variabili di sessione specifiche se necessario
                // $_SESSION['user_type_fe'] = 'cliente'; // Un flag se necessario per auth_check
                // $_SESSION['is_logged_in_fe'] = true; // Un flag booleano

                // DEBUG OPZIONALE: Logga il successo con i dati salvati
                /*
                error_log("DEBUG LOGIN USER SUCCESSO: Loggato Cliente ID " . $_SESSION['cliente_id'] . 
                          " Nome " . $_SESSION['cliente_nome'] . 
                          " Email " . $_SESSION['cliente_email']);
                */

                return $user; // Ritorna i dati base dell'utente (senza password hash)
            }
            // Se l'utente non è trovato O la password non è corretta
            return false; // Indica che il login è fallito (credenziali non corrispondono)

        } catch (PDOException $e) {
            // Cattura errori specifici del database durante la query
            error_log("Errore PDO ClienteManager::loginUser(): " . $e->getMessage());
            // Non mostrare l'errore DB all'utente finale
            return false; // Indica fallimento (errore tecnico)
        }
    }

    /**
     * Recupera i dettagli completi del profilo di un utente Cliente dal database.
     * @param int $idCliente L'ID del cliente.
     * @return array|false I dati del profilo (IDCliente, Nome, Cognome, Email, IndirizzoConsegna, Telefono, DataRegistrazione) o false se non trovato/errore.
     */
    public function getProfiloUtente($idCliente) {
        // Validazione basica dell'input
        if (!$this->db_conn || !is_numeric($idCliente) || $idCliente <= 0) {
             error_log("ClienteManager::getProfiloUtente - Connessione DB nulla o ID cliente non valido: $idCliente");
            return false;
        }

        try {
            // Prepara e esegui la query per recuperare il profilo del cliente
            $sql = "SELECT IDCliente, Nome, Cognome, Email, IndirizzoConsegna, Telefono, DataRegistrazione 
                    FROM CLIENTE 
                    WHERE IDCliente = :id_cliente";
            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            error_log("Errore PDO ClienteManager::getProfiloUtente(ID: $idCliente): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Aggiorna i dettagli del profilo di un cliente esistente.
     * La password viene aggiornata solo se viene fornita una nuova password.
     * @param int $idCliente L'ID del cliente da aggiornare.
     * @param string $nome Nuovo nome.
     * @param string $cognome Nuovo cognome.
     * @param string $email Nuova email.
     * @param string|null $indirizzo Nuovo indirizzo (opzionale).
     * @param string|null $telefono Nuovo telefono (opzionale).
     * @param string|null $newPassword Nuova password in chiaro (opzionale).
     * @return bool True in caso di successo, false altrimenti.
     */
     public function updateProfiloUtente($idCliente, $nome, $cognome, $email, $indirizzo, $telefono, $newPassword = null) {
        // Validazione basica dell'input
        if (!$this->db_conn || !is_numeric($idCliente) || $idCliente <= 0) {
            $_SESSION['feedback_message'] = "Dati utente non validi per l'aggiornamento.";
             $_SESSION['feedback_type'] = 'danger';
            error_log("ClienteManager::updateProfiloUtente - Connessione DB nulla o ID cliente non valido: $idCliente");
            return false;
        }

        // Controlla se la nuova email fornita è già usata da un ALTRO cliente (escludendo il cliente corrente)
        if ($this->emailExists($email, $idCliente)) {
            // Messaggio di errore già impostato da emailExists
            return false; 
        }

        try {
            // Costruisci la query SQL di UPDATE
            $sql_parts = ["Nome = :nome", "Cognome = :cognome", "Email = :email", "IndirizzoConsegna = :indirizzo", "Telefono = :telefono"];
            $params = [
                ':nome' => $nome,
                ':cognome' => $cognome,
                ':email' => $email,
                ':indirizzo' => $indirizzo,
                ':telefono' => $telefono,
                ':id_cliente' => $idCliente // Parametro per la clausola WHERE
            ];

            // Se è stata fornita una nuova password, aggiungila alla query e ai parametri
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                     $_SESSION['feedback_message'] = "La nuova password deve essere di almeno 6 caratteri.";
                     $_SESSION['feedback_type'] = 'warning';
                     return false; 
                }
                $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
                if ($password_hash === false) { 
                    throw new Exception("Errore durante l'hashing della nuova password."); 
                }
                $sql_parts[] = "PasswordHash = :passwordhash";
                $params[':passwordhash'] = $password_hash;
            }
            
            // Completa la query UPDATE
            $sql = "UPDATE CLIENTE SET " . implode(", ", $sql_parts) . " WHERE IDCliente = :id_cliente";
            
            // Prepara e esegui la query
            $stmt = $this->db_conn->prepare($sql);
            $success = $stmt->execute($params);

            if ($success) {
                // Se l'aggiornamento nel DB ha successo, aggiorna anche i dati in sessione se è l'utente loggato corrente
                // L'ID cliente loggato è $_SESSION['cliente_id']
                if (isset($_SESSION['cliente_id']) && (int)$_SESSION['cliente_id'] === (int)$idCliente) {
                    $_SESSION['cliente_nome'] = $nome;
                    $_SESSION['cliente_cognome'] = $cognome;
                    $_SESSION['cliente_email'] = $email;
                    // L'indirizzo e telefono potrebbero anche essere salvati in sessione se usati frequentemente
                    // $_SESSION['cliente_indirizzo'] = $indirizzo;
                    // $_SESSION['cliente_telefono'] = $telefono;
                }
                return true; // Indica successo
            }
            return false; // Indica fallimento (es. 0 righe affette - ID cliente non trovato nel WHERE, anche se getProfiloUtente dovrebbe impedirlo di arrivare qui)

        } catch (PDOException $e) { 
            error_log("Errore PDO ClienteManager::updateProfiloUtente(ID: $idCliente): " . $e->getMessage() . " SQL: " . $sql);
            $_SESSION['feedback_message'] = "Errore tecnico durante l'aggiornamento del profilo nel database.";
            $_SESSION['feedback_type'] = 'danger';
            return false; // Indica fallimento
        } catch (Exception $e) { 
            error_log("Eccezione ClienteManager::updateProfiloUtente(ID: $idCliente): " . $e->getMessage());
            $_SESSION['feedback_message'] = "Si è verificato un errore imprevisto durante l'aggiornamento del profilo.";
            $_SESSION['feedback_type'] = 'danger';
            return false; // Indica fallimento
        }
    }
}
?>