<?php
include("./conexao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $valor = floatval($_POST['valor'] ?? 0);
    $imagem = $_POST['imagem'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $dia = $_POST['dia_da_semana'] ?? '';
    $pt_dia = isset($_POST['pt_dia']) ? 1 : 0;

    $sql = "INSERT INTO novo_cardapio (nome, descricao, valor, imagem, categoria, dia_da_semana, pt_dia)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "<script>alert('Erro na preparação da query: " . $conn->error . "');</script>";
    } else {
        $stmt->bind_param("ssdsssi", $nome, $descricao, $valor, $imagem, $categoria, $dia, $pt_dia);

        if ($stmt->execute()) {
            echo "<script>alert('Produto inserido com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao inserir o produto: " . $stmt->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Incluir Produto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
            margin: 0;
        }

        .form-container {
            max-height: 75vh;
            overflow-y: auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            max-width: 600px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            color: #3A6332;
            margin-bottom: 5px;
        }

        input[type="text"],
        textarea,
        select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            width: 100%;
        }

        .checkbox {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }

        .checkbox input[type="checkbox"] {
            margin-right: 8px;
        }

        input[type="submit"] {
            background-color: #3A6332;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            align-self: flex-start;
        }

        input[type="submit"]:hover {
            background-color: #2c4f27;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Inserir Novo Produto</h2>
    <form method="POST">
        <label>Nome do Produto</label>
        <input type="text" name="nome" placeholder="Insira o nome do produto" required>

        <label>Descrição Do Produto:</label>
        <textarea name="descricao" rows="3" placeholder="Insira a descrição completa" required></textarea>

        <label>Insira o Valor (R$):</label>
        <input type="text" name="valor" placeholder="Insira o valor em Reais" required>

        <label>Insira a Imagem (URL):</label>
        <input type="text" name="imagem" placeholder="images/nomedoarquivo.jpg">

        <label>Categoria:</label>
        <select name="categoria" required>
            <option value="massas">Massas</option>
            <option value="tradicionais">Tradicionais Italianos</option>
            <option value="entradas">Entradas e Saídas</option>
            <option value="vinhos">Vinhos</option>
            <option value="sobremesas">Sobremesas</option>
            <option value="bebidas">Bebidas</option>
        </select>

        <div class="checkbox">
            <input type="checkbox" name="pt_dia" id="pt_dia">
            <label for="pt_dia">Prato do dia?</label>
        </div>

        <label>Dia da Semana:</label>
        <select name="dia_da_semana">
            <option value="">--Não é prato do dia--</option>
            <option value="segunda">Segunda</option>
            <option value="terca">Terça</option>
            <option value="quarta">Quarta</option>
            <option value="quinta">Quinta</option>
            <option value="sexta">Sexta</option>
            <option value="sabado">Sábado</option>
            <option value="domingo">Domingo</option>
        </select>

        <input type="submit" value="Inserir Produto">
    </form>
</div>

</body>
</html>
