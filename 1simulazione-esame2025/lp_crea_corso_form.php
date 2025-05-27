<?php
// 1simulazione-esame2025/lp_crea_corso_form.php
$page_title = "Crea Nuovo Corso";
require_once 'lp_header.php';
require_once 'lp_auth_check.php'; // Per verificare il login
check_login('insegnante'); // Solo insegnanti possono accedere

$form_data_corso = $_SESSION['lp_form_data_corso'] ?? [];
unset($_SESSION['lp_form_data_corso']);
?>

<h2 class="page-title">Crea un Nuovo Corso Virtuale</h2>
<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="lp_crea_corso_action.php" method="POST" class="form-styled needs-validation" novalidate>
            <div class="form-group">
                <label for="nome_corso">Nome del Corso: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nome_corso" name="nome_corso" value="<?php echo htmlspecialchars($form_data_corso['nome_corso'] ?? ''); ?>" required>
                <div class="invalid-feedback">Inserisci il nome del corso.</div>
            </div>
            <div class="form-group">
                <label for="lingua">Lingua: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="lingua" name="lingua" value="<?php echo htmlspecialchars($form_data_corso['lingua'] ?? ''); ?>" placeholder="Es. Inglese, Spagnolo" required>
                 <div class="invalid-feedback">Inserisci la lingua del corso.</div>
            </div>
            <div class="form-group">
                <label for="livello">Livello di Difficoltà: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="livello" name="livello" value="<?php echo htmlspecialchars($form_data_corso['livello'] ?? ''); ?>" placeholder="Es. A1, B2, C1" required>
                <div class="invalid-feedback">Inserisci il livello del corso.</div>
            </div>
            <div class="form-group">
                <label for="descrizione_corso">Descrizione Breve (Opzionale):</label>
                <textarea class="form-control" id="descrizione_corso" name="descrizione_corso" rows="4"><?php echo htmlspecialchars($form_data_corso['descrizione_corso'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-submit">Crea Corso</button>
             <a href="lp_dashboard_insegnante.php" class="btn btn-secondary ms-2">Annulla</a>
        </form>
    </div>
</div>
<script> /* ... (script validazione Bootstrap) ... */ </script>
<?php require_once 'lp_footer.php'; ?>