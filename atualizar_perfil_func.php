<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: perfil_func.php");
    exit();
}

try {
    $telefone = $_POST['telefone'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $cpf = $_SESSION['usuario_cpf'];

    // Inicia transação
    $conn->begin_transaction();

    // Atualiza telefone na tabela usuarios
    $stmt = $conn->prepare("UPDATE usuarios SET telefone = ? WHERE cpf = ?");
    $stmt->bind_param("ss", $telefone, $cpf);
    $stmt->execute();

    // Verifica se já tem endereço cadastrado
    $stmt = $conn->prepare("SELECT 1 FROM enderecos WHERE fk_cpf = ?");
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $tem_endereco = $stmt->get_result()->num_rows > 0;

    if ($tem_endereco) {
        // Atualiza endereço existente
        $stmt = $conn->prepare("UPDATE enderecos SET cep=?, numero=?, tipo=? WHERE fk_cpf=?");
        $stmt->bind_param("ssss", $cep, $numero, $tipo, $cpf);
    } else {
        // Insere novo endereço
        $stmt = $conn->prepare("INSERT INTO enderecos (cep, numero, tipo, fk_cpf) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $cep, $numero, $tipo, $cpf);
    }
    $stmt->execute();

    $conn->commit();
    $_SESSION['mensagem'] = "Dados atualizados com sucesso!";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['erro'] = "Erro ao atualizar: " . $e->getMessage();
}

header("Location: home_func.php?page=perfil_func");
exit();
?>
