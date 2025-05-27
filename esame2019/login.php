<nav>
<ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="poi.php">Punti di Interesse</a></li>
    <li><a href="inserisci_poi.php">Aggiungi POI</a></li>
    <li><a href="commento.php">Commenti</a></li>
</ul>
</nav>


<?php
// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "202425_5ib_eltaras_turismo";

$conn = new mysqli($servername, $username, $password, $dbname);

// Controlla la connessione
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Gestione del login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Utente trovato
        header("Location: index.html"); // Reindirizza a una pagina di successo
    } else {
        // Utente non trovato
        echo "Username o password errati!";
    }
}

$conn->close();
?>
