<?php
$servername = "localhost";
$username = "ElTaras";
$password = "ciao";
$dbname = "202425_5ib_eltaras_turismo";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
