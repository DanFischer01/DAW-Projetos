<?php
require_once 'config.php';
$usuarios = lerUsuarios();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Lista de Gestores</title></head>
<body>
    <h2>Gestores Registados</h2>
    <table border="1">
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Cargo</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?php echo htmlspecialchars($u['nome']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo htmlspecialchars($u['cargo']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br><a href="index.php">Voltar ao Menu</a>
</body>
</html>
