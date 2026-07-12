<?php
// api/servicos.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once 'db.php';

try {
    // Busca apenas os serviços ativos para mostrar na Home e no Agendamento
    $stmt = $pdo->prepare("SELECT id, nome, descricao, categoria, duracao_minutos, preco FROM servicos WHERE ativo = 1 ORDER BY nome ASC");
    $stmt->execute();
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($servicos);
} catch (PDOException $e) {
    echo json_encode(["erro" => "Erro ao carregar serviços: " . $e->getMessage()]);
}
?>