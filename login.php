<?php
// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'pedidos_db';
$username = 'root';
$password = '051080';

session_start(); // Inicia a sessão

$mensagemErro = ""; // Inicializa a variável de mensagem de erro

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Coletar os dados do formulário
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Consultar o usuário no banco de dados
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar se a senha está correta
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id']; // Salva o ID do usuário na sessão
            $_SESSION['usuario_nome'] = $usuario['nome']; // Salva o nome do usuário na sessão
            header("Location: index.php"); // Redireciona para a página principal
            exit();
        } else {
            $mensagemErro = "Email ou senha incorretos.";
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
    <title>Login</title>
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

        <h2>Login</h2>

        <?php if ($mensagemErro): ?>
            <div class="alert alert-danger"><?php echo $mensagemErro; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <!-- Botão de registro -->
        <div class="mt-3">
            <a href="register.php" class="btn btn-secondary">Se registrar</a>
        </div>
    </div>
</body>
</html>
