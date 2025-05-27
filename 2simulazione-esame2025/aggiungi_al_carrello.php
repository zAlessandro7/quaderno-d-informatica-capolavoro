<?php
// 2simulazione-esame2025/aggiungi_al_carrello.php
ini_set('display_errors', 1); // Per debug, rimuovi in produzione
error_reporting(E_ALL);     // Per debug

require_once __DIR__ . '/CarrelloManager.php';
// Non serve database_config.php qui perché CarrelloManager non usa il DB direttamente per questa azione.
// Se RistoranteManager fosse usato per validare il piatto, allora servirebbe.

$carrelloMgr = new CarrelloManager(); // Il costruttore avvia la sessione

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_piatto = filter_input(INPUT_POST, 'id_piatto', FILTER_VALIDATE_INT);
    $nome_piatto = trim(filter_input(INPUT_POST, 'nome_piatto', FILTER_SANITIZE_SPECIAL_CHARS));
    $prezzo_piatto_str = $_POST['prezzo_piatto'] ?? '0'; // Prendi come stringa
    $prezzo_piatto = filter_var(str_replace(',', '.', $prezzo_piatto_str), FILTER_VALIDATE_FLOAT); // Converti virgola in punto per float
    $quantita = filter_input(INPUT_POST, 'quantita', FILTER_VALIDATE_INT);
    $id_ristorante_piatto = filter_input(INPUT_POST, 'id_ristorante_piatto', FILTER_VALIDATE_INT);

    // Debug: Controlla i valori ricevuti e filtrati
    /*
    echo "<pre>POST Data:\n"; print_r($_POST); echo "</pre>";
    echo "<pre>Filtered Data:\n";
    var_dump($id_piatto, $nome_piatto, $prezzo_piatto_str, $prezzo_piatto, $quantita, $id_ristorante_piatto);
    echo "</pre>";
    */

    if ($id_piatto && !empty($nome_piatto) && $prezzo_piatto !== false && $prezzo_piatto >= 0 && $quantita && $quantita > 0 && $id_ristorante_piatto) {
        if (!$carrelloMgr->aggiungiPiatto($id_piatto, $nome_piatto, $prezzo_piatto, $quantita, $id_ristorante_piatto)) {
            // Il messaggio di errore è già stato impostato nella sessione da CarrelloManager
            // se c'è un conflitto di ristoranti.
        }
        // Messaggio di successo impostato da CarrelloManager
    } else {
        $_SESSION['feedback_message_ristorante'] = "Errore: Dati del piatto non validi o mancanti per l'aggiunta al carrello. Riprova.";
        $_SESSION['feedback_type_ristorante'] = "error_msg";
        // Log per te:
        error_log("Dati non validi in aggiungi_al_carrello.php: " . print_r($_POST, true));
    }
    
    // Reindirizza sempre alla pagina del ristorante da cui si è tentato di aggiungere
    // Il ?id=... è cruciale
    if ($id_ristorante_piatto) {
        header('Location: ristorante.php?id=' . $id_ristorante_piatto);
    } else {
        // Fallback se id_ristorante_piatto non è disponibile (non dovrebbe succedere con il form corretto)
        header('Location: index.php'); 
    }
    exit;

} else { // Metodo non POST
    header('Location: index.php');
    exit;
}
?>