<!-- ES02A/login.php -->
<?php
// Le credenziali per l'accesso
$username = 'admin';
$password = '12345';

// Messaggio di errore o di successo
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_username = $_POST['username'] ?? '';
    $input_password = $_POST['password'] ?? '';

    // Controllo delle credenziali
    if ($input_username == $username && $input_password == $password) {
        header("Location: welcome.php"); // Reindirizza alla pagina protetta
        exit();
    } else {
        $message = 'Accesso negato! Credenziali errate.';
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Accesso</title>
</head>
<body>
    <h1>Login</h1>
    <p style="color: red;"><?php echo $message; ?></p>
    <form action="login.php" method="POST">
        <label for="username">Nome Utente:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Accedi">
        <button type="button">Click Me!</button>
    </form>
</body>

</html>
