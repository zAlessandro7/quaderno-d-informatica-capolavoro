<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Film e Attori</title>
</head>
<body>
    <h1>Inserimento dati Film e Attori</h1>

    <?php
    // Connessione al database
    $servername = "localhost";
    $username = "ElTaras";
    $password = "ciao";
    $dbname = "202425_5ib_eltaras_film_db"; 

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica la connessione
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Inserimento Film
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inserisci_film'])) {
        $codice_film = $_POST['Codice_Film'];
        $titolo = $_POST['Titolo'];
        $anno_produzione = $_POST['Anno_Produzione'];
        $regista = $_POST['Regista'];

        $sql = "INSERT INTO Film (Codice_Film, Titolo, Anno_Produzione, Regista)
                VALUES ('$codice_film', '$titolo', '$anno_produzione', '$regista')";

        if ($conn->query($sql) === TRUE) {
            echo "Nuovo film inserito con successo!";
        } else {
            echo "Errore: " . $sql . "<br>" . $conn->error;
        }
    }

    // Inserimento Attori
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inserisci_attore'])) {
        $codice_attore = $_POST['Codice_Attore'];
        $nome = $_POST['Nome'];
        $cognome = $_POST['Cognome'];
        $data_nascita = $_POST['Data_Nascita'];
        $nazionalita = $_POST['Nazionalità']; // Aggiungi la variabile Nazionalità

        $sql = "INSERT INTO Attori (Codice_Attore, Nome, Cognome, Data_Nascita, Nazionalità)
                VALUES ('$codice_attore', '$nome', '$cognome', '$data_nascita', '$nazionalita')"; // Modifica la query

        if ($conn->query($sql) === TRUE) {
            echo "Nuovo attore inserito con successo!";
        } else {
            echo "Errore: " . $sql . "<br>" . $conn->error;
        }
    }
    ?>

    <h2>Inserisci un nuovo Film</h2>
    <form method="POST" action="">
        <label for="Codice_Film">Codice Film:</label>
        <input type="text" id="Codice_Film" name="Codice_Film" required><br><br>

        <label for="Titolo">Titolo:</label>
        <input type="text" id="Titolo" name="Titolo" required><br><br>

        <label for="Anno_Produzione">Anno di Produzione:</label>
        <input type="number" id="Anno_Produzione" name="Anno_Produzione" required><br><br>

        <label for="Regista">Regista:</label>
        <input type="text" id="Regista" name="Regista" required><br><br>

        <input type="submit" name="inserisci_film" value="Inserisci Film">
    </form>

    <h2>Inserisci un nuovo Attore</h2>
    <form method="POST" action="">
        <label for="Codice_Attore">Codice Attore:</label>
        <input type="text" id="Codice_Attore" name="Codice_Attore" required><br><br>

        <label for="Nome">Nome:</label>
        <input type="text" id="Nome" name="Nome" required><br><br>

        <label for="Cognome">Cognome:</label>
        <input type="text" id="Cognome" name="Cognome" required><br><br>

        <label for="Data_Nascita">Data di Nascita:</label>
        <input type="date" id="Data_Nascita" name="Data_Nascita" required><br><br>

        <label for="Nazionalità">Nazionalità:</label> <!-- Aggiungi questo campo -->
        <input type="text" id="Nazionalità" name="Nazionalità" required><br><br>

        <input type="submit" name="inserisci_attore" value="Inserisci Attore">
    </form>

    <p>Clicca sul link qui sotto per andare alla home page.</p>
    <a href="esercizio4.php">Home</a>
</body>
</html>
