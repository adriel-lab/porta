<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ccb";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
