<?php
// Include il file per la connessione al database
include('db.php');

// Verifica se il modulo è stato inviato tramite metodo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Recupera i dati inviati dal form
    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'];
    $indirizzo = $_POST['indirizzo'];

    // Prepara la query SQL per inserire i dati nella tabella "poi"
   
    $sql = "INSERT INTO poi (nome, descrizione, indirizzo) VALUES ('$nome', '$descrizione', '$indirizzo')";
    
    // Esegue la query e controlla se è andata a buon fine
    if ($conn->query($sql) === TRUE) {
        // Messaggio di conferma se l'inserimento ha avuto successo
        echo "POI aggiunto con successo!";
    } else {
        // Messaggio di errore se l'inserimento è fallito
        echo "Errore nell'inserimento del POI: " . $conn->error;
    }
}
?>

<!-- Form HTML per l'inserimento di un nuovo POI -->
<form action="inserisci_poi.php" method="POST">
    <!-- Campo per inserire il nome del POI -->
    <label for="nome">Nome POI:</label>
    <input type="text" id="nome" name="nome" required><br>
    
    <!-- Campo per inserire la descrizione del POI -->
    <label for="descrizione">Descrizione:</label>
    <textarea id="descrizione" name="descrizione" required></textarea><br>
    
    <!-- Campo per inserire l'indirizzo del POI -->
    <label for="indirizzo">Indirizzo:</label>
    <input type="text" id="indirizzo" name="indirizzo" required><br>
    
    <!-- Pulsante per inviare il modulo -->
    <input type="submit" value="Aggiungi POI">
</form>

<a href="../">Menù sito</a>
<a href="index.php">Torna su Esame2019!</a>
