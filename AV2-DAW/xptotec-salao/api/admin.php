<?php
// api/admin.php
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php'; 

$acao = $_GET['acao'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($acao) {
        
        case 'listar_agendamentos':
            $query = "
                SELECT ag.id, ag.data_reserva, ag.hora_reserva, ag.status,
                       c.nome AS cliente_nome, 
                       s.nome AS servico_nome, 
                       f.nome AS profissional_nome
                FROM agendamentos ag
                JOIN clientes c ON ag.cliente_id = c.id
                JOIN servicos s ON ag.servico_id = s.id
                JOIN funcionarios f ON ag.funcionario_id = f.id
                ORDER BY ag.data_reserva DESC, ag.hora_reserva DESC
            ";
            $stmt = $pdo->query($query);
            $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($agendamentos);
            break;

        case 'cancelar_agendamento_admin':
            if (empty($input['agendamento_id'])) {
                echo json_encode(['erro' => 'ID do agendamento não fornecido.']);
                exit;
            }
            
            $id = $input['agendamento_id'];
            $stmt = $pdo->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['sucesso' => 'Agendamento cancelado com sucesso pelo administrador!']);
            break;

        case 'listar_servicos_admin':
            // Lista apenas os serviços que não foram deletados logicamente (ativo = 1)
            $stmt = $pdo->query("SELECT id, nome, categoria, preco FROM servicos WHERE ativo = 1 ORDER BY nome ASC");
            $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($servicos);
            break;

        case 'excluir_servico':
            if (empty($input['servico_id'])) {
                echo json_encode(['erro' => 'ID do serviço não fornecido.']);
                exit;
            }
            $id = $input['servico_id'];
            // SOFT DELETE: Apenas altera o status para 0 (inativo), não deleta a linha
            $stmt = $pdo->prepare("UPDATE servicos SET ativo = 0 WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['sucesso' => 'Serviço excluído com sucesso!']);
            break;

        case 'cadastrar_servico':
            if (empty($input['nome']) || empty($input['categoria']) || empty($input['preco'])) {
                echo json_encode(['erro' => 'Preencha os campos obrigatórios do serviço.']);
                exit;
            }
            // Já insere o serviço como ativo (1)
            $stmt = $pdo->prepare("INSERT INTO servicos (nome, descricao, categoria, duracao_minutos, preco, ativo) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([
                $input['nome'], $input['descricao'] ?? null, $input['categoria'], $input['duracao_minutos'] ?? 30, $input['preco']
            ]);
            echo json_encode(['sucesso' => 'Serviço cadastrado com sucesso!']);
            break;

        case 'cadastrar_funcionario':
            if (empty($input['nome'])) {
                echo json_encode(['erro' => 'O nome do funcionário é obrigatório.']);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO funcionarios (nome, cargo, especialidade) VALUES (?, 'profissional', ?)");
            $stmt->execute([$input['nome'], $input['especialidade'] ?? null]);
            echo json_encode(['sucesso' => 'Profissional cadastrado com sucesso!']);
            break;

        case 'listar_avaliacoes':
            $query = "
                SELECT a.nota, a.comentario, c.nome AS cliente_nome, s.nome AS servico_nome, f.nome AS profissional_nome
                FROM avaliacoes a
                JOIN agendamentos ag ON a.agendamento_id = ag.id
                JOIN clientes c ON ag.cliente_id = c.id
                JOIN servicos s ON ag.servico_id = s.id
                JOIN funcionarios f ON ag.funcionario_id = f.id
                ORDER BY a.data_avaliacao DESC
            ";
            $stmt = $pdo->query($query);
            $avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($avaliacoes);
            break;

        case 'login':
            if (empty($input['email']) || empty($input['senha'])) {
                echo json_encode(['erro' => 'Preencha email e senha.']);
                exit;
            }
            $stmt = $pdo->prepare("SELECT id, nome, cargo FROM funcionarios WHERE email = ? AND senha = ? AND cargo = 'admin'");
            $stmt->execute([$input['email'], $input['senha']]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($admin) {
                echo json_encode(['admin' => $admin]);
            } else {
                echo json_encode(['erro' => 'Credenciais inválidas ou sem permissão de administrador.']);
            }
            break;

        default:
            echo json_encode(['erro' => 'Ação inválida.']);
            break;
    }
} catch (PDOException $e) {
    if ($e->getCode() == '23000') {
        echo json_encode(['erro' => 'Não é possível realizar a operação. Existem vínculos ativos no banco de dados.']);
    } else {
        echo json_encode(['erro' => 'Erro no Banco de Dados: ' . $e->getMessage()]);
    }
}
?>