<?php 
require_once 'config.php';
$id = $_GET['id'] ?? null;

if ($id) {
    $perguntas = lerPerguntas();
    // Filtra o array removendo quem tem o ID enviado
    $novasPerguntas = array_filter($perguntas, function($p) use ($id) {
        return $p['id'] != $id;
    });
    salvarPerguntas($novasPerguntas);
}

header("Location: perguntas_listar.php");
exit;
