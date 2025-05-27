<?php
// 2simulazione-esame2025/miei_ordini.php
// Pagina per visualizzare gli ordini effettuati dall'utente cliente loggato.

// Include l'header HTML comune (che gestisce sessione, config, ecc.)
// Assumendo che header.php sia nella stessa cartella.
require_once 'header.php'; 

// Include la classe specifica per la gestione degli Ordini
require_once __DIR__ . '/OrdineManager.php'; 

// *** CORREZIONE QUI: AGGIUNTO __DIR__ . '/' ***
// Include l'helper per il controllo dell'autenticazione (check_login)
// Assumendo che auth_check.php sia nella stessa cartella.
require_once __DIR__ . '/auth_check.php'; 


// --- LOGICA PHP DELLA PAGINA ---

// check_login() da auth_check.php assicura che l'utente sia loggato come cliente.
// Per questa pagina, richiediamo che l'utente sia di tipo 'cliente'.
// Se non è loggato o non è 'cliente', check_login reindirizzerà.
// check_login restituisce l'ID utente loggato se il controllo ha successo.
$id_cliente_loggato = check_login('cliente', 'login.php'); // Questo farà exit() se non loggato o non cliente


// Recupera gli ordini del cliente loggato dal database
$ordineMgr = new OrdineManager(); 
$miei_ordini = []; 

// Controlla se la connessione nel manager è disponibile
if ($ordineMgr->getConnection()) { 
    $miei_ordini = $ordineMgr->getOrdiniByClienteId($id_cliente_loggato);
} else {
     error_log("miei_ordini.php: Connessione OrdineManager non disponibile per getOrdiniByClienteId.");
}


// --- INIZIO HTML DELLA PAGINA ---
// L'header HTML è già stato incluso da require_once 'header.php';

// Il messaggio di feedback globale viene mostrato da header.php
// non serve mostrarlo di nuovo qui

?>

<div class="container mt-5">
    <h1 class="text-center mb-4 page-title">I Tuoi Ordini</h1>

    <?php 
    // Messaggi di feedback globali mostrati da header.php
    ?>


    <?php if (!empty($miei_ordini)): ?>
        <div class="list-group">
            <?php foreach ($miei_ordini as $ordine): ?>
                <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 shadow-sm">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Ordine #<?php echo htmlspecialchars($ordine['IDOrdine']); ?></h5>
                        <small class="text-muted"><?php echo date("d/m/Y H:i", strtotime($ordine['DataOraOrdine'])); ?></small>
                    </div>
                    <p class="mb-1">
                        Da: <strong><?php echo htmlspecialchars($ordine['NomeRistorante']); ?></strong> | 
                        Stato: <span class="badge bg-<?php 
                            switch($ordine['StatoOrdine']){
                                case 'consegnato': echo 'success'; break;
                                case 'in consegna': echo 'info'; break;
                                case 'in preparazione': echo 'warning'; break;
                                case 'annullato': echo 'danger'; break;
                                case 'confermato': echo 'primary'; break;
                                default: echo 'secondary'; break;
                            }
                        ?>"><?php echo htmlspecialchars(ucfirst($ordine['StatoOrdine'])); ?></span>
                    </p>
                    <p class="mb-1">Totale: <strong>€ <?php echo number_format($ordine['TotaleOrdine'], 2, ',', '.'); ?></strong></p>
                    
                    <div class="mt-2">
                        <!-- Link per vedere dettagli ordine completo -->
                        <a href="dettagli_ordine.php?id_ordine=<?php echo $ordine['IDOrdine']; ?>" class="btn btn-outline-primary btn-sm me-2">Vedi Dettagli</a>
                        
                        <!-- --- PULSANTE PER LASCIARE RECENSIONE --- -->
                        <?php 
                            $can_review = ($ordine['StatoOrdine'] === 'consegnato'); // Puoi recensire solo se CONSEGNATO
                            $ordine_gia_recensito = false; // Implementa questa verifica

                            // TODO: Implementare verifica se recensione GIA' stata lasciata
                            // Questa logica richiede un campo in tabella ORDINE o una query a RECENSIONE
                            /*
                            if ($can_review) {
                                try {
                                    $conn_rec = $ordineMgr->getConnection();
                                    if ($conn_rec) {
                                        $stmt_check = $conn_rec->prepare("SELECT IDRecensione FROM RECENSIONE WHERE IDOrdine = ?");
                                        $stmt_check->bindParam(1, $ordine['IDOrdine'], PDO::PARAM_INT);
                                        $stmt_check->execute();
                                        if ($stmt_check->fetch()) {
                                            $ordine_gia_recensito = true; 
                                        }
                                    } else {
                                         error_log("miei_ordini.php: Connessione DB non disponibile per verifica recensione singola.");
                                    }
                                } catch(PDOException $e) {
                                    error_log("miei_ordini.php: PDO Error checking recensione: " . $e->getMessage());
                                }
                            }
                            */

                            if ($can_review && !$ordine_gia_recensito) {
                                echo '<a href="lascia_recensione.php?id_ordine=' . $ordine['IDOrdine'] . '" class="btn btn-outline-info btn-sm me-2">Lascia Recensione</a>';
                            } elseif ($ordine['StatoOrdine'] === 'consegnato' && $ordine_gia_recensito) {
                                echo '<span class="text-success me-2"><small>Recensito</small></span>';
                            }
                        ?>
                         <!-- TODO: Aggiungi pulsante per annullare ordine -->
                         <?php
                            if ($ordine['StatoOrdine'] === 'in attesa') {
                                // Questo link/form dovrebbe chiamare un action script (es. annulla_ordine.php)
                                // Usare un form POST è più sicuro
                                echo '<a href="annulla_ordine.php?id_ordine=' . $ordine['IDOrdine'] . '" class="btn btn-outline-danger btn-sm" onclick="return confirm(\'Sei sicuro di voler annullare questo ordine?\');">Annulla Ordine</a>';
                            }
                         ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center" role="alert">
            Non hai ancora effettuato nessun ordine.
        </div>
        <p class="text-center"><a href="index.php" class="btn btn-primary">Esplora i Ristoranti</a></p>
    <?php endif; ?>
</div>

<?php
// TODO: Creare i file dettagli_ordine.php, lascia_recensione.php, annulla_ordine.php, processa_recensione.php
require_once 'footer.php'; 
?>