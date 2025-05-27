<!DOCTYPE html>
<html>
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
    <title>Tabella Pitagorica</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Tabella Pitagorica</h2>
    <table>
        <thead>
            <tr>
                <th></th>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <th><?php echo $i; ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <tr>
                    <th><?php echo $i; ?></th>
                    <?php for ($j = 1; $j <= 10; $j++): ?>
                        <td><?php echo $i * $j; ?></td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</body>

<!DOCTYPE html>
<html>
<head>
    <title>Messaggio Personalizzato</title>
</head>
<body>
    <?php
    $nome = "Paolo";
    $ora = date("H");
    $messaggio = "";

    if ($ora >= 8 && $ora < 12) {
        $messaggio = "Buongiorno";
    } elseif ($ora >= 12 && $ora < 20) {
        $messaggio = "Buonasera";
    } else {
        $messaggio = "Buonanotte";
    }

    $browser = $_SERVER['HTTP_USER_AGENT'];

    echo "<p>$messaggio $nome, benvenuta nella mia prima pagina PHP</p>";
    echo "<p>Stai usando il browser $browser</p>";
    ?>
</body>
</html>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schemi di Triangoli</title>
    <style>
        body {
            font-family: monospace;
            white-space: pre;
        }
    </style>
</head>
<body>

<?php
// Funzione per stampare un triangolo (a, b, c, d)
function stampaTriangolo($tipo) {
    // Ciclo per le righe
    for ($i = 1; $i <= 10; $i++) {
        // Ciclo per gli spazi
        for ($j = 10; $j > $i; $j--) {
            echo '&nbsp;'; // Spazi a sinistra
        }
        
        // Asterischi per il triangolo
        if ($tipo == 'a') {
            // Triangolo tipo (a)
            for ($k = 1; $k <= $i; $k++) {
                echo '*'; // Stampa un asterisco
            }
        } elseif ($tipo == 'b') {
            // Triangolo tipo (b)
            for ($k = 10; $k >= $i; $k--) {
                echo '*'; // Stampa un asterisco
            }
        } elseif ($tipo == 'c') {
            // Triangolo tipo (c)
            for ($k = 10; $k >= $i; $k--) {
                echo '*'; // Stampa un asterisco
            }
        } elseif ($tipo == 'd') {
            // Triangolo tipo (d)
            for ($k = 1; $k <= $i; $k++) {
                echo '*'; // Stampa un asterisco
            }
        }
        
        echo '<br>'; // Vai a capo alla fine della riga
    }
}

// Stampa i triangoli uno sotto l'altro
echo "<h3>Triangolo (a)</h3>";
stampaTriangolo('a');
echo "<h3>Triangolo (b)</h3>";
stampaTriangolo('b');
echo "<h3>Triangolo (c)</h3>";
stampaTriangolo('c');
echo "<h3>Triangolo (d)</h3>";
stampaTriangolo('d');
?>

</body>
</html>


</html>
