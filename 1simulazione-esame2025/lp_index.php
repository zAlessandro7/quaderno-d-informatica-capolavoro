<?php
// 1simulazione-esame2025/lp_index.php
// Questo file è la homepage per la Piattaforma Lingue.

ini_set('display_errors', 1); // Forza la visualizzazione degli errori
ini_set('display_startup_errors', 1); // Forza la visualizzazione degli errori di avvio
error_reporting(E_ALL); // Mostra tutti i tipi di errore

$page_title = "Benvenuto su LinguePlatform";

// lp_header.php avvia la sessione (se non già fatto) 
// e definisce $lp_user_type e $lp_user_name leggendo dalla sessione in modo sicuro.
require_once 'lp_header.php'; 

// Determina lo stato del login in modo sicuro, utilizzando le variabili già definite in lp_header.php
// $lp_user_type è già disponibile qui perché è definito in lp_header.php
$is_logged_in_lp = false; // Rinomina per evitare conflitto con lp_header se lì è globale
if (isset($lp_user_type) && $lp_user_type !== null) { // Controlla se $lp_user_type è stato effettivamente impostato in header
    if ($lp_user_type === 'studente' && isset($_SESSION['studente_id_lingue'])) {
        $is_logged_in_lp = true;
    } elseif ($lp_user_type === 'insegnante' && isset($_SESSION['insegnante_id_lingue'])) {
        $is_logged_in_lp = true;
    }
}
// A questo punto, $lp_user_type contiene il tipo se loggato, o null se non loggato (da lp_header.php)
// e $is_logged_in_lp è true o false.
?>
<div class="welcome-section px-4 py-5 my-1 text-center">
    <img class="d-block mx-auto mb-4" src="https://www.amaplast.org/archivioFiles/News/lingue.png" alt="Logo Piattaforma Lingue" width="120" style="opacity:0.9;">
    <h1 class="display-4 fw-bold text-body-emphasis">Impara una Nuova Lingua con Noi!</h1>
    <div class="col-lg-7 mx-auto">
        <p class="lead mb-4" style="font-size: 1.15rem;">
            La nostra piattaforma offre corsi interattivi creati da insegnanti qualificati, 
            esercizi stimolanti per ogni livello e un ambiente di apprendimento coinvolgente. 
            Inizia oggi il tuo viaggio nel mondo delle lingue!
        </p>
        
        <?php if (!$is_logged_in_lp): ?>
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="lp_registrazione_form.php" class="btn btn-primary btn-lg px-4 gap-3">Registrati Ora</a>
                <a href="lp_login_form.php" class="btn btn-outline-secondary btn-lg px-4">Accedi</a>
            </div>
        <?php else: // L'utente è loggato ?>
            <p class="lead mb-3">Bentornato/a, <strong><?php echo htmlspecialchars($_SESSION[$lp_user_type . '_nome_lingue'] ?? 'Utente'); ?></strong>!</p>
            <?php if ($lp_user_type === 'studente'): ?>
                 <p><a href="lp_dashboard_studente.php" class="btn btn-success btn-lg px-4">Vai alla tua Dashboard Studente</a></p>
                 <p><a href="lp_corsi.php" class="btn btn-info btn-lg px-4 mt-2">Esplora i Corsi</a></p>
            <?php elseif ($lp_user_type === 'insegnante'): ?>
                 <p><a href="lp_dashboard_insegnante.php" class="btn btn-success btn-lg px-4">Vai alla tua Dashboard Insegnante</a></p>
                 <p><a href="lp_crea_corso_form.php" class="btn btn-warning btn-lg px-4 mt-2">Crea un Nuovo Corso</a></p>
            <?php else: ?>
                <p class="text-danger">Si è verificato un errore con il tuo stato di login. Prova a <a href="lp_logout.php">effettuare nuovamente il logout e login</a>.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 text-center feature-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="var(--lp-primary-color)" class="bi bi-translate mb-3" viewBox="0 0 16 16">
                <path d="M4.545 6.714 4.11 8H3l1.862-5h1.284L8 8H6.833l-.435-1.286H4.545zm1.634-.736L5.5 4.426h-.04L4.781 5.978h1.398zm7.245 1.991a15.373 15.373 0 0 0-2.032.445.02.02 0 0 1-.02-.012.019.019 0 0 1 .01-.021 12.963 12.963 0 0 0 .456-2.146c.04-.16.054-.33.057-.5.003-.17.003-.339.002-.503a.02.02 0 0 1 .019-.021.02.02 0 0 1 .02.021c-.002.142-.002.297-.005.439-.014.59-.121 1.162-.298 1.699-.133.406-.189.425-.266.597a.02.02 0 0 1-.018.009.02.02 0 0 1-.019-.021z"/>
                <path d="M0 2a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v3h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-3H2a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H2zm7.138 9.995c.193.166.45.418.697.671.685.757.994 1.396.994 2.088a1.09 1.09 0 0 1-.331.787 1.166 1.166 0 0 1-.787.331c-.565 0-1.15-.33-1.562-.914a.02.02 0 0 1 .002-.018.02.02 0 0 1 .019.005.02.02 0 0 1 .018.018c.175.28.431.515.764.652.072.03.124.038.19.043.077.007.16.012.25.012.266 0 .501-.06.69-.172.181-.103.302-.24.352-.41a.88.88 0 0 0-.086-.945c-.104-.126-.26-.307-.462-.55a15.373 15.373 0 0 0-.843-.924 c-.064-.072-.098-.082-.153-.093a.02.02 0 0 1-.018-.009.02.02 0 0 1 .005-.019.02.02 0 0 1 .018-.004z"/>
            </svg>
            <h4>Corsi Interattivi</h4>
            <p>Accedi a una vasta gamma di corsi per tutti i livelli, dall'A1 al C2.</p>
        </div>
        <div class="col-md-4 text-center feature-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="var(--lp-secondary-color)" class="bi bi-person-check-fill mb-3" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
            </svg>
            <h4>Docenti Esperti</h4>
            <p>Impara da insegnanti qualificati pronti a guidarti nel tuo percorso.</p>
        </div>
        <div class="col-md-4 text-center feature-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="var(--lp-primary-color)" class="bi bi-joystick mb-3" viewBox="0 0 16 16">
                <path d="M10 2a2 2 0 0 1-1.5 1.937v5.087c.863.083 1.5.377 1.5.726 0 .414-.895.75-2 .75s-2-.336-2-.75c0-.35.637-.643 1.5-.726V3.937A2 2 0 0 1 6 2h4zM6 0a2 2 0 0 0-2 2v1.528c-1.473.307-2.527 1.255-2.527 2.472 0 1.36.936 2.556 2.786 2.848V13a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V9.848c1.85-.292 2.786-1.488 2.786-2.848 0-1.217-1.054-2.165-2.527-2.472V2a2 2 0 0 0-2-2H6zm0 2.433V3.77c.464.06.804.163.994.258.19.094.283.217.283.363 0 .153-.153.414-.431.622-.278.208-.687.352-1.25.445V6.36c.374.073.69.188.908.323.217.135.326.31.326.511 0 .223-.24.553-.66.768-.42.215-.983.347-1.786.429V10.5h1v-2.63l-.002-.025c.001-.022.002-.046.002-.07v-.55c0-.29-.04-.522-.108-.7-.067-.177-.177-.312-.326-.39-.15-.078-.38-.132-.678-.15V5.318a3.91 3.91 0 0 0 .678-.15c.149-.078.259-.213.326-.39.068-.178.108-.41.108-.7v-.55c0-.024.001-.048.002-.07L7.5 2.63V2.433H6z"/>
            </svg>
            <h4>Esercizi Interattivi</h4>
            <p>Metti alla prova le tue conoscenze con attività divertenti e formative.</p>
        </div>
    </div>
</div>

<style>
    .welcome-section { text-align: center; padding: 40px 20px; }
    .welcome-section img { }
    .welcome-section h1.display-4 { color: var(--lp-primary-color, #4A90E2); margin-bottom: 20px; }
    .welcome-section .lead { font-size: 1.15rem; color: #555; margin-bottom: 30px; max-width: 700px; margin-left:auto; margin-right:auto;}
    .cta-buttons .btn, .welcome-section .btn { margin: 0 8px; padding: 10px 22px; font-weight: 600; text-decoration:none; border-radius:25px; }
    .btn-primary { background-color: var(--lp-primary-color, #4A90E2); border-color: var(--lp-primary-color, #4A90E2); color:white;}
    .btn-primary:hover { background-color: #3a80c1; border-color: #3a80c1; }
    .btn-outline-secondary { border-color: var(--lp-secondary-color, #F5A623); color: var(--lp-secondary-color, #F5A623); }
    .btn-outline-secondary:hover { background-color: var(--lp-secondary-color, #F5A623); color:white; }
    .btn-success { background-color: var(--lp-success-color, #28a745); border-color: var(--lp-success-color, #28a745); }
    .btn-success:hover { background-color: #218838; border-color: #1e7e34; }
    .feature-item { margin-bottom: 30px; }
    .feature-item svg { margin-bottom: 1rem !important; }
    .feature-item h4 { color: var(--lp-text-color); margin-top: 15px; margin-bottom: 10px; font-weight: 600; }
    .feature-item p { font-size: 0.95em; color: #6c757d; padding: 0 10px; }
</style>

<?php
// Pulisci il flag di debug se era stato impostato
if (isset($_SESSION['feedback_message_processed_for_this_page'])) { 
    unset($_SESSION['feedback_message_processed_for_this_page']);
}
require_once 'lp_footer.php';
?>