<?php
// 2simulazione-esame2025/dettagli_ordine.php
$page_title = "Dettagli Ordine"; // Titolo di default
require_once 'header.php';      // Include header, session_start(), config, etc.
require_once 'auth_check.php';  // Per verificare login
require_once 'OrdineManager.php'; // Per recuperare i dettagli dell'ordine

// check_login() assicura che l'utente sia loggato come cliente
$id_cliente_loggato = check_login('cliente', 'login.php'); // Richiede login come 'cliente'


// Recupera l'ID dell'ordine dall'URL (parametro 'id_ordine')
$id_ordine = $_GET['id_ordine'] ?? null;

// Validazione dell'ID ordine
if (!is_numeric($id_ordine) || $id_ordine <= 0) {
    $_SESSION['feedback_message'] = "ID ordine non valido.";
    $_SESSION['feedback_type'] = 'danger';
    header('Location: miei_ordini.php'); // Reindirizza alla lista ordini se ID non valido
    exit;
}

// Recupera i dettagli dell'ordine e i suoi piatti dal database
$ordineMgr = new OrdineManager(); 
$ordine_completo = $ordineMgr->getDettagliOrdine($id_ordine, $id_cliente_loggato); // Questo metodo verifica anche l'appartenenza al cliente

// Se l'ordine non è stato trovato o non appartiene al cliente loggato
if (!$ordine_completo) {
    $_SESSION['feedback_message'] = "Ordine non trovato o non hai i permessi per visualizzarlo.";
    $_SESSION['feedback_type'] = 'danger';
    header('Location: miei_ordini.php'); // Reindirizza alla lista ordini
    exit;
}

// Se arrivi qui, l'ordine è stato trovato ed appartiene al cliente.
// I dettagli dell'ordine (inclusi i piatti in $ordine_completo['dettagli_piatti']) sono disponibili.
$page_title = "Dettagli Ordine #" . htmlspecialchars($ordine_completo['IDOrdine']); // Imposta il titolo della pagina


// --- INIZIO HTML DELLA PAGINA ---
// L'header HTML è già stato incluso da require_once 'header.php';

?>

<div class="container mt-5">
    <h1 class="text-center mb-4 page-title"><?php echo htmlspecialchars($page_title); ?></h1>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            Dettagli Ordine
        </div>
        <div class="card-body">
            <p><strong>Ordine #<?php echo htmlspecialchars($ordine_completo['IDOrdine']); ?></strong></p>
            <p>Data e Ora: <?php echo date("d/m/Y H:i", strtotime($ordine_completo['DataOraOrdine'])); ?></p>
            <p>Ristorante: <strong><?php echo htmlspecialchars($ordine_completo['NomeRistorante']); ?></strong></p>
            <p>Stato: <span class="badge bg-<?php 
                 switch($ordine_completo['StatoOrdine']){
                     case 'consegnato': echo 'success'; break;
                     case 'in consegna': echo 'info'; break;
                     case 'in preparazione': echo 'warning'; break;
                     case 'annullato': echo 'danger'; break;
                     case 'confermato': echo 'primary'; break;
                     default: echo 'secondary'; break;
                 }
             ?>"><?php echo htmlspecialchars(ucfirst($ordine_completo['StatoOrdine'])); ?></span>
            </p>
            <p>Indirizzo di Consegna: <?php echo nl2br(htmlspecialchars($ordine_completo['IndirizzoConsegnaOrdine'])); ?></p>
            <?php if (!empty($ordine_completo['NoteCliente'])): ?>
                <p>Note Cliente: <?php echo nl2br(htmlspecialchars($ordine_completo['NoteCliente'])); ?></p>
            <?php endif; ?>
            <p>Metodo di Pagamento: <?php echo htmlspecialchars($ordine_completo['MetodoPagamento']); ?></p>
            <?php if (!empty($ordine_completo['IDPagamento'])): ?>
                <p>ID Pagamento: <?php echo htmlspecialchars($ordine_completo['IDPagamento']); ?></p>
            <?php endif; ?>

            <hr>

            <h5>Piatti Ordinati:</h5>
            <?php if (!empty($ordine_completo['dettagli_piatti'])): ?>
                <ul class="list-group mb-3">
                    <?php foreach ($ordine_completo['dettagli_piatti'] as $piatto_dettaglio): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($piatto_dettaglio['Quantita']); ?> x <?php echo htmlspecialchars($piatto_dettaglio['NomePiatto']); ?>
                            <span>€ <?php echo number_format($piatto_dettaglio['PrezzoUnitarioAlMomentoOrdine'] * $piatto_dettaglio['Quantita'], 2, ',', '.'); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <h6>Totale Ordine: <strong>€ <?php echo number_format($ordine_completo['TotaleOrdine'], 2, ',', '.'); ?></strong></h6>
            <?php else: ?>
                <p class="text-muted">Nessun dettaglio piatto trovato per questo ordine.</p>
            <?php endif; ?>

             <hr>

            <!-- Link per tornare indietro -->
            <p class="text-center">
                <a href="miei_ordini.php" class="btn btn-secondary">« Torna ai Miei Ordini</a>
            </p>

        </div>
    </div>

</div>

<?php
// TODO: Se richiesto, implementare anche la visualizzazione della recensione per questo ordine qui
require_once 'footer.php';
?>