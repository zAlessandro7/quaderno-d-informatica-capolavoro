<?php include 'header.php'; ?>

<h1>Registrati</h1>
<form method="post">
    <!-- Form di registrazione -->
</form>


<?php include 'header.php'; ?>

<h1>Visualizza Eventi</h1>
<!-- Codice per visualizzare gli eventi -->


<?php
include 'db.php';

$categoria = $_GET['categoria'] ?? ''; // Filtra per categoria (se fornita)
$sql = "SELECT * FROM eventi WHERE categoria LIKE '%$categoria%' ORDER BY data";

$result = $conn->query($sql);

// Debug: Verifica se la query è corretta
if (!$result) {
    die("Errore nella query: " . $conn->error);
}

// Controlla se ci sono risultati
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<h3>" . $row['titolo'] . "</h3>";
        echo "<p>Luogo: " . $row['luogo'] . "</p>";
        echo "<p>Data: " . $row['data'] . "</p>";
        echo "<p>Artisti: " . $row['artisti'] . "</p>";
        echo "<hr>";
    }
} else {
    echo "<p>Nessun evento trovato.</p>";
}
?>
