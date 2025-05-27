<?php
// 1simulazione-esame2025/lp_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/lp_database_config.php';

$lp_user_type = $_SESSION['lp_user_type'] ?? null;
$lp_user_name = null;
if ($lp_user_type === 'studente' && isset($_SESSION['studente_nome_lingue'])) {
    $lp_user_name = $_SESSION['studente_nome_lingue'];
} elseif ($lp_user_type === 'insegnante' && isset($_SESSION['insegnante_nome_lingue'])) {
    $lp_user_name = $_SESSION['insegnante_nome_lingue'];
}

if (!function_exists('getLinguePlatformDBConnection_page')) {
    function getLinguePlatformDBConnection_page() {
        static $conn_lp_page = null;
        if ($conn_lp_page === null) {
            $dbname_lingue = get_lp_db_name();
            try {
                $dsn = "mysql:host=" . LP_DB_HOST . ";dbname=" . $dbname_lingue . ";charset=" . LP_DB_CHARSET;
                $conn_lp_page = new PDO($dsn, LP_DB_USER_APP, LP_DB_PASS_APP);
                $conn_lp_page->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $conn_lp_page->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $conn_lp_page->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                error_log("FALLIMENTO CONNESSIONE PDO in header_lingue a " . htmlspecialchars($dbname_lingue) . ": " . $e->getMessage());
                die("Impossibile connettersi al database della Piattaforma Lingue. Riprova più tardi.");
            }
        }
        return $conn_lp_page;
    }
}
$page_title_resolved = $page_title ?? "Piattaforma Lingue";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title_resolved); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SOLO IL CSS SPECIFICO PER LINGUE PLATFORM -->
    <link rel="stylesheet" href="assets/css/lp_style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: var(--lp-header-bg, #2C3E50);">
        <div class="container">
            <a class="navbar-brand" href="lp_index.php" style="font-weight:bold; font-size: 1.6rem;">LinguePlatform</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#lpNav" aria-controls="lpNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="lpNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lp_index.php' ? 'active' : ''; ?>" href="lp_index.php">Home LP</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lp_corsi.php' ? 'active' : ''; ?>" href="lp_corsi.php">Corsi</a></li>
                    <?php if ($lp_user_type): ?>
                        <?php if ($lp_user_type === 'studente'): ?>
                            <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lp_dashboard_studente.php' ? 'active' : ''; ?>" href="lp_dashboard_studente.php">My Dashboard</a></li>
                        <?php elseif ($lp_user_type === 'insegnante'): ?>
                            <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lp_dashboard_insegnante.php' ? 'active' : ''; ?>" href="lp_dashboard_insegnante.php">My Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lp_crea_corso_form.php' ? 'active' : ''; ?>" href="lp_crea_corso_form.php">Crea Corso</a></li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUserLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Ciao, <?php echo htmlspecialchars($lp_user_name ?? 'Utente'); ?>!
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUserLink">
                                <li><a class="dropdown-item" href="lp_profilo.php">Il Mio Profilo</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="lp_logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lp_login_form.php' ? 'active' : ''; ?>" href="lp_login_form.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lp_registrazione_form.php' ? 'active' : ''; ?>" href="lp_registrazione_form.php">Registrati</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="../index.php" title="Torna al menu principale del sito">Sito Gen.</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4 page-content-lingue" style="padding-top: 70px;"> <!-- Aggiunto padding-top per navbar fissa -->
    <?php
    // Mostra messaggi di feedback globali dalla sessione per LinguePlatform
    if (isset($_SESSION['lp_feedback_success'])) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>" . htmlspecialchars($_SESSION['lp_feedback_success']) . "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        unset($_SESSION['lp_feedback_success']);
    }
    if (isset($_SESSION['lp_feedback_error'])) {
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>" . $_SESSION['lp_feedback_error'] . "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        unset($_SESSION['lp_feedback_error']);
    }
    ?>