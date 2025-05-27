<?php
// 2simulazione-esame2025/login.php
require_once 'header.php'; // Include session_start() e config

if (isset($_SESSION['cliente_id'])) {
    header('Location: index.php'); // Già loggato
    exit;
}

// Recupera l'email dalla sessione per ripopolamento, se presente
$email_val = htmlspecialchars($_SESSION['form_data_login']['email'] ?? '');
unset($_SESSION['form_data_login']); // Pulisci dopo averla usata
?>

<h2 class="page-title">Login Food Express</h2>

<?php
if (isset($_SESSION['login_error'])) {
    echo "<div class='message_container error_msg'><p>" . $_SESSION['login_error'] . "</p></div>";
    unset($_SESSION['login_error']);
}
if (isset($_SESSION['registration_success'])) { // Mostra messaggio da registrazione se presente
    echo "<div class='message_container ok_msg'><p>" . htmlspecialchars($_SESSION['registration_success']) . "</p></div>";
    unset($_SESSION['registration_success']);
}
?>

<form action="login_action.php" method="POST" class="form-styled">
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $email_val; ?>" required>
    </div>
    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn-submit">Login</button>
</form>
<p style="text-align: center; margin-top: 15px;">Non hai un account? <a href="registrazione.php" style="color:var(--primary-color);">Registrati ora!</a></p>

<!-- Stili (possono essere centralizzati in style_foodexpress.css) -->
<style>
.form-styled { max-width: 400px; margin: 20px auto; padding: 25px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
.form-group input[type="email"],
.form-group input[type="password"] { width: 100%; padding: 12px; border: 1px solid var(--medium-gray, #ccc); border-radius: 6px; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
.form-group input:focus { border-color: var(--primary-color, #FF6347); outline: none; box-shadow: 0 0 0 2px rgba(255, 99, 71, 0.2); }
.btn-submit { display: inline-block; background: var(--primary-color, #FF6347); color: #fff; padding: 12px 25px; border: none; border-radius: 25px; cursor: pointer; font-size: 1.05em; font-weight:500; }
.btn-submit:hover { background: #e04a30; }
</style>

<?php
require_once 'footer.php';
?>