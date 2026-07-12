<?php
// api/db.php

$host = 'localhost';
$dbname = 'xptotec_salao';
$username = 'root'; // Altere para o seu usuário do MySQL
$password = '';     // Altere para a sua senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configura o PDO para lançar exceções em caso de erros (ótimo para debugar)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Retorna os dados sempre como array associativo
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Como é uma API, se der erro de banco, retornamos um JSON com o erro
    header('Content-Type: application/json');
    echo json_encode(["erro" => "Falha na conexão com o banco de dados", "detalhe" => $e->getMessage()]);
    exit;
}
?>