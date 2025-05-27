<nav>
<ul>
    <li><a href="../">Menù sito</a><7li>
    <li><a href="index.php">Home Esame2019</a></li>
    <li><a href="poi.php">Punti di Interesse</a></li>
    <li><a href="inserisci_poi.php">Aggiungi POI</a></li>
    <li><a href="commento.php">Commenti</a></li>
</ul>
</nav>


<?php
// Connessione al database
$servername = "localhost";
$username = "ElTaras";
$password = "ciao";
$dbname = "202425_5ib_eltaras_turismo";

$conn = new mysqli($servername, $username, $password, $dbname);

// Controlla la connessione
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Aggiungi un commento
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $poi_id = $_POST['poi_id'];
    $user_id = $_POST['user_id'];
    $voto = $_POST['voto'];
    $commento = $_POST['commento'];

    $sql = "INSERT INTO commenti (poi_id, user_id, voto, commento) VALUES ('$poi_id', '$user_id', '$voto', '$commento')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Commento aggiunto con successo!";
    } else {
        echo "Errore: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
