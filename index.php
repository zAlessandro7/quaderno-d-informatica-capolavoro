<?php
session_start(); 
$install_flag_file = '.installed.flag'; 
$show_install_button = false;

if (!file_exists($install_flag_file)) {
    $show_install_button = true;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sito Principale - Menù Generale</title>
    <link rel="stylesheet" href="stili.css">
    <style>
        .message_container { padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid transparent; }
        .message_container p { margin: 0; }
        .ok_msg { background-color: #e6ffed; border-color: #5cb85c; color: #3c763d; }
        .error_msg { background-color: #f2dede; border-color: #ebccd1; color: #a94442; }
        .warn_msg { background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b; }
        #install-section { border: 2px dashed #ccc; padding: 20px; margin-bottom:20px; background-color: #f9f9f9;}
        #install-section input[type="submit"] { padding: 10px 15px; font-size: 1.1em; cursor:pointer; background-color: #4CAF50; color:white; border:none; border-radius:5px;}
        #install-section input[type="submit"]:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <header><h1>Menù generale</h1></header>
    <?php
    if (isset($_SESSION['message'])) {
        $message_class = 'warn_msg';
        if (strpos(strtolower($_SESSION['message']), 'successo') !== false || strpos(strtolower($_SESSION['message']), 'completata') !== false && strpos(strtolower($_SESSION['message']), 'fallita') === false) {
            $message_class = 'ok_msg';
        } elseif (strpos(strtolower($_SESSION['message']), 'errore') !== false || strpos(strtolower($_SESSION['message']), 'fallita') !== false) {
            $message_class = 'error_msg';
        }
        echo "<div class='message_container " . $message_class . "'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
    }
    ?>
    <?php if ($show_install_button): ?>
        <main id="install-section">
            <h2>Installazione Richiesta</h2>
            <p>Prima di visualizzare il sito, è necessario completare l'installazione del database.</p>
            <form action="install.php" method="POST"><input type="submit" value="Installa il Database" /></form>
            <p><small>Se l'installazione è già stata eseguita ma vedi questo messaggio, è necessario eliminare il file <code><?php echo htmlspecialchars($install_flag_file); ?></code> dalla directory principale del progetto per poter reinstallare.</small></p>
        </main><hr>
    <?php else: ?>
        <nav>
            <div class="esercizi"><h2>Esercizi del Quaderno di Informatica</h2>
                <ul>
                    <li><a href="quaderno_informatica_et/esercizio1.php">Esercizio 1</a></li>
                    <li><a href="quaderno_informatica_et/esercizio2.php">Esercizio 2</a></li>
                    <li><a href="quaderno_informatica_et/esercizio3.php">Esercizio 3</a></li>
                    <li><a href="quaderno_informatica_et/esercizio4.php">Esercizio 4</a></li>
                </ul>
            </div>
            <div class="esame"><h2>Tracce d'esame</h2>
                <ul>
                    <li><a href="esame2019/index.php">Esame 2019</a></li>
                    <li><a href="esame2017/index.php">Esame 2017</a></li>
                    <li><a href="1simulazione-esame2025/lp_index.php">1^ Simulazione esame 2025</a>
                    <li><a href="2simulazione-esame2025/index.php">2^ Simulazione esame 2025</a></li>
                </ul>
            </div>
        </nav>
    <?php endif; ?>
    <footer>
        <p>© <?php echo date("Y"); ?> El Taras Alessandro 5IB A.S 2024/2025</p>
        <p>Ultimo aggiornamento del sito: Aggiornato al 19/05/2025</p>
        <p><i>Aggiornamento sito: inserimento torna alla home + inserimento utente ElTaras + installa il database 1 utilizzo + aggiunta esercizio 2 simulazione Esame di Stato 2025, inserimento recensione.</i></p>
    </footer>
</body>
</html>