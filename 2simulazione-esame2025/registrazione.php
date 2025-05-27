<?php
// 2simulazione-esame2025/registrazione.php
require_once 'header.php'; // Includerà session_start() e config

if (isset($_SESSION['cliente_id'])) {
    header('Location: index.php'); // Già loggato, reindirizza
    exit;
}

// Recupera i dati del form dalla sessione, se presenti (per ripopolamento dopo errore)
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // Pulisci dopo averli recuperati

$nome_val = htmlspecialchars($form_data['nome'] ?? '');
$cognome_val = htmlspecialchars($form_data['cognome'] ?? '');
$email_val = htmlspecialchars($form_data['email'] ?? '');
$indirizzo_val = htmlspecialchars($form_data['indirizzo'] ?? '');
$telefono_val = htmlspecialchars($form_data['telefono'] ?? '');
?>

<h2 class="page-title">Registrati a Food Express</h2>

<?php
if (isset($_SESSION['registration_error'])) {
    echo "<div class='message_container error_msg'><p>" . $_SESSION['registration_error'] . "</p></div>";
    unset($_SESSION['registration_error']);
}
if (isset($_SESSION['registration_success'])) { // Raramente mostrato qui, di solito si reindirizza a login
    echo "<div class='message_container ok_msg'><p>" . htmlspecialchars($_SESSION['registration_success']) . "</p></div>";
    unset($_SESSION['registration_success']);
}
?>

<form action="effettua_registrazione.php" method="POST" class="form-styled">
    <div class="form-group">
        <label for="nome">Nome: <span class="required">*</span></label>
        <input type="text" id="nome" name="nome" value="<?php echo $nome_val; ?>" required>
    </div>
    <div class="form-group">
        <label for="cognome">Cognome: <span class="required">*</span></label>
        <input type="text" id="cognome" name="cognome" value="<?php echo $cognome_val; ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email: <span class="required">*</span></label>
        <input type="email" id="email" name="email" value="<?php echo $email_val; ?>" required>
    </div>
    <div class="form-group">
        <label for="password">Password: <span class="required">*</span> (min. 6 caratteri)</label>
        <input type="password" id="password" name="password" required minlength="6">
    </div>
    <div class="form-group">
        <label for="password_confirm">Conferma Password: <span class="required">*</span></label>
        <input type="password" id="password_confirm" name="password_confirm" required>
    </div>
    <div class="form-group">
        <label for="indirizzo">Indirizzo di Consegna Principale:</label>
        <textarea id="indirizzo" name="indirizzo" rows="3"><?php echo $indirizzo_val; ?></textarea>
    </div>
    <div class="form-group">
        <label for="telefono">Telefono:</label>
        <input type="tel" id="telefono" name="telefono" value="<?php echo $telefono_val; ?>">
    </div>
    <button type="submit" class="btn-submit">Registrati</button>
</form>
<p style="text-align: center; margin-top: 15px;">Hai già un account? <a href="login.php" style="color:var(--primary-color);">Accedi ora!</a></p>


<style>
/* ... (stili per .form-styled, .form-group, .btn-submit come prima) ... */
.form-styled { max-width: 500px; margin: 20px auto; padding: 25px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--dark-gray, #555); }
.form-group label .required { color: var(--error-color, red); }
.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group input[type="tel"],
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--medium-gray, #ccc);
    border-radius: 6px;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
    transition: border-color 0.3s ease;
}
.form-group input:focus, .form-group textarea:focus {
    border-color: var(--primary-color, #FF6347);
    outline: none;
    box-shadow: 0 0 0 2px rgba(255, 99, 71, 0.2);
}
.form-group textarea { resize: vertical; min-height: 80px;}
.btn-submit {
    display: inline-block;
    background: var(--primary-color, #FF6347);
    color: #fff;
    padding: 12px 25px;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 1.05em;
    font-weight: 500;
    transition: background-color 0.3s ease, transform 0.2s ease;
}
.btn-submit:hover { background: #e04a30;  transform: translateY(-2px); }
</style>

<?php
require_once 'footer.php';
?>