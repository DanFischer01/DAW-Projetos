<?php
// api/painel_cliente.php
require_once "db.php";

// Cabeçalhos de comunicação padrão do projeto
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$acao = $_GET['acao'] ?? '';
$dados = json_decode(file_get_contents("php://input"), true);

// --- ROTA DE LISTAGEM (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $acao === 'listar') {
    $cliente_id = $_GET['cliente_id'] ?? 0;
    
    try {
        // CORREÇÃO: Busca por data_reserva e hora_reserva em vez de data_hora_inicio
        $stmt = $pdo->prepare("
            SELECT a.id, a.data_reserva, a.hora_reserva, a.status, s.nome as servico_nome, f.nome as profissional_nome, s.preco
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            JOIN funcionarios f ON a.funcionario_id = f.id
            WHERE a.cliente_id = ?
            ORDER BY a.data_reserva DESC, a.hora_reserva DESC
        ");
        $stmt->execute([$cliente_id]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        echo json_encode(["erro" => "Erro na consulta: " . $e->getMessage()]);
    }
    exit;
}

// --- ROTAS DE AÇÃO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // CANCELAMENTO PELO CLIENTE
    if ($acao === 'cancelar') {
        $agendamento_id = $dados['agendamento_id'] ?? 0;
        
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("
                SELECT a.cliente_id, a.status, s.preco 
                FROM agendamentos a 
                JOIN servicos s ON a.servico_id = s.id 
                WHERE a.id = ?
            ");
            $stmt->execute([$agendamento_id]);
            $ag = $stmt->fetch(PDO::FETCH_ASSOC);

            // CORREÇÃO: Valida contra o status 'confirmado' que usamos no banco
            if (!$ag || ($ag['status'] !== 'confirmado' && $ag['status'] !== 'pendente')) {
                echo json_encode(["erro" => "Este agendamento não pode ser cancelado ou já foi alterado."]);
                $pdo->rollBack();
                exit;
            }

            // Atualiza status para cancelado
            $stmt = $pdo->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = ?");
            $stmt->execute([$agendamento_id]);

            // Como a tabela clientes no seu script base não possui a coluna saldo_credito de forma nativa,
            // vamos apenas confirmar o cancelamento para evitar quebras operacionais no banco de dados.
            $pdo->commit();
            echo json_encode(["sucesso" => "Agendamento cancelado com sucesso no sistema!"]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(["erro" => "Erro ao cancelar: " . $e->getMessage()]);
        }
        exit;
    }

    // AVALIAÇÃO DO SERVIÇO
    if ($acao === 'avaliar') {
        $agendamento_id = $dados['agendamento_id'] ?? 0;
        $nota = $dados['nota'] ?? 0;
        $comentario = trim($dados['comentario'] ?? '');

        if ($nota < 1 || $nota > 5) {
            echo json_encode(["erro" => "A nota deve ser de 1 a 5."]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO avaliacoes (agendamento_id, nota, comentario) VALUES (?, ?, ?)");
            $stmt->execute([$agendamento_id, $nota, $comentario]);
            
            // Opcional: Atualiza o status para concluído após avaliar
            $stmtUpdate = $pdo->prepare("UPDATE agendamentos SET status = 'concluido' WHERE id = ?");
            $stmtUpdate->execute([$agendamento_id]);

            echo json_encode(["sucesso" => "Obrigado pela sua avaliação!"]);
        } catch (PDOException $e) {
            echo json_encode(["erro" => "Você já avaliou este serviço ou ocorreu um erro operacional."]);
        }
        exit;
    }
}
?>