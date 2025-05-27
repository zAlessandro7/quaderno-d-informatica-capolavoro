
<nav>
<ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="poi.php">Punti di Interesse</a></li>
    <li><a href="inserisci_poi.php">Aggiungi POI</a></li>
    <li><a href="commento.php">Commenti</a></li>
</ul>
</nav>


<?php

include('db.php'); // Connessione al database

// Verifica che sia stato passato un id nella query string
if (isset($_GET['id'])) {
    $poi_id = $_GET['id'];

    // Verifica che il POI esista nel database
    $query = "SELECT * FROM poi WHERE id = $poi_id";
    $result = mysqli_query($conn, $query);
    $poi = mysqli_fetch_assoc($result);

    if (!$poi) {
        echo "POI non trovato!";
        exit;
    }
} else {
    echo "ID POI non valido.";
    exit;
}

// Aggiungi commento
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $commento = mysqli_real_escape_string($conn, $_POST['commento']);
    $voto = intval($_POST['voto']);
    $utente = mysqli_real_escape_string($conn, $_POST['utente']);

    if ($voto >= 1 && $voto <= 5 && !empty($commento)) {
        $insert_query = "INSERT INTO commenti (poi_id, utente, commento, voto, data_commento) 
                         VALUES ('$poi_id', '$utente', '$commento', '$voto', NOW())";

        if (mysqli_query($conn, $insert_query)) {
            echo "Commento aggiunto con successo!";
        } else {
            echo "Errore nell'inserimento del commento: " . mysqli_error($conn);
        }
    } else {
        echo "Il voto deve essere tra 1 e 5 e il commento non può essere vuoto.";
    }
}

// Recupera i commenti per il POI
$query_commenti = "SELECT * FROM commenti WHERE poi_id = $poi_id ORDER BY data_commento DESC";
$result_commenti = mysqli_query($conn, $query_commenti);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $poi['nome']; ?></title>
</head>
<body>

<h1><?php echo $poi['nome']; ?></h1>
<p><?php echo $poi['descrizione']; ?></p>

<h2>Lascia un commento</h2>
<form method="POST">
    <label for="utente">Nome utente:</label>
    <input type="text" name="utente" id="utente" required><br>

    <label for="voto">Voto (1-5):</label>
    <input type="number" name="voto" id="voto" min="1" max="5" required><br>

    <label for="commento">Commento:</label>
    <textarea name="commento" id="commento" required></textarea><br>

    <button type="submit">Aggiungi commento</button>
</form>

<h2>Commenti</h2>
<?php
if (mysqli_num_rows($result_commenti) > 0) {
    while ($commento = mysqli_fetch_assoc($result_commenti)) {
        echo "<div>";
        echo "<strong>" . htmlspecialchars($commento['utente']) . "</strong> - Voto: " . $commento['voto'] . "<br>";
        echo "<p>" . nl2br(htmlspecialchars($commento['commento'])) . "</p>";
        echo "<small>Commentato il: " . $commento['data_commento'] . "</small>";
        echo "</div><hr>";
    }
} else {
    echo "Nessun commento per questo POI.";
}
?>

</body>
</html>
