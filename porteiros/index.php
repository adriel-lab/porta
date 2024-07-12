<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Porteiros</title>
</head>
<body>
    <h1>Cadastro de Porteiros</h1>
    <form action="cadastra_porteiro.php" method="POST">
        <label for="nome_porteiro">Nome do Porteiro:</label>
        <input type="text" id="nome_porteiro" name="nome_porteiro" required><br>

        <label for="igreja">Igreja:</label>
        <select id="igreja" name="igreja_id" required>
            <?php
            include 'conexao.php';
            $result = $conn->query("SELECT id, nome FROM igrejas");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['nome']}</option>";
            }
            $conn->close();
            ?>
        </select><br>

        <label for="restricoes">Dias de Trabalho:</label><br>
        <input type="checkbox" id="quinta_tarde" name="restricoes[]" value="Thursday">
        <label for="quinta_tarde">Quinta a tarde</label><br>
        
        <input type="checkbox" id="quarta_noite" name="restricoes[]" value="Wednesday">
        <label for="quarta_noite">Quarta a Noite</label><br>
        
        <input type="checkbox" id="sabado_noite" name="restricoes[]" value="Saturday">
        <label for="sabado_noite">Sabado a noite</label><br>
        
        <input type="checkbox" id="domingo_noite" name="restricoes[]" value="Sunday">
        <label for="domingo_noite">Domingo a Noite</label><br>
        
        <input type="checkbox" id="domingo_jovens" name="restricoes[]" value="SundayR">
        <label for="domingo_jovens">Domingo reunião de Jovens</label><br>
        
        <input type="checkbox" id="todos" name="restricoes[]" value="All">
        <label for="todos">Todos</label><br>

        <button type="submit">Cadastrar Porteiro</button>
    </form>
</body>
</html>
