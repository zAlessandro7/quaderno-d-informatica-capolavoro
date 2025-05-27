<?php
// 2simulazione-esame2025/aggiorna_carrello.php
require_once __DIR__ . '/CarrelloManager.php';
$carrelloMgr = new CarrelloManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['empty_cart'])) {
        $_SESSION['feedback_message_carrello'] = $carrelloMgr->svuotaCarrelloCompletamente();
        $_SESSION['feedback_type_carrello'] = "ok_msg";
    } elseif (isset($_POST['remove_item'])) {
        $id_piatto_da_rimuovere = filter_var($_POST['remove_item'], FILTER_VALIDATE_INT);
        $_SESSION['feedback_message_carrello'] = $carrelloMgr->rimuoviPiatto($id_piatto_da_rimuovere);
        $_SESSION['feedback_type_carrello'] = "ok_msg";
    } elseif (isset($_POST['update_cart']) && isset($_POST['quantita']) && is_array($_POST['quantita'])) {
        foreach ($_POST['quantita'] as $id_piatto => $nuova_quantita) {
            // La validazione della quantità avviene dentro aggiornaQuantita
            $carrelloMgr->aggiornaQuantita(filter_var($id_piatto, FILTER_VALIDATE_INT), filter_var($nuova_quantita, FILTER_VALIDATE_INT));
        }
        $_SESSION['feedback_message_carrello'] = "Carrello aggiornato.";
        $_SESSION['feedback_type_carrello'] = "ok_msg";
    }
    header('Location: carrello.php'); // Reindirizza a carrello.php nella stessa cartella
    exit;
} else {
    header('Location: index.php');
    exit;
}
?>