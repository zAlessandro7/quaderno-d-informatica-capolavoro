<?php
// 1simulazione-esame2025/LpEsercizioCatalogoManager.php

// Assicura che la sessione sia attiva (potrebbe non servire qui ma buona pratica se si gestiscono feedback in futuro)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/LpDatabaseConnection.php';

class LpEsercizioCatalogoManager {
    private $db_conn;

    public function __construct() {
        $db_object = new LpDatabaseConnection();
        $this->db_conn = $db_object->getConnection();
    }

    /**
     * Recupera tutti gli esercizi del catalogo con il nome del tema associato.
     * @return array Un array di esercizi o un array vuoto.
     */
    public function getAllEserciziConTema() {
        if (!$this->db_conn) return [];
        try {
            $sql = "SELECT ec.IDEsercizio, ec.TitoloEsercizio, ec.DescrizioneEsercizio, 
                           ec.DifficoltaLinguistica, ec.PuntiOttenibili, ec.ImmagineURL, ec.VideoURL,
                           tl.NomeTema
                    FROM ESERCIZIO_CATALOGO ec
                    LEFT JOIN TEMA_LINGUISTICO tl ON ec.IDTema = tl.IDTema
                    ORDER BY tl.NomeTema ASC, ec.TitoloEsercizio ASC";
            
            // DEBUG OPZIONALE
            // error_log("DEBUG SQL (getAllEserciziConTema):\n" . $sql);

            $stmt = $this->db_conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore PDO LpEsercizioCatalogoManager::getAllEserciziConTema(): " . $e->getMessage() . " SQL: " . $sql);
            // In un'applicazione reale, mostra un messaggio amichevole all'utente
            // $_SESSION['lp_feedback_error'] = "Impossibile caricare l'elenco degli esercizi.";
            return [];
        }
    }

    /**
     * Recupera i dettagli di un esercizio del catalogo tramite il suo ID.
     * @param int $idEsercizio L'ID dell'esercizio.
     * @return array|false I dettagli dell'esercizio o false se non trovato/errore.
     */
    public function getEsercizioById($idEsercizio) {
        if (!$this->db_conn || !is_numeric($idEsercizio) || $idEsercizio <= 0) {
            error_log("LpEsercizioCatalogoManager::getEsercizioById - ID esercizio non valido: $idEsercizio");
            return false;
        }
        try {
            $sql = "SELECT ec.*, tl.NomeTema 
                    FROM ESERCIZIO_CATALOGO ec
                    LEFT JOIN TEMA_LINGUISTICO tl ON ec.IDTema = tl.IDTema
                    WHERE ec.IDEsercizio = :id_esercizio";
            
            // DEBUG OPZIONALE
            // error_log("DEBUG SQL (getEsercizioById):\n" . $sql . " Param: :id_esercizio = $idEsercizio");

            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':id_esercizio', $idEsercizio, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore PDO LpEsercizioCatalogoManager::getEsercizioById(ID: $idEsercizio): " . $e->getMessage() . " SQL: " . $sql);
            return false;
        }
    }
    
    // TODO: Aggiungere metodi per creare esercizi nel catalogo (per insegnanti/admin)
    // public function creaEsercizioCatalogo($idTema, $titolo, $descrizione, $difficolta, $punti, $imgUrl=null, $videoUrl=null) { ... }
}
?>