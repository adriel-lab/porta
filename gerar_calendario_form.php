<?php
// Inclui o arquivo de conexão com o banco de dados
include 'conexao.php';

// Verificando a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Consulta SQL para obter as igrejas
$sqlIgrejas = "SELECT * FROM igrejas";
$resultIgrejas = $conn->query($sqlIgrejas);

// Array para armazenar as igrejas
$igrejas = [];
if ($resultIgrejas->num_rows > 0) {
    while ($row = $resultIgrejas->fetch_assoc()) {
        $igrejas[$row['id']] = [
            'nome' => $row['nome'],
            'dias_culto' => explode(', ', $row['dias_culto'])
        ];
    }
}

// Exibe o formulário para seleção de igreja e datas
echo '<form method="post" action="gerar_calendario.php">
    <label for="igreja">Selecione a Igreja:</label>
    <select name="igreja" id="igreja" required>';
foreach ($igrejas as $id => $igreja) {
    echo "<option value=\"$id\">{$igreja['nome']}</option>";
}
echo '</select><br>
    <label for="data_inicio">Data de Início (MM/AAAA):</label>
    <input type="text" name="data_inicio" id="data_inicio" placeholder="MM/AAAA" required><br>
    <label for="data_fim">Data de Fim (MM/AAAA):</label>
    <input type="text" name="data_fim" id="data_fim" placeholder="MM/AAAA" required><br>
    <input type="submit" value="Gerar Calendário">
</form>';

// Fechar conexão com o banco de dados
$conn->close();
?>
