<form action="registrazione.php" method="POST">
    <label for="username">Nome Utente:</label><br>
    <input type="text" id="username" name="username" required>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required>

    <label for="confirm_password">Conferma Password:</label><br>
    <input type="password" id="confirm_password" name="confirm_password" required>

    <button type="submit" name="registrati">Registrati</button>
</form>



<?php
// Percorso del file di testo dove memorizzare le credenziali
$file_path = 'utenti.txt';  // Il file 'utenti.txt' deve essere nella stessa cartella del tuo script PHP

// Verifica che il modulo sia stato inviato
if (isset($_POST['registrati'])) {
    // Recupera i dati dal modulo
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verifica che le password corrispondano
    if ($password !== $confirm_password) {
        echo "Le password non corrispondono!";
    } else {
        // Crea l'hash della password per sicurezza
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Verifica se il file esiste
        if (file_exists($file_path)) {
            // Apri il file in modalità append per aggiungere nuove credenziali
            $file = fopen($file_path, 'a');
            if ($file) {
                // Scrivi i dati nel file: username e password hashata
                fwrite($file, "$username:$hashed_password\n");
                fclose($file);  // Chiudi il file
                echo "Registrazione completata con successo!";
            } else {
                echo "Errore nell'aprire il file.";
            }
        } else {
            // Se il file non esiste, crea il file e scrivi le credenziali
            $file = fopen($file_path, 'w');
            if ($file) {
                fwrite($file, "$username:$hashed_password\n");
                fclose($file);
                echo "Registrazione completata con successo!";
            } else {
                echo "Errore nell'aprire il file.";
            }
        }
    }
}
?>

