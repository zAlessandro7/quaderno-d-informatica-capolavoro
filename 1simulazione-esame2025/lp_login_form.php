<?php
// 1simulazione-esame2025/lp_login_form.php
$page_title = "Login Piattaforma Lingue";
require_once 'lp_header.php'; // Include session_start() e config

if (isset($_SESSION['studente_id_lingue']) || isset($_SESSION['insegnante_id_lingue'])) { // Controlla entrambe le chiavi ID specifiche
    $dashboardPage = ($_SESSION['lp_user_type'] === 'studente') ? 'lp_dashboard_studente.php' : 'lp_dashboard_insegnante.php';
    header('Location: ' . $dashboardPage); 
    exit; // Già loggato
}
$form_data = $_SESSION['lp_form_data_login'] ?? [];
unset($_SESSION['lp_form_data_login']);
?>
<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <h2 class="text-center mb-4 page-title">Accedi</h2>
        <form action="lp_login_action.php" method="POST" class="form-styled needs-validation" novalidate>
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                <div class="invalid-feedback">Inserisci la tua email.</div>
            </div>
            <div class="form-group mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">Inserisci la tua password.</div>
            </div>
             <div class="form-group mb-3">
                <label class="form-label">Accedi come:</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_utente_login" id="tipo_studente_login" value="studente" checked required>
                    <label class="form-check-label" for="tipo_studente_login">Studente</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipo_utente_login" id="tipo_insegnante_login" value="insegnante" required>
                    <label class="form-check-label" for="tipo_insegnante_login">Insegnante</label>
                </div>
                 <div class="invalid-feedback">Seleziona il tipo di utente.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-submit">Accedi</button>
        </form>
        <p class="text-center mt-3">Non hai un account? <a href="lp_registrazione_form.php" style="color:var(--lp-primary-color);">Registrati ora!</a></p>
    </div>
</div>
<script>
// Esempio di validazione Bootstrap client-side
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
})()
</script>
<?php require_once 'lp_footer.php'; ?>