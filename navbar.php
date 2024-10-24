<?php
session_start(); // Certifique-se de iniciar a sessão para acessar o nome do usuário
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="path/to/logo.png" alt="Logotipo" width="30" height="24" class="d-inline-block align-text-top">
            Nome da Empresa
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <!-- Adicione outros links de navegação aqui, se necessário -->
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                </li>
                <!-- Exemplo de link adicional -->
                <li class="nav-item">
                    <a class="nav-link" href="pagina.php">Outra Página</a>
                </li>
            </ul>
            <div class="d-flex">
                <?php if (isset($_SESSION['usuario_nome'])): ?>
                    <span class="navbar-text me-3">
                        Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                    </span>
                    <form action="logout.php" method="POST">
                        <button type="submit" class="btn btn-danger">Deslogar</button>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Inclua o Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
