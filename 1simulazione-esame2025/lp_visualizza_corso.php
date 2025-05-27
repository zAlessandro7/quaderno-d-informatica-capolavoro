<?php
// 1simulazione-esame2025/lp_visualizza_corso.php
$page_title = "Dettagli Corso"; 
require_once 'lp_header.php';      // Include session_start(), config, etc.
require_once 'LpCorsoManager.php'; // Include LpCorsoManager

// *** AGGIUNGI QUESTA RIGA PER INCLUDERE IL FILE CON check_login() ***
require_once 'lp_auth_check.php'; 

// Verifica che l'utente sia loggato (qualsiasi tipo può visualizzare i dettagli del corso pubblico)
// Se vuoi che solo gli studenti ISCRITTI o gli insegnanti CREATORI/GESTORI vedano,
// la logica di check_login e/o recupero dati deve essere più restrittiva o aggiungere un controllo qui.
$current_user_id = check_login(); // Ora check_login() dovrebbe essere definita
$current_user_type = $_SESSION['lp_user_type'] ?? null;


// Recupera l'ID del corso dall'URL
$id_corso = $_GET['id_corso'] ?? null;

// Validazione dell'ID corso
if (!is_numeric($id_corso) || $id_corso <= 0) {
    $_SESSION['lp_feedback_error'] = "ID corso non valido.";
    $_SESSION['lp_feedback_type'] = 'error'; 
    header('Location: lp_corsi.php'); // Reindirizza all'elenco corsi
    exit;
}

// Recupera i dettagli del corso e gli esercizi
$corsoMgr = new LpCorsoManager();
$dettagli_corso = $corsoMgr->getCorsoById($id_corso);
$esercizi_del_corso = []; // Inizializza a vuoto

if (!$dettagli_corso) {
    $_SESSION['lp_feedback_error'] = "Corso non trovato o non disponibile.";
     $_SESSION['lp_feedback_type'] = 'danger';
    header('Location: lp_corsi.php'); // Reindirizza all'elenco corsi
    exit;
}

// Se il corso è stato trovato, recupera i suoi esercizi
$esercizi_del_corso = $corsoMgr->getEserciziByCorsoId($id_corso);

// Imposta il titolo della pagina con il nome del corso (usato da lp_header.php)
$page_title = htmlspecialchars($dettagli_corso['NomeCorso']);

// NOTA: Non includere lp_header.php una seconda volta. La variabile $page_title
// viene impostata qui e sarà usata da lp_header.php quando verrà eseguito (all'inizio del file).


?>

<!-- L'HTML inizia dopo lp_header.php -->

<h2 class="page-title mb-4"><?php echo htmlspecialchars($dettagli_corso['NomeCorso']); ?></h2>
<p class="lead text-center mb-4">
    Lingua: <strong><?php echo htmlspecialchars($dettagli_corso['Lingua']); ?></strong> | 
    Livello: <strong><?php echo htmlspecialchars($dettagli_corso['LivelloDifficolta']); ?></strong> | 
    Docente: <strong><?php echo htmlspecialchars($dettagli_corso['NomeInsegnante'] . ' ' . $dettagli_corso['CognomeInsegnante']); ?></strong>
</p>

<?php if ($dettagli_corso['DescrizioneCorso']): ?>
    <div class="card mb-4">
        <div class="card-header">Descrizione del Corso</div>
        <div class="card-body">
            <p class="card-text"><?php echo nl2br(htmlspecialchars($dettagli_corso['DescrizioneCorso'])); ?></p>
        </div>
    </div>
<?php endif; ?>

<h3 class="mt-5 mb-4">Esercizi del Corso:</h3>

<?php if (!empty($esercizi_del_corso)): ?>
    <div class="list-group">
        <?php foreach ($esercizi_del_corso as $esercizio): ?>
            <div class="list-group-item list-group-item-action flex-column align-items-start mb-2 shadow-sm">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><?php echo htmlspecialchars($esercizio['OrdineInSequenza']); ?>. <?php echo htmlspecialchars($esercizio['TitoloEsercizio']); ?></h5>
                    <small class="text-muted">Punti: <?php echo $esercizio['PuntiOttenibili']; ?></small>
                </div>
                <p class="mb-1"><?php echo htmlspecialchars($esercizio['DescrizioneEsercizio']); ?></p>
                <small class="text-muted">
                    Tema: <?php echo htmlspecialchars($esercizio['NomeTema'] ?: 'N/D'); ?> | 
                    Difficoltà: <?php echo htmlspecialchars($esercizio['DifficoltaLinguistica']); ?>
                </small>
                
                <?php if ($esercizio['ImmagineEsercizioURL']): ?>
                    <div class="mt-2">
                         <img src="<?php echo htmlspecialchars($esercizio['ImmagineEsercizioURL']); ?>" alt="Immagine Esercizio" style="max-width: 200px; height: auto; border-radius: 8px;">
                    </div>
                <?php endif; ?>
                <?php if ($esercizio['VideoEsercizioURL']): ?>
                     <div class="mt-2">
                        <a href="<?php echo htmlspecialchars($esercizio['VideoEsercizioURL']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">Guarda Video</a>
                    </div>
                <?php endif; ?>

                <div class="mt-3">
                    <?php 
                        // TODO: Controlla se lo studente loggato ha già svolto questo esercizio
                        $ha_svolto = false; // Logica da implementare con SvolgimentoEsercizio
                        $punteggio_ottenuto = null; // Logica da implementare

                        if ($ha_svolto) {
                            echo "<span class='badge bg-success me-2'>Completato</span>";
                            if ($punteggio_ottenuto !== null) {
                                echo "<small>Punteggio ottenuto: " . htmlspecialchars($punteggio_ottenuto) . "</small>";
                            }
                        } else {
                            // Link per svolgere l'esercizio (devi creare questa pagina)
                            // Passa l'ID dell'istanza specifica dell'esercizio in questo corso (IDEsercizioCorso)
                            echo '<a href="lp_svolgi_esercizio.php?id_esercizio_corso=' . $esercizio['IDEsercizioCorso'] . '" class="btn btn-primary btn-sm">Svolgi Esercizio</a>';
                        }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info" role="alert">
        Nessun esercizio disponibile per questo corso al momento.
    </div>
<?php endif; ?>

<?php
require_once 'lp_footer.php'; // Include il footer
?>