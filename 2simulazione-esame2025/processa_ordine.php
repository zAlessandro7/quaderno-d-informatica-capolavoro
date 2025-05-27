<?php
// 2simulazione-esame2025/processa_ordine.php
// Script per ricevere e processare la conferma dell'ordine.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include l'helper per il controllo dell'autenticazione
require_once __DIR__ . '/auth_check.php'; // Per verificare che l'utente sia loggato (di FoodExpress)
// Include le classi manager necessarie
require_once __DIR__ . '/OrdineManager.php'; // Per creare l'ordine nel DB
require_once __DIR__ . '/CarrelloManager.php'; // Per svuotare il carrello

// check_login() assicura che l'utente sia loggato come cliente
// Passa 'cliente' per richiedere che l'utente loggato sia di quel tipo, altrimenti reindirizza al login.
$id_cliente_loggato = check_login('cliente', 'login.php'); 


// Assicurati che la richiesta sia POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recupera i dati dal form (pulisci e valida)
    // Usa filter_input per maggiore sicurezza
    $id_ristorante = filter_input(INPUT_POST, 'id_ristorante', FILTER_VALIDATE_INT);
    $totale_ordine = filter_input(INPUT_POST, 'totale_ordine', FILTER_VALIDATE_FLOAT);
    $indirizzo_consegna = trim(filter_input(INPUT_POST, 'indirizzo_consegna', FILTER_SANITIZE_SPECIAL_CHARS));
    $metodo_pagamento = trim(filter_input(INPUT_POST, 'metodo_pagamento', FILTER_SANITIZE_SPECIAL_CHARS));
    $note_cliente = trim(filter_input(INPUT_POST, 'note_cliente', FILTER_SANITIZE_SPECIAL_CHARS));

    // Validazione server-side (essenziale!)
    $errors = [];
    if (!is_numeric($id_ristorante) || $id_ristorante <= 0) $errors[] = "ID ristorante non valido.";
    if (!is_numeric($totale_ordine) || $totale_ordine < 0) $errors[] = "Totale ordine non valido.";
    if (empty($indirizzo_consegna)) $errors[] = "Indirizzo di consegna obbligatorio.";
    if (empty($metodo_pagamento)) $errors[] = "Metodo di pagamento obbligatorio.";
    // TODO: Valida che il metodo di pagamento sia uno valido (es. "alla consegna", "online")

    // Recupera gli articoli del carrello dalla sessione (più sicuro che fidarsi dei dati POST)
    $carrelloMgr = new CarrelloManager();
    $carrello_items = $carrelloMgr->getCarrelloItems();
    $id_ristorante_carrello_sessione = $carrelloMgr->getIdRistoranteAttivo(); // ID ristorante dal carrello in sessione

    // Confronta ID ristorante dal form e dalla sessione per sicurezza
    if (!$id_ristorante_carrello_sessione || $id_ristorante !== $id_ristorante_carrello_sessione) {
         $errors[] = "Errore di sicurezza: Disallineamento ristorante tra carrello e form.";
         error_log("SECURITY ALERT: Disallineamento ID Ristorante tra form POST ($id_ristorante) e sessione carrello ($id_ristorante_carrello_sessione) per Cliente ID $id_cliente_loggato");
    }

    // Controlla che il carrello in sessione non sia vuoto dopo tutti i controlli
    if (empty($carrello_items)) {
         $errors[] = "Il carrello è vuoto. Impossibile processare un ordine vuoto.";
    }

    // Controlla che il totale nel form corrisponda al totale del carrello in sessione (per sicurezza)
    // Potrebbe esserci una leggera differenza dovuta ad arrotondamenti, gestisci con tolleranza se necessario.
    $totale_carrello_calcolato = $carrelloMgr->getTotaleCarrello();
    if (abs($totale_ordine - $totale_carrello_calcolato) > 0.01) { // Tolleranza di 1 centesimo
         $errors[] = "Errore di sicurezza: Disallineamento del totale ordine calcolato ({$totale_carrello_calcolato}) vs totale form ({$totale_ordine}).";
         error_log("SECURITY ALERT: Totale ordine disallineato tra form POST ($totale_ordine) e carrello sessione ($totale_carrello_calcolato) per Cliente ID $id_cliente_loggato");
    }


    if (!empty($errors)) {
        $_SESSION['feedback_message'] = "Errore nel checkout: " . implode("<br>", $errors);
        $_SESSION['feedback_type'] = 'danger';
        $_SESSION['form_data_checkout'] = $_POST; // Salva i dati form per ripopolare (opzionale)
        // Reindirizza al checkout form
        header('Location: checkout.php');
        exit;
    }

    // --- Se la validazione è OK, salva l'ordine nel database ---
    $ordineMgr = new OrdineManager();
    
    // Chiama il metodo creaNuovoOrdine nel Manager
    $id_nuovo_ordine = $ordineMgr->creaNuovoOrdine(
        $id_cliente_loggato,
        $indirizzo_consegna,
        $metodo_pagamento,
        $note_cliente,
        $carrello_items, // Passa gli items dal carrello in sessione
        $id_ristorante,    // Passa l'ID ristorante verificato
        $totale_ordine     // Passa il totale verificato
    );

    if ($id_nuovo_ordine) {
        // Ordine creato con successo!

        // Svuota il carrello in sessione
        $carrelloMgr->svuotaCarrelloCompletamente();

        // Imposta un messaggio di successo per la pagina successiva (Miei Ordini)
        $_SESSION['feedback_message'] = "Ordine #" . $id_nuovo_ordine . " creato con successo!";
        $_SESSION['feedback_type'] = 'success';

        // Reindirizza alla pagina "I Miei Ordini"
        header('Location: miei_ordini.php');
        exit;

    } else {
        // Se creaNuovoOrdine restituisce false, il messaggio di errore è già stato impostato nel Manager
        if (!isset($_SESSION['feedback_message'])) { // Fallback se il Manager non ha impostato un messaggio specifico
             $_SESSION['feedback_message'] = "Impossibile finalizzare l'ordine. Riprova.";
             $_SESSION['feedback_type'] = 'danger';
        }
        // Non svuotare il carrello se la creazione ordine fallisce!
        $_SESSION['form_data_checkout'] = $_POST; // Salva i dati form per ripopolare (opzionale)
        // Reindirizza di nuovo al checkout
        header('Location: checkout.php');
        exit;
    }

} else {
    // Se non è una richiesta POST (accesso diretto), reindirizza al carrello o homepage
     header('Location: carrello.php'); // O index.php
     exit;
}
?>