<?php
// 1simulazione-esame2025/lp_auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function check_login($requiredType = null, $redirectPage = 'lp_login_form.php') {
    $isLoggedIn = false;
    $userType = $_SESSION['lp_user_type'] ?? null; // Chiave sessione specifica per questa app
    $userId = null;

    if ($userType === 'studente' && isset($_SESSION['studente_id'])) {
        $isLoggedIn = true;
        $userId = $_SESSION['studente_id'];
    } elseif ($userType === 'insegnante' && isset($_SESSION['insegnante_id'])) {
        $isLoggedIn = true;
        $userId = $_SESSION['insegnante_id'];
    }

    if (!$isLoggedIn) {
        $_SESSION['lp_feedback_error'] = "Devi effettuare il login per accedere a questa pagina.";
        $_SESSION['redirect_after_login_lp'] = $_SERVER['REQUEST_URI']; // Salva la pagina corrente per reindirizzare dopo il login
        header("Location: " . $redirectPage);
        exit;
    }

    if ($requiredType !== null && $userType !== $requiredType) {
        $_SESSION['lp_feedback_error'] = "Accesso non autorizzato. Questa pagina è riservata a: " . ucfirst($requiredType) . ".";
        $dashboardPage = ($userType === 'studente') ? 'lp_dashboard_studente.php' : 'lp_dashboard_insegnante.php';
        header("Location: " . $dashboardPage);
        exit;
    }
    return $userId; // Restituisce l'ID dell'utente loggato
}
?>