<?php
// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'pedidos_db';
$username = 'root';
$password = '051080';

$mensagemSucesso = ""; // Inicializa a variável de mensagem de sucesso
$mensagemErro = ""; // Inicializa a variável de mensagem de erro

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Coletar os dados do formulário
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografar a senha

        // Inserir o usuário no banco de dados
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        
        if ($stmt->execute()) {
            $mensagemSucesso = "Usuário cadastrado com sucesso!";
        } else {
            $mensagemErro = "Erro ao cadastrar o usuário.";
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
    <title>Cadastrar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logo-container {
            margin-bottom: 30px; /* Espaçamento abaixo do logo */
            text-align: left; /* Alinhamento à esquerda */
        }
        .logo-container img {
            max-width: 150px; /* Ajuste o tamanho máximo do logo */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Seção do Logo -->
        <div class="logo-container">
            <img src="logo.png" alt="Logo da Empresa"> <!-- Substitua pelo caminho do seu logo -->
        </div>

        <h2>Cadastrar Usuário</h2>

        <?php if ($mensagemSucesso): ?>
            <div class="alert alert-success"><?php echo $mensagemSucesso; ?></div>
            <a href="login.php" class="btn btn-primary">Fazer Login</a>
        <?php else: ?>
            <?php if ($mensagemErro): ?>
                <div class="alert alert-danger"><?php echo $mensagemErro; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn btn-success">Cadastrar</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
