<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_porteiro = $_POST['nome_porteiro'];
    $igreja_id = $_POST['igreja_id'];
    $restricoes = isset($_POST['restricoes']) ? implode(', ', $_POST['restricoes']) : '';

    $stmt = $conn->prepare("INSERT INTO porteiros (nome, igreja_id, dias_trabalho) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $nome_porteiro, $igreja_id, $restricoes);

    if ($stmt->execute()) {
        echo "Porteiro cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar porteiro: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
