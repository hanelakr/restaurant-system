<?php
session_start();
include("conexao.php"); 

if (!isset($_SESSION['usuario_id'])) {
    echo "Erro: Usuário não autenticado.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cep = mysqli_real_escape_string($conn, $_POST['cep']);
    $numero = mysqli_real_escape_string($conn, $_POST['numero']);
    $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);
    $usuario_id = $_SESSION['usuario_id']; 

    $sql = "INSERT INTO enderecos (usuario_id, cep, numero, tipo) VALUES ('$usuario_id', '$cep', '$numero', '$tipo')";
    
    if (mysqli_query($conn, $sql)) {
        echo "sucesso";
    } else {
        echo "Erro ao salvar: " . mysqli_error($conn);
    }
} else {
    echo "Erro: Método inválido.";
}

mysqli_close($conn);
?>
