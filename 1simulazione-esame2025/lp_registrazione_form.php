<?php
// 1simulazione-esame2025/lp_registrazione_form.php
$page_title = "Registrazione Piattaforma Lingue";
require_once 'lp_header.php';

if (isset($_SESSION['studente_id_lingue']) || isset($_SESSION['insegnante_id_lingue'])) {
    $dashboardPage = ($_SESSION['lp_user_type'] === 'studente') ? 'lp_dashboard_studente.php' : 'lp_dashboard_insegnante.php';
    header('Location: ' . $dashboardPage); 
    exit; // Già loggato
}
$form_data = $_SESSION['lp_form_data'] ?? []; 
unset($_SESSION['lp_form_data']);
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <h2 class="text-center mb-4 page-title">Registrati</h2>
        <form action="lp_registrazione_action.php" method="POST" class="form-styled needs-validation" novalidate>
            <div class="form-group mb-3">
                <label for="nome" class="form-label">Nome: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($form_data['nome'] ?? ''); ?>" required>
                <div class="invalid-feedback">Inserisci il tuo nome.</div>
            </div>
            <div class="form-group mb-3">
                <label for="cognome" class="form-label">Cognome: <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="cognome" name="cognome" value="<?php echo htmlspecialchars($form_data['cognome'] ?? ''); ?>" required>
                <div class="invalid-feedback">Inserisci il tuo cognome.</div>
            </div>
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email: <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                <div class="invalid-feedback">Inserisci un'email valida.</div>
            </div>
            <div class="form-group mb-3">
                <label for="password" class="form-label">Password: <span class="text-danger">*</span> (min. 6 caratteri)</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                <div class="invalid-feedback">La password deve essere di almeno 6 caratteri.</div>
            </div>
            <div class="form-group mb-3">
                <label for="password_confirm" class="form-label">Conferma Password: <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                <div class="invalid-feedback">Le password devono coincidere.</div>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">Registrati come: <span class="text-danger">*</span></label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_utente" id="tipo_studente" value="studente" <?php echo (isset($form_data['tipo_utente']) && $form_data['tipo_utente'] == 'studente') ? 'checked' : ''; ?> required>
                    <label class="form-check-label" for="tipo_studente">Studente</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_utente" id="tipo_insegnante" value="insegnante" <?php echo (isset($form_data['tipo_utente']) && $form_data['tipo_utente'] == 'insegnante') ? 'checked' : ''; ?> required>
                    <label class="form-check-label" for="tipo_insegnante">Insegnante</label>
                </div>
                 <div class="invalid-feedback">Seleziona il tipo di utente.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-submit">Registrati</button>
        </form>
        <p class="text-center mt-3">Hai già un account? <a href="lp_login_form.php" style="color:var(--lp-primary-color);">Accedi</a></p>
    </div>
</div>
<script> /* ... (script validazione Bootstrap come prima) ... */ </script>
<?php require_once 'lp_footer.php'; ?>