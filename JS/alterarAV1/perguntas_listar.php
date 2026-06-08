<?php 
require_once 'config.php';
$perguntas = lerPerguntas();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Perguntas</title>
</head>
<body>
    <h2>Lista de Perguntas</h2>

    <label for="busca">Filtrar Enunciado/Tipo:</label>
    <input type="text" id="busca" placeholder="Digite para pesquisar..." style="margin-bottom: 10px; width: 250px; padding: 4px;">

    <table border="1" id="tabelaPerguntas">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Enunciado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($perguntas as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td class="col-tipo"><?php echo ($p['tipo'] == 'multipla' ? 'Múltipla' : 'Texto'); ?></td>
                <td class="col-enunciado"><?php echo htmlspecialchars(substr($p['enunciado'], 0, 50)) . (strlen($p['enunciado']) > 50 ? '...' : ''); ?></td>
                <td>
                    <a href="pergunta_detalhe.php?id=<?php echo $p['id']; ?>">Visualizar</a> | 
                    <a href="perguntas_editar.php?id=<?php echo $p['id']; ?>">Editar</a> | 
                    <a href="perguntas_excluir.php?id=<?php echo $p['id']; ?>" 
                       onclick="return confirm('Tem certeza que deseja excluir esta pergunta?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr id="linhaNaoEncontrado" style="display: none;">
                <td colspan="4" style="text-align: center; color: gray;">Nenhuma pergunta encontrada.</td>
            </tr>
        </tbody>
    </table>
    <br><a href="index.php">Voltar ao Menu</a>

    <script>
        document.getElementById('busca').addEventListener('input', function() {
            const termoBusca = this.value.toLowerCase();
            const linhas = document.querySelectorAll('#tabelaPerguntas tbody tr:not(#linhaNaoEncontrado)');
            let algumResultado = false;

            linhas.forEach(linha => {
                const tipo = linha.querySelector('.col-tipo').textContent.toLowerCase();
                const enunciado = linha.querySelector('.col-enunciado').textContent.toLowerCase();

                if (tipo.includes(termoBusca) || enunciado.includes(termoBusca)) {
                    linha.style.display = '';
                    algumResultado = true;
                } else {
                    linha.style.display = 'none';
                }
            });

            // Exibe mensagem caso a busca zere os resultados
            document.getElementById('linhaNaoEncontrado').style.display = algumResultado ? 'none' : '';
        });
    </script>
</body>
</html>
