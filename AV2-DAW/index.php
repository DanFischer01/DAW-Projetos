<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Beleza Feminina</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .fundo {
            background-image: url('fundo-salao.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            filter: blur(5px); 
            z-index: -1;
        }

        /* Estilo do Botão */
        .container-botao {
            z-index: 1;
        }

        .botao-acessar {
            background-color: #ff4081; 
            color: white;
            padding: 20px 50px;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: background-color 0.3s;
        }

        .botao-acessar:hover {
            background-color: #e91e63; /* Tom de rosa mais escuro ao passar o mouse */
        }
    </style>
</head>
<body>

    <div class="fundo"></div>

    <div class="container-botao">
        <a href="agendamento.php" class="botao-acessar">ACESSAR</a>
    </div>

</body>
</html>
