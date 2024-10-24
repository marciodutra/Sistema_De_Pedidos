<?php
// Definindo o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'pedidos_db'; // Nome do banco de dados
$username = 'root';
$password = '051080';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

// Consultar vendas agrupadas por setor
$query = "SELECT setor, SUM(total) AS total_vendas, COUNT(*) AS quantidade_vendas 
          FROM pedidos 
          GROUP BY setor";

$pedidos = [];
try {
    $stmt = $conn->query($query);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar vendas: " . $e->getMessage();
}

// Gerar Word
if (isset($_POST['gerar_word'])) {
    // Criar o conteúdo do arquivo DOCX
    $zip = new ZipArchive();
    $filename = "relatorio_vendas.docx";

    if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
        exit("Não foi possível abrir <$filename>\n");
    }

    // Adiciona o conteúdo XML do Word
    $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
    <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
        <w:body>
            <w:p>
                <w:r>
                    <w:rPr>
                        <w:sz w:val="48"/> <!-- Tamanho da fonte maior -->
                        <w:color w:val="FF0000"/> <!-- Cor vermelha -->
                    </w:rPr>
                    <w:t>Relatório de Vendas por Setor</w:t>
                </w:r>
            </w:p>
            <w:p>
                <w:r>
                    <w:rPr>
                        <w:sz w:val="36"/> <!-- Aumenta a fonte da data e hora -->
                        <w:color w:val="FF0000"/> <!-- Cor vermelha para data e hora -->
                    </w:rPr>
                    <w:t>Data: ' . date('d/m/Y H:i:s') . '</w:t>
                </w:r>
            </w:p>
            <w:p>
                <w:r>
                    <w:t></w:t>
                </w:r>
            </w:p>
            <w:tbl>
                <w:tr>
                    <w:tc><w:p><w:r><w:t>Setor</w:t></w:r></w:p></w:tc>
                    <w:tc><w:p><w:r><w:t>Total de Vendas</w:t></w:r></w:p></w:tc>
                    <w:tc><w:p><w:r><w:t>Quantidade de Vendas</w:t></w:r></w:p></w:tc>
                </w:tr>');

    // Adicionando os dados à tabela
    foreach ($pedidos as $pedido) {
        $zip->addFromString('word/document.xml', '<w:tr>
            <w:tc><w:p><w:r><w:t>' . htmlspecialchars($pedido['setor']) . '</w:t></w:r></w:p></w:tc>
            <w:tc><w:p><w:r><w:t>' . number_format($pedido['total_vendas'], 2, ',', '.') . '</w:t></w:r></w:p></w:tc>
            <w:tc><w:p><w:r><w:t>' . $pedido['quantidade_vendas'] . '</w:t></w:r></w:p></w:tc>
        </w:tr>');
    }

    // Fecha a tabela e o arquivo
    $zip->addFromString('word/document.xml', '</w:tbl></w:body></w:document>');

    $zip->close();

    // Enviar o arquivo para download
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    readfile($filename);
    unlink($filename); // Remove o arquivo temporário após o download
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            /* Oculta os botões e links durante a impressão */
            .no-print {
                display: none;
            }
        }
        h2 {
            font-size: 36px; /* Aumentar o tamanho do título */
            color: red; /* Título em vermelho */
            text-align: center; /* Centraliza o título */
        }
        .date-time {
            font-size: 24px; /* Aumentar o tamanho da data e hora */
            color: red; /* Data e hora em vermelho */
            text-align: center; /* Centraliza a data e hora */
        }
        .table {
            margin: 0 auto; /* Centraliza a tabela */
            text-align: center; /* Centraliza o conteúdo da tabela */
        }
        .table th, .table td {
            font-size: 20px; /* Aumentar o tamanho das células da tabela */
            color: red; /* Cor vermelha para as células */
        }

        .logo {
            max-width: 200px; /* Largura máxima do logo */
            margin-bottom: 20px; /* Espaço abaixo do logo */
        }
    </style>
</head>
<body>
<div class="text-center mb-4">
            <img src="logo.png" alt="Logo" style="max-width: 200px;"> <!-- Ajuste o tamanho conforme necessário -->
        </div>
    <div class="container mt-5">
        <h2>Relatório de Vendas por Setor</h2>
        <p class="date-time">Data: <?php echo date('d/m/Y H:i:s'); ?></p> <!-- Exibir data e hora -->
        <table class="table">
            <thead>
                <tr>
                    <th>Setor</th>
                    <th>Total de Vendas</th>
                    <th>Quantidade de Vendas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pedido['setor']); ?></td>
                        <td><?php echo number_format($pedido['total_vendas'], 2, ',', '.'); ?></td>
                        <td><?php echo $pedido['quantidade_vendas']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <button class="btn btn-primary no-print" onclick="window.print()">Imprimir Relatório</button>
        <a href="index.php" class="btn btn-secondary no-print">Voltar</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
