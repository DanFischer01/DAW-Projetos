<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nome = htmlspecialchars(trim($_POST['nome']));
    $matricula = htmlspecialchars(trim($_POST['matricula']));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (strlen($nome) < 3 || empty($matricula) || !$email) {
        die("Erro: Dados inválidos enviados. Por favor, volte e preencha corretamente.");
    }

    $arquivo = 'alunos.txt';
    $linha = "Matrícula: $matricula | Nome: $nome | E-mail: $email" . PHP_EOL;

    if ($fp = fopen($arquivo, 'a')) {
        fwrite($fp, $linha);
        fclose($fp); 

        echo "<h2>Aluno cadastrado com sucesso!</h2>";
        echo "<p><a href='index.php'>Voltar para o formulário</a></p>";
        echo "<h3>Alunos já cadastrados:</h3>";
        
        echo "<pre>" . htmlspecialchars(file_get_contents($arquivo)) . "</pre>";
    } else {
        echo "Erro ao abrir o arquivo para salvar os dados.";
    }

} else {
    header("Location: index.php");
    exit();
}
?>
