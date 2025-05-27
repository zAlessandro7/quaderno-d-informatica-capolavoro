<?php
// 2simulazione-esame2025/auth_check.php
// Helper per controllare l'autenticazione e l'autorizzazione per il progetto Food Express.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Controlla se l'utente è loggato nel contesto Food Express e opzionalmente di un tipo specifico.
 * Reindirizza alla pagina di login se non autenticato o non autorizzato.
 * @param string|null $requiredType Tipo di utente richiesto ('cliente'). Se null, controlla solo il login generico Food Express.
 * @param string $redirectPage Pagina a cui reindirizzare in caso di fallimento (default: login.php).
 * @return int|null L'ID dell'utente loggato se autenticato, altrimenti la funzione fa exit().
 */
function check_login($requiredType = null, $redirectPage = 'login.php') {
    $isLoggedIn = false;
    $userId = null;
    $userType = null; // Tipo utente dentro FoodExpress (es. 'cliente')

    // Verifica se l'utente è loggato come CLIENTE FoodExpress
    if (isset($_SESSION['cliente_id'])) { // <<< CHIAVE DI SESSIONE PER CLIENTE FOODEXPRESS (dovrebbe essere impostata da effettua_login.php)
        $isLoggedIn = true;
        $userId = $_SESSION['cliente_id'];
        $userType = 'cliente'; // Per questo progetto, il tipo autenticato qui è 'cliente'
    }
    // Se avessi altri ruoli loggabili SOLO in FoodExpress (es. admin_fe), li verifichi qui

    // --- DEBUG OPZIONALE ---
    /*
    error_log("DEBUG AUTH_CHECK: Checking login for " . $_SERVER['REQUEST_URI']);
    error_log("DEBUG AUTH_CHECK: Sessione: " . print_r($_SESSION, true));
    error_log("DEBUG AUTH_CHECK: LoggedIn = " . ($isLoggedIn ? 'true' : 'false') . ", UserID = " . ($userId ?? 'null') . ", UserType = " . ($userType ?? 'null') . ", RequiredType = " . ($requiredType ?? 'null'));
    */
    // --- FINE DEBUG OPZIONALE ---


    if (!$isLoggedIn) {
        error_log("Auth check fallito: utente non loggato. Tentativo di accesso a: " . $_SERVER['REQUEST_URI']);
        $_SESSION['feedback_message'] = "Devi effettuare il login per accedere a questa pagina.";
        $_SESSION['feedback_type'] = 'warning';
        // Salva l'URL corrente per reindirizzare dopo il login
        if (basename($_SERVER['PHP_SELF']) !== $redirectPage) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        header("Location: " . $redirectPage);
        exit; // Termina lo script
    }

    // Se è richiesto un tipo specifico (es. 'cliente') e l'utente loggato non corrisponde
    if ($requiredType !== null && $userType !== $requiredType) {
        error_log("Auth check fallito: tipo utente errato. Richiesto: $requiredType, Trovato: $userType. Tentativo di accesso a: " . $_SERVER['REQUEST_URI']);
         $_SESSION['feedback_message'] = "Accesso non autorizzato. Questa pagina è riservata a: " . ucfirst($requiredType) . ".";
         $_SESSION['feedback_type'] = 'danger';
        // Reindirizza alla homepage o a una dashboard predefinita
        header("Location: index.php"); // lp_index.php nel caso di LP, ma qui è index.php di FE
        exit;
    }

    // Se l'utente è loggato e del tipo richiesto (o nessun tipo richiesto), restituisce l'ID
    return $userId;
}
?>