<?php
// 2simulazione-esame2025/checkout.php
// Pagina per la conferma finale dell'ordine prima di salvarlo nel database.

// ATTIVA QUESTO PER DEBUG COMPLETO SE NECESSARIO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// echo "DEBUG: Inizio checkout.php<br>"; // DEBUG INIZIALE


$page_title = "Checkout"; // Titolo specifico per questa pagina

// Include i file helper e le classi manager necessarie per questa pagina
require_once 'header.php';      // Include header, session_start(), config, UtenteManager (di FoodExpress), RistoranteManager, CarrelloManager (se gestiti lì)
require_once __DIR__ . '/auth_check.php';  // Helper per verificare che l'utente sia loggato (di FoodExpress)
require_once __DIR__ . '/OrdineManager.php'; // Manager per le operazioni sugli ordini (incluso getDettagliOrdine se usato, ma checkout non lo usa per i dettagli riepilogo)
require_once __DIR__ . '/CarrelloManager.php'; // Manager per accedere ai dati del carrello in sessione
require_once __DIR__ . '/ClienteManager.php'; // Manager per recuperare i dettagli del cliente
require_once __DIR__ . '/RistoranteManager.php'; // Manager per recuperare i dettagli del ristorante


// --- LOGICA PHP DELLA PAGINA ---

// check_login() da auth_check.php assicura che l'utente sia loggato come cliente per poter fare checkout.
// Se non è loggato o non è 'cliente', check_login reindirizzerà alla pagina di login.
// check_login restituisce l'ID utente loggato se il controllo ha successo.
$id_cliente_loggato = check_login('cliente', 'login.php'); // Richiede login come 'cliente'. Fa exit() se fallisce.


// Recupera i dati necessari per mostrare il riepilogo dell'ordine: carrello, dettagli cliente, dettagli ristorante
$carrelloMgr = new CarrelloManager(); // Crea un'istanza del Manager Carrello
$carrello_items = $carrelloMgr->getCarrelloItems(); // Ottiene gli articoli dal carrello in sessione
$id_ristorante_carrello = $carrelloMgr->getIdRistoranteAttivo(); // Ottiene l'ID del ristorante dal carrello
$totale_carrello = $carrelloMgr->getTotaleCarrello(); // Calcola il totale del carrello


// DEBUG: Mostra lo stato del carrello recuperato
// echo "<pre>DEBUG: Carrello Items: "; print_r($carrello_items); echo "</pre>";
// echo "<pre>DEBUG: ID Ristorante Carrello: "; var_dump($id_ristorante_carrello); echo "</pre>";


// --- CONTROLLI DI VALIDAZIONE PRIMA DI MOSTRARE IL FORM ---
// Verifico se il carrello è vuoto o se manca l'ID del ristorante nel carrello.
// Se non ci sono articoli o non è associato a un ristorante, non si può fare checkout.
if (empty($carrello_items) || !$id_ristorante_carrello) {
     // Imposta un messaggio di feedback per informare l'utente
     $_SESSION['feedback_message'] = "Il carrello è vuoto o incompleto. Non puoi procedere al checkout.";
     $_SESSION['feedback_type'] = 'warning'; // Tipo per messaggio di warning

     // Reindirizza l'utente alla pagina del carrello
     header('Location: carrello.php'); 
     exit; // Termina l'esecuzione dello script qui.
}

// Recupera i dettagli completi del cliente loggato (incluso indirizzo principale)
$clienteMgr = new ClienteManager(); // Crea un'istanza del Manager Clienti
$cliente_details = $clienteMgr->getProfiloUtente($id_cliente_loggato); // Recupera i dettagli del cliente dal DB


// Recupera i dettagli del ristorante dal database
$ristoranteMgr = new RistoranteManager(); // Crea un'istanza del Manager Ristoranti
$ristorante_details = $ristoranteMgr->getRistoranteDetails($id_ristorante_carrello); // Recupera i dettagli del ristorante


// Verifica che i dettagli del cliente e del ristorante siano stati trovati.
// Se non sono stati trovati (anche se l'utente è loggato e il carrello non è vuoto),
// c'è un problema con i dati nel DB o con le classi Manager.
if (!$cliente_details || !$ristorante_details) {
     error_log("Checkout: Dettagli cliente o ristorante non trovati nel DB. Cliente ID: " . ($id_cliente_loggato ?? 'N/D') . ", Ristorante ID: " . ($id_ristorante_carrello ?? 'N/D'));
     $_SESSION['feedback_message'] = "Errore nel recupero dei tuoi dettagli o del ristorante. Riprova.";
     $_SESSION['feedback_type'] = 'danger'; // Tipo per messaggio di errore
     // Reindirizza l'utente al carrello
     header('Location: carrello.php'); 
     exit; // Termina l'esecuzione
}

// --- PREPARA I DATI DEL FORM PER RIPOPOLAMENTO (se c'è stato un errore nel processa_ordine) ---
// Recupera dati del form precedentemente inviato salvati in sessione, se presenti
$form_data_checkout = $_SESSION['form_data_checkout'] ?? [];
// Pulisci questi dati dalla sessione dopo averli recuperati per evitare che riappaiano
unset($_SESSION['form_data_checkout']);


// --- INIZIO HTML DELLA PAGINA ---
// L'header HTML è già stato incluso da require_once 'header.php';
// I messaggi di feedback globali (es. da altre action) sono mostrati da header.php

?>

<div class="container mt-5">
    <h1 class="text-center mb-4 page-title">Checkout - Conferma Ordine</h1>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- SEZIONE RIEPILOGO ORDINE -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    Riepilogo Ordine da <strong><?php echo htmlspecialchars($ristorante_details['NomeRistorante']); ?></strong>
                </div>
                <ul class="list-group list-group-flush">
                    <?php 
                    // Cicla sugli articoli nel carrello per mostrarli
                    foreach ($carrello_items as $item): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span><?php echo htmlspecialchars($item['quantita']); ?> x <?php echo htmlspecialchars($item['nome']); ?></span>
                                <span>€ <?php echo number_format($item['prezzo'] * $item['quantita'], 2, ',', '.'); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <li class="list-group-item list-group-item-info">
                         <div class="d-flex justify-content-between">
                            <strong>Totale Carrello:</strong>
                            <strong>€ <?php echo number_format($totale_carrello, 2, ',', '.'); ?></strong>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- SEZIONE DETTAGLI CONSEGNA E PAGAMENTO CON IL FORM -->
            <div class="card shadow-sm">
                <div class="card-header">
                    Dettagli Consegna e Pagamento
                </div>
                <div class="card-body">
                    <!-- Form per inviare i dati finali a processa_ordine.php -->
                    <form action="processa_ordine.php" method="POST" class="needs-validation" novalidate>
                        <!-- Campi nascosti con ID del ristorante e totale (usati per sicurezza nel processamento) -->
                        <input type="hidden" name="id_ristorante" value="<?php echo htmlspecialchars($id_ristorante_carrello); ?>">
                        <input type="hidden" name="totale_ordine" value="<?php echo htmlspecialchars($totale_carrello); ?>">
                        <!-- Non passare gli items del carrello come campi nascosti, rileggili dalla sessione in processa_ordine.php per sicurezza -->

                        <div class="mb-3">
                            <label for="indirizzo_consegna" class="form-label">Indirizzo di Consegna: <span class="text-danger">*</span></label>
                            <!-- Se c'erano dati precedenti dal form (in sessione), usali per ripopolare, altrimenti usa l'indirizzo principale del cliente -->
                            <textarea class="form-control" id="indirizzo_consegna" name="indirizzo_consegna" rows="3" required><?php echo htmlspecialchars($form_data_checkout['indirizzo_consegna'] ?? $cliente_details['IndirizzoConsegna'] ?? ''); ?></textarea>
                            <div class="invalid-feedback">Inserisci l'indirizzo di consegna.</div>
                             <?php if (empty($cliente_details['IndirizzoConsegna'])): ?>
                                <small class="form-text text-muted">Il tuo profilo non ha un indirizzo principale salvato. Compila qui l'indirizzo per questo ordine.</small>
                             <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="metodo_pagamento" class="form-label">Metodo di Pagamento: <span class="text-danger">*</span></label>
                            <select class="form-select" id="metodo_pagamento" name="metodo_pagamento" required>
                                <option value="" <?php echo !isset($form_data_checkout['metodo_pagamento']) || $form_data_checkout['metodo_pagamento'] === '' ? 'selected' : ''; ?>>Seleziona un metodo</option>
                                <option value="alla consegna" <?php echo (isset($form_data_checkout['metodo_pagamento']) && $form_data_checkout['metodo_pagamento'] === 'alla consegna') ? 'selected' : ''; ?>>Pagamento alla consegna</option>
                                <!-- TODO: Aggiungere altri metodi di pagamento se richiesto -->
                                <!-- <option value="online" <?php //echo (isset($form_data_checkout['metodo_pagamento']) && $form_data_checkout['metodo_pagamento'] === 'online') ? 'selected' : ''; ?>>Pagamento online</option> -->
                            </select>
                            <div class="invalid-feedback">Seleziona un metodo di pagamento.</div>
                        </div>

                        <div class="mb-3">
                            <label for="note_cliente" class="form-label">Note per il Ristorante (Opzionale):</label>
                            <textarea class="form-control" id="note_cliente" name="note_cliente" rows="2"><?php echo htmlspecialchars($form_data_checkout['note_cliente'] ?? ''); ?></textarea>
                        </div>

                        <!-- PULSANTE PER CONFERMARE E INVIARE L'ORDINE -->
                        <button type="submit" class="btn btn-success btn-lg w-100">Conferma e Invia Ordine</button>
                    </form>
                </div>
            </div>

            <!-- Link per tornare al carrello -->
            <p class="text-center mt-4">
                <a href="carrello.php" class="btn btn-secondary">« Torna al Carrello</a>
            </p>

        </div>
    </div>
</div>

<script>
// Validazione Bootstrap client-side (assicura che i campi required siano compilati)
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault() // Ferma l'invio se il form non è valido
          event.stopPropagation() // Ferma la propagazione dell'evento
        }
        form.classList.add('was-validated') // Aggiunge classi per mostrare feedback
      }, false)
    })
})()
</script>

<?php 
// TODO: Creare i file processa_ordine.php (action), dettagli_ordine.php, lascia_recensione.php, annulla_ordine.php, ecc.
require_once 'footer.php'; // Include il footer HTML comune
?>