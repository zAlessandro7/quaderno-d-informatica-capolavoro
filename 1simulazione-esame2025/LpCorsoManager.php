<?php
// 1simulazione-esame2025/LpCorsoManager.php
// Classe per la gestione dei Corsi Virtuali e degli esercizi associati ai corsi.

// Assicura che la sessione sia attiva (potrebbe non servire qui ma buona pratica se si gestiscono feedback in futuro)
if (session_status() === PHP_SESSION_NONE) {
    // ATTENZIONE: Assicurati che la funzione "start()" non sia definita altrove per errore.
    // La funzione corretta di PHP è session_start(). Se il tuo errore "Call to undefined function start()"
    // persiste, verifica che non ci sia una definizione di "start()" in qualche file incluso.
    // Per ora, uso session_start() come dovrebbe essere.
    session_start(); 
}

// Include la classe di connessione al database (che a sua volta include la configurazione)
require_once __DIR__ . '/LpDatabaseConnection.php';

class LpCorsoManager {
    private $db_conn; // Connessione PDO

    public function __construct() {
        // Il costruttore di LpDatabaseConnection gestisce la session_start() e la connessione.
        // Se la connessione fallisce, lancerà un die().
        $db_object = new LpDatabaseConnection();
        $this->db_conn = $db_object->getConnection();
    }

    /**
     * Crea un nuovo corso virtuale nel database.
     * @param int $idInsegnante L'ID dell'insegnante creatore. QUESTO ID DEVE ESISTERE IN TABELLA INSEGNANTE.
     * @param string $nomeCorso Nome del corso.
     * @param string $lingua Lingua del corso.
     * @param string $livello Livello di difficoltà.
     * @param string|null $descrizione Descrizione del corso (opzionale).
     * @return bool True in caso di successo, false altrimenti.
     * 
     * Nota: L'errore "Insegnante creatore non valido" (codice 1452) significa che l'ID passato per $idInsegnante non esiste nella tabella INSEGNANTE.
     * La causa è esterna a questo metodo (problema di autenticazione/sessione o dati DB non corretti).
     */
    public function creaCorso($idInsegnante, $nomeCorso, $lingua, $livello, $descrizione = null) {
        // La connessione dovrebbe essere già disponibile dal costruttore (o lo script è terminato con die)
        if (!$this->db_conn) {
            $_SESSION['lp_feedback_error'] = "Errore di connessione al database.";
            error_log("LpCorsoManager::creaCorso - Connessione DB non disponibile.");
            return false;
        }

        // Validazione minima dei parametri in input (più dettagliata va fatta prima, nel file action)
        // Il controllo che $idInsegnante esista nel DB è fatto dalla FOREIGN KEY e catturato sotto.
        if (!is_numeric($idInsegnante) || $idInsegnante <= 0 || empty(trim($nomeCorso)) || empty(trim($lingua)) || empty(trim($livello))) {
             $_SESSION['lp_feedback_error'] = "Dati insufficienti o non validi per creare il corso (Nome, Lingua, Livello obbligatori).";
             error_log("LpCorsoManager::creaCorso - Validazione falita per ID_Insegnante: $idInsegnante, Nome: '$nomeCorso', Lingua: '$lingua', Livello: '$livello'");
             return false;
        }

        // Genera un codice iscrizione univoco (semplificato, basato su lingua, livello e una stringa casuale/timestamp)
        // Pulisce il livello da caratteri non alfanumerici per il codice
        $codiceIscrizione = strtoupper(substr(trim($lingua), 0, 3)) . preg_replace('/[^A-Za-z0-9]/', '', trim($livello)) . '_' . bin2hex(random_bytes(4)); // Usiamo bin2hex + random_bytes per maggiore casualità/univocità

        try {
            $sql = "INSERT INTO CORSO_VIRTUALE (IDInsegnanteCreatore, NomeCorso, Lingua, LivelloDifficolta, CodiceIscrizione, DescrizioneCorso, DataCreazione, Attivo) 
                    VALUES (:id_insegnante, :nome_corso, :lingua, :livello, :codice, :descrizione, NOW(), TRUE)";
            
            // DEBUG OPZIONALE: Stampa la query e i parametri prima dell'esecuzione
            /*
            error_log("DEBUG LpCorsoManager::creaCorso SQL: " . $sql);
            error_log("DEBUG LpCorsoManager::creaCorso Params: " . print_r([
                ':id_insegnante' => $idInsegnante,
                ':nome_corso' => $nomeCorso,
                ':lingua' => $lingua,
                ':livello' => $livello,
                ':codice' => $codiceIscrizione,
                ':descrizione' => $descrizione
            ], true));
            */

            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':id_insegnante', $idInsegnante, PDO::PARAM_INT);
            $stmt->bindParam(':nome_corso', $nomeCorso);
            $stmt->bindParam(':lingua', $lingua);
            $stmt->bindParam(':livello', $livello);
            $stmt->bindParam(':codice', $codiceIscrizione);
            $stmt->bindParam(':descrizione', $descrizione); // PDO gestisce automaticamente NULL se $descrizione è null
            
            return $stmt->execute(); // Restituisce true in caso di successo

        } catch (PDOException $e) {
            // Registra l'errore completo nel log del server
            error_log("PDOException in LpCorsoManager::creaCorso(): " . $e->getMessage() . " --- SQL: " . $sql . " --- Params: " . print_r([$idInsegnante, $nomeCorso, $lingua, $livello, $codiceIscrizione, $descrizione], true));
            
            // Imposta un messaggio di errore amichevole per l'utente, più specifico se possibile
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) { // Codice errore MySQL per Duplicate entry (CodiceIscrizione)
                 // Questo potrebbe succedere se il codice iscrizione generato casualmente è già in uso
                 $_SESSION['lp_feedback_error'] = "Errore nella creazione del corso: il codice iscrizione generato ('".htmlspecialchars($codiceIscrizione)."') esiste già. Riprova (potrebbe essere una rara coincidenza).";
            } else if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1452) { // FK constraint fails (IDInsegnanteCreatore non trovato)
                 // Questo è l'errore che vedi: l'ID Insegnante passato non esiste in tabella INSEGNANTE
                 $_SESSION['lp_feedback_error'] = "Errore nella creazione del corso: Insegnante creatore non valido (ID fornito non esiste nel DB)."; 
            }
            else {
                 $_SESSION['lp_feedback_error'] = "Errore tecnico durante la creazione del corso. Si prega di riprovare.";
                 // DEBUG OPZIONALE: Mostra l'errore PDO specifico all'utente SOLO in ambiente di sviluppo
                 // $_SESSION['lp_feedback_error'] .= " Dettaglio (DEBUG): " . htmlspecialchars($e->getMessage());
            }
            return false; // Indica fallimento
        }
    }

    /**
     * Recupera tutti i corsi virtuali attivi con i dettagli dell'insegnante creatore.
     * @return array Un array di corsi o un array vuoto.
     */
    public function getAllCorsiDisponibili() {
        if (!$this->db_conn) return [];
        try {
            $sql = "SELECT cv.IDCorso, cv.NomeCorso, cv.Lingua, cv.LivelloDifficolta, cv.DescrizioneCorso, cv.CodiceIscrizione,
                           i.Nome AS NomeInsegnante, i.Cognome AS CognomeInsegnante
                    FROM CORSO_VIRTUALE cv
                    JOIN INSEGNANTE i ON cv.IDInsegnanteCreatore = i.IDInsegnante
                    WHERE cv.Attivo = TRUE
                    ORDER BY cv.NomeCorso ASC";
            
            // DEBUG OPZIONALE: Mostra la query
            // error_log("DEBUG SQL (getAllCorsiDisponibili): " . $sql);

            $stmt = $this->db_conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Assicura FETCH_ASSOC
        } catch (PDOException $e) {
            error_log("Errore PDO LpCorsoManager::getAllCorsiDisponibili(): " . $e->getMessage() . " SQL: " . $sql);
            return [];
        }
    }
    
    /**
     * Recupera i dettagli di un corso virtuale tramite il suo ID.
     * @param int $idCorso L'ID del corso.
     * @return array|false I dettagli del corso o false se non trovato/errore.
     */
    public function getCorsoById($idCorso) {
        if (!$this->db_conn || !is_numeric($idCorso) || $idCorso <= 0) {
            error_log("LpCorsoManager::getCorsoById - ID corso non valido: $idCorso");
            return false;
        }
        try {
            $sql = "SELECT cv.*, i.Nome AS NomeInsegnante, i.Cognome AS CognomeInsegnante
                    FROM CORSO_VIRTUALE cv
                    JOIN INSEGNANTE i ON cv.IDInsegnanteCreatore = i.IDInsegnante
                    WHERE cv.IDCorso = :id_corso AND cv.Attivo = TRUE"; // Verifica che sia attivo
            
            // DEBUG OPZIONALE
            // error_log("DEBUG SQL (getCorsoById): " . $sql . " Param: :id_corso = $idCorso");

            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':id_corso', $idCorso, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC); // fetch() restituisce false se non trova righe
        } catch (PDOException $e) {
            error_log("Errore PDO LpCorsoManager::getCorsoById(ID: $idCorso): " . $e->getMessage() . " SQL: " . $sql);
            return false;
        }
    }

    /**
     * Recupera tutti i corsi creati da un specifico insegnante.
     * @param int $idInsegnante L'ID dell'insegnante.
     * @return array Un array di corsi o un array vuoto.
     */
    public function getCorsiByInsegnanteId($idInsegnante) {
        if (!$this->db_conn || !is_numeric($idInsegnante) || $idInsegnante <= 0) {
            error_log("LpCorsoManager::getCorsiByInsegnanteId - ID insegnante non valido: $idInsegnante");
            return [];
        }
        try {
            // AGGIUNGI 'DataCreazione' alla clausola SELECT
            $sql = "SELECT IDCorso, NomeCorso, Lingua, LivelloDifficolta, CodiceIscrizione, DescrizioneCorso, Attivo, DataCreazione 
                    FROM CORSO_VIRTUALE 
                    WHERE IDInsegnanteCreatore = :id_insegnante 
                    ORDER BY DataCreazione DESC";
            
            // DEBUG OPZIONALE
            // error_log("DEBUG SQL (getCorsiByInsegnanteId): " . $sql . " Param: :id_insegnante = $idInsegnante");

            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':id_insegnante', $idInsegnante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore PDO LpCorsoManager::getCorsiByInsegnanteId(ID: $idInsegnante): " . $e->getMessage() . " SQL: " . $sql);
            return [];
        }
    }
    
    /**
     * Recupera tutti gli esercizi associati a un corso specifico.
     * Questo include dettagli dall'ESERCIZIO_CATALOGO e l'ordine nel corso.
     * @param int $idCorso L'ID del corso.
     * @return array Un array di esercizi o un array vuoto.
     */
    public function getEserciziByCorsoId($idCorso) {
        // Verifica che la connessione DB sia disponibile e l'ID corso sia valido
        if (!$this->db_conn || !is_numeric($idCorso) || $idCorso <= 0) {
            error_log("LpCorsoManager::getEserciziByCorsoId - Connessione DB nulla o ID corso non valido: $idCorso");
            return []; 
        }

        try {
            $sql = "SELECT 
                        edc.IDEsercizioCorso,       -- L'ID dell'istanza specifica nel corso (per es. svolgimento)
                        edc.OrdineInSequenza,       -- L'ordine in cui appare nel corso
                        ec.IDEsercizio AS IDEsercizioCatalogo, -- L'ID dell'esercizio nel catalogo generale
                        ec.TitoloEsercizio,
                        ec.DescrizioneEsercizio,
                        ec.DifficoltaLinguistica,
                        ec.PuntiOttenibili,
                        ec.ImmagineURL AS ImmagineEsercizioURL, -- Rinominato per chiarezza
                        ec.VideoURL AS VideoEsercizioURL,     -- Rinominato
                        tl.NomeTema                 -- Nome del tema (LEFT JOIN)
                    FROM ESERCIZIO_DEL_CORSO edc
                    JOIN ESERCIZIO_CATALOGO ec ON edc.IDEsercizioCatalogo = ec.IDEsercizio
                    LEFT JOIN TEMA_LINGUISTICO tl ON ec.IDTema = tl.IDTema -- LEFT JOIN per includere esercizi senza tema
                    WHERE edc.IDCorso = :id_corso AND edc.VisibileAgliStudenti = TRUE -- Solo esercizi visibili
                    ORDER BY edc.OrdineInSequenza ASC"; // Ordina per la sequenza definita nel corso

            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':id_corso', $idCorso, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 

        } catch (PDOException $e) {
            error_log("Errore PDO LpCorsoManager::getEserciziByCorsoId(ID: $idCorso): " . $e->getMessage() . " SQL: " . $sql);
            return []; 
        }
    }
    
    // TODO: Aggiungere metodi per ISCRIZIONE_CORSO, SVOLGIMENTO_ESERCIZIO, Classifiche
    // public function getIscrittiAlCorso($idCorso) { ... }
    // public function aggiungiEsercizioAlCorso($idCorso, $idEsercizioCatalogo, $ordine) { ... }
    // public function getIscrittiAlCorso($idCorso) { ... }
    // public function iscriviStudenteAlCorso($idStudente, $idCorso) { ... }
    // public function registraSvolgimentoEsercizio($idStudente, $idEsercizioCorso, $punteggio) { ... }
    // public function getClassificaCorso($idCorso) { ... }
    // public function getClassificaEsercizioNelCorso($idEsercizioCorso) { ... }
}
?>