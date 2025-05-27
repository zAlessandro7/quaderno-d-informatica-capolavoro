<?php
// 2simulazione-esame2025/OrdineManager.php
// Classe per la gestione degli Ordini e DettagliOrdine nel progetto Food Express.

// --- INIZIO CONTROLLI ANTI-PARSER-ERROR E INCLUDE NECESSARI ---
// Assicurati che questo sia il PRIMO tag PHP nel file.
// Non ci devono essere spazi, linee vuote o altri caratteri prima di questo.
// Assicurati che la codifica del file sia UTF-8 SENZA BOM.

// Assicura che la sessione sia attiva (necessario per gestire i messaggi di feedback)
// Questo controllo è sicuro e dovrebbe essere all'inizio di ogni script che usa sessioni.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include la configurazione del database specifica per Food Express
// Questo file DEVE definire DB_HOST, DB_USER_APP, DB_PASS_APP, DB_CHARSET,
// DB_PREFIX (se applicabile), get_db_name(), DB_NAME_FOODEXPRESS.
require_once __DIR__ . '/database_config.php'; 

// Include la classe di connessione al database che userà la configurazione
require_once __DIR__ . '/DatabaseConnection.php'; 

// Questo commento multi-linea (righe 18-23 circa nel codice precedente)
// è correttamente chiuso qui sotto.
/*
if (!defined('DB_NAME_FOODEXPRESS')) {
     error_log("DB_NAME_FOODEXPRESS non definito in OrdineManager.php. Controlla database_config.php");
     // In produzione, potresti voler lanciare un'eccezione o mostrare un errore generico
     // die("Errore di configurazione del database Food Express.");
}
*/ // <<< Questo */ chiude il commento multi-linea. La definizione della classe inizia DOPO.


class OrdineManager { // <<< Questa riga è circa la 26 nel codice corretto
    private $db_conn; // Connessione PDO al database Food Express (202425_5IB_ElTaras_FoodExpressDB)

    /**
     * Costruttore della classe OrdineManager. Stabilisce la connessione al database Food Express.
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
             error_log("OrdineManager CONSTRUCT: Connessione DB Food Express fallita.");
             // La classe DatabaseConnection dovrebbe già fare die() se la connessione fallisce.
             // Non facciamo die() di nuovo qui per non avere messaggi doppi, ma $this->db_conn sarà null.
             $this->db_conn = null; 
        }
    }

    /**
     * Restituisce l'oggetto connessione PDO. Utile se altri metodi esterni hanno bisogno di fare query.
     * @return PDO|null L'oggetto connessione PDO.
     */
    // INIZIO DEFINIZIONE METODO getConnect()
    public function getConnection() {
        return $this->db_conn;
    }
    // FINE DEFINIZIONE METODO getConnect()


    /**
     * Recupera tutti gli ordini di un cliente specifico.
     * Include dettagli base dell'ordine e il nome del ristorante.
     * @param int $idCliente L'ID del cliente loggato.
     * @return array Un array di ordini o un array vuoto.
     */
    public function getOrdiniByClienteId($idCliente) {
        // Verifica se la connessione DB è disponibile prima di fare query
        if (!$this->db_conn) {
            error_log("OrdineManager::getOrdiniByClienteId - Connessione DB non disponibile.");
            return []; // Ritorna un array vuoto in caso di connessione mancante
        }
        // Validazione basica dell'input
        if (!is_numeric($idCliente) || $idCliente <= 0) {
            error_log("OrdineManager::getOrdiniByClienteId - ID cliente non valido: $idCliente");
            return []; // Ritorna un array vuoto se l'ID non è valido
        }

        try {
            // Query per ottenere gli ordini di un cliente, uniti al nome del ristorante
            $sql = "SELECT 
                        o.IDOrdine, 
                        o.DataOraOrdine, 
                        o.StatoOrdine, 
                        o.TotaleOrdine, 
                        r.NomeRistorante
                    FROM ORDINE o
                    JOIN RISTORANTE r ON o.IDRistorante = r.IDRistorante
                    WHERE o.IDCliente = :id_cliente
                    ORDER BY o.DataOraOrdine DESC";

            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT); // Associa l'ID cliente come intero
            $stmt->execute();

            // fetchAll() restituisce un array vuoto se non trova righe, che è quello che vogliamo qui.
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 

        } catch (PDOException $e) {
            // Logga l'errore PDO nel log del server
            error_log("Errore PDO OrdineManager::getOrdiniByClienteId(ID Cliente: $idCliente): " . $e->getMessage() . " SQL: " . $sql);
            // In un'applicazione reale, potresti voler impostare un messaggio di errore sessione qui
            // $_SESSION['feedback_message'] = "Impossibile caricare i tuoi ordini al momento.";
            // $_SESSION['feedback_type'] = 'error_msg';
            return []; // Ritorna un array vuoto in caso di errore DB
        }
    }

    /**
     * Recupera i dettagli di un singolo ordine, inclusi i piatti ordinati.
     * @param int $idOrdine L'ID dell'ordine.
     * @param int $idCliente L'ID del cliente (per sicurezza, verificare che l'ordine appartenga a questo cliente).
     * @return array|false I dettagli dell'ordine completo (ordine + dettagli_piatti) o false se non trovato/non appartiene al cliente.
     */
    public function getDettagliOrdine($idOrdine, $idCliente) {
         // Validazione basica dell'ID ordine e cliente
         if (!$this->db_conn || !is_numeric($idOrdine) || $idOrdine <= 0 || !is_numeric($idCliente) || $idCliente <= 0) {
            error_log("OrdineManager::getDettagliOrdine - Connessione DB nulla o ID ordine/cliente non validi: Ordine=$idOrdine, Cliente=$idCliente");
            return false;
        }
        
        try {
            // Recupera i dettagli dell'ordine principale E verifica che appartenga al cliente loggato
            $sql_ordine = "SELECT o.*, r.NomeRistorante 
                           FROM ORDINE o 
                           JOIN RISTORANTE r ON o.IDRistorante = r.IDRistorante 
                           WHERE o.IDOrdine = :id_ordine AND o.IDCliente = :id_cliente";
            $stmt_ordine = $this->db_conn->prepare($sql_ordine);
            $stmt_ordine->bindParam(':id_ordine', $idOrdine, PDO::PARAM_INT);
            $stmt_ordine->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt_ordine->execute();
            $ordine = $stmt_ordine->fetch(PDO::FETCH_ASSOC); // fetch() restituisce false se non trova righe

            if (!$ordine) {
                // Ordine non trovato O non appartiene a questo cliente
                return false;
            }

            // Recupera i dettagli dei piatti per questo ordine dai DettagliOrdine
            $sql_dettagli = "SELECT dod.Quantita, dod.PrezzoUnitarioAlMomentoOrdine, p.NomePiatto 
                             FROM DETTAGLIO_ORDINE dod
                             JOIN PIATTO p ON dod.IDPiatto = p.IDPiatto
                             WHERE dod.IDOrdine = :id_ordine";
            $stmt_dettagli = $this->db_conn->prepare($sql_dettagli);
            $stmt_dettagli->bindParam(':id_ordine', $idOrdine, PDO::PARAM_INT);
            $stmt_dettagli->execute();
            $dettagli = $stmt_dettagli->fetchAll(PDO::FETCH_ASSOC); // fetchAll() restituisce un array vuoto se non trova righe

            // Aggiunge l'array dei dettagli dei piatti all'array dell'ordine principale
            $ordine['dettagli_piatti'] = $dettagli;

            return $ordine; // Ritorna l'array con tutti i dettagli

        } catch (PDOException $e) {
            // Logga l'errore PDO nel log del server
            error_log("Errore PDO OrdineManager::getDettagliOrdine(ID Ordine: $idOrdine, ID Cliente: $idCliente): " . $e->getMessage() . " SQL Ordine: " . $sql_ordine);
            // In un'applicazione reale, potresti voler impostare un messaggio di errore sessione
            // $_SESSION['feedback_message'] = "Impossibile caricare i dettagli dell'ordine.";
            // $_SESSION['feedback_type'] = 'error_msg';
            return false; // Indica fallimento (errore DB)
        }
    }

    /**
     * Salva una nuova recensione nel database.
     * @param int $idOrdine L'ID dell'ordine a cui si riferisce la recensione.
     * @param int $idCliente L'ID del cliente che lascia la recensione.
     * @param int $idRistorante L'ID del ristorante dell'ordine.
     * @param int $voto Il punteggio della recensione (1-5).
     * @param string|null $testo Testo della recensione (opzionale, max 160 caratteri).
     * @return bool True in caso di successo, false altrimenti.
     */
    public function salvaRecensione($idOrdine, $idCliente, $idRistorante, $voto, $testo = null) {
        // Verifica connessione e validità basica input
        if (!$this->db_conn) {
            error_log("OrdineManager::salvaRecensione - Connessione DB non disponibile.");
            return false;
        }
        if (!is_numeric($idOrdine) || $idOrdine <= 0 || !is_numeric($idCliente) || $idCliente <= 0 || !is_numeric($idRistorante) || $idRistorante <= 0 || !is_numeric($voto) || $voto < 1 || $voto > 5) {
             error_log("OrdineManager::salvaRecensione - Dati input non validi: Ordine=$idOrdine, Cliente=$idCliente, Ristorante=$idRistorante, Voto=$voto");
             $_SESSION['feedback_message'] = "Dati per la recensione non validi.";
             $_SESSION['feedback_type'] = 'danger';
             return false;
        }

        // Opzionale: Verifica che l'ordine esista, sia "consegnato", appartenga al cliente e non abbia già una recensione
        // Questo può essere fatto qui o nel file action (processa_recensione.php)
        // Farlo qui rende il manager più robusto.
        // Esempio (semplificato):
        /*
        $ordine_verif = $this->getDettagliOrdine($idOrdine, $idCliente); // Riutilizza metodo esistente per fetchare l'ordine E verificare appartenenza
        if (!$ordine_verif || $ordine_verif['StatoOrdine'] !== 'consegnato') {
            $_SESSION['feedback_message'] = "Non puoi lasciare una recensione per questo ordine.";
            $_SESSION['feedback_type'] = 'danger';
            error_log("OrdineManager::salvaRecensione - Ordine non valido per recensione: ID " . ($ordine_verif['IDOrdine'] ?? 'N/D') . ", Stato " . ($ordine_verif['StatoOrdine'] ?? 'N/D'));
            return false;
        }
        // Verifica se recensione esiste già per questo ordine
        try {
            $stmt_check_recensione = $this->db_conn->prepare("SELECT IDRecensione FROM RECENSIONE WHERE IDOrdine = :id_ordine");
            $stmt_check_recensione->bindParam(':id_ordine', $idOrdine, PDO::PARAM_INT);
            $stmt_check_recensione->execute();
            if ($stmt_check_recensione->fetch()) {
                 $_SESSION['feedback_message'] = "Hai già lasciato una recensione per questo ordine.";
                 $_SESSION['feedback_type'] = 'warning';
                 error_log("OrdineManager::salvaRecensione - Recensione già esistente per Ordine ID $idOrdine");
                 return false; // Recensione già esiste
            }
        } catch (PDOException $e) {
             error_log("Errore PDO check recensione: " . $e->getMessage());
             $_SESSION['feedback_message'] = "Errore tecnico nella verifica della recensione.";
             $_SESSION['feedback_type'] = 'danger';
             return false;
        }
        */


        try {
            // Prepara la query INSERT
            $sql = "INSERT INTO RECENSIONE (IDOrdine, IDCliente, IDRistorante, Voto, TestoRecensione, DataRecensione) 
                    VALUES (:id_ordine, :id_cliente, :id_ristorante, :voto, :testo, NOW())";
            
            $stmt = $this->db_conn->prepare($sql);
            
            // Bind dei parametri
            $stmt->bindParam(':id_ordine', $idOrdine, PDO::PARAM_INT);
            $stmt->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt->bindParam(':id_ristorante', $idRistorante, PDO::PARAM_INT);
            $stmt->bindParam(':voto', $voto, PDO::PARAM_INT);
            // Limita il testo a 160 caratteri come da traccia (assicurati che la colonna sia sufficientemente lunga nel DB)
            $testoTruncated = substr(trim($testo ?? ''), 0, 160);
            $stmt->bindParam(':testo', $testoTruncated, PDO::PARAM_STR); 

            // Esegui la query
            return $stmt->execute(); // Restituisce true in caso di successo

        } catch (PDOException $e) {
            // Registra l'errore completo nel log del server
            error_log("Errore PDO OrdineManager::salvaRecensione(ID Ordine: $idOrdine): " . $e->getMessage() . " --- SQL: " . $sql . " --- Params: " . print_r([$idOrdine, $idCliente, $idRistorante, $voto, $testo], true));
            
            // Controlla se l'errore è dovuto al UNIQUE constraint su IDOrdine (recensione già esistente)
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) { // Codice errore MySQL per Duplicate entry
                 $_SESSION['feedback_message'] = "Hai già lasciato una recensione per questo ordine.";
                 $_SESSION['feedback_type'] = 'warning';
            } else {
                 $_SESSION['feedback_message'] = "Errore tecnico durante il salvataggio della recensione nel database.";
                 $_SESSION['feedback_type'] = 'danger';
                 // DEBUG OPZIONALE: Mostra l'errore PDO specifico all'utente SOLO in ambiente di sviluppo
                 // $_SESSION['feedback_message'] .= " Dettaglio (DEBUG): " . htmlspecialchars($e->getMessage());
            }
            return false; // Indica fallimento
        }
    }

    /**
     * Crea un nuovo ordine nel database.
     * Prende i dati principali e gli articoli dal carrello in sessione.
     * @param int $idCliente L'ID del cliente che effettua l'ordine.
     * @param string $indirizzoConsegna L'indirizzo di consegna per questo specifico ordine.
     * @param string $metodoPagamento Il metodo di pagamento scelto.
     * @param string|null $noteClienti Eventuali note del cliente.
     * @param array $itemsCarrello Gli articoli del carrello (array di array con id_piatto, quantita, prezzo).
     * @param int $idRistorante L'ID del ristorante da cui si sta ordinando.
     * @param float $totaleOrdine Il totale calcolato dell'ordine.
     * @return int|false L'ID del nuovo ordine creato in caso di successo, false altrimenti.
     */
    public function creaNuovoOrdine($idCliente, $indirizzoConsegna, $metodoPagamento, $noteClienti, $itemsCarrello, $idRistorante, $totaleOrdine) {
        // Verifica connessione e validità basica input
        if (!$this->db_conn) {
            error_log("OrdineManager::creaNuovoOrdine - Connessione DB non disponibile.");
            $_SESSION['feedback_message'] = "Errore di connessione al database.";
            $_SESSION['feedback_type'] = 'danger';
            return false;
        }
         if (!is_numeric($idCliente) || $idCliente <= 0 || empty(trim($indirizzoConsegna)) || empty(trim($metodoPagamento)) || !is_array($itemsCarrello) || empty($itemsCarrello) || !is_numeric($idRistorante) || $idRistorante <= 0 || !is_numeric($totaleOrdine) || $totaleOrdine < 0) {
             error_log("OrdineManager::creaNuovoOrdine - Dati input non validi/mancanti. Cliente=$idCliente, Ristorante=$idRistorante, Totale=$totaleOrdine, Items=" . count($itemsCarrello) . " Indirizzo=" . empty(trim($indirizzoConsegna)));
             $_SESSION['feedback_message'] = "Dati dell'ordine non validi o mancanti.";
             $_SESSION['feedback_type'] = 'danger';
             return false;
         }

        try {
            // Inizia una transazione per garantire l'integrità (o tutto l'ordine viene salvato, o nulla)
            $this->db_conn->beginTransaction();

            // 1. Inserisci il record nella tabella ORDINE
            $sql_ordine = "INSERT INTO ORDINE (IDCliente, IDRistorante, DataOraOrdine, StatoOrdine, IndirizzoConsegnaOrdine, NoteCliente, TotaleOrdine, MetodoPagamento) 
                           VALUES (:id_cliente, :id_ristorante, NOW(), 'in attesa', :indirizzo, :note, :total, :metodo)";
            
            $stmt_ordine = $this->db_conn->prepare($sql_ordine);
            $stmt_ordine->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
            $stmt_ordine->bindParam(':id_ristorante', $idRistorante, PDO::PARAM_INT);
            $stmt_ordine->bindParam(':indirizzo', $indirizzoConsegna);
            $stmt_ordine->bindParam(':note', $noteClienti);
            $stmt_ordine->bindParam(':total', $totaleOrdine);
            $stmt_ordine->bindParam(':metodo', $metodoPagamento);
            
            $stmt_ordine->execute();

            // Recupera l'ID del nuovo ordine inserito (necessario per i dettagli)
            $id_nuovo_ordine = $this->db_conn->lastInsertId();

            if (!$id_nuovo_ordine) {
                throw new Exception("Impossibile recuperare l'ID del nuovo ordine inserito.");
            }

            // 2. Inserisci i record nella tabella DETTAGLIO_ORDINE per ogni piatto nel carrello
            $sql_dettaglio = "INSERT INTO DETTAGLIO_ORDINE (IDOrdine, IDPiatto, Quantita, PrezzoUnitarioAlMomentoOrdine) 
                              VALUES (:id_ordine, :id_piatto, :quantita, :prezzo_unitario)";
            $stmt_dettaglio = $this->db_conn->prepare($sql_dettaglio);

            foreach ($itemsCarrello as $item) {
                // Assicurati che i dati degli item siano validi
                if (!isset($item['id_piatto']) || !is_numeric($item['id_piatto']) || $item['id_piatto'] <= 0 ||
                    !isset($item['quantita']) || !is_numeric($item['quantita']) || $item['quantita'] <= 0 ||
                    !isset($item['prezzo']) || !is_numeric($item['prezzo']) || $item['prezzo'] < 0) {
                    
                    // Questo indica un problema serio nel carrello in sessione
                    throw new Exception("Dati di un piatto nel carrello non validi.");
                }

                $stmt_dettaglio->bindParam(':id_ordine', $id_nuovo_ordine, PDO::PARAM_INT);
                $stmt_dettaglio->bindParam(':id_piatto', $item['id_piatto'], PDO::PARAM_INT);
                $stmt_dettaglio->bindParam(':quantita', $item['quantita'], PDO::PARAM_INT);
                $stmt_dettaglio->bindParam(':prezzo_unitario', $item['prezzo']); // Prezzo salvato nel carrello
                
                $stmt_dettaglio->execute();
            }

            // Se tutto è andato bene, conferma la transazione
            $this->db_conn->commit();

            // Restituisce l'ID del nuovo ordine creato
            return (int)$id_nuovo_ordine;

        } catch (PDOException $e) {
            // Se si verifica un errore PDO, annulla la transazione
            if ($this->db_conn && $this->db_conn->inTransaction()) { // Verifica se la transazione è iniziata
                $this->db_conn->rollback();
            }
            error_log("PDOException in OrdineManager::creaNuovoOrdine(): " . $e->getMessage() . " --- SQL: " . ($sql_ordine ?? $sql_dettaglio));
            $_SESSION['feedback_message'] = "Errore tecnico durante la creazione dell'ordine. Riprova.";
            $_SESSION['feedback_type'] = 'danger';
            return false; // Indica fallimento
        } catch (Exception $e) {
             // Cattura altre eccezioni (es. impossibilità di recuperare lastInsertId, dati carrello invalidi)
            if ($this->db_conn && $this->db_conn->inTransaction()) { // Verifica se la transazione è iniziata
                $this->db_conn->rollback();
            }
            error_log("Eccezione in OrdineManager::creaNuovoOrdine(): " . $e->getMessage());
            $_SESSION['feedback_message'] = "Si è verificato un problema durante la finalizzazione dell'ordine.";
            $_SESSION['feedback_type'] = 'danger';
             return false; // Indica fallimento
        }
    }
    // TODO: Aggiungere metodi per:
    // - aggiornare lo stato di un ordine (per admin/ristorante)
    // - gestire recensioni (già in parte in salvaRecensione)
    // public function annullaOrdine($idOrdine, $idCliente) { ... }
    // public function aggiornaStatoOrdine($idOrdine, $nuovoStato) { ... }
}
?>