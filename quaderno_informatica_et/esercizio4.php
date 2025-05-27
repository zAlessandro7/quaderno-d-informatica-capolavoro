<!DOCTYPE html>
<html lang="it">
<a href="../" class="menu-button">Menù sito</a>

<style>
  .menu-button {
    display: inline-block;
    background-color: #007bff; /* Blu accattivante */
    color: white; /* Testo bianco */
    text-decoration: none; /* Rimuove la sottolineatura */
    padding: 10px 20px; /* Spaziatura interna */
    border-radius: 5px; /* Angoli arrotondati */
    font-size: 16px; /* Dimensione del testo */
    font-weight: bold; /* Testo in grassetto */
    transition: background-color 0.3s, transform 0.2s; /* Animazione */
  }

  .menu-button:hover {
    background-color: #0056b3; /* Cambia colore al passaggio del mouse */
    transform: scale(1.05); /* Leggero ingrandimento */
  }

  .menu-button:active {
    background-color: #003d80; /* Colore più scuro al clic */
    transform: scale(0.95); /* Leggero rimpicciolimento */
  }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Film e Proiezioni</title>
</head>
<body>
    <h1>Gestione Film e Proiezioni</h1>

    <?php
        // Configurazione per la connessione al database
        $host = "localhost";        
        $username = "ElTaras";     
        $password = "ciao";         
        $dbname = "202425_5ib_eltaras_film_db";        
    
        // Creazione della connessione
        $conn = new mysqli($host, $username, $password, $dbname);
    
        // Controllo della connessione
        if ($conn->connect_error) {
            // Mostra un messaggio di errore in caso di connessione fallita
            echo "<p style='color: red;'>Connessione fallita: " . $conn->connect_error . "</p>";
        } else {
            // Mostra un messaggio di successo in caso di connessione riuscita
            echo "<p style='color: green;'>Connessione al database avvenuta con successo!</p>";
        }
    ?>

    <form action="esercizio4.php" method="post">
        <input type="submit" name="view_film" value="Visualizza Film">
        <input type="submit" name="view_attore" value="Visualizza Attori">
        <input type="submit" name="view_proiezione" value="Visualizza Proiezioni">
    </form>

<?php
    if (isset($_POST['view_film'])) {
        $result = $conn->query("SELECT * FROM Film");
        echo "<h2>Film</h2><table border='1'><tr><th>Codice Film</th><th>Titolo</th><th>Anno di Produzione</th><th>Regista</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['Codice_Film'] . "</td><td>" . $row['Titolo'] . "</td><td>" . $row['Anno_Produzione'] . "</td><td>" . $row['Regista'] . "</td></tr>";
        }
        echo "</table>";
    } elseif (isset($_POST['view_attore'])) {
        // Modifica la query per utilizzare il nome corretto della tabella (Attori)
        $result = $conn->query("SELECT * FROM Attori"); // Assicurati che la tabella sia "Attori"
        
        if ($result) {
            echo "<h2>Attori</h2><table border='1'><tr><th>Codice Attore</th><th>Nome</th><th>Cognome</th><th>Nazionalità</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row['Codice_Attore'] . "</td><td>" . $row['Nome'] . "</td><td>" . $row['Cognome'] . "</td><td>" . $row['Nazionalità'] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>Errore nella query: " . $conn->error . "</p>";
        }
    } elseif (isset($_POST['view_proiezione'])) {
        $result = $conn->query("SELECT * FROM Proiezione");
        echo "<h2>Proiezioni</h2><table border='1'><tr><th>Codice Proiezione</th><th>Città</th><th>Sala</th><th>Data</th><th>Ora</th><th>Numero Spettatori</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['Codice_Proiezione'] . "</td><td>" . $row['Città'] . "</td><td>" . $row['Sala'] . "</td><td>" . $row['Data'] . "</td><td>" . $row['Ora'] . "</td><td>" . $row['Numero_Spettatori'] . "</td></tr>";
        }
        echo "</table>";
    }
?>

<p>Clicca sul link qui sotto per andare alla pagina di inserimento dati:</p>
<a href="insert_data.php">Pagina di inserimento dati</a>

</body>
</html>
