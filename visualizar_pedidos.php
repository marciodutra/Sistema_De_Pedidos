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

// Verificar se há pesquisa
$searchQuery = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : '');

// Verificar se o pedido deve ser marcado como pago
if (isset($_GET['pago_id'])) {
    $pago_id = $_GET['pago_id'];
    
    try {
        // Atualizar o status do pedido para 'Pago'
        $update_sql = "UPDATE pedidos SET status = 'Pago' WHERE id = :id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindParam(':id', $pago_id, PDO::PARAM_INT);
        
        if ($update_stmt->execute()) {
            // Redirecionar para evitar reenvio do formulário e preservar a pesquisa
            header('Location: visualizar_pedidos.php?search=' . urlencode($searchQuery));
            exit();
        } else {
            echo "Erro ao marcar o pedido como pago.";
        }
    } catch (PDOException $e) {
        echo "Erro ao atualizar o status: " . $e->getMessage();
    }
}

// Verificar se o pedido deve ser excluído
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    try {
        // Excluir o pedido do banco de dados
        $delete_sql = "DELETE FROM pedidos WHERE id = :id";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
        
        if ($delete_stmt->execute()) {
            // Redirecionar após a exclusão e preservar a pesquisa
            header('Location: visualizar_pedidos.php?search=' . urlencode($searchQuery));
            exit();
        } else {
            echo "Erro ao excluir o pedido.";
        }
    } catch (PDOException $e) {
        echo "Erro ao excluir o pedido: " . $e->getMessage();
    }
}

// Inicializar pedidos e total
$pedidos = [];
$totalPedidosCliente = 0;

// Verificar se há pesquisa
if (!empty($searchQuery)) {
    try {
        // Consultar pedidos com filtro pelo nome do cliente
        $stmt = $conn->prepare("SELECT * FROM pedidos WHERE nome_cliente LIKE :search");
        $stmt->bindValue(':search', "%$searchQuery%");
        $stmt->execute();
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcular o total dos pedidos do cliente
        $stmtTotal = $conn->prepare("SELECT SUM(total) AS total_cliente FROM pedidos WHERE nome_cliente LIKE :search");
        $stmtTotal->bindValue(':search', "%$searchQuery%");
        $stmtTotal->execute();
        $totalPedidosCliente = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_cliente'];
    } catch (PDOException $e) {
        echo "Erro ao buscar pedidos: " . $e->getMessage();
    }
} else {
    // Se não houver pesquisa, trazer todos os pedidos
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
            justify-content: flex-end; /* Alinha o total à direita */
            margin-top: 20px; /* Adiciona um espaço acima do total */
        }
        .total-alert {
            background-color: #f8d7da; /* Cor de fundo vermelho clara */
            color: #721c24; /* Cor do texto em vermelho escuro */
            padding: 20px; /* Adiciona preenchimento ao redor do texto */
            border-radius: 5px; /* Cantos arredondados */
            width: 250px; /* Largura fixa para o total */
            text-align: center; /* Centraliza o texto */
            font-size: 1.5em; /* Aumenta o tamanho da fonte */
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
        <h2>Visualizar Pedidos</h2>
        
        <!-- Formulário de Pesquisa -->
        <form action="visualizar_pedidos.php" method="POST" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Pesquisar pelo nome do cliente" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="btn btn-primary" type="submit">Pesquisar</button>
                <button class="btn btn-danger" type="submit" name="clear_all">Limpar Todos</button> <!-- Botão para limpar todos os pedidos -->
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
                <div class="total-alert">
                    Total dos Pedidos do Cliente: <?php echo number_format($totalPedidosCliente, 2, ',', '.'); ?>
                </div>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
