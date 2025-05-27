<?php
// 2simulazione-esame2025/effettua_registrazione.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Includi ClienteManager.php (che a sua volta include DatabaseConnection.php e database_config.php)
// Assumendo che tutti questi file siano nella stessa directory (2simulazione-esame2025/)
require_once __DIR__ . '/ClienteManager.php';

// Controlla se la richiesta è POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $indirizzo = trim(filter_var($_POST['indirizzo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS));
    $telefono = trim(filter_var($_POST['telefono'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS));

    // Validazione server-side di base
    $errors = [];
    if (empty($nome)) $errors[] = "Il campo Nome è obbligatorio.";
    if (empty($cognome)) $errors[] = "Il campo Cognome è obbligatorio.";
    if (empty($email)) {
        $errors[] = "Il campo Email è obbligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Il formato dell'Email non è valido.";
    }
    if (empty($password)) {
        $errors[] = "Il campo Password è obbligatorio.";
    } elseif (strlen($password) < 6) {
        $errors[] = "La Password deve essere di almeno 6 caratteri.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Le password non coincidono.";
    }
    // Puoi aggiungere altre validazioni per telefono, indirizzo se necessario

    if (!empty($errors)) {
        // Se ci sono errori di validazione, salvali in sessione e reindirizza
        $_SESSION['registration_error'] = implode("<br>", $errors);
        // Salva anche i valori inseriti per ripopolare il form (opzionale, ma buona UX)
        $_SESSION['form_data'] = $_POST; 
        header('Location: registrazione.php');
        exit;
    }

    // Se la validazione base è OK, procedi con la logica di registrazione
    try {
        $clienteMgr = new ClienteManager(); // Crea un'istanza del manager

        // Il metodo registerUser ora gestisce il controllo dell'email esistente
        // e imposta $_SESSION['registration_error'] internamente se l'email esiste.
        if ($clienteMgr->registerUser($nome, $cognome, $email, $password, $indirizzo, $telefono)) {
            $_SESSION['registration_success'] = "Registrazione completata con successo! Ora puoi effettuare il login.";
            unset($_SESSION['form_data']); // Pulisci i dati del form dalla sessione
            header('Location: login.php'); // Reindirizza alla pagina di login
            exit;
        } else {
            // Se registerUser restituisce false, un messaggio di errore 
            // (es. "Email già registrata" o errore tecnico) 
            // dovrebbe essere già stato impostato in sessione da ClienteManager.
            // Salva i dati del form per ripopolare
            $_SESSION['form_data'] = $_POST;
        }
    } catch (Exception $e) { // Cattura eccezioni generiche (es. dalla connessione DB in DatabaseConnection)
        error_log("Eccezione in effettua_registrazione.php: " . $e->getMessage());
        $_SESSION['registration_error'] = "Si è verificato un errore tecnico imprevisto. Riprova più tardi.";
        $_SESSION['form_data'] = $_POST;
    }

    // Se si arriva qui, qualcosa è andato storto (es. email già esistente o errore DB)
    header('Location: registrazione.php');
    exit;

} else {
    // Se non è una richiesta POST, reindirizza alla pagina di registrazione
    $_SESSION['registration_error'] = "Metodo di richiesta non valido.";
    header('Location: registrazione.php');
    exit;
}
?>