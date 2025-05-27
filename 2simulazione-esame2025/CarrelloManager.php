<?php
// 2simulazione-esame2025/CarrelloManager.php

class CarrelloManager {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Inizializza il carrello se non esiste o non ha la struttura corretta
        if (!isset($_SESSION['carrello']) || !is_array($_SESSION['carrello']) || 
            !isset($_SESSION['carrello']['id_ristorante_attivo']) || !isset($_SESSION['carrello']['items'])) {
            $this->initCarrello();
        }
    }

    private function initCarrello() {
        $_SESSION['carrello'] = ['id_ristorante_attivo' => null, 'items' => []];
    }

    public function aggiungiPiatto($id_piatto, $nome_piatto, $prezzo_piatto, $quantita, $id_ristorante_del_piatto) {
        if (!is_numeric($id_piatto) || $id_piatto <= 0 ||
            empty(trim($nome_piatto)) ||
            !is_numeric($prezzo_piatto) || $prezzo_piatto < 0 ||
            !is_numeric($quantita) || $quantita <= 0 ||
            !is_numeric($id_ristorante_del_piatto) || $id_ristorante_del_piatto <= 0) {
            
            $_SESSION['feedback_message_ristorante'] = "Dati del piatto non validi per l'aggiunta.";
            $_SESSION['feedback_type_ristorante'] = "error_msg";
            return false;
        }

        $id_ristorante_nel_carrello = $_SESSION['carrello']['id_ristorante_attivo'] ?? null;

        if ($id_ristorante_nel_carrello !== null && $id_ristorante_nel_carrello != $id_ristorante_del_piatto) {
            $_SESSION['feedback_message_ristorante'] = "Puoi ordinare solo da un ristorante alla volta. Svuota il carrello o completa l'ordine attuale per ordinare da un altro ristorante.";
            $_SESSION['feedback_type_ristorante'] = "error_msg";
            return false;
        }
        
        if ($id_ristorante_nel_carrello === null) {
            $_SESSION['carrello']['id_ristorante_attivo'] = $id_ristorante_del_piatto;
        }

        if (isset($_SESSION['carrello']['items'][$id_piatto])) {
            $_SESSION['carrello']['items'][$id_piatto]['quantita'] += $quantita;
        } else {
            $_SESSION['carrello']['items'][$id_piatto] = [
                'id_piatto' => $id_piatto,
                'nome' => $nome_piatto,
                'prezzo' => (float)$prezzo_piatto, // Assicura sia float
                'quantita' => (int)$quantita,     // Assicura sia int
                'id_ristorante_piatto' => $id_ristorante_del_piatto
            ];
        }
        $_SESSION['feedback_message_ristorante'] = htmlspecialchars($quantita) . " x " . htmlspecialchars($nome_piatto) . " aggiunto/i al carrello!";
        $_SESSION['feedback_type_ristorante'] = "ok_msg";
        return true;
    }

    public function aggiornaQuantita($id_piatto, $nuova_quantita) {
        if (!is_numeric($id_piatto) || !is_numeric($nuova_quantita)) return "Dati non validi.";

        if (isset($_SESSION['carrello']['items'][$id_piatto])) {
            if ($nuova_quantita > 0 && $nuova_quantita <= 10) { // Limite esempio
                $_SESSION['carrello']['items'][$id_piatto]['quantita'] = (int)$nuova_quantita;
                return "Quantità aggiornata per " . htmlspecialchars($_SESSION['carrello']['items'][$id_piatto]['nome']) . ".";
            } elseif ($nuova_quantita <= 0) {
                $nome_piatto_rimosso = $_SESSION['carrello']['items'][$id_piatto]['nome'];
                unset($_SESSION['carrello']['items'][$id_piatto]);
                if (empty($_SESSION['carrello']['items'])) { $this->svuotaCarrelloCompletamente(); }
                return htmlspecialchars($nome_piatto_rimosso) . " rimosso dal carrello.";
            } else {
                return "Quantità non valida (max 10).";
            }
        }
        return "Articolo non trovato per l'aggiornamento.";
    }

    public function rimuoviPiatto($id_piatto) {
        if (!is_numeric($id_piatto)) return "ID piatto non valido.";

        if (isset($_SESSION['carrello']['items'][$id_piatto])) {
            $nome_piatto_rimosso = $_SESSION['carrello']['items'][$id_piatto]['nome'];
            unset($_SESSION['carrello']['items'][$id_piatto]);
            if (empty($_SESSION['carrello']['items'])) {
                $this->svuotaCarrelloCompletamente();
            }
            return htmlspecialchars($nome_piatto_rimosso) . " rimosso dal carrello.";
        }
        return "Articolo non trovato per la rimozione.";
    }

    public function getCarrelloItems() {
        return $_SESSION['carrello']['items'] ?? [];
    }

    public function getIdRistoranteAttivo() {
        return $_SESSION['carrello']['id_ristorante_attivo'] ?? null;
    }

    public function svuotaCarrelloCompletamente() {
        $this->initCarrello();
        return "Il carrello è stato svuotato.";
    }

    public function getNumeroTotaleArticoli() {
        $count = 0;
        foreach ($this->getCarrelloItems() as $item) {
            $count += $item['quantita'];
        }
        return $count;
    }

    public function getTotaleCarrello() {
        $totale = 0;
        foreach ($this->getCarrelloItems() as $item) {
            $totale += $item['prezzo'] * $item['quantita'];
        }
        return $totale;
    }
}
?>