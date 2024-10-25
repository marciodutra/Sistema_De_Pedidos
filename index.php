<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário';
$mensagemSucesso = "";

$host = 'localhost';
$dbname = 'pedidos_db';
$username = 'root';
$password = '051080';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome_cliente = $_POST['nome_cliente'];
        $setor = $_POST['setor'];
        $produto = $_POST['produto'];
        $quantidade = $_POST['quantidade'];
        $preco = $_POST['preco'];
        $total = $quantidade * $preco;
        $status = 'Pendente';
        
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
            $mensagemSucesso = "Pedido cadastrado com sucesso!";
        } else {
            $mensagemSucesso = "Erro ao cadastrar o pedido.";
        }
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
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
            color: green;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .logout-container {
            position: absolute;
            top: 10px;
            right: 20px;
            text-align: right;
        }
        .logo-container img {
            max-width: 150px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center logo-container mb-4">
            <img src="logo.png" alt="Logo">
        </div>

        <div class="form-container">
            <h2 class="text-center">Cadastrar Pedido</h2>
            
            <div class="logout-container">
                <p><strong>Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>!</strong></p>
                <form action="index.php" method="POST">
                    <button type="submit" name="logout" class="btn btn-danger">Deslogar</button>
                </form>
            </div>

            <?php if ($mensagemSucesso): ?>
                <div class="mensagem-sucesso text-center"><?php echo $mensagemSucesso; ?></div>
            <?php endif; ?>

            <div class="text-center mb-3">
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
                    <label for="preco" class="form-label">Preço</label>
                    <input type="number" class="form-control" id="preco" name="preco" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Cadastrar Pedido</button>
            </form>
        </div>
    </div>
</body>
</html>
