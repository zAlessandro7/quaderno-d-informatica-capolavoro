<?php
// Verifica se il form è stato inviato e se N è valido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera il numero N scelto
    $N = isset($_POST['numero']) ? (int)$_POST['numero'] : 0;
}
?>

<!DOCTYPE html>
<html lang="it">
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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabella dei Quadrati e Cubi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            color: #4CAF50;
        }

        form {
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 50%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h1>Genera la Tabella dei Quadrati e Cubi</h1>

    <!-- Form per selezionare il numero N -->
    <?php if (!isset($N)): ?>
        <form method="POST" action="">
            <label for="numero">Seleziona un numero N (tra 1 e 10):</label>
            <select name="numero" id="numero" required>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <input type="submit" value="Crea tabella">
        </form>
    <?php endif; ?>

    <?php
    // Se il form è stato inviato, generiamo la tabella
    if (isset($N) && $N >= 1 && $N <= 10):
    ?>
        <table>
            <thead>
                <tr>
                    <th>Numero</th>
                    <th>Quadrato</th>
                    <th>Cubo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Genera la tabella con i quadrati e cubi dei numeri da 1 a N
                for ($i = 1; $i <= $N; $i++):
                    $quadrato = $i * $i;
                    $cubo = $i * $i * $i;
                ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $quadrato ?></td>
                        <td><?= $cubo ?></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>

</html>
