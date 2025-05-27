<?php
// 1simulazione-esame2025/LpUtenteManager.php
require_once __DIR__ . '/LpDatabaseConnection.php';

class LpUtenteManager {
    private $db_conn;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $db_object = new LpDatabaseConnection(); 
        $this->db_conn = $db_object->getConnection();
    }

    private function emailExists($email, $userType = 'studente', $excludeId = null) {
        $userTypeNormalized = strtolower($userType);
        $table = ($userTypeNormalized === 'insegnante') ? 'INSEGNANTE' : 'STUDENTE';
        $idField = ($userTypeNormalized === 'insegnante') ? 'IDInsegnante' : 'IDStudente';
        if (!$this->db_conn) {error_log("LpUtenteManager::emailExists - Connessione DB nulla."); return true;}
        try {
            $sql = "SELECT `$idField` FROM `$table` WHERE Email = :email";
            if ($excludeId !== null && is_numeric($excludeId)) {
                $sql .= " AND `$idField` != :exclude_id";
            }
            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            if ($excludeId !== null && is_numeric($excludeId)) {
                $stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetch() ? true : false;
        } catch (PDOException $e) {
            error_log("Errore LpUtenteManager::emailExists($userTypeNormalized, excludeId: $excludeId): " . $e->getMessage() . " SQL: " . $sql);
            return true; 
        }
    }
    
    public function registerUtente($nome, $cognome, $email, $password, $tipoUtente) {
        $tipoUtenteNormalized = strtolower($tipoUtente);
        if ($tipoUtenteNormalized !== 'studente' && $tipoUtenteNormalized !== 'insegnante') {
            $_SESSION['lp_feedback_error'] = "Tipo utente non valido specificato per la registrazione.";
            return false;
        }
        if ($this->emailExists($email, $tipoUtenteNormalized)) {
            $_SESSION['lp_feedback_error'] = "L'email '" . htmlspecialchars($email) . "' è già registrata come " . $tipoUtenteNormalized . ".";
            return false;
        }
        
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            if ($password_hash === false) { throw new Exception("Errore durante l'hashing della password."); }
            
            $table = ($tipoUtenteNormalized === 'insegnante') ? 'INSEGNANTE' : 'STUDENTE';
            $sql = "INSERT INTO `$table` (Nome, Cognome, Email, PasswordHash, DataRegistrazione) VALUES (:nome, :cognome, :email, :passwordhash, NOW())";
            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cognome', $cognome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':passwordhash', $password_hash);
            return $stmt->execute();
        } catch (Exception $e) { 
            error_log("Errore LpUtenteManager::registerUtente($tipoUtenteNormalized): " . $e->getMessage());
            $_SESSION['lp_feedback_error'] = "Errore tecnico durante la registrazione. Riprova.";
            return false;
        }
    }

    /**
     * Tenta il login di un utente.
     * @param string $email L'email dell'utente.
     * @param string $password La password in chiaro.
     * @param string $userType Il tipo di utente atteso ('studente' o 'insegnante').
     * @return bool True se il login ha successo e la sessione è impostata, false altrimenti.
     */
    public function loginUtente($email, $password, $userType = 'studente') {
        $userTypeNormalized = strtolower($userType);
        $table = ($userTypeNormalized === 'insegnante') ? 'INSEGNANTE' : 'STUDENTE';
        $idField = ($userTypeNormalized === 'insegnante') ? 'IDInsegnante' : 'IDStudente';
        
        $sessionUserIdKey = $userTypeNormalized . '_id_lingue';
        $sessionUserNameKey = $userTypeNormalized . '_nome_lingue';
        $sessionUserTypeKey = 'lp_user_type';
        $sessionUserEmailKey = 'lp_user_email';

        if (!$this->db_conn) {
            error_log("LpUtenteManager::loginUtente - Connessione DB non disponibile.");
            return false;
        }
        try {
            // Recupera l'utente per email
            $stmt = $this->db_conn->prepare("SELECT `$idField`, Nome, Cognome, Email, PasswordHash FROM `$table` WHERE Email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica la password
            if ($user && password_verify($password, $user['PasswordHash'])) {
                // Login successo

                // ** PULIZIA CHIAVI SESSIONE PRECEDENTI **
                unset($_SESSION['studente_id_lingue']);
                unset($_SESSION['studente_nome_lingue']);
                unset($_SESSION['insegnante_id_lingue']);
                unset($_SESSION['insegnante_nome_lingue']);
                unset($_SESSION[$sessionUserTypeKey]);
                unset($_SESSION[$sessionUserEmailKey]);

                // ** IMPOSTAZIONE NUOVE CHIAVI SESSIONE USANDO I DATI RECUPERATI DAL DB ($user) **
                $_SESSION[$sessionUserIdKey] = (int)$user[$idField];     // <<< ASSICURATI CHE QUESTO PRENDA L'ID CORRETTO DAL RISULTATO DB
                $_SESSION[$sessionUserNameKey] = $user['Nome'];     // Prende il nome dal DB
                $_SESSION[$sessionUserEmailKey] = $user['Email'];   // Prende l'email dal DB
                $_SESSION[$sessionUserTypeKey] = $userTypeNormalized;   // Prende il tipo dal form/logica

                // DEBUG OPZIONALE: Verifica valori sessione DOPO averli impostati
                /*
                error_log("DEBUG LOGINUTENTE SUCCESSO: Loggato " . $userTypeNormalized . 
                          " ID " . $_SESSION[$sessionUserIdKey] . 
                          " Nome " . $_SESSION[$sessionUserNameKey] . 
                          " Email " . $_SESSION[$sessionUserEmailKey] . 
                          " Tipo " . $_SESSION[$sessionUserTypeKey]);
                */

                return true; // Indica successo
            }
            return false; // Email non trovata o password non corretta
        } catch (PDOException $e) {
            error_log("Errore PDO LpUtenteManager::loginUtente($userTypeNormalized): " . $e->getMessage());
            // Non mostrare l'errore DB all'utente
            return false;
        }
    }

    public function getProfiloUtente($userId, $userType) {
        if (!$this->db_conn || !is_numeric($userId) || $userId <= 0) {
             error_log("LpUtenteManager::getProfiloUtente - Connessione DB nulla o UserID non valido: $userId");
            return false;
        }
        $userTypeNormalized = strtolower(trim($userType ?? ''));
        if ($userTypeNormalized !== 'studente' && $userTypeNormalized !== 'insegnante') {
            error_log("LpUtenteManager::getProfiloUtente - UserType non valido: $userType");
            return false;
        }

        $table = ($userTypeNormalized === 'insegnante') ? 'INSEGNANTE' : 'STUDENTE';
        $idField = ($userTypeNormalized === 'insegnante') ? 'IDInsegnante' : 'IDStudente';

        $sql = "SELECT `$idField` AS ID, Nome, Cognome, Email, DataRegistrazione FROM `$table` WHERE `$idField` = :user_id";
        try {
            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore PDO LpUtenteManager::getProfiloUtente($userTypeNormalized, ID: $userId): " . $e->getMessage());
            return false;
        }
    }

    public function updateProfiloUtente($userId, $userType, $nome, $cognome, $email, $newPassword = null) {
        if (!$this->db_conn || !is_numeric($userId) || (strtolower($userType) !== 'studente' && strtolower($userType) !== 'insegnante')) {
            $_SESSION['lp_feedback_error'] = "Dati utente non validi per l'aggiornamento.";
            return false;
        }
        $userTypeNormalized = strtolower($userType);
        $table = ($userTypeNormalized === 'insegnante') ? 'INSEGNANTE' : 'STUDENTE';
        $idField = ($userTypeNormalized === 'insegnante') ? 'IDInsegnante' : 'IDStudente';

        if ($this->emailExists($email, $userTypeNormalized, $userId)) { // Passa $userId per escluderlo dal check
            $_SESSION['lp_feedback_error'] = "L'email fornita è già in uso da un altro account.";
            return false;
        }
        try {
            $sql_parts = ["Nome = :nome", "Cognome = :cognome", "Email = :email"];
            $params = [
                ':nome' => $nome,
                ':cognome' => $cognome,
                ':email' => $email,
                ':user_id' => $userId
            ];
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                     $_SESSION['lp_feedback_error'] = "La nuova password deve essere di almeno 6 caratteri.";
                     return false;
                }
                $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
                if ($password_hash === false) { throw new Exception("Errore hashing nuova password."); }
                $sql_parts[] = "PasswordHash = :passwordhash";
                $params[':passwordhash'] = $password_hash;
            }
            $sql = "UPDATE `$table` SET " . implode(", ", $sql_parts) . " WHERE `$idField` = :user_id";
            $stmt = $this->db_conn->prepare($sql);
            if ($stmt->execute($params)) {
                // Aggiorna anche sessione se è l'utente loggato corrente
                $sessionUserIdKey = $userTypeNormalized . '_id_lingue';
                $sessionUserNameKey = $userTypeNormalized . '_nome_lingue';
                $sessionUserEmailKey = 'lp_user_email';

                if (isset($_SESSION[$sessionUserIdKey]) && $_SESSION[$sessionUserIdKey] == $userId) {
                    $_SESSION[$sessionUserNameKey] = $nome;
                    $_SESSION[$sessionUserEmailKey] = $email;
                }
                return true;
            }
            return false;
        } catch (Exception $e) { 
            error_log("Errore LpUtenteManager::updateProfiloUtente($userTypeNormalized, ID: $userId): " . $e->getMessage());
            $_SESSION['lp_feedback_error'] = "Errore tecnico durante l'aggiornamento del profilo.";
            return false;
        }
    }
}
?>