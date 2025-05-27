<?php
// 2simulazione-esame2025/processa_recensione.php
// Script per ricevere e salvare una recensione inviata dall'utente.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth_check.php'; // Per verificare che l'utente sia loggato (di FoodExpress)
require_once __DIR__ . '/OrdineManager.php'; // Per salvare la recensione

// check_login() assicura che l'utente sia loggato come cliente (solo i clienti possono recensire ordini?)
// Passa 'cliente' per richiedere che l'utente loggato sia di quel tipo, altrimenti reindirizza al login.
$id_cliente_loggato = check_login('cliente', 'login.php'); 


// Assicurati che la richiesta sia POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recupera i dati dal form (pulisci e valida)
    // Usa filter_input per maggiore sicurezza
    $id_ordine = filter_input(INPUT_POST, 'id_ordine', FILTER_VALIDATE_INT);
    $id_ristorante = filter_input(INPUT_POST, 'id_ristorante', FILTER_VALIDATE_INT); // L'ID del ristorante è passato dal form
    $voto = filter_input(INPUT_POST, 'voto', FILTER_VALIDATE_INT);
    $testo_recensione = trim(filter_input(INPUT_POST, 'testo_recensione', FILTER_SANITIZE_SPECIAL_CHARS)); // Sanifica il testo

    // Validazione server-side (anche se il form ha validazione client-side, rifalla qui)
    $errors = [];
    if (!is_numeric($id_ordine) || $id_ordine <= 0) $errors[] = "ID ordine non valido.";
    if (!is_numeric($id_ristorante) || $id_ristorante <= 0) $errors[] = "ID ristorante non valido.";
    if (!is_numeric($voto) || $voto < 1 || $voto > 5) $errors[] = "Voto non valido (deve essere tra 1 e 5).";
    // Il testo è opzionale e limitato a 160 caratteri dal DB.


    if (!empty($errors)) {
        $_SESSION['feedback_message'] = "Errore nella recensione: " . implode("<br>", $errors);
        $_SESSION['feedback_type'] = 'danger';
        // Reindirizza alla pagina da cui si proviene (o a un default)
        $redirect_url = "lascia_recensione.php?id_ordine=" . ($id_ordine ?? ''); // Torna al form con l'ID ordine
        header('Location: ' . $redirect_url);
        exit;
    }

    // ID Cliente loggato è già verificato da check_login all'inizio dello script.

    // Chiama il Manager per salvare la recensione
    $ordineMgr = new OrdineManager();
    
    // Il metodo salvaRecensione nel Manager fa già il controllo del UNIQUE constraint (1062).
    // Puoi aggiungere qui una verifica più complessa dello stato/appartenenza dell'ordine se preferisci.
    // Ma per ora, lasciamo che il Manager gestisca l'inserimento.

    // Salva la recensione nel database
    if ($ordineMgr->salvaRecensione($id_ordine, $id_cliente_loggato, $id_ristorante, $voto, $testo_recensione)) {
        // Messaggio di successo impostato nel Manager
        $_SESSION['feedback_message'] = "Recensione inviata con successo per Ordine #" . htmlspecialchars($id_ordine) . ".";
        $_SESSION['feedback_type'] = 'success';
    } else {
        // Il messaggio di errore specifico (es. già recensito, errore DB) è impostato nel Manager
        if (!isset($_SESSION['feedback_message'])) { // Fallback se il Manager non ha impostato un messaggio specifico
            $_SESSION['feedback_message'] = "Errore sconosciuto durante l'invio della recensione.";
            $_SESSION['feedback_type'] = 'danger';
        }
    }

    // Reindirizza alla pagina "I Miei Ordini" dopo aver processato la recensione
    header('Location: miei_ordini.php');
    exit;

} else {
    // Se non è una richiesta POST (accesso diretto), reindirizza alla lista ordini
    header('Location: miei_ordini.php');
    exit;
}
?>