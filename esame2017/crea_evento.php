<?php include 'header.php'; ?>

<h1>Crea un Nuovo Evento</h1>
<form method="post">
    <!-- Form del nuovo evento -->
</form>

<?php include 'header.php'; ?>

<h1>Crea un Nuovo Evento</h1>
<form method="post">
    <!-- Form per la creazione di eventi -->
</form>

<?php include 'footer.php'; ?>

<?php
include 'db.php'; // Connessione al database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoria = $_POST['categoria'];
    $luogo = $_POST['luogo'];
    $data = $_POST['data'];
    $titolo = $_POST['titolo'];
    $artisti = $_POST['artisti'];

    $sql = "INSERT INTO eventi (categoria, luogo, data, titolo, artisti)
            VALUES ('$categoria', '$luogo', '$data', '$titolo', '$artisti')";

    if ($conn->query($sql) === TRUE) {
        echo "Evento creato con successo!";
    } else {
        echo "Errore: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Evento</title>
</head>
<body>
    <h1>Crea un Nuovo Evento</h1>
    <form method="post">
        <label for="categoria">Categoria:</label><br>
        <input type="text" name="categoria" id="categoria" required><br><br>

        <label for="luogo">Luogo:</label><br>
        <input type="text" name="luogo" id="luogo" required><br><br>

        <label for="data">Data:</label><br>
        <input type="date" name="data" id="data" required><br><br>

        <label for="titolo">Titolo:</label><br>
        <input type="text" name="titolo" id="titolo" required><br><br>

        <label for="artisti">Artisti:</label><br>
        <textarea name="artisti" id="artisti" required></textarea><br><br>

        <button type="submit">Crea Evento</button>
    </form>
</body>
</html>
