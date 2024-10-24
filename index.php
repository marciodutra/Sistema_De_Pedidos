<?php
// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'pedidos_db';
$username = 'root';
$password = '051080';

$mensagemSucesso = ""; // Inicializa a variável de mensagem de sucesso

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Coletar os dados do formulário
        $nome_cliente = $_POST['nome_cliente'];
        $setor = $_POST['setor'];
        $produto = $_POST['produto'];
        $quantidade = $_POST['quantidade'];
        $preco = $_POST['preco'];
        $total = $quantidade * $preco;
        $status = 'Pendente'; // Status inicial
        
        // Inserir o pedido no banco de dados
        $sql = "INSERT INTO pedidos (nome_cliente, setor, produto, quantidade, preco, total, status) 
                VALUES (:nome_cliente, :setor, :produto, :quantidade, :preco, :total, :status)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome_cliente', $nome_cliente);
        $stmt->bindParam(':setor', $setor);
        $stmt->bindParam(':produto', $produto);
        $stmt->bindParam(':quantidade', $quantidade);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':status', $status);
        
        if ($stmt->execute()) {
            $mensagemSucesso = "Pedido cadastrado com sucesso!"; // Atribui a mensagem de sucesso
        } else {
            $mensagemSucesso = "Erro ao cadastrar o pedido.";
        }
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .mensagem-sucesso {
            color: green; /* Cor verde */
            font-size: 2rem; /* Tamanho da fonte maior */
            font-weight: bold; /* Negrito */
            margin-bottom: 20px; /* Margem inferior */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Cadastrar Pedido</h2>

        <?php if ($mensagemSucesso): ?>
            <div class="mensagem-sucesso"><?php echo $mensagemSucesso; ?></div> <!-- Mensagem de sucesso -->
        <?php endif; ?>

        <!-- Botões para navegação -->
        <div class="mb-3">            
            <a href="visualizar_pedidos.php" class="btn btn-secondary">Visualizar Pedidos</a>
            <a href="relatorio_vendas.php" class="btn btn-info">Visualizar Relatório</a>
        </div>
        
        <form action="index.php" method="POST">
            <div class="mb-3">
                <label for="nome_cliente" class="form-label">Nome do Cliente</label>
                <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" required>
            </div>
            <div class="mb-3">
                <label for="setor" class="form-label">Setor</label>
                <select class="form-control" id="setor" name="setor" required>
                    <option value="" disabled selected>Selecione um setor</option>
                    <option value="Bazar">Bazar</option>
                    <option value="Brechó">Brechó</option>
                    <option value="Feirão">Feirão</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="produto" class="form-label">Produto</label>
                <input type="text" class="form-control" id="produto" name="produto" value="Peças" readonly required>
            </div>
            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input type="number" class="form-control" id="quantidade" name="quantidade" required>
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço Unitário</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
            </div>
            <button type="submit" class="btn btn-success">Cadastrar Pedido</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>