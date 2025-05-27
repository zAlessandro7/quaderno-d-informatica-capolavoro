<?php
// 2simulazione-esame2025/ristorante.php
require_once 'header.php'; // Assumendo che header.php sia nella stessa cartella

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['feedback_message'] = "ID Ristorante non valido.";
    $_SESSION['feedback_type'] = "error_msg";
    header('Location: index.php');
    exit;
}
$id_ristorante_pagina = (int)$_GET['id'];
$conn = getFoodExpressDBConnection(); // Funzione da header.php

// Ottieni info ristorante
$ristorante = null;
try {
    $stmt_rist = $conn->prepare("SELECT IDRistorante, NomeRistorante FROM RISTORANTE WHERE IDRistorante = :id_rist AND Attivo = TRUE");
    $stmt_rist->bindParam(':id_rist', $id_ristorante_pagina, PDO::PARAM_INT);
    $stmt_rist->execute();
    $ristorante = $stmt_rist->fetch();
} catch (PDOException $e) {
    error_log("Errore recupero ristorante in ristorante.php: " . $e->getMessage());
}

if (!$ristorante) {
    $_SESSION['feedback_message'] = "Ristorante non trovato o non attivo.";
    $_SESSION['feedback_type'] = "error_msg";
    header('Location: index.php');
    exit;
}

// Ottieni piatti del ristorante
$piatti_per_categoria = [];
try {
    $stmt_piatti = $conn->prepare("SELECT IDPiatto, NomePiatto, Descrizione, Prezzo, ImmaginePiattoURL, CategoriaPiatto FROM PIATTO WHERE IDRistorante = :id_rist AND Disponibilita = TRUE ORDER BY CategoriaPiatto, NomePiatto ASC");
    $stmt_piatti->bindParam(':id_rist', $id_ristorante_pagina, PDO::PARAM_INT);
    $stmt_piatti->execute();
    $result_piatti_all = $stmt_piatti->fetchAll();

    if ($result_piatti_all) {
        foreach ($result_piatti_all as $row) {
            $piatti_per_categoria[$row['CategoriaPiatto']][] = $row;
        }
    }
} catch (PDOException $e) {
    error_log("Errore recupero piatti in ristorante.php: " . $e->getMessage());
}
?>

<h2 class="page-title">Menù di <?php echo htmlspecialchars($ristorante['NomeRistorante']); ?></h2>

<?php
if (isset($_SESSION['feedback_message_ristorante'])) {
    $feedback_type_class = $_SESSION['feedback_type_ristorante'] ?? 'warn_msg';
    echo "<div class='message_container " . $feedback_type_class . "'><p>" . $_SESSION['feedback_message_ristorante'] . "</p></div>";
    unset($_SESSION['feedback_message_ristorante']);
    unset($_SESSION['feedback_type_ristorante']);
}
?>

<?php if (!empty($piatti_per_categoria)): ?>
    <?php foreach ($piatti_per_categoria as $categoria => $piatti_nella_categoria): ?>
        <h3><?php echo htmlspecialchars($categoria ?: "Altro"); ?></h3>
        <div class="lista-piatti">
            <?php foreach ($piatti_nella_categoria as $piatto): ?>
                <div class="piatto-card">
                    <div class="img-container">
                        <img src="<?php 
                            $immaginePiattoSrc = 'assets/images/piatti/default_piatto.png';
                            if (!empty($piatto['ImmaginePiattoURL'])) {
                                if (filter_var($piatto['ImmaginePiattoURL'], FILTER_VALIDATE_URL)) {
                                    $immaginePiattoSrc = htmlspecialchars($piatto['ImmaginePiattoURL']);
                                } 
                                elseif (file_exists(__DIR__ . '/' . $piatto['ImmaginePiattoURL'])) { 
                                    $immaginePiattoSrc = htmlspecialchars($piatto['ImmaginePiattoURL']);
                                } elseif (file_exists($piatto['ImmaginePiattoURL'])) { 
                                    $immaginePiattoSrc = htmlspecialchars($piatto['ImmaginePiattoURL']);
                                }
                            }
                            echo $immaginePiattoSrc; 
                            ?>" alt="<?php echo htmlspecialchars($piatto['NomePiatto']); ?>">
                    </div>
                    <h4><?php echo htmlspecialchars($piatto['NomePiatto']); ?></h4>
                    <p class="descrizione"><?php echo nl2br(htmlspecialchars($piatto['Descrizione'])); ?></p>
                    <p class="prezzo">€ <?php echo number_format($piatto['Prezzo'], 2, ',', '.'); ?></p>
                    
                    <form action="aggiungi_al_carrello.php" method="post" class="form-add-cart">
                        <input type="hidden" name="id_piatto" value="<?php echo $piatto['IDPiatto']; ?>">
                        <input type="hidden" name="nome_piatto" value="<?php echo htmlspecialchars($piatto['NomePiatto']); ?>">
                        <input type="hidden" name="prezzo_piatto" value="<?php echo $piatto['Prezzo']; ?>">
                        <input type="hidden" name="id_ristorante_piatto" value="<?php echo $id_ristorante_pagina; ?>">
                        <label for="quantita_<?php echo $piatto['IDPiatto']; ?>">Q.tà:</label>
                        <input type="number" id="quantita_<?php echo $piatto['IDPiatto']; ?>" name="quantita" value="1" min="1" max="10" style="width: 60px; padding: 5px; margin-right: 5px;">
                        <button type="submit" class="btn-small">Aggiungi</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nessun piatto disponibile per questo ristorante al momento.</p>
<?php endif; ?>
<!-- FINE DEL BLOCCO CHE VISUALIZZA I PIATTI. ASSICURATI CHE NON SIA RIPETUTO DOPO QUESTO PUNTO -->

<br>
<p><a href="carrello.php" class="btn-vedi-menu" style="display: inline-block; width: auto; margin-top:20px;">Vai al Carrello</a></p>

<style>
/* ... i tuoi stili per questa pagina ... */
.lista-piatti { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;}
.piatto-card { background: #fff; border: 1px solid #eee; border-radius: 8px; padding: 15px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;}
.piatto-card .img-container { width:100%; height: 180px; overflow:hidden; margin-bottom:15px; border-radius: 6px; background-color: #f0f0f0; }
.piatto-card .img-container img { width: 100%; height: 100%; object-fit: cover; }
.piatto-card h4 { margin: 10px 0 5px 0; font-size: 1.1em; color: #333;}
.piatto-card .descrizione { font-size: 0.85em; color: #666; flex-grow: 1; margin-bottom: 10px; min-height: 40px;}
.piatto-card .prezzo { font-weight: bold; color: #e8491d; margin: 10px 0; font-size: 1.2em;}
.form-add-cart label { font-size: 0.9em; }
.btn-small { background: #5cb85c; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size:0.9em; }
.btn-small:hover { background: #4cae4c; }
.btn-vedi-menu { display: inline-block; background: #007bff; color: #fff; padding: 10px 15px; text-align: center; text-decoration: none; border-radius: 5px; font-weight: 500; transition: background-color 0.3s ease; }
.btn-vedi-menu:hover { background: #0069d9; }
</style>

<?php
$conn = null; 
require_once 'footer.php';
?>