<?php
// 2simulazione-esame2025/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/database_config.php';
require_once __DIR__ . '/CarrelloManager.php'; 

$carrelloMgrGlobal = new CarrelloManager();
$numero_articoli_nel_carrello = $carrelloMgrGlobal->getNumeroTotaleArticoli();

if (!function_exists('getFoodExpressDBConnection')) {
    // ... (tua funzione getFoodExpressDBConnection) ...
    function getFoodExpressDBConnection() {
        static $conn_fe = null;
        if ($conn_fe === null) {
            $dbname_foodexpress = get_db_name(DB_NAME_FOODEXPRESS);
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . $dbname_foodexpress . ";charset=" . DB_CHARSET;
                $conn_fe = new PDO($dsn, DB_USER_APP, DB_PASS_APP);
                $conn_fe->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conn_fe->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $conn_fe->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                error_log("FALLIMENTO CONNESSIONE PDO in header a " . htmlspecialchars($dbname_foodexpress) . ": " . $e->getMessage());
                die("Impossibile connettersi al database del servizio Food Express. Si prega di riprovare più tardi.");
            }
        }
        return $conn_fe;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Express - Consegna a Domicilio</title>
    <!-- Link al CSS specifico del progetto Food Express -->
    <link rel="stylesheet" href="style_foodexpress.css">
    <!-- Commenta o rimuovi se non usi uno stile generale esterno a questo progetto -->
    <!-- <link rel="stylesheet" href="../stili.css"> -->
</head>
<body>
    <header class="food-express-header">
        <div class="container">
            <a href="index.php" class="logo"><h1>Food Express</h1></a>
            <nav>
                <ul>
                    <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Ristoranti</a></li>
                    <li><a href="carrello.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'carrello.php' ? 'active' : ''; ?>">Carrello
                        (<?php echo $numero_articoli_nel_carrello; ?>)
                    </a></li>
                    <?php if (isset($_SESSION['cliente_id'])): ?>
                        <li><a href="miei_ordini.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'miei_ordini.php' ? 'active' : ''; ?>">I Miei Ordini</a></li>
                        <li><span>Ciao, <?php echo htmlspecialchars($_SESSION['cliente_nome'] ?? 'Utente'); ?>!</span></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Login</a></li>
                        <li><a href="registrazione.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'registrazione.php' ? 'active' : ''; ?>">Registrati</a></li>
                    <?php endif; ?>
                     <li><a href="../index.php">Menù Generale Sito</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
    <?php
    if (isset($_SESSION['feedback_message'])) {
        $feedback_type_class = $_SESSION['feedback_type'] ?? 'warn_msg';
        echo "<div class='message_container " . $feedback_type_class . "'><p>" . $_SESSION['feedback_message'] . "</p></div>";
        unset($_SESSION['feedback_message']);
        unset($_SESSION['feedback_type']);
    }
    ?>