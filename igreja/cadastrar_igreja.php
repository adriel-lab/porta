<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_igreja = $_POST['nome_igreja'];
    $dias_culto = isset($_POST['dias_culto']) ? implode(', ', $_POST['dias_culto']) : '';

    $stmt = $conn->prepare("INSERT INTO igrejas (nome, dias_culto) VALUES (?, ?)");
    $stmt->bind_param("ss", $nome_igreja, $dias_culto);

    if ($stmt->execute()) {
        echo "Igreja cadastrada com sucesso!";
    } else {
        echo "Erro ao cadastrar igreja: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
