<?php
// 2simulazione-esame2025/RistoranteManager.php

// Assicura che la sessione sia attiva
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database_config.php'; 
require_once __DIR__ . '/DatabaseConnection.php'; 

/*
if (!defined('DB_NAME_FOODEXPRESS')) {
     error_log("DB_NAME_FOODEXPRESS non definito in RistoranteManager.php. Controlla database_config.php");
}
*/

class RistoranteManager {
    private $db_conn; 

    public function __construct() {
        try {
            $db_object = new DatabaseConnection(DB_NAME_FOODEXPRESS); 
            $this->db_conn = $db_object->getConnection();
        } catch (Exception $e) {
             error_log("RistoranteManager CONSTRUCT: Connessione DB Food Express fallita.");
             $this->db_conn = null; 
        }
    }

    /**
     * Recupera tutti i ristoranti attivi.
     * @return array
     */
    public function getAllActiveRistoranti() {
        if (!$this->db_conn) return [];
        try {
            $sql = "SELECT IDRistorante, NomeRistorante, IndirizzoRistorante, ImmagineLogoURL FROM RISTORANTE WHERE Attivo = TRUE ORDER BY NomeRistorante ASC";
            $stmt = $this->db_conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore PDO RistoranteManager::getAllActiveRistoranti(): " . $e->getMessage() . " SQL: " . $sql);
            return [];
        }
    }

    /**
     * Recupera i dettagli di un singolo ristorante.
     * @param int $idRistorante
     * @return array|false
     */
    public function getRistoranteDetails($idRistorante) {
        if (!$this->db_conn || !is_numeric($idRistorante) || $idRistorante <= 0) {
            error_log("RistoranteManager::getRistoranteDetails - Connessione DB nulla o ID ristorante non valido: $idRistorante");
            return false; 
        }
        try {
            $sql = "SELECT IDRistorante, NomeRistorante, IndirizzoRistorante, OrariApertura, ImmagineLogoURL FROM RISTORANTE WHERE IDRistorante = :id AND Attivo = TRUE";
            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':id', $idRistorante, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Errore PDO RistoranteManager::getRistoranteDetails(ID: $idRistorante): " . $e->getMessage() . " SQL: " . $sql);
            return false;
        }
    }

    /**
     * Recupera tutti i piatti di un ristorante specifico.
     * @param int $idRistorante
     * @return array (raggruppati per categoria) o array vuoto.
     */
    public function getPiattiByRistorante($idRistorante) {
        if (!$this->db_conn || !is_numeric($idRistorante) || $idRistorante <= 0) {
             error_log("RistoranteManager::getPiattiByRistorante - Connessione DB nulla o ID ristorante non valido: $idRistorante");
            return [];
        }
        try {
            $sql = "SELECT IDPiatto, NomePiatto, Descrizione, Prezzo, ImmaginePiattoURL, CategoriaPiatto 
                     FROM PIATTO 
                     WHERE IDRistorante = :id_ristorante AND Disponibilita = TRUE 
                     ORDER BY CategoriaPiatto, NomePiatto ASC";
            $stmt = $this->db_conn->prepare($sql);
            $stmt->bindParam(':id_ristorante', $idRistorante, PDO::PARAM_INT);
            $stmt->execute();
            
            $result_piatti_all = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $piatti_per_categoria = [];
            if ($result_piatti_all) {
                foreach ($result_piatti_all as $row) {
                    $piatti_per_categoria[$row['CategoriaPiatto']][] = $row;
                }
            }
            return $piatti_per_categoria;
        } catch (PDOException $e) {
            error_log("Errore PDO RistoranteManager::getPiattiByRistorante(ID: $idRistorante): " . $e->getMessage() . " SQL: " . $sql);
            return [];
        }
    }
    
    // TODO: Aggiungere metodi per creare esercizi nel catalogo (per insegnanti/admin)
    // Il metodo creaEsercizioCatalogo() qui sotto è un TODO e deve essere commentato o rimosso se non è implementato.
    /*
    public function creaEsercizioCatalogo($idTema, $titolo, $descrizione, $difficolta, $punti, $imgUrl=null, $videoUrl=null) { 
         // NOTA: Questa logica di creazione esercizio appartiene più al progetto LinguePlatform
         // se non ci sono esercizi specifici da creare solo per FoodExpress in questo contesto.
         // Assicurati di avere una tabella ESERCIZIO_CATALOGO nel DB FoodExpressDB se la implementi qui.

         if (!$this->db_conn) return false;
         try {
             $sql = "INSERT INTO ESERCIZIO_CATALOGO (IDTema, TitoloEsercizio, DescrizioneEsercizio, DifficoltaLinguistica, PuntiOttenibili, ImmagineURL, VideoURL) VALUES (:idTema, :titolo, :descrizione, :difficolta, :punti, :imgUrl, :videoUrl)";
             $stmt = $this->db_conn->prepare($sql);
             $stmt->bindParam(':idTema', $idTema, PDO::PARAM_INT);
             $stmt->bindParam(':titolo', $titolo);
             $stmt->bindParam(':descrizione', $descrizione);
             $stmt->bindParam(':difficolta', $difficolta);
             $stmt->bindParam(':punti', $punti, PDO::PARAM_INT);
             $stmt->bindParam(':imgUrl', $imgUrl);
             $stmt->bindParam(':videoUrl', $videoUrl);
             return $stmt->execute();
         } catch (PDOException $e) {
             error_log("Errore PDO RistoranteManager::creaEsercizioCatalogo(): " . $e->getMessage() . " SQL: " . $sql);
             return false;
         }
    }
    */ // <<< Assicurati che il commento multi-linea sia chiuso correttamente qui!


} // <<< Assicurati che la graffa chiuda la classe
?>
<!-- Non dovrebbe esserci nulla DOPO il tag di chiusura PHP in un file di classe -->