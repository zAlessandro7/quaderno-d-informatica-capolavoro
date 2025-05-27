<?php
// 2simulazione-esame2025/carrello.php
require_once 'header.php'; // Include session_start(), config, CarrelloManager e la funzione getFoodExpressDBConnection()

// $carrelloMgrGlobal è già istanziato in header.php, possiamo riutilizzarlo se necessario,
// ma per chiarezza delle responsabilità, CarrelloManager è principalmente per la logica interna del carrello.
// Per visualizzare, leggiamo direttamente dalla sessione dopo che il manager l'ha preparata.

$carrello_items = $_SESSION['carrello']['items'] ?? [];
$id_ristorante_attivo = $_SESSION['carrello']['id_ristorante_attivo'] ?? null;
$nome_ristorante_carrello = "Nessun ristorante selezionato"; // Default

if ($id_ristorante_attivo) {
    $conn = getFoodExpressDBConnection(); // Funzione da header.php
    try {
        $stmt = $conn->prepare("SELECT NomeRistorante FROM RISTORANTE WHERE IDRistorante = :id_rist");
        $stmt->bindParam(':id_rist', $id_ristorante_attivo, PDO::PARAM_INT);
        $stmt->execute();
        $rist_info = $stmt->fetch();
        if ($rist_info) {
            $nome_ristorante_carrello = $rist_info['NomeRistorante'];
        }
    } catch (PDOException $e) {
        error_log("Errore recupero nome ristorante per carrello: " . $e->getMessage());
        // Non bloccare, usa nome default o un messaggio di errore
        $nome_ristorante_carrello = "Errore nel caricamento del nome ristorante";
    }
    $conn = null; // Chiudi la connessione specifica per questa query
}

?>

<h2 class="page-title">Il Tuo Carrello</h2>

<?php
// Mostra messaggi di feedback (es. dopo aggiornamento quantità, rimozione, svuotamento)
if (isset($_SESSION['feedback_message_carrello'])) {
    $feedback_type_class = $_SESSION['feedback_type_carrello'] ?? 'warn_msg';
    echo "<div class='message_container " . $feedback_type_class . "'><p>" . $_SESSION['feedback_message_carrello'] . "</p></div>";
    unset($_SESSION['feedback_message_carrello']);
    unset($_SESSION['feedback_type_carrello']);
}
?>

<?php if (!empty($carrello_items)): ?>
    <p style="text-align:center; font-size: 1.1em; margin-bottom: 20px;">Stai ordinando da: <strong style="color: var(--primary-color);"><?php echo htmlspecialchars($nome_ristorante_carrello); ?></strong></p>
    
    <form action="aggiorna_carrello.php" method="POST"> <!-- Punta allo script action per aggiornare/rimuovere -->
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Prodotto</th>
                    <th style="text-align:right;">Prezzo Unit.</th>
                    <th style="text-align:center;">Quantità</th>
                    <th style="text-align:right;">Subtotale</th>
                    <th style="text-align:center;">Rimuovi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totale_generale = 0;
                foreach ($carrello_items as $id_piatto => $item): 
                    $subtotale_item = (float)$item['prezzo'] * (int)$item['quantita'];
                    $totale_generale += $subtotale_item;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nome']); ?></td>
                        <td style="text-align:right;">€ <?php echo number_format($item['prezzo'], 2, ',', '.'); ?></td>
                        <td style="text-align:center;">
                            <input type="number" name="quantita[<?php echo $id_piatto; ?>]" value="<?php echo $item['quantita']; ?>" min="0" max="10" class="cart-quantity-input">
                            <!-- Se la quantità è 0, aggiorna_carrello.php lo rimuoverà -->
                        </td>
                        <td style="text-align:right;">€ <?php echo number_format($subtotale_item, 2, ',', '.'); ?></td>
                        <td style="text-align:center;">
                            <button type="submit" name="remove_item" value="<?php echo $id_piatto; ?>" class="btn-small btn-remove" title="Rimuovi articolo">X</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right; font-weight:bold; font-size: 1.2em;">Totale Ordine:</td>
                    <td style="text-align:right; font-weight:bold; font-size: 1.2em;">€ <?php echo number_format($totale_generale, 2, ',', '.'); ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="cart-actions">
            <a href="ristorante.php?id=<?php echo $id_ristorante_attivo; ?>" class="btn-secondary">« Continua lo Shopping</a>
            <button type="submit" name="update_cart" value="1" class="btn-secondary">Aggiorna Quantità</button>
            <?php if(isset($_SESSION['cliente_id'])): ?>
                <a href="checkout.php" class="btn-checkout">Procedi al Checkout »</a>
            <?php else: ?>
                <p style="margin-top:15px; text-align:right; font-weight:500;">
                    <a href="login.php?redirect=carrello.php" style="color:var(--primary-color); text-decoration:underline;">Effettua il login</a> o 
                    <a href="registrazione.php" style="color:var(--primary-color); text-decoration:underline;">registrati</a> per procedere con l'ordine.
                </p>
            <?php endif; ?>
        </div>
    </form>
    <form action="aggiorna_carrello.php" method="POST" style="margin-top:30px; text-align:center;">
        <button type="submit" name="empty_cart" value="1" class="btn-remove btn-small" onclick="return confirm('Sei sicuro di voler svuotare completamente il carrello?');">Svuota Tutto il Carrello</button>
    </form>

<?php else: ?>
    <p style="text-align: center; font-size: 1.2em; margin-top: 30px;">Il tuo carrello è attualmente vuoto.</p>
    <p style="text-align: center; margin-top: 20px;"><a href="index.php" class="btn-vedi-menu">Sfoglia i Ristoranti</a></p>
<?php endif; ?>

<style>
/* Stili già presenti in style_foodexpress.css per .cart-table, .btn-small, .btn-remove, .btn-secondary, .btn-checkout
   ma puoi aggiungere o sovrascrivere stili specifici qui se necessario. */
.cart-table { width: 100%; border-collapse: collapse; /* Cambiato da separate per un look più pulito */ margin-top: 20px; font-size: 0.95em; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-radius: 8px; overflow:hidden; /* Per i bordi arrotondati */}
.cart-table th, .cart-table td { border-bottom: 1px solid var(--medium-gray); padding: 12px 15px; text-align: left; }
.cart-table thead th { background-color: var(--light-gray); font-weight: 600; color: var(--text-color); border-bottom: 2px solid var(--primary-color); }
.cart-table tbody tr:last-child td { border-bottom: none; }
.cart-table tbody tr:hover td { background-color: #fcfcfc; } /* Leggero hover sulle righe */
.cart-table tfoot td { font-weight: bold; background-color: var(--light-gray); border-top: 2px solid var(--primary-color); }

.cart-quantity-input {
    width: 60px;
    padding: 8px;
    text-align: center;
    border: 1px solid var(--medium-gray);
    border-radius: 4px;
    font-family: 'Poppins', sans-serif;
}
.cart-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px dashed var(--medium-gray);
}
.btn-vedi-menu { /* Per il pulsante "Sfoglia i Ristoranti" */
    display: inline-block; width: auto;
}
</style>

<?php
require_once 'footer.php';
?>