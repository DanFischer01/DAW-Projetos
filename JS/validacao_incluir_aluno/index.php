<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Alunos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .error { color: red; font-size: 0.9em; margin-top: 5px; display: none; }
    </style>
</head>
<body>

    <h2>Cadastro de Aluno</h2>
    
    <form id="formAluno" action="cadastrar.php" method="POST">
        <div class="form-group">
            <label for="nome">Nome Completo:</label>
            <input type="text" id="nome" name="nome">
            <div id="erro-nome" class="error">O nome deve ter pelo menos 3 caracteres.</div>
        </div>

        <div class="form-group">
            <label for="matricula">Matrícula (Apenas números):</label>
            <input type="text" id="matricula" name="matricula">
            <div id="erro-matricula" class="error">A matrícula deve conter apenas números (mínimo 4 dígitos).</div>
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email">
            <div id="erro-email" class="error">Insira um e-mail válido.</div>
        </div>

        <button type="submit">Cadastrar Aluno</button>
    </form>

    <script>
        document.getElementById('formAluno').addEventListener('submit', function(e) {
            let valido = true;

            document.querySelectorAll('.error').forEach(el => el.style.display = 'none');

            const nome = document.getElementById('nome').value.trim();
            const matricula = document.getElementById('matricula').value.trim();
            const email = document.getElementById('email').value.trim();

            if (nome.length < 3) {
                document.getElementById('erro-nome').style.display = 'block';
                valido = false;
            }

            const regexMatricula = /^[0-9]{4,}$/;
            if (!regexMatricula.test(matricula)) {
                document.getElementById('erro-matricula').style.display = 'block';
                valido = false;
            }

            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexEmail.test(email)) {
                document.getElementById('erro-email').style.display = 'block';
                valido = false;
            }

            if (!valido) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
