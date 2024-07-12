<?php
require_once 'vendor/autoload.php'; // Caminho para o autoload do Composer
use Dompdf\Dompdf;

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

// Verifica se o formulário foi submetido
if (isset($_POST['igreja'], $_POST['data_inicio'], $_POST['data_fim'])) {
    $igrejaId = $_POST['igreja'];
    $dataInicio = $_POST['data_inicio'];
    $dataFim = $_POST['data_fim'];

    // Separar mês e ano das datas de início e fim
    list($mesInicio, $anoInicio) = explode('/', $dataInicio);
    list($mesFim, $anoFim) = explode('/', $dataFim);

    // Consulta SQL para obter os porteiros
    $sqlPorteiros = "SELECT * FROM porteiros";
    $resultPorteiros = $conn->query($sqlPorteiros);

    // Array para armazenar os dias de trabalho dos porteiros
    $porteiros = [];
    if ($resultPorteiros->num_rows > 0) {
        while ($row = $resultPorteiros->fetch_assoc()) {
            $porteiros[$row['id']] = [
                'nome' => $row['nome'],
                'dias_trabalho' => explode(', ', $row['dias_trabalho'])
            ];
        }
    }

    // Percentual de aleatoriedade (0 a 100)
    $percentualAleatoriedade = 100;

    // Função para atribuir porteiros aos dias de culto
    function atribuirPorteiros($igreja, $porteiros, $percentualAleatoriedade)
    {
        $atribuicoes = [];
        $diasCulto = $igreja['dias_culto'];

        // Definir dias onde serão atribuídos 3 porteiros
        $diasTresPorteiros = ['Sunday', 'Wednesday', 'Saturday'];

        // Inicializa atribuições para cada dia de culto
        foreach ($diasCulto as $diaCulto) {
            $atribuicoes[$diaCulto] = [];

            // Determina o número de porteiros com base no dia da semana
            $numeroPorteiros = in_array($diaCulto, $diasTresPorteiros) ? 3 : 2;

            // Seleciona porteiros disponíveis para o dia de culto
            $porteirosDisponiveis = [];
            foreach ($porteiros as $porteiro) {
                if (in_array($diaCulto, $porteiro['dias_trabalho']) || in_array('All', $porteiro['dias_trabalho'])) {
                    $porteirosDisponiveis[] = $porteiro['nome'];
                }
            }

            // Armazena os porteiros disponíveis para o dia de culto
            if (!empty($porteirosDisponiveis)) {
                for ($week = 0; $week < 6; $week++) {
                    // Aplica a porcentagem de aleatoriedade
                    if (rand(0, 100) < $percentualAleatoriedade) {
                        shuffle($porteirosDisponiveis); // Embaralha o array para seleção aleatória
                    }
                    // Seleciona até o número correto de porteiros disponíveis para este dia de culto em cada semana
                    $atribuicoes[$diaCulto][$week] = array_slice($porteirosDisponiveis, 0, $numeroPorteiros);
                }
            }
        }

        // Atribuição específica para "SundayR"
        $atribuicoes['SundayR'] = [];
        $porteirosDisponiveisSundayR = [];
        foreach ($porteiros as $porteiro) {
            if (in_array('SundayR', $porteiro['dias_trabalho'])) {
                $porteirosDisponiveisSundayR[] = $porteiro['nome'];
            }
        }

        // Armazena os porteiros disponíveis para SundayR e aplica aleatoriedade
        if (!empty($porteirosDisponiveisSundayR)) {
            for ($week = 0; $week < 6; $week++) {
                // Aplica a porcentagem de aleatoriedade
                if (rand(0, 100) < $percentualAleatoriedade) {
                    shuffle($porteirosDisponiveisSundayR);
                }
                // Seleciona até dois porteiros disponíveis para SundayR em cada semana
                $atribuicoes['SundayR'][$week] = array_slice($porteirosDisponiveisSundayR, 0, 2);
            }
        }

        return $atribuicoes;
    }


    // Função para gerar o calendário
    function gerarCalendario($mesInicio, $anoInicio, $mesFim, $anoFim, $igreja, $porteiros, $percentualAleatoriedade)
    {
        $mesAtual = $mesInicio;
        $anoAtual = $anoInicio;
        echo "    <style>
        .page {
     page-break-after: always;
     margin-bottom: 50px; /* Espaço entre páginas */

  
   
 }
     </style>";
        while ($anoAtual < $anoFim || ($anoAtual == $anoFim && $mesAtual <= $mesFim)) {

            echo "<div class='page'>";
            setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
            echo "<h2 style='text-align: center;'>Calendário de " . mb_convert_case(strftime("%B", mktime(0, 0, 0, $mesAtual, 1, $anoAtual)), MB_CASE_UPPER, "UTF-8") . " de $anoAtual - Igreja: " . $igreja['nome'] . "</h2>";

            echo "<div style='width: 100%; max-width: 21cm; margin: 0 auto;'>"; // Tamanho A4 aproximado, margem automática
            echo "<table style='border: 1px solid #ccc; width: 100%; table-layout: fixed;'>";
            echo "<tr><th style='padding: 10px; border: 1px solid #ccc; width: 12%;'><h5>DomingoRJM</h5></th><th style='padding: 10px; border: 1px solid #ccc; width: 12%;'>Domingo</th><th style='padding: 10px; border: 1px solid #ccc; width: 12%;'>Segunda</th><th style='padding: 10px; border: 1px solid #ccc; width: 12%;'>Terça</th><th style='padding: 10px; border: 1px solid #ccc; width: 12%;'>Quarta</th><th style='padding: 10px; border: 1px solid #ccc; width: 12%;'>Quinta</th><th style='padding: 10px; border: 1px solid #ccc; width: 12%;'>Sexta</th><th style='padding: 10px; border: 1px solid #ccc; width: 12%;'>Sábado</th></tr>";

            // Definir o primeiro dia do mês
            $firstDay = mktime(0, 0, 0, $mesAtual, 1, $anoAtual);

            // Número de dias no mês
            $numDays = date("t", $firstDay);

            // Dia da semana em que o mês começa (0 = Domingo, 6 = Sábado)
            $startDay = date("w", $firstDay);

            // Contador de dias
            $dayCount = 1;

            // Atribuir porteiros aos dias de culto com a porcentagem de aleatoriedade
            $atribuicoesPorteiros = atribuirPorteiros($igreja, $porteiros, $percentualAleatoriedade);

            // Loop para criar as linhas do calendário
            for ($row = 0; $row < 6; $row++) {
                echo "<tr>";
                for ($col = 0; $col < 8; $col++) {
                    if ($dayCount > $numDays) {
                        break;
                    } elseif ($row == 0 && $col < $startDay + 1) { // Ajuste +1 por causa da nova coluna "DomingoR"
                        echo "<td style='padding: 10px; border: 1px solid #ccc; width: 12%;'></td>";
                    } else {
                        $currentDate = date("Y-m-d", mktime(0, 0, 0, $mesAtual, $dayCount, $anoAtual));
                        $dayOfWeek = date("l", strtotime($currentDate));
                        $week = intval(date("W", strtotime($currentDate))) % 6;

                        // Verificar se é dia de culto
                        $porteirosSelecionados = [];
                        if (in_array($dayOfWeek, $igreja['dias_culto'])) {
                            if (isset($atribuicoesPorteiros[$dayOfWeek][$week])) {
                                $porteirosSelecionados = $atribuicoesPorteiros[$dayOfWeek][$week];
                            }
                        }

                        // Verificar se é dia de reunião em algum domingo (SundayR)
                        if ($col == 0) { // Primeira coluna é "DomingoR"
                            $porteirosSelecionadosSundayR = isset($atribuicoesPorteiros['SundayR'][$week]) ? $atribuicoesPorteiros['SundayR'][$week] : [];

                            echo "<td style='padding: 10px; border: 1px solid #ccc; vertical-align: top; width: 12%;'>";
                            echo "<div style='font-weight: bold;'>$dayCount</div>";

                            if (!empty($porteirosSelecionadosSundayR)) {
                                echo "<div style='margin-top: 5px;' >";
                                foreach ($porteirosSelecionadosSundayR as $key => $porteiro) {
                                    if ($key > 0) {
                                        echo "<br>";
                                    }
                                    echo "<div style='font-size: 15px;' >• " . $porteiro . "</div>";
                                }
                                echo "</div>";
                            }
                            echo "</td>";
                        } else {
                            echo "<td style='padding: 10px; border: 1px solid #ccc; vertical-align: top; width: 12%;'>";
                            echo "<div style='font-weight: bold;'>$dayCount</div>";

                            if (!empty($porteirosSelecionados)) {
                                echo "<div style='margin-top: 5px;'>";
                                foreach ($porteirosSelecionados as $key => $porteiro) {
                                    if ($key > 0) {
                                        //echo "<br>";
                                    }
                                    echo "<div style='font-size: 15px;' >• " . $porteiro . "</div>";
                                }
                                echo "</div>";
                            }
                            echo "</td>";
                            $dayCount++;
                        }
                    }
                }
                echo "</tr>";
            }

            echo "</table>";
            echo "</div>"; // Fechando a div que controla a largura da tabela
            echo "<br>";
            echo "<br>";
            echo "• Porta Frente <br>";
            echo "• Porta Lateral <br>";
            echo "• Porta Galeria <br>";
            echo "</div>";
            // Atualizar mês e ano para o próximo mês
            if ($mesAtual == 12) {
                $mesAtual = 1;
                $anoAtual++;
            } else {
                $mesAtual++;
            }
        }


      
        // Definir o charset UTF-8 para PHP
    
        
        // Incluir o arquivo de conexão
        require 'conexao.php';
        
        // Query SQL para selecionar nomes e telefones dos porteiros ordenados por nome
        $sql = "SELECT nome, telefone FROM porteiros ORDER BY nome ASC";
        
        // Executar a query
        $resultado = $conn->query($sql);
     echo "<center>";
        // Verificar se há resultados
        if ($resultado->num_rows > 0) {
         
            echo '<html>
        
            <body>
          
                <table style="width: 50%; border-collapse: collapse; margin-top: 20px;">
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #ddd;">Nome</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #ddd;">Telefone</th>
                    </tr>';
        
            // Loop através dos resultados
            while ($row = $resultado->fetch_assoc()) {
                // Imprimir cada linha da tabela
                echo '<tr style="border-bottom: 1px solid #ddd;">' .
                '<td style="padding: 10px; text-align: left;">' . $row['nome'] . '</td>' .
                '<td style="padding: 10px; text-align: left;">(' . substr($row['telefone'], 0, 2) . ') ' . substr($row['telefone'], 2, 4) . '-' . substr($row['telefone'], 6) . '</td>' .
            '</tr>';
       
            }
        
            // Fim da tabela HTML
            echo '</table>
           
            </body>
            </html>';
        } else {
            // Caso não haja resultados
            echo 'Nenhum registro encontrado.';
        }
      
        echo "</center>";

    }


    // Gerar o calendário com base nas datas fornecidas
    ob_start();
    gerarCalendario($mesInicio, $anoInicio, $mesFim, $anoFim, $igrejas[$igrejaId], $porteiros, $percentualAleatoriedade);
    $html = ob_get_clean();

    // Cria uma nova instância do domPDF
    $dompdf = new Dompdf();

    // Carrega o HTML no domPDF
    $dompdf->loadHtml($html);

    // Renderiza o PDF
    $dompdf->render();

    // Saída do PDF para o navegador
    $dompdf->stream("calendario.pdf", array("Attachment" => false));
} else {
    // Exibe o formulário para seleção de igreja e datas
    echo '<form method="post" action="">
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
}

// Fechar conexão com o banco de dados
$conn->close();
