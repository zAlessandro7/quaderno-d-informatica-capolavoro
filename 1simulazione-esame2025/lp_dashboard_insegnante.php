<?php
// 1simulazione-esame2025/lp_dashboard_insegnante.php
$page_title = "Dashboard Insegnante";
require_once 'lp_header.php';      // Include session_start, config, etc.
require_once 'lp_auth_check.php';  // Include la funzione check_login
require_once 'LpCorsoManager.php'; // Per recuperare i corsi dell'insegnante

check_login('insegnante'); // Assicura che solo un insegnante loggato possa accedere

$id_insegnante_loggato = $_SESSION['insegnante_id'] ?? null;
$nome_insegnante_loggato = $_SESSION['insegnante_nome'] ?? 'Docente';

$corsoMgr = new LpCorsoManager();
$miei_corsi = [];
if ($id_insegnante_loggato) {
    // Dovrai creare un metodo in LpCorsoManager per prendere i corsi di un insegnante
    // Per ora, per semplicità, recuperiamo tutti i corsi e poi potremmo filtrare
    // o meglio, modifichiamo LpCorsoManager
    $miei_corsi = $corsoMgr->getCorsiByInsegnanteId($id_insegnante_loggato);
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Dashboard Insegnante</h1>
        <a href="lp_crea_corso_form.php" class="btn btn-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill me-2" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
            </svg>
            Crea Nuovo Corso
        </a>
    </div>

    <p class="lead">Benvenuto/a, <?php echo htmlspecialchars($nome_insegnante_loggato); ?>!</p>
    <p>Da questa dashboard puoi gestire i tuoi corsi, monitorare i progressi degli studenti e creare nuovo materiale didattico.</p>

    <hr class="my-4">

    <h3>I Tuoi Corsi Creati</h3>
    <?php if (!empty($miei_corsi)): ?>
        <div class="list-group">
            <?php foreach ($miei_corsi as $corso): ?>
                <div class="list-group-item list-group-item-action flex-column align-items-start mb-2 shadow-sm">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?php echo htmlspecialchars($corso['NomeCorso']); ?></h5>
                        <small class="text-muted">Codice Iscrizione: <?php echo htmlspecialchars($corso['CodiceIscrizione']); ?></small>
                    </div>
                    <p class="mb-1">
                        <strong>Lingua:</strong> <?php echo htmlspecialchars($corso['Lingua']); ?> | 
                        <strong>Livello:</strong> <?php echo htmlspecialchars($corso['LivelloDifficolta']); ?>
                    </p>
                    <small class="text-muted">Creato il: <?php echo date("d/m/Y", strtotime($corso['DataCreazione'])); ?></small>
                    <div class="mt-2">
                        <a href="lp_gestisci_corso.php?id_corso=<?php echo $corso['IDCorso']; ?>" class="btn btn-primary btn-sm">Gestisci Corso</a>
                        <a href="lp_classifica_corso.php?id_corso=<?php echo $corso['IDCorso']; ?>" class="btn btn-info btn-sm">Vedi Classifica</a>
                        <!-- Aggiungi altri link azioni se necessario, es. Modifica, Elimina (conferma!) -->
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            Non hai ancora creato nessun corso. <a href="lp_crea_corso_form.php" class="alert-link">Inizia ora creandone uno!</a>
        </div>
    <?php endif; ?>

    <hr class="my-4">

    <!-- Altre sezioni per la dashboard dell'insegnante -->
    <!-- <h4>Statistiche Rapide</h4>
    <p>Numero totale studenti iscritti ai tuoi corsi: X</p>
    <p>Esercizi più popolari: Y</p> -->

</div>

<?php
require_once 'lp_footer.php';
?>