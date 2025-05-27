<?php
// 2simulazione-esame2025/effettua_login.php
session_start();
require_once __DIR__ . '/ClienteManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) { /* ... */ header('Location: login.php'); exit; }

    try {
        $clienteMgr = new ClienteManager();
        $cliente = $clienteMgr->loginUser($email, $password);

        if ($cliente) {
            $_SESSION['cliente_id'] = $cliente['IDCliente'];
            $_SESSION['cliente_nome'] = $cliente['Nome'];
            // ... (imposta altre info sessione se necessario)
            $_SESSION['feedback_message'] = "Login effettuato. Bentornato, " . htmlspecialchars($cliente['Nome']) . "!";
            $_SESSION['feedback_type'] = "ok_msg";
            header('Location: index.php'); // Reindirizza a index.php nella stessa cartella
            exit;
        } else {
            $_SESSION['login_error'] = "Email o password non validi.";
        }
    } catch (Exception $e) {
        error_log("Errore login (action): " . $e->getMessage());
        $_SESSION['login_error'] = "Errore tecnico durante il login.";
    }
    header('Location: login.php');
    exit;
} else {
    header('Location: login.php');
    exit;
}
?>