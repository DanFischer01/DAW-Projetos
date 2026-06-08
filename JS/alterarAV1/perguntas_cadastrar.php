<?php 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $perguntas = lerPerguntas();
    
    // Filtra opções vazias caso o usuário tenha criado campos a mais sem preencher
    $respostas = $_POST['tipo'] == 'multipla' 
        ? array_filter($_POST['opcoes'], function($value) { return trim($value) !== ''; })
        : $_POST['resposta_texto'];

    $nova = [
        "id" => time(),
        "tipo" => $_POST['tipo'],
        "enunciado" => $_POST['enunciado'],
        "respostas" => array_values((array)$respostas) // Garante reindexação se for array
    ];
    $perguntas[] = $nova;
    salvarPerguntas($perguntas);
    header("Location: perguntas_listar.php");
    exit;
}

$tipo = $_GET['tipo'] ?? 'multipla';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Pergunta</title>
</head>
<body>
    <h2>Cadastrar Pergunta: <?php echo ucfirst($tipo); ?></h2>
    <form method="POST" id="formPergunta">
        <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
        
        <label>Enunciado:</label><br>
        <textarea name="enunciado" id="enunciado" required style="width: 300px; height: 80px;"></textarea><br><br>

        <?php if ($tipo == 'multipla'): ?>
            <label>Opções da Pergunta:</label><br>
            <div id="container-opcoes">
                <input type="text" name="opcoes[]" placeholder="Opção 1" required><br>
                <input type="text" name="opcoes[]" placeholder="Opção 2" required><br>
            </div>
            <button type="button" id="btnAdicionarOpcao" style="margin-top: 5px;">+ Adicionar Opção</button>
            <br>
        <?php else: ?>
            <label>Resposta Correta (Texto):</label><br>
            <input type="text" name="resposta_texto" required><br>
        <?php endif; ?>

        <br><br>
        <button type="submit">Salvar Pergunta</button>
        <a href="index.php">Voltar</a>
    </form>

    <script>
        // JS para manipulação dinâmica das opções de múltipla escolha
        const btnAdicionar = document.getElementById('btnAdicionarOpcao');
        const containerOpcoes = document.getElementById('container-opcoes');
        const form = document.getElementById('formPergunta');

        if (btnAdicionar) {
            let contadorOpcoes = 3;
            btnAdicionar.addEventListener('click', function() {
                const div = document.createElement('div');
                div.style.styleFloat = 'left';
                div.style.margin = '3px 0';
                
                div.innerHTML = `
                    <input type="text" name="opcoes[]" placeholder="Opção ${contadorOpcoes}" required>
                    <button type="button" class="btn-remover" style="color: red; cursor:pointer;">X</button>
                `;
                containerOpcoes.appendChild(div);
                contadorOpcoes++;

                // Evento para remover a linha criada
                div.querySelector('.btn-remover').addEventListener('click', function() {
                    div.remove();
                });
            });
        }

        // Validação extra antes do envio
        form.addEventListener('submit', function(e) {
            const tipo = document.querySelector('input[name="tipo"]').value;
            if (tipo === 'multipla') {
                const inputs = containerOpcoes.querySelectorAll('input[type="text"]');
                if (inputs.length < 2) {
                    e.preventDefault();
                    alert('Uma pergunta de múltipla escolha precisa de pelo menos 2 opções!');
                }
            }
        });
    </script>
</body>
</html>
