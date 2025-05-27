<?php
include('db.php');  // Includi la connessione al database

// Query per ottenere tutti i POI dal database
$sql = "SELECT * FROM poi";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punti di Interesse</title>
    <link rel="stylesheet" href="styles.css"> <!-- Aggiungi il tuo file di stile -->
</head>
<body>

    <!-- Navbar -->
    <nav>
        <ul>
            <li><a href="index.php">Home Esame2019</a></li>
            <li><a href="inserisci_poi.php">Aggiungi POI</a></li>
            
            <li><a href="../">Menù sito</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Benvenuto nel nostro sito POI</h1>
        <p>Esplora i punti di interesse e lascia i tuoi commenti!</p>
        
        <div class="content-container">
            <!-- Qui aggiungi il contenuto dinamico (es. i POI) -->
            <section>
                <h2>Punti di Interesse</h2>
                <?php
                // Controlla se ci sono POI nel database
                if ($result->num_rows > 0) {
                    // Visualizza ogni POI
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='poi-item'>";
                        echo "<h3>" . $row['nome'] . "</h3>";
                        echo "<p>" . $row['descrizione'] . "</p>";
                        
                        // Se il POI ha un'immagine, mostra l'immagine
                        if (!empty($row['immagine'])) {
                            echo "<img src='" . $row['immagine'] . "' alt='Immagine di " . $row['nome'] . "'>";
                        }

                        // Link per visualizzare i dettagli del POI
                        echo "<a href='poi.php?id=" . $row['id'] . "' class='btn'>Scopri di più</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Nessun punto di interesse trovato.</p>";
                }
                ?>
            </section>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 POI Turismo. Tutti i diritti riservati.</p>
    </footer>

    <?php
    // Chiudi la connessione al database
    $conn->close();
    ?>

</body>
</html>
