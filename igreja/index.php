<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Igrejas</title>
</head>
<body>
    <h1>Cadastro de Igrejas</h1>
    <form action="cadastrar_igreja.php" method="POST">
        <label for="nome_igreja">Nome da Igreja:</label>
        <input type="text" id="nome_igreja" name="nome_igreja" required><br>

        <label for="dias_culto">Dias de Culto:</label><br>
        <input type="checkbox" id="segunda" name="dias_culto[]" value="Monday">
        <label for="segunda">Segunda</label><br>
        
        <input type="checkbox" id="terca" name="dias_culto[]" value="Tuesday">
        <label for="terca">Terça</label><br>
        
        <input type="checkbox" id="quarta" name="dias_culto[]" value="Wednesday">
        <label for="quarta">Quarta</label><br>
        
        <input type="checkbox" id="quinta" name="dias_culto[]" value="Thursday">
        <label for="quinta">Quinta</label><br>
        
        <input type="checkbox" id="sexta" name="dias_culto[]" value="Friday">
        <label for="sexta">Sexta</label><br>
        
        <input type="checkbox" id="sabado" name="dias_culto[]" value="Saturday">
        <label for="sabado">Sábado</label><br>
        
        <input type="checkbox" id="domingo" name="dias_culto[]" value="Sunday">
        <label for="domingo">Domingo</label><br>
        
        <input type="checkbox" id="domingo_jovens" name="dias_culto[]" value="SundayR">
        <label for="domingo_jovens">Domingo Reunião de Jovens</label><br>

        <button type="submit">Cadastrar Igreja</button>
    </form>
</body>
</html>
