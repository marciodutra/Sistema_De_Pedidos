<?php
// config.php
session_start(); // Inicia a sessão

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
?>
