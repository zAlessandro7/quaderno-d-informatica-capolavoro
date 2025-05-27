<?php
// 2simulazione-esame2025/lascia_recensione.php
$page_title = "Lascia una Recensione";
require_once 'header.php';      // Include header, session_start(), config, etc.
require_once 'auth_check.php';  // Per verificare che l'utente sia loggato (di FoodExpress)
require_once 'OrdineManager.php'; // Include OrdineManager per verificare l'ordine

// check_login() assicura che l'utente sia loggato come cliente per poter lasciare una recensione.
$id_cliente_loggato = check_login('cliente', 'login.php'); // Richiede login come 'cliente'. Fa exit() se fallisce.


// Recupera l'ID dell'ordine dall'URL
$id_ordine = $_GET['id_ordine'] ?? null;

// --- VERIFICHE PRIMA DI MOSTRARE IL FORM ---
$ordineMgr = new OrdineManager();
$ordine_da_recensire = null;
$gia_recensito = false;

// 1. Verifica validità ID Ordine
if (!is_numeric($id_ordine) || $id_ordine <= 0) {
    $_SESSION['feedback_message'] = "ID ordine non valido per la recensione.";
    $_SESSION['feedback_type'] = 'danger';
    header('Location: miei_ordini.php'); // Reindirizza alla lista ordini
    exit;
}

// 2. Recupera i dettagli dell'ordine per verificare stato e appartenenza al cliente
$ordine_verif = $ordineMgr->getDettagliOrdine($id_ordine, $id_cliente_loggato);

// Se l'ordine non esiste o non appartiene al cliente loggato
if (!$ordine_verif) {
    $_SESSION['feedback_message'] = "Ordine non trovato o non appartiene a te.";
    $_SESSION['feedback_type'] = 'danger';
    header('Location: miei_ordini.php');
    exit;
}

// 3. Verifica stato ordine (deve essere "consegnato")
if ($ordine_verif['StatoOrdine'] !== 'consegnato') {
    $_SESSION['feedback_message'] = "Puoi lasciare una recensione solo per ordini 'consegnati'.";
    $_SESSION['feedback_type'] = 'warning';
    header('Location: miei_ordini.php');
    exit;
}

// 4. Verifica se l'ordine è già stato recensito
// Aggiungi un metodo a OrdineManager o fai la query qui
try {
    $conn = $ordineMgr->getConnection(); // Ottieni la connessione dal Manager
    if ($conn) {
         $stmt_check = $conn->prepare("SELECT IDRecensione FROM RECENSIONE WHERE IDOrdine = :id_ordine");
         $stmt_check->bindParam(':id_ordine', $id_ordine, PDO::PARAM_INT);
         $stmt_check->execute();
         if ($stmt_check->fetch()) {
             $gia_recensito = true;
         }
    } else {
        // La connessione DB non è disponibile - gestito da check_login o messaggio in header
        // Non bloccare qui, assumi non recensito ( Manager.salvaRecensione lo bloccherà con UNIQUE )
    }
} catch (PDOException $e) {
     error_log("lascia_recensione.php: Errore PDO verifica recensione: " . $e->getMessage());
     // Continua, il Manager.salvaRecensione gestirà l'errore UNIQUE (1062)
}


// 5. Se l'ordine è già stato recensito, reindirizza
if ($gia_recensito) {
    $_SESSION['feedback_message'] = "Hai già lasciato una recensione per questo ordine.";
    $_SESSION['feedback_type'] = 'warning';
    header('Location: miei_ordini.php'); // Torna alla lista ordini
    exit;
}

// --- FINE VERIFICHE ---

// Se arrivi qui, l'ordine è valido, tuo, consegnato e non recensito.
$ordine_da_recensire = $ordine_verif; // Usa i dettagli verificati per mostrare nel form

// Imposta il titolo della pagina con l'ID dell'ordine (usato da header.php)
$page_title = "Recensisci Ordine #" . $ordine_da_recensire['IDOrdine'];

?>

<div class="container mt-5">
    <h1 class="text-center mb-4 page-title"><?php echo htmlspecialchars($page_title); ?></h1>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            Recensione per Ordine #<?php echo htmlspecialchars($ordine_da_recensire['IDOrdine']); ?> - Da: <strong><?php echo htmlspecialchars($ordine_da_recensire['NomeRistorante']); ?></strong>
        </div>
        <div class="card-body">
            <!-- Form che invia i dati allo script action per processare la recensione -->
            <form action="processa_recensione.php" method="POST" class="needs-validation" novalidate>
                <!-- Campi nascosti necessari per processa_recensione.php -->
                <input type="hidden" name="id_ordine" value="<?php echo htmlspecialchars($ordine_da_recensire['IDOrdine']); ?>">
                <input type="hidden" name="id_ristorante" value="<?php echo htmlspecialchars($ordine_da_recensire['IDRistorante']); ?>">
                <!-- ID Cliente non serve passarlo nel form, prendilo dalla sessione in processa_recensione.php per sicurezza -->

                <div class="mb-3">
                    <label for="voto" class="form-label">Voto (1-5 Stelle): <span class="text-danger">*</span></label>
                    <select class="form-select" id="voto" name="voto" required>
                        <option value="">Seleziona un voto</option>
                        <option value="5">5 - Eccellente</option>
                        <option value="4">4 - Molto Buono</option>
                        <option value="3">3 - Buono</option>
                        <option value="2">2 - Discreto</option>
                        <option value="1">1 - Scarso</option>
                    </select>
                    <div class="invalid-feedback">Seleziona un voto.</div>
                </div>

                <div class="mb-3">
                    <label for="testo_recensione" class="form-label">Lascia un commento (max 160 caratteri):</label>
                    <textarea class="form-control" id="testo_recensione" name="testo_recensione" rows="3" maxlength="160"></textarea>
                     <small class="form-text text-muted">Opzionale.</small>
                </div>

                <button type="submit" class="btn btn-primary">Invia Recensione</button>
                <a href="miei_ordini.php" class="btn btn-secondary ms-2">Annulla</a>
            </form>
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
        form.classList.add('was-validated') // Aggiunge classi per mostrare feedback di validazione
      }, false)
    })
})()
</script>

<?php 
// TODO: Creare i file processa_recensione.php (action), dettagli_ordine.php, annulla_ordine.php
require_once 'footer.php'; // Include il footer HTML comune
?>