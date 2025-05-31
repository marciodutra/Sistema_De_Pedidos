<?php
// config.php
session_start(); // Inicia a sessão

// Conexão com o banco de dados
$host = 'sql106.infinityfree.com';
$dbname = 'if0_36007209_pedidos_db';
$username = 'if0_36007209';
$password = 'KEYZxkMa9uV4C';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
