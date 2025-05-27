<?php
// 1simulazione-esame2025/lp_corsi.php
$page_title = "Elenco Corsi Disponibili";
require_once 'lp_header.php';
require_once 'LpCorsoManager.php';

$corsoMgr = new LpCorsoManager();
$corsi = $corsoMgr->getAllCorsiDisponibili();
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Corsi Virtuali Disponibili</h1>
        <?php if (isset($_SESSION['lp_user_type']) && $_SESSION['lp_user_type'] == 'insegnante'): ?>
            <a href="lp_crea_corso_form.php" class="btn btn-warning">Crea un Nuovo Corso</a>
        <?php endif; ?>
    </div>

    <?php if (empty($corsi)): ?>
        <div class="alert alert-info" role="alert">
            Al momento non ci sono corsi disponibili. Torna a trovarci presto!
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($corsi as $corso): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <!-- Potresti aggiungere un'immagine per il corso qui se la prevedi nel DB -->
                        <!-- <img src="..." class="card-img-top" alt="Immagine corso"> -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($corso['NomeCorso']); ?></h5>
                            <p class="card-text mb-1"><small class="text-muted">Docente: <?php echo htmlspecialchars($corso['NomeInsegnante'] . ' ' . $corso['CognomeInsegnante']); ?></small></p>
                            <p class="card-text">
                                <strong>Lingua:</strong> <?php echo htmlspecialchars($corso['Lingua']); ?><br>
                                <strong>Livello:</strong> <?php echo htmlspecialchars($corso['LivelloDifficolta']); ?>
                            </p>
                            <p class="card-text небольшие"><?php echo htmlspecialchars(substr($corso['DescrizioneCorso'] ?? 'Nessuna descrizione disponibile.', 0, 100)) . (strlen($corso['DescrizioneCorso'] ?? '') > 100 ? '...' : ''); ?></p>
                            <div class="mt-auto"> <!-- Allinea i pulsanti in basso -->
                                <?php if (isset($_SESSION['lp_user_type']) && $_SESSION['lp_user_type'] == 'studente'): ?>
                                    <!-- Qui dovresti controllare se lo studente è già iscritto -->
                                    <a href="lp_iscrizione_action.php?id_corso=<?php echo $corso['IDCorso']; ?>" class="btn btn-success btn-sm me-2">Iscriviti</a>
                                <?php endif; ?>
                                <a href="lp_visualizza_corso.php?id_corso=<?php echo $corso['IDCorso']; ?>" class="btn btn-primary btn-sm">Dettagli Corso</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'lp_footer.php';
?>