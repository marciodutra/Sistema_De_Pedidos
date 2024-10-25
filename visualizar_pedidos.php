<?php
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

// Inicializar as variáveis
$searchQuery = '';
$pedidos = [];
$totalPedidosCliente = 0; // Inicializar total dos pedidos do cliente
$totalComDesconto = 0; // Inicializar total com desconto

// Verificar se há pesquisa
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
} elseif (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

// Marcar um pedido como Pago
if (isset($_GET['pago_id'])) {
    $pedidoId = $_GET['pago_id'];
    try {
        $stmtPago = $conn->prepare("UPDATE pedidos SET status = 'Pago' WHERE id = :id");
        $stmtPago->bindValue(':id', $pedidoId, PDO::PARAM_INT);
        $stmtPago->execute();
        header("Location: visualizar_pedidos.php?search=" . urlencode($searchQuery));
        exit();
    } catch (PDOException $e) {
        echo "Erro ao marcar como Pago: " . $e->getMessage();
    }
}

// Marcar todos os pedidos do cliente como Pago
if (isset($_POST['mark_all_paid']) && !empty($searchQuery)) {
    try {
        $stmtPagoTodos = $conn->prepare("UPDATE pedidos SET status = 'Pago' WHERE nome_cliente LIKE :search");
        $stmtPagoTodos->bindValue(':search', "%$searchQuery%");
        $stmtPagoTodos->execute();
        header("Location: visualizar_pedidos.php?search=" . urlencode($searchQuery));
        exit();
    } catch (PDOException $e) {
        echo "Erro ao marcar todos como Pago: " . $e->getMessage();
    }
}

// Aplicar 50% de desconto
if (isset($_POST['apply_discount']) && !empty($searchQuery)) {
    try {
        $stmtTotal = $conn->prepare("SELECT SUM(total) AS total_cliente FROM pedidos WHERE nome_cliente LIKE :search");
        $stmtTotal->bindValue(':search', "%$searchQuery%");
        $stmtTotal->execute();
        $totalPedidosCliente = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_cliente'];
        $totalComDesconto = $totalPedidosCliente * 0.5;
    } catch (PDOException $e) {
        echo "Erro ao calcular o desconto: " . $e->getMessage();
    }
}

// Consultar pedidos
if (!empty($searchQuery)) {
    try {
        $stmt = $conn->prepare("SELECT * FROM pedidos WHERE nome_cliente LIKE :search");
        $stmt->bindValue(':search', "%$searchQuery%");
        $stmt->execute();
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtTotal = $conn->prepare("SELECT SUM(total) AS total_cliente FROM pedidos WHERE nome_cliente LIKE :search");
        $stmtTotal->bindValue(':search', "%$searchQuery%");
        $stmtTotal->execute();
        $totalPedidosCliente = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_cliente'];
    } catch (PDOException $e) {
        echo "Erro ao buscar pedidos: " . $e->getMessage();
    }
} else {
    try {
        $stmt = $conn->query("SELECT * FROM pedidos");
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erro ao buscar pedidos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .total-container {
            display: flex;
            justify-content: center; /* Centraliza o conteúdo */
            margin-top: 20px;
        }
        .total-alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 5px;
            width: 300px; /* Aumenta a largura */
            text-align: center;
            font-size: 2em; /* Aumenta o tamanho da fonte */
            font-weight: bold; /* Destaca o texto */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Visualizar Pedidos</h2>

        <!-- Formulário de Pesquisa -->
        <form action="visualizar_pedidos.php" method="POST" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Pesquisar pelo nome do cliente" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="btn btn-primary" type="submit">Pesquisar</button>
                <button class="btn btn-danger" type="submit" name="clear_all">Limpar Todos</button>
                <?php if (!empty($searchQuery)): ?>
                    <button class="btn btn-success" type="submit" name="mark_all_paid">Marcar Todos como Pago</button>
                    <button class="btn btn-warning" type="submit" name="apply_discount">Aplicar 50% de Desconto</button>
                <?php endif; ?>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Cliente</th>
                    <th>Setor</th>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?php echo $pedido['id']; ?></td>
                        <td><?php echo htmlspecialchars($pedido['nome_cliente']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['setor']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['produto']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['quantidade']); ?></td>
                        <td><?php echo number_format($pedido['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($pedido['status']); ?></td>
                        <td>
                            <?php if ($pedido['status'] != 'Pago'): ?>
                                <a href="visualizar_pedidos.php?pago_id=<?php echo $pedido['id']; ?>&search=<?php echo urlencode($searchQuery); ?>" class="btn btn-danger">Marcar como Pago</a>
                            <?php else: ?>
                                <button class="btn btn-success" disabled>Pago</button>
                            <?php endif; ?>
                            <a href="visualizar_pedidos.php?delete_id=<?php echo $pedido['id']; ?>&search=<?php echo urlencode($searchQuery); ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este pedido?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Exibir o total de todos os pedidos do cliente pesquisado -->
        <?php if ($totalPedidosCliente > 0): ?>
            <div class="total-container">
                <div class="total-alert" style="margin-right: 20px;">
                    Total dos Pedidos do Cliente: <?php echo number_format($totalPedidosCliente, 2, ',', '.'); ?>
                </div>
                <?php if (isset($totalComDesconto) && $totalComDesconto > 0): ?>
                    <div class="total-alert">
                        Total com 50% de Desconto: <?php echo number_format($totalComDesconto, 2, ',', '.'); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
