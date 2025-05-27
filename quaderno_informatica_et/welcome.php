<!-- ES02A/welcome.php -->
<?php
session_start(); // Avvia la sessione

// Verifica se l'utente  è loggato
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: esercizio4.php'); // Se non è loggato, rimandiamo al login
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Riservata - Benvenuto</title>
   
</head>
<body>

    <h1>Benvenuto nella Pagina Riservata!</h1>
    <div class="message">
        <h2>Controllo credenziali</h2>
        <p>Benvenuto nell'area autorizzata, <?php echo $_SESSION['username']; ?>!</p>
    </div>

</body>
</html>
