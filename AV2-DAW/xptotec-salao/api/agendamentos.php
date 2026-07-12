<?php
// api/agendamentos.php
header("Content-Type: application/json");
require_once "db.php";

$acao = $_GET['acao'] ?? '';
$dados = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // 1. Buscar profissionais (Corrigido para listar profissionais ativos)
    if ($acao === 'buscar_profissionais') {
        $stmt = $pdo->query("SELECT id, nome, especialidade FROM funcionarios WHERE cargo = 'profissional' ORDER BY nome ASC");
        echo json_encode($stmt->fetchAll());
        exit;
    }

    // 2. Buscar horários disponíveis cruzando durações
    if ($acao === 'horarios_disponiveis') {
        $funcionario_id = $_GET['funcionario_id'] ?? 0;
        $data = $_GET['data'] ?? '';
        $servico_id = $_GET['servico_id'] ?? 0;

        if (!$funcionario_id || !$data || !$servico_id) {
            echo json_encode(["erro" => "Dados insuficientes."]);
            exit;
        }

        // Pega a duração do serviço que o cliente quer agendar
        $stmt = $pdo->prepare("SELECT duracao_minutos FROM servicos WHERE id = ?");
        $stmt->execute([$servico_id]);
        $servico = $stmt->fetch();
        $duracao_desejada = $servico ? (int)$servico['duracao_minutos'] : 60;

        // Pega os agendamentos daquele dia E a duração de cada serviço agendado
        $stmt = $pdo->prepare("
            SELECT a.hora_reserva, s.duracao_minutos 
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            WHERE a.funcionario_id = ? AND a.data_reserva = ? AND a.status != 'cancelado'
        ");
        $stmt->execute([$funcionario_id, $data]);
        $agendamentos_existentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $horarios_livres = [];
        $inicio_expediente = strtotime("09:00");
        $fim_expediente = strtotime("18:00");
        $hora_atual = $inicio_expediente;

        while (($hora_atual + ($duracao_desejada * 60)) <= $fim_expediente) {
            $cand_inicio = $hora_atual;
            $cand_fim = $hora_atual + ($duracao_desejada * 60);
            $hora_formatada = date("H:i", $hora_atual);
            
            $conflito = false;
            foreach ($agendamentos_existentes as $ag) {
                $ag_inicio = strtotime($ag['hora_reserva']);
                $ag_fim = $ag_inicio + ($ag['duracao_minutos'] * 60);

                // Lógica real de sobreposição (overlap)
                if ($cand_inicio < $ag_fim && $cand_fim > $ag_inicio) {
                    $conflito = true;
                    break;
                }
            }

            if (!$conflito) {
                $horarios_livres[] = $hora_formatada;
            }
            
            // Incrementa de 30 em 30 min para gerar a grade de horários
            $hora_atual += (30 * 60); 
        }

        echo json_encode($horarios_livres);
        exit;
    }
}

// 3. Salvar o Agendamento com Double-Check de Sobreposição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $acao === 'agendar') {
    $cliente_id = $dados['cliente_id'] ?? 0;
    $servico_id = $dados['servico_id'] ?? 0;
    $funcionario_id = $dados['funcionario_id'] ?? 0;
    $data = $dados['data'] ?? '';
    $hora = $dados['hora'] ?? '';
    $cartao = $dados['cartao'] ?? ''; 

    if (!$cliente_id || !$servico_id || !$funcionario_id || !$data || !$hora || empty($cartao)) {
        echo json_encode(["erro" => "Preencha todos os campos obrigatórios."]);
        exit;
    }

    try {
        // Pega a duração do serviço sendo agendado
        $stmt_dur = $pdo->prepare("SELECT duracao_minutos FROM servicos WHERE id = ?");
        $stmt_dur->execute([$servico_id]);
        $duracao_novo = $stmt_dur->fetchColumn();
        
        $novo_inicio = strtotime($hora);
        $novo_fim = $novo_inicio + ($duracao_novo * 60);

        // Busca a grade ocupada novamente para evitar que 2 clientes cliquem juntos
        $stmt = $pdo->prepare("
            SELECT a.hora_reserva, s.duracao_minutos 
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            WHERE a.funcionario_id = ? AND a.data_reserva = ? AND a.status != 'cancelado'
        ");
        $stmt->execute([$funcionario_id, $data]);
        $existentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conflito_final = false;
        foreach ($existentes as $ag) {
            $ag_inicio = strtotime($ag['hora_reserva']);
            $ag_fim = $ag_inicio + ($ag['duracao_minutos'] * 60);
            if ($novo_inicio < $ag_fim && $novo_fim > $ag_inicio) {
                $conflito_final = true; 
                break;
            }
        }

        if ($conflito_final) {
            echo json_encode(["erro" => "Este horário sobrepõe outro agendamento. Tente outro horário."]);
            exit;
        }

        $cartao_limpo = str_replace(' ', '', $cartao);
        $cartao_final = !empty($cartao_limpo) ? substr($cartao_limpo, -4) : null;

        $stmt = $pdo->prepare("INSERT INTO agendamentos (cliente_id, servico_id, funcionario_id, data_reserva, hora_reserva, status, cartao_final) VALUES (?, ?, ?, ?, ?, 'confirmado', ?)");
        $stmt->execute([$cliente_id, $servico_id, $funcionario_id, $data, $hora, $cartao_final]);
        
        echo json_encode(["sucesso" => "Agendamento confirmado com sucesso!"]);
    } catch (PDOException $e) {
        echo json_encode(["erro" => "Erro ao processar reserva: " . $e->getMessage()]);
    }
    exit;
}
?>