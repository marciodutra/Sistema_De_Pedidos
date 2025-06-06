<?php
// inserir_pedido.php

$host = 'sql106.infinityfree.com';
$dbname = 'if0_36007209_pedidos_db';
$username = 'if0_36007209';
$password = 'KEYZxkMa9uV4C';

try {
    // Conectar ao banco de dados
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Receber dados do formulário
    $setor = $_POST['setor'];
    $produto = $_POST['produto'];
    $quantidade = $_POST['quantidade'];
    $preco = $_POST['preco'];

    // Calcular o total
    $total = $quantidade * $preco;

    // Inserir dados na tabela
    $sql = "INSERT INTO pedidos (setor, produto, quantidade, preco, total) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$setor, $produto, $quantidade, $preco, $total]);

    // Redirecionar para a página de visualização
    header('Location: visualizar_pedidos.php');
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
