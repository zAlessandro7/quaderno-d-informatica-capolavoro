<?php
// 2simulazione-esame2025/index.php
require_once 'header.php'; 

$conn = getFoodExpressDBConnection(); 
$ristoranti = [];

try {
    $stmt = $conn->query("SELECT IDRistorante, NomeRistorante, IndirizzoRistorante, ImmagineLogoURL FROM RISTORANTE WHERE Attivo = TRUE ORDER BY NomeRistorante ASC");
    if ($stmt) {
        $ristoranti = $stmt->fetchAll(); 
    } else {
        error_log("Query ristoranti fallita in 2simulazione-esame2025/index.php");
    }
} catch (PDOException $e) {
    if (!isset($_SESSION['feedback_message'])) { 
        $_SESSION['feedback_message'] = "Errore nel caricamento dei ristoranti: " . $e->getMessage();
        $_SESSION['feedback_type'] = "error_msg";
    }
    error_log("Errore PDO caricamento ristoranti in index.php: " . $e->getMessage());
}
?>

<h2 class="page-title">Scegli il tuo Ristorante Preferito</h2>

<!-- Mostra messaggi di feedback dalla sessione -->
<?php
if (isset($_SESSION['feedback_message']) && !empty($_SESSION['feedback_message'])) {
    $feedback_type_class = $_SESSION['feedback_type'] ?? 'warn_msg';
    echo "<div class='message_container " . $feedback_type_class . "'><p>" . $_SESSION['feedback_message'] . "</p></div>";
    unset($_SESSION['feedback_message']);
    unset($_SESSION['feedback_type']);
}
?>

<div class="lista-ristoranti">
    <?php if (!empty($ristoranti)): ?>
        <?php foreach ($ristoranti as $ristorante): ?>
            <div class="ristorante-card">
                <div class="img-container">
                    <img src="<?php 
                        $imgSrc = 'assets/images/default_logo.png'; // Immagine di default
                        if (!empty($ristorante['ImmagineLogoURL'])) {
                            // Controlla se è un URL assoluto
                            if (filter_var($ristorante['ImmagineLogoURL'], FILTER_VALIDATE_URL)) {
                                $imgSrc = htmlspecialchars($ristorante['ImmagineLogoURL']);
                            } 
                            // Altrimenti, trattalo come un percorso relativo alla cartella del progetto Food Express (2simulazione-esame2025)
                            // e verifica se il file esiste
                            elseif (file_exists(__DIR__ . '/' . $ristorante['ImmagineLogoURL'])) {
                                // __DIR__ qui è C:\...\2simulazione-esame2025
                                $imgSrc = htmlspecialchars($ristorante['ImmagineLogoURL']);
                            }
                            // Aggiungi un altro controllo se il percorso nel DB è già relativo alla webroot di 2simulazione-esame2025
                            elseif (file_exists($ristorante['ImmagineLogoURL'])) { 
                                $imgSrc = htmlspecialchars($ristorante['ImmagineLogoURL']);
                            }
                        }
                        echo $imgSrc; 
                        ?>" alt="Logo <?php echo htmlspecialchars($ristorante['NomeRistorante']); ?>">
                </div>
                <div class="ristorante-info">
                    <h3><?php echo htmlspecialchars($ristorante['NomeRistorante']); ?></h3>
                    <p class="indirizzo"><?php echo htmlspecialchars($ristorante['IndirizzoRistorante']); ?></p>
                </div>
                <div class="ristorante-actions">
                     <a href="ristorante.php?id=<?php echo $ristorante['IDRistorante']; ?>" class="btn-vedi-menu">Vedi Menù e Ordina</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; font-size: 1.1em; color: var(--dark-gray);">Al momento non ci sono ristoranti disponibili. Torna a trovarci presto!</p>
        <?php 
            if (isset($e) && !isset($_SESSION['feedback_message_processed_for_this_page'])) { 
                echo "<p class='error_msg' style='text-align:center;'>Dettagli errore DB (solo per debug): " . htmlspecialchars($e->getMessage()) . "</p>";
                $_SESSION['feedback_message_processed_for_this_page'] = true; 
            }
        ?>
    <?php endif; ?>
</div>

<?php
if (isset($_SESSION['feedback_message_processed_for_this_page'])) { 
    unset($_SESSION['feedback_message_processed_for_this_page']);
}
$conn = null; 
require_once 'footer.php';
?>