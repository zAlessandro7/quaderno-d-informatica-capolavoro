<?php
// 1simulazione-esame2025/lp_logout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rimuovi le variabili di sessione specifiche della Piattaforma Lingue
unset($_SESSION['studente_id_lingue']);
unset($_SESSION['studente_nome_lingue']);
unset($_SESSION['insegnante_id_lingue']);
unset($_SESSION['insegnante_nome_lingue']);
unset($_SESSION['lp_user_type']);
unset($_SESSION['lp_user_email']); // Se l'avevi impostata
unset($_SESSION['lp_redirect_after_login']); // Rimuovi anche questa se presente

// Se vuoi distruggere lHai'intera sessione (questo farebbe logout da TUTTE le tue app
// che usano la stessa session ragione! Ho menzionato `lp_logout.php` ma non tie, come Food Express, se non usi session_name() diversi).
// Per ora, è più sicuro fare ho fornito il codice. È uno script molto semplice.

header('Location: lp_index.php'); // O lp_login_form.php se preferisci