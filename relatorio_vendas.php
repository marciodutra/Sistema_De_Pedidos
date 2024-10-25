<?php
// Definindo o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'pedidos_db';
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

// Consulta para obter os totais de todas as vendas e quantidade total
$query_totals = "SELECT SUM(total) AS total_geral, COUNT(*) AS quantidade_geral FROM pedidos";
try {
    $stmt_totals = $conn->query($query_totals);
    $totals = $stmt_totals->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao calcular total geral: " . $e->getMessage();
}

// Gerar Word
// (O código para gerar o Word permanece o mesmo)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none; } }
        h2 { font-size: 36px; color: red; text-align: center; }
        .date-time { font-size: 24px; color: red; text-align: center; }
        .table { margin: 0 auto; text-align: center; }
        .table th, .table td { font-size: 20px; color: red; }
        .logo { max-width: 200px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="text-center mb-4">
        <img src="logo.png" alt="Logo" style="max-width: 200px;">
    </div>
    <div class="container mt-5">
        <h2>Relatório de Vendas por Setor</h2>
        <p class="date-time">Data: <?php echo date('d/m/Y H:i:s'); ?></p>
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
                <tr>
                    <td><strong>Total Geral</strong></td>
                    <td><strong><?php echo number_format($totals['total_geral'], 2, ',', '.'); ?></strong></td>
                    <td><strong><?php echo $totals['quantidade_geral']; ?></strong></td>
                </tr>
            </tbody>
        </table>
        
        <button class="btn btn-primary no-print" onclick="window.print()">Imprimir Relatório</button>
        <a href="index.php" class="btn btn-secondary no-print">Voltar</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
