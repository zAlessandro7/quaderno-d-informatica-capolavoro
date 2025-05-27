<?php
// 2simulazione-esame2025/logout.php
session_start();

// Rimuovi tutte le variabili di sessione specifiche dell'utente
unset($_SESSION['cliente_id']);
unset($_SESSION['cliente_nome']);
unset($_SESSION['cliente_cognome']);
// Non distruggere l'intera sessione se vuoi mantenere il carrello per utenti non loggati,
// altrimenti puoi usare session_destroy() e poi session_start() di nuovo.
// Per ora, manteniamo il carrello se presente.

// session_destroy(); // Se vuoi cancellare tutto, incluso il carrello

$_SESSION['feedback_message'] = "Logout effettuato con successo.";
$_SESSION['feedback_type'] = "ok_msg";
header('Location: login.php'); // O index.php se preferisci
exit;
?>