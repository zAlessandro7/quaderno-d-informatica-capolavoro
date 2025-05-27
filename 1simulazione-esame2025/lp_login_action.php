<?php
// 1simulazione-esame2025/lp_login_action.php
// Questo script gestisce la logica di elaborazione del form di login per la Piattaforma Lingue.

// RIGHE DI DEBUG ALL'INIZIO ASSOLUTO (Mantieni attive durante il debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo "DEBUG: Script lp_login_action.php raggiunto!<br>"; 

// È FONDAMENTALE avviare la sessione per poterla usare e modificare
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // <<< Funzione corretta
}
echo "DEBUG: Sessione avviata/ripresa (ID: " . session_id() . ").<br>"; 

// Includi la classe per la gestione degli utenti (che include anche la connessione al DB)
require_once __DIR__ . '/LpUtenteManager.php';
echo "DEBUG: LpUtenteManager.php incluso.<br>";

// Reindirizza se l'utente è GIA' loggato (controllo basato sulle chiavi di sessione impostate da LpUtenteManager)
// Questo controllo dovrebbe essere fatto PRIMA di processare i dati POST
if ((isset($_SESSION['studente_id_lingue']) && ($_SESSION['lp_user_type'] ?? null) === 'studente') ||
    (isset($_SESSION['insegnante_id_lingue']) && ($_SESSION['lp_user_type'] ?? null) === 'insegnante')) {
    
    echo "DEBUG: Utente già loggato. Reindirizzo alla dashboard appropriata...<br>"; // DEBUG
    $loggedInUserType = $_SESSION['lp_user_type'] ?? null; // Recupera il tipo loggato

    // Determina la dashboard a cui reindirizzare
    $dashboardPage = 'lp_index.php'; // Fallback
    if ($loggedInUserType === 'studente') {
        $dashboardPage = 'lp_dashboard_studente.php';
    } elseif ($loggedInUserType === 'insegnante') {
        $dashboardPage = 'lp_dashboard_insegnante.php';
    }

    // Non impostare messaggi di successo qui per login già avvenuto, lp_header li gestisce.
    // Potresti impostare un messaggio se l'utente viene reindirizzato qui con una richiesta POST
    // ma è già loggato, ma per semplicità non lo facciamo.

    header('Location: ' . $dashboardPage);
    exit; // Termina lo script dopo il reindirizzamento
}

echo "<pre>DEBUG: _SERVER['REQUEST_METHOD'] = " . $_SERVER['REQUEST_METHOD'] . "</pre>"; // DEBUG

// Processa i dati del form solo se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "DEBUG: Richiesta POST ricevuta.<br>"; 
    echo "<pre>DEBUG: _POST data: "; print_r($_POST); echo "</pre>"; // DEBUG: Mostra i dati ricevuti

    // Recupera e filtra i dati dal form POST
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'] ?? ''; // La password non va filtrata con FILTER_SANITIZE_SPECIAL_CHARS, solo trimmata o usata diretta
    $tipoUtenteLogin = strtolower(trim($_POST['tipo_utente_login'] ?? '')); // Normalizza a minuscolo e trimma

    echo "<pre>DEBUG: Dati recuperati/filtrati: Email='$email', TipoLogin='$tipoUtenteLogin'</pre>"; // DEBUG (non mostrare la password)

    // Esegui validazione server-side di base
    $errors = [];
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'indirizzo email non è valido o è mancante.";
    }
    if (empty($password)) {
        $errors[] = "La password non può essere vuota.";
    }
    if ($tipoUtenteLogin !== 'studente' && $tipoUtenteLogin !== 'insegnante') {
         // Questo non dovrebbe succedere con i radio button del form, ma è una sicurezza
        $errors[] = "Tipo utente selezionato non valido.";
    }

    // Gestisci errori di validazione
    if (!empty($errors)) {
        echo "DEBUG: Errori di validazione trovati.<br>"; // DEBUG
        $_SESSION['lp_feedback_error'] = implode("<br>", $errors); // Unisci errori in un messaggio
        $_SESSION['lp_form_data_login'] = ['email' => $email, 'tipo_utente_login' => $tipoUtenteLogin]; // Salva per ripopolare
        
        // Reindirizza alla pagina di login form
        header('Location: lp_login_form.php'); 
        exit; // Termina
    }

    echo "DEBUG: Validazione base superata. Tento login con LpUtenteManager...<br>"; 
    try {
        $utenteMgr = new LpUtenteManager(); // Crea istanza del manager

        // Chiama il metodo loginUtente nel manager. Questo metodo gestisce la verifica password
        // e IMPOSTA LE VARIABILI DI SESSIONE se il login ha successo.
        $loginSuccess = $utenteMgr->loginUtente($email, $password, $tipoUtenteLogin);

        // Verifica il risultato del metodo loginUtente
        if ($loginSuccess) { // loginUtente restituisce true se successo
            echo "DEBUG: LpUtenteManager::loginUtente() SUCCESSO! Sessione impostata.<br>"; // DEBUG
            
            // Il manager ha già impostato:
            // $_SESSION['studente_id_lingue'] o $_SESSION['insegnante_id_lingue']
            // $_SESSION['studente_nome_lingue'] o $_SESSION['insegnante_nome_lingue']
            // $_SESSION['lp_user_type'] ('studente' o 'insegnante')
            // $_SESSION['lp_user_email']

            // Imposta un messaggio di feedback globale per la pagina di destinazione
            $_SESSION['lp_feedback_success'] = "Login effettuato con successo! Benvenuto/a.";
            
            // Pulisci i dati del form dalla sessione
            unset($_SESSION['lp_form_data_login']);

            // Determina la pagina di destinazione dopo il login
            // Prova a reindirizzare all'URL salvato prima del login (se presente)
            $redirect_url = $_SESSION['lp_redirect_after_login'] ?? null;
            
            // Se non c'è un URL di redirect salvato, vai alla dashboard appropriata
            if ($redirect_url === null) {
                $loggedInUserType = $_SESSION['lp_user_type'] ?? null; // Recupera il tipo loggato dalla sessione
                if ($loggedInUserType === 'studente') {
                    $redirect_url = 'lp_dashboard_studente.php';
                } elseif ($loggedInUserType === 'insegnante') {
                    $redirect_url = 'lp_dashboard_insegnante.php';
                } else {
                    // Fallback se il tipo utente non è impostato correttamente in sessione (non dovrebbe succedere)
                    error_log("lp_login_action.php: Tipo utente loggato non riconosciuto in sessione: " . $loggedInUserType);
                    $redirect_url = 'lp_index.php'; 
                    $_SESSION['lp_feedback_error'] = "Login effettuato, ma tipo utente non riconosciuto. Contatta l'assistenza.";
                    unset($_SESSION['lp_feedback_success']); // Rimuovi messaggio di successo se c'è un problema
                }
            }
            
            // Pulisci l'URL di redirect salvato dalla sessione una volta usato
            unset($_SESSION['lp_redirect_after_login']);

            echo "DEBUG: Reindirizzamento a: " . htmlspecialchars($redirect_url) . "<br>"; 
            echo "<pre>DEBUG: Sessione FINALE prima del redirect: "; print_r($_SESSION); echo "</pre>";
            
            // Esegui il reindirizzamento HTTP
            header('Location: ' . $redirect_url); 
            exit; // TERMINA SEMPRE DOPO UN HEADER LOCATION

        } else { // loginUtente ha restituito false
            echo "DEBUG: LpUtenteManager::loginUtente() FALLITO (credenziali non valide o tipo errato).<br>"; // DEBUG
            // Il manager non imposta messaggi di errore in sessione in caso di fallimento login
            // quindi impostiamo il messaggio qui.
            $_SESSION['lp_feedback_error'] = "Credenziali non valide o tipo utente errato per l'account specificato.";
            $_SESSION['lp_form_data_login'] = $_POST; // Salva i dati (incluso il tipo utente) per ripopolare il form
            
            // Reindirizza alla pagina di login form
            header('Location: lp_login_form.php');
            exit; // TERMINA SEMPRE DOPO UN HEADER LOCATION
        }

    } catch (Exception $e) {
        // Cattura eccezioni generiche (es. da PDO in LpDatabaseConnection)
        error_log("Eccezione in lp_login_action.php: " . $e->getMessage());
        echo "DEBUG: Eccezione catturata: " . $e->getMessage() . "<br>"; 
        $_SESSION['lp_feedback_error'] = "Si è verificato un errore tecnico durante il login. Riprova più tardi.";
        $_SESSION['lp_form_data_login'] = $_POST; 
        
        // Reindirizza alla pagina di login form in caso di eccezione
        header('Location: lp_login_form.php');
        exit; // TERMINA SEMPRE DOPO UN HEADER LOCATION
    }

} else { // Se la richiesta non è POST (es. accesso diretto via URL)
    echo "DEBUG: Metodo non POST. Reindirizzamento a lp_index.php.<br>"; 
    // Non impostare un errore, semplicemente reindirizza alla homepage o alla pagina di login
    header('Location: lp_index.php'); // O lp_login_form.php se preferisci
    exit; // TERMINA SEMPRE DOPO UN HEADER LOCATION
}
?>