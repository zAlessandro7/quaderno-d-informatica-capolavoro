<?php
// 1simulazione-esame2025/lp_registrazione_action.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/LpUtenteManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $tipoUtente = strtolower(trim($_POST['tipo_utente'] ?? ''));

    $errors = [];
    if (empty($nome)) $errors[] = "Nome obbligatorio.";
    if (empty($cognome)) $errors[] = "Cognome obbligatorio.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email non valida.";
    if (empty($password) || strlen($password) < 6) $errors[] = "Password min. 6 caratteri.";
    if ($password !== $password_confirm) $errors[] = "Le password non coincidono.";
    if ($tipoUtente !== 'studente' && $tipoUtente !== 'insegnante') $errors[] = "Tipo utente non valido.";

    if (!empty($errors)) {
        $_SESSION['lp_feedback_error'] = implode("<br>", $errors);
        $_SESSION['lp_form_data'] = $_POST;
        header('Location: lp_registrazione_form.php');
        exit;
    }

    $utenteMgr = new LpUtenteManager();
    if ($utenteMgr->registerUtente($nome, $cognome, $email, $password, $tipoUtente)) {
        $_SESSION['lp_feedback_success'] = "Registrazione come " . ucfirst($tipoUtente) . " completata con successo! Ora puoi effettuare il login.";
        unset($_SESSION['lp_form_data']);
        header('Location: lp_login_form.php');
        exit;
    } else {
        // L'errore (es. email esistente) dovrebbe essere già in $_SESSION['lp_feedback_error'] da LpUtenteManager
        if (!isset($_SESSION['lp_feedback_error'])) { // Fallback
             $_SESSION['lp_feedback_error'] = "Errore sconosciuto durante la registrazione.";
        }
        $_SESSION['lp_form_data'] = $_POST;
        header('Location: lp_registrazione_form.php');
        exit;
    }
} else {
    header('Location: lp_registrazione_form.php');
    exit;
}
?>