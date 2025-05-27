<?php
// 2simulazione-esame2025/login_action.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/ClienteManager.php'; // Includi la classe ClienteManager

// Reindirizza se l'utente è già loggato
if (isset($_SESSION['cliente_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'] ?? ''; // Non trimmare la password

    $errors = [];
    if (empty($email)) {
        $errors[] = "Il campo Email è obbligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Il formato dell'Email non è valido.";
    }
    if (empty($password)) {
        $errors[] = "Il campo Password è obbligatorio.";
    }

    if (!empty($errors)) {
        $_SESSION['login_error'] = implode("<br>", $errors);
        $_SESSION['form_data_login'] = ['email' => $email]; // Salva l'email per ripopolamento
        header('Location: login.php');
        exit;
    }

    try {
        $clienteMgr = new ClienteManager();
        $cliente_data = $clienteMgr->loginUser($email, $password);

        if ($cliente_data) { // loginUser restituisce i dati dell'utente o false
            // Login successo
            $_SESSION['cliente_id'] = $cliente_data['IDCliente'];
            $_SESSION['cliente_nome'] = $cliente_data['Nome'];
            $_SESSION['cliente_cognome'] = $cliente_data['Cognome'];
            // Potresti voler salvare anche l'email in sessione se ti serve
            // $_SESSION['cliente_email'] = $email;

            unset($_SESSION['form_data_login']); // Pulisci i dati del form dalla sessione

            // Messaggio di benvenuto globale
            $_SESSION['feedback_message'] = "Login effettuato con successo. Bentornato, " . htmlspecialchars($cliente_data['Nome']) . "!";
            $_SESSION['feedback_type'] = "ok_msg";
            
            // Reindirizza alla pagina precedente o alla dashboard/index
            $redirect_url = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect_url);
            exit;
        } else {
            $_SESSION['login_error'] = "Credenziali non valide. Controlla email e password.";
            $_SESSION['form_data_login'] = ['email' => $email];
        }
    } catch (Exception $e) {
        error_log("Eccezione in login_action.php: " . $e->getMessage());
        $_SESSION['login_error'] = "Si è verificato un errore tecnico durante il login. Riprova più tardi.";
        $_SESSION['form_data_login'] = ['email' => $email];
    }

    header('Location: login.php'); // Se il login fallisce, torna alla pagina di login
    exit;

} else {
    // Se non è una richiesta POST, reindirizza alla pagina di login
    $_SESSION['login_error'] = "Metodo di richiesta non valido.";
    header('Location: login.php');
    exit;
}
?>