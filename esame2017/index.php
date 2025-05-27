<?php include 'header.php'; ?>

<h1>Benvenuti nella Web Community</h1>
<p>Condividi e scopri eventi dal vivo in tutta Italia! Esplora concerti, spettacoli teatrali, balletti e altro ancora.</p>

<section>
    <h2>Che cosa vuoi fare?</h2>
    <ul>
        <li><a href="crea_evento.php">Crea un nuovo evento</a></li>
        <li><a href="eventi.php">Visualizza gli eventi</a></li>
        <li><a href="register.php">Registrati alla community</a></li>
    </ul>
</section>

<section>
    <h2>Eventi Recenti</h2>
    <?php include 'header.php'; ?>

<h1>Benvenuti nella Web Community</h1>
<p>Condividi e scopri eventi dal vivo in tutta Italia!</p>

<?php include 'footer.php'; ?>

    <?php
    include 'db.php';

    $sql = "SELECT * FROM eventi ORDER BY data DESC LIMIT 5";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<h3>" . $row['titolo'] . "</h3>";
            echo "<p>Luogo: " . $row['luogo'] . "</p>";
            echo "<p>Data: " . $row['data'] . "</p>";
            echo "<p>Categoria: " . $row['categoria'] . "</p>";
            echo "<hr>";
        }
    } else {
        echo "<p>Non ci sono eventi recenti al momento.</p>";
    }
    ?>
</section>

</body>
</html>
