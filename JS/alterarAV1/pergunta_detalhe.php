<?php
require_once 'config.php';
$id = $_GET['id'] ?? null;
$p = buscarPorId($id);

if (!$p) die("Pergunta não encontrada.");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Detalhes da Pergunta</title></head>
<body>
    <h2>Detalhes da Pergunta</h2>
    <p><strong>ID:</strong> <?php echo $p['id']; ?></p>
    <p><strong>Tipo:</strong> <?php echo ucfirst($p['tipo']); ?></p>
    <p><strong>Enunciado:</strong> <?php echo nl2br($p['enunciado']); ?></p>
    
    <strong>Respostas/Opções:</strong>
    <ul>
        <?php 
        if (is_array($p['respostas'])) {
            foreach ($p['respostas'] as $r) echo "<li>$r</li>";
        } else {
            echo "<li>" . $p['respostas'] . "</li>";
        }
        ?>
    </ul>
    <br><a href="perguntas_listar.php">Voltar para a Lista</a>
</body>
</html>
