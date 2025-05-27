<?php
// 1simulazione-esame2025/lp_crea_corso_action.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/LpCorsoManager.php';
require_once __DIR__ . '/lp_auth_check.php';
check_login('insegnante'); // Proteggi questa azione

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_corso = trim($_POST['nome_corso'] ?? '');
    $lingua = trim($_POST['lingua'] ?? '');
    $livello = trim($_POST['livello'] ?? '');
    $descrizione = trim($_POST['descrizione_corso'] ?? null);
    $id_insegnante = $_SESSION['insegnante_id']; // Dall'autenticazione

    $errors = [];
    if (empty($nome_corso)) $errors[] = "Nome corso obbligatorio.";
    if (empty($lingua)) $errors[] = "Lingua obbligatoria.";
    if (empty($livello)) $errors[] = "Livello obbligatorio.";
    // Aggiungi altre validazioni se necessario

    if (!empty($errors)) {
        $_SESSION['lp_feedback_error'] = implode("<br>", $errors);
        $_SESSION['lp_form_data_corso'] = $_POST;
        header('Location: lp_crea_corso_form.php');
        exit;
    }

    $corsoMgr = new LpCorsoManager();
    if ($corsoMgr->creaCorso($id_insegnante, $nome_corso, $lingua, $livello, $descrizione)) {
        $_SESSION['lp_feedback_success'] = "Corso '" . htmlspecialchars($nome_corso) . "' creato con successo!";
        unset($_SESSION['lp_form_data_corso']);
        header('Location: lp_dashboard_insegnante.php'); // O a una pagina di gestione corsi
        exit;
    } else {
        // L'errore dovrebbe essere già in sessione da LpCorsoManager
        if (!isset($_SESSION['lp_feedback_error'])) {
             $_SESSION['lp_feedback_error'] = "Impossibile creare il corso. Riprova.";
        }
        $_SESSION['lp_form_data_corso'] = $_POST;
        header('Location: lp_crea_corso_form.php');
        exit;
    }
} else {
    header('Location: lp_crea_corso_form.php');
    exit;
}
?>