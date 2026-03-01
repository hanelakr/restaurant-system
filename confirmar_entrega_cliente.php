<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'])) {
    $id_pedido = $_POST['id_pedido'];

    $stmt = $conn->prepare("UPDATE pedidos_delivery SET status = 'entregue' WHERE id_pedido = ? AND fk_cpf = ?");
    $stmt->bind_param("is", $id_pedido, $_SESSION['usuario_cpf']);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Entrega confirmada com sucesso!';
    } else {
        $_SESSION['erro'] = 'Erro ao confirmar entrega.';
    }
}

header('Location: perfil.php');
exit();
