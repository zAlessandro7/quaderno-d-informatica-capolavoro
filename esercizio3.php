<a href="../" class="menu-button">Menù sito</a>

<style>
  .menu-button {
    display: inline-block;
    background-color: #007bff; /* Blu accattivante */
    color: white; /* Testo bianco */
    text-decoration: none; /* Rimuove la sottolineatura */
    padding: 10px 20px; /* Spaziatura interna */
    border-radius: 5px; /* Angoli arrotondati */
    font-size: 16px; /* Dimensione del testo */
    font-weight: bold; /* Testo in grassetto */
    transition: background-color 0.3s, transform 0.2s; /* Animazione */
  }

  .menu-button:hover {
    background-color: #0056b3; /* Cambia colore al passaggio del mouse */
    transform: scale(1.05); /* Leggero ingrandimento */
  }

  .menu-button:active {
    background-color: #003d80; /* Colore più scuro al clic */
    transform: scale(0.95); /* Leggero rimpicciolimento */
  }
</style>

<?php
// Variabili per errori di validazione
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera i dati dal form
    $nome = $_POST['nome'] ?? '';
    $cognome = $_POST['cognome'] ?? '';
    $data_nascita = $_POST['data_nascita'] ?? '';
    $codice_fiscale = $_POST['codice_fiscale'] ?? '';
    $email = $_POST['email'] ?? '';
    $cellulare = $_POST['cellulare'] ?? '';
    $via = $_POST['via'] ?? '';
    $cap = $_POST['cap'] ?? '';
    $comune = $_POST['comune'] ?? '';
    $provincia = $_POST['provincia'] ?? '';
    $nickname = $_POST['nickname'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validazione nome e cognome (solo lettere e spazi)
    if (!preg_match("/^[a-zA-Z\s]+$/", $nome)) {
        $errors['nome'] = "Il nome può contenere solo lettere e spazi.";
    }
    if (!preg_match("/^[a-zA-Z\s']+$/", $cognome)) {
        $errors['cognome'] = "Il cognome può contenere solo lettere, spazi e apostrofi.";
    }

    // Validazione data di nascita (data obbligatoria)
    if (empty($data_nascita)) {
        $errors['data_nascita'] = "La data di nascita è obbligatoria.";
    }

    // Validazione email (uso del tipo email HTML5)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'email non è valida.";
    }

    // Validazione cellulare (12 cifre, numerico)
    if (!empty($cellulare) && !preg_match("/^\d{12}$/", $cellulare)) {
        $errors['cellulare'] = "Il cellulare deve contenere 12 cifre.";
    }

    // Validazione indirizzo (tutti i campi obbligatori)
    if (empty($via) || empty($cap) || empty($comune) || empty($provincia)) {
        $errors['indirizzo'] = "Tutti i campi dell'indirizzo sono obbligatori.";
    }

    // Validazione nickname (deve essere diverso da nome e cognome)
    if ($nickname == $nome || $nickname == $cognome) {
        $errors['nickname'] = "Il nickname deve essere diverso da nome e cognome.";
    }

    // Validazione password (minimo 8 caratteri, almeno una maiuscola, un numero, un carattere speciale)
    if (!preg_match("/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $password)) {
        $errors['password'] = "La password deve contenere almeno 8 caratteri, una lettera maiuscola, un numero e un carattere speciale.";
    }

    // Se non ci sono errori, visualizza i dati
    if (empty($errors)) {
        // Redirigi alla pagina di visualizzazione dei dati
        header("Location: visualizza.php?nome=$nome&cognome=$cognome&data_nascita=$data_nascita&codice_fiscale=$codice_fiscale&email=$email&cellulare=$cellulare&via=$via&cap=$cap&comune=$comune&provincia=$provincia&nickname=$nickname");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Anagrafica Utenti</title>
</head>
<body>
    <h1>Modulo di Registrazione</h1>

    <form action="esercizio4.php" method="POST">
        <label for="nome">Nome: </label>
        <input type="text" name="nome" id="nome" value="<?= $nome ?? '' ?>" required>
        <span><?= $errors['nome'] ?? '' ?></span><br><br>

        <label for="cognome">Cognome: </label>
        <input type="text" name="cognome" id="cognome" value="<?= $cognome ?? '' ?>" required>
        <span><?= $errors['cognome'] ?? '' ?></span><br><br>

        <label for="data_nascita">Data di nascita: </label>
        <input type="date" name="data_nascita" id="data_nascita" value="<?= $data_nascita ?? '' ?>" required>
        <span><?= $errors['data_nascita'] ?? '' ?></span><br><br>

        <label for="codice_fiscale">Codice Fiscale: </label>
        <input type="text" name="codice_fiscale" id="codice_fiscale" value="<?= $codice_fiscale ?? '' ?>"><br><br>

        <label for="email">Email: </label>
        <input type="email" name="email" id="email" value="<?= $email ?? '' ?>" required>
        <span><?= $errors['email'] ?? '' ?></span><br><br>

        <label for="cellulare">Cellulare: </label>
        <input type="text" name="cellulare" id="cellulare" value="<?= $cellulare ?? '' ?>">
        <span><?= $errors['cellulare'] ?? '' ?></span><br><br>

        <label for="via">Via/Piazza: </label>
        <input type="text" name="via" id="via" value="<?= $via ?? '' ?>" required><br><br>

        <label for="cap">CAP: </label>
        <input type="text" name="cap" id="cap" value="<?= $cap ?? '' ?>" required><br><br>

        <label for="comune">Comune: </label>
        <input type="text" name="comune" id="comune" value="<?= $comune ?? '' ?>" required><br><br>

        <label for="provincia">Provincia: </label>
        <input type="text" name="provincia" id="provincia" value="<?= $provincia ?? '' ?>" required><br><br>

        <label for="nickname">Nickname: </label>
        <input type="text" name="nickname" id="nickname" value="<?= $nickname ?? '' ?>" required>
        <span><?= $errors['nickname'] ?? '' ?></span><br><br>

        <label for="password">Password: </label>
        <input type="password" name="password" id="password" required>
        <span><?= $errors['password'] ?? '' ?></span><br><br>

        <input type="submit" value="Registrati">
    </form>

</body>

</html>
