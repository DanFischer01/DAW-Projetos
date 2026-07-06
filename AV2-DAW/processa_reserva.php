<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_cliente = $_POST['nome_cliente'];
    $id_servico = $_POST['id_servico'];
    $id_profissional = !empty($_POST['id_profissional']) ? $_POST['id_profissional'] : 'NULL';
    $data_hora = $_POST['data_hora'];

    // Se o profissional foi escolhido, verifica se ele já tem agendamento nesse horário
    if ($id_profissional !== 'NULL') {
        $sql_verifica = "SELECT id FROM agendamentos WHERE id_profissional = $id_profissional AND data_hora = '$data_hora' AND status = 'Confirmado'";
        $resultado = $conn->query($sql_verifica);

        if ($resultado->num_rows > 0) {
            echo "<script>alert('Desculpe, este profissional já possui um agendamento neste horário.'); window.history.back();</script>";
            exit;
        }
    } else {
        // Se escolheu "Qualquer profissional", busca o primeiro disponível da especialidade do serviço
        $sql_esp = "SELECT especialidade FROM servicos WHERE id = $id_servico";
        $res_esp = $conn->query($sql_esp);
        $row_esp = $res_esp->fetch_assoc();
        $especialidade = $row_esp['especialidade'];

        $sql_prof_disp = "SELECT id FROM profissionais WHERE TRIM(especialidade) = '$especialidade' AND id NOT IN (SELECT IFNULL(id_profissional, 0) FROM agendamentos WHERE data_hora = '$data_hora' AND status = 'Confirmado') LIMIT 1";
        $res_prof = $conn->query($sql_prof_disp);

        if ($res_prof->num_rows > 0) {
            $row_prof = $res_prof->fetch_assoc();
            $id_profissional = $row_prof['id'];
        } else {
            echo "<script>alert('Não há profissionais disponíveis para esta especialidade neste horário.'); window.history.back();</script>";
            exit;
        }
    }

    // Insere o agendamento no banco
    $sql_inserir = "INSERT INTO agendamentos (id_servico, id_profissional, nome_cliente, data_hora) VALUES ($id_servico, $id_profissional, '$nome_cliente', '$data_hora')";

    if ($conn->query($sql_inserir) === TRUE) {
        echo "<html lang='pt-br'><head><meta charset='UTF-8'></head><body>";
        echo "<script>alert('Reserva concluída e paga com sucesso!'); window.location.href='agendamento.php';</script>";        
        echo "</body></html>";
    } else {
        echo "Erro: " . $sql_inserir . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
