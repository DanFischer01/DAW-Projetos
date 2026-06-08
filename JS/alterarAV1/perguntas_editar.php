<?php
require_once 'config.php';
$id = $_GET['id'] ?? null;
$pergunta = buscarPorId($id);

if (!$pergunta) {
    header("Location: perguntas_listar.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $perguntas = lerPerguntas();
    foreach ($perguntas as &$p) {
        if ($p['id'] == $id) {
            $p['enunciado'] = $_POST['enunciado'];
            
            if ($p['tipo'] == 'multipla') {
                $opcoesFiltradas = array_filter($_POST['opcoes'], function($value) { return trim($value) !== ''; });
                $p['respostas'] = array_values($opcoesFiltradas);
            } else {
                $p['respostas'] = $_POST['resposta_texto'];
            }
        }
    }
    salvarPerguntas($perguntas);
    header("Location: perguntas_listar.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Pergunta</title>
</head>
<body>
    <h2>Editar Pergunta (<?php echo ucfirst($pergunta['tipo']); ?>)</h2>
    <form method="POST" id="formEditar">
        <label>Enunciado:</label><br>
        <textarea name="enunciado" required style="width: 300px; height: 80px;"><?php echo htmlspecialchars($pergunta['enunciado']); ?></textarea><br><br>

        <?php if ($pergunta['tipo'] == 'multipla'): ?>
            <label>Opções:</label><br>
            <div id="container-opcoes">
                <?php foreach ($pergunta['respostas'] as $idx => $opcao): ?>
                    <div style="margin: 3px 0;">
                        <input type="text" name="opcoes[]" value="<?php echo htmlspecialchars($opcao); ?>" required>
                        <?php if ($idx >= 2): ?>
                            <button type="button" class="btn-remover" style="color: red; cursor:pointer;">X</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="btnAdicionarOpcao" style="margin-top: 5px;">+ Adicionar Opção</button>
            <br>
        <?php else: ?>
            <label>Resposta Correta (Texto):</label><br>
            <input type="text" name="resposta_texto" value="<?php echo htmlspecialchars($pergunta['respostas']); ?>" required><br>
        <?php endif; ?>

        <br><button type="submit">Salvar Alterações</button>
        <a href="perguntas_listar.php">Cancelar</a>
    </form>

    <script>
        const btnAdicionar = document.getElementById('btnAdicionarOpcao');
        const containerOpcoes = document.getElementById('container-opcoes');
        const form = document.getElementById('formEditar');

        if (btnAdicionar) {
            // Atribui evento de remoção aos botões X já existentes no carregamento da página
            containerOpcoes.querySelectorAll('.btn-remover').forEach(btn => {
                btn.addEventListener('click', function() {
                    btn.parentElement.remove();
                });
            });

            btnAdicionar.addEventListener('click', function() {
                const div = document.createElement('div');
                div.style.margin = '3px 0';
                div.innerHTML = `
                    <input type="text" name="opcoes[]" placeholder="Nova Opção" required>
                    <button type="button" class="btn-remover" style="color: red; cursor:pointer;">X</button>
                `;
                containerOpcoes.appendChild(div);

                div.querySelector('.btn-remover').addEventListener('click', function() {
                    div.remove();
                });
            });
        }

        form.addEventListener('submit', function(e) {
            if (containerOpcoes) {
                const inputs = containerOpcoes.querySelectorAll('input[type="text"]');
                if (inputs.length < 2) {
                    e.preventDefault();
                    alert('A pergunta deve conter pelo menos 2 opções.');
                }
            }
        });
    </script>
</body>
</html>
