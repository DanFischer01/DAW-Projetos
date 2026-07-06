<?php include 'conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Reserva - Beleza Feminina</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f9f9f9; }
        .container { max-width: 600px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .campo { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        select, input { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background-color: #ff4081; color: white; border: none; padding: 10px 15px; width: 100%; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #e91e63; }
    </style>
</head>
<body>

<div class="container">
    <h2>Reserva de Serviços</h2>
    <form action="processa_reserva.php" method="POST" onsubmit="return validarPagamento()">
        
        <div class="campo">
            <label>Nome Completo:</label>
            <input type="text" name="nome_cliente" required>
        </div>

        <div class="campo">
            <label>Filtro por Especialidade:</label>
            <select id="especialidade" onchange="filtrarOpcoes()">
                <option value="">Selecione...</option>
                <option value="Cabelo">Cabelo</option>
                <option value="Unhas">Unhas</option>
                <option value="Estética">Estética</option>
            </select>
        </div>

        <div class="campo">
            <label>Serviço:</label>
            <select name="id_servico" id="servico" required>
                <option value="">Selecione uma especialidade primeiro</option>
            </select>
        </div>

        <div class="campo">
            <label>Profissional:</label>
            <select name="id_profissional" id="profissional">
                <option value="">Qualquer profissional disponível no momento</option>
            </select>
        </div>

        <div class="campo">
            <label>Data e Horário:</label>
            <input type="datetime-local" name="data_hora" required>
        </div>

        <hr>
        <h3>Pagamento Exclusivo via Cartão de Crédito</h3>
        
        <div class="campo">
            <label>Número do Cartão:</label>
            <input type="text" id="cartao" placeholder="0000 0000 0000 0000" required>
        </div>

        <div class="campo">
            <label>Validade:</label>
            <input type="text" id="validade" placeholder="MM/AA" style="width: 45%;" required>
            <label style="display:inline-block; width: 8%;">CVV:</label>
            <input type="text" id="cvv" placeholder="000" style="width: 43%;" required>
        </div>

        <button type="submit">Confirmar Agendamento e Pagar</button>
    </form>
</div>

<script src="script.js"></script>
</body>
</html>
