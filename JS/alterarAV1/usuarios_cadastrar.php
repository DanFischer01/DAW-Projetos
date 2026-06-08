<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuarios = lerUsuarios();
    $novo = [
        "id" => time(),
        "nome" => $_POST['nome'],
        "email" => $_POST['email'],
        "cargo" => $_POST['cargo']
    ];
    $usuarios[] = $novo;
    salvarUsuarios($usuarios);
    header("Location: usuarios_listar.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Cadastrar Gestor</title></head>
<body>
    <h2>Registar Novo Gestor</h2>
    <form method="POST" id="formUsuario">
        <label>Nome:</label><br>
        <input type="text" name="nome" id="nome" required><br><br>
        
        <label>E-mail:</label><br>
        <input type="email" name="email" id="email" required><br><br>
        
        <label>Cargo:</label><br>
        <input type="text" name="cargo" id="cargo" required><br><br>

        <button type="submit">Guardar Utilizador</button>
        <a href="index.php">Voltar</a>
    </form>

    <script>
        document.getElementById('formUsuario').addEventListener('submit', function(e) {
            const nome = document.getElementById('nome').value.trim();
            const email = document.getElementById('email').value.trim();

            if (nome.length < 3) {
                e.preventDefault();
                alert('O nome do gestor deve ter pelo menos 3 caracteres.');
                return;
            }

            // Expressão regular simples para checagem adicional de e-mail no JS
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexEmail.test(email)) {
                e.preventDefault();
                alert('Por favor, insira um endereço de e-mail válido.');
            }
        });
    </script>
</body>
</html>
