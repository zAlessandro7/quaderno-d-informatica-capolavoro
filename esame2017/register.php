<?php include 'header.php'; ?>

<h1>Crea un Nuovo Evento</h1>
<form method="post">
    <!-- Form per la creazione di eventi -->
</form>

<?php include 'footer.php'; ?>


<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = $_POST['nickname'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $categorie = $_POST['categorie'];

    $sql = "INSERT INTO utenti (nickname, nome, cognome, email, categorie_interessate)
            VALUES ('$nickname', '$nome', '$cognome', '$email', '$categorie')";

    if ($conn->query($sql) === TRUE) {
        echo "Registrazione completata!";
    } else {
        echo "Errore: " . $conn->error;
    }
}
?>
<form method="post">
    <input type="text" name="nickname" placeholder="Nickname" required><br>
    <input type="text" name="nome" placeholder="Nome" required><br>
    <input type="text" name="cognome" placeholder="Cognome" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <textarea name="categorie" placeholder="Categorie di interesse" required></textarea><br>
    <button type="submit">Registrati</button>
</form>
