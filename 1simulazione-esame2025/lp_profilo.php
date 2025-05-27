<?php
// 1simulazione-esame2025/lp_profilo.php

// Riga ~1: ATTIVA QUESTO PER DEBUG DETTAGLIATO ALL'INIZIO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Riga ~6:
$page_title = "Il Mio Profilo";
require_once 'lp_header.php';
require_once 'lp_auth_check.php';
require_once 'LpUtenteManager.php';

// Riga ~12:
// DEBUG: Vediamo la sessione PRIMA di chiamare check_login
// echo "DEBUG PROFILO - Sessione INIZIALE:<pre>"; print_r($_SESSION); echo "</pre>";

// Riga ~15:
$current_user_id = check_login();
$current_user_type = $_SESSION['lp_user_type'] ?? null;

// Riga ~18:
// DEBUG: Vediamo cosa abbiamo ottenuto dalla sessione e da check_login
/*
echo "<div style='background: #f0f0f0; border: 1px solid #ccc; padding: 10px; margin-bottom:15px;'>";
echo "<strong>DEBUG INFO da lp_profilo.php (dopo check_login):</strong><br>";
echo "User ID recuperato: "; var_dump($current_user_id); echo "<br>";
echo "User Type recuperato da sessione: "; var_dump($current_user_type); echo "<br>";
echo "Intera SESSIONE:<pre>"; print_r($_SESSION); echo "</pre>";
echo "</div>";
*/

// Riga ~30:
$profilo = null; 
if ($current_user_id && $current_user_type) { // Apre graffa 1
    $utenteMgr = new LpUtenteManager();
    $profilo = $utenteMgr->getProfiloUtente($current_user_id, $current_user_type);
    // DEBUG: Risultato da getProfiloUtente
    // echo "<pre>DEBUG PROFILO - Risultato getProfiloUtente: "; var_dump($profilo); echo "</pre>";
} else { // Apre graffa 2 (corrispondente a else)
    error_log("lp_profilo.php: User ID o User Type mancanti dalla sessione dopo check_login.");
    // echo "<p class='error_msg'>DEBUG: User ID o User Type mancanti dopo check_login.</p>";
} // Chiude graffa 2 (corrispondente a else) - QUESTA POTREBBE ESSERE LA RIGA 32 O VICINA

// Riga ~42:
if (!$profilo) { // Apre graffa 3
    $_SESSION['lp_feedback_error'] = "Impossibile caricare i dati del profilo. (UserID: " . htmlspecialchars($current_user_id ?? 'N/D') . ", UserType: " . htmlspecialchars($current_user_type ?? 'N/D') . ")";
    
    // TEMPORANEAMENTE COMMENTA IL REDIRECT PER VEDERE I MESSAGGI DI DEBUG
    // $dashboardPage = ($current_user_type === 'studente') ? 'lp_dashboard_studente.php' : 'lp_dashboard_insegnante.php';
    // if (empty($dashboardPage) || $dashboardPage === '.php') $dashboardPage = 'lp_index.php'; // Fallback
    // header("Location: " . $dashboardPage);
    // exit;
    echo "<div class='message_container error_msg'><p><strong>FALLIMENTO CARICAMENTO PROFILO.</strong> Controllare i messaggi di debug e i log degli errori.</p><p>" . ($_SESSION['lp_feedback_error'] ?? '') . "</p></div>";
    // Non mostrare il resto della pagina se il profilo non può essere caricato
    require_once 'lp_footer.php';
    exit;
} // Chiude graffa 3

// Riga ~57:
// Gestione invio form per modifica
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profilo'])) { // Apre graffa 4
    // ... (logica di update profilo come nella tua risposta precedente) ...
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    $update_errors = [];
    if (empty($nome)) $update_errors[] = "Il nome è obbligatorio.";
    // ... altre validazioni ...

    if (empty($update_errors)) { // Apre graffa 5
        // ... logica di update ...
        if (!isset($utenteMgr)) $utenteMgr = new LpUtenteManager(); 
        if ($utenteMgr->updateProfiloUtente($current_user_id, $current_user_type, $nome, $cognome, $email, $new_password_da_passare)) {
            // ...
        } else {
            // ...
        }
    } else { // Apre graffa 6 (corrispondente a else per empty($update_errors))
        $_SESSION['lp_feedback_error'] = implode("<br>", $update_errors);
    } // Chiude graffa 6
} // Chiude graffa 4

?>

<!-- Riga ~90: Inizio HTML per visualizzare profilo e form -->
<div class="row justify-content-center">
    <!-- ... resto dell'HTML ... -->
</div>

<?php
require_once 'lp_footer.php';
?>