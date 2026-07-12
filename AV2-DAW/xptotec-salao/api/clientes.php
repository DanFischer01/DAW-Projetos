<?php
// api/clientes.php
header("Content-Type: application/json");
require_once "db.php";

$dados = json_decode(file_get_contents("php://input"), true);
$acao = $_GET['acao'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ---------------- CADASTRAR CLIENTE ----------------
    if ($acao === 'cadastrar') {
        $nome = trim($dados['nome'] ?? '');
        $email = trim($dados['email'] ?? '');
        $senha = trim($dados['senha'] ?? '');
        $telefone = trim($dados['telefone'] ?? '');

        if (empty($nome) || empty($email) || empty($senha)) {
            echo json_encode(["erro" => "Preencha todos os campos obrigatórios."]);
            exit;
        }

        // Verifica se o e-mail já existe
        $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(["erro" => "Este e-mail já está cadastrado."]);
            exit;
        }

        // Criptografa a senha por segurança
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, senha, telefone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $senhaHash, $telefone]);
            echo json_encode(["sucesso" => "Cadastro realizado com sucesso!"]);
        } catch (PDOException $e) {
            echo json_encode(["erro" => "Erro ao cadastrar: " . $e->getMessage()]);
        }
        exit;
    }

    // ---------------- LOGIN DO CLIENTE ----------------
    if ($acao === 'login') {
        $email = trim($dados['email'] ?? '');
        $senha = trim($dados['senha'] ?? '');

        if (empty($email) || empty($senha)) {
            echo json_encode(["erro" => "Preencha e-mail e senha."]);
            exit;
        }

        $stmt = $pdo->prepare("SELECT id, nome, senha FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        $cliente = $stmt->fetch();

        if ($cliente && password_verify($senha, $cliente['senha'])) {
            // Inicia a sessão no PHP para guardar o login
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['cliente_id'] = $cliente['id'];
            $_SESSION['cliente_nome'] = $cliente['nome'];

            echo json_encode([
                "sucesso" => "Login efetuado com sucesso!",
                "cliente" => ["id" => $cliente['id'], "nome" => $cliente['nome']]
            ]);
        } else {
            echo json_encode(["erro" => "E-mail ou senha inválidos."]);
        }
        exit;
    }
}
?>