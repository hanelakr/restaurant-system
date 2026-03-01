<?php
include ("./conexao.php");

$busca = $_GET['busca'] ?? '';
$sql = "SELECT * FROM novo_cardapio WHERE nome LIKE '%$busca%' OR categoria LIKE '%$busca%'";
$resultado = mysqli_query($conn, $sql);


if (isset($_SESSION['mensagem'])) {
    echo "<p style='color: green; text-align: center; font-weight: bold;'>" . $_SESSION['mensagem'] . "</p>";
    unset($_SESSION['mensagem']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Produto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
            margin: 0;
        }

        form.busca {
            text-align: center;
            margin-bottom: 20px;
        }

        form.busca input[type="text"] {
            padding: 10px;
            width: 300px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-right: 10px;
        }

        form.busca button {
            padding: 10px 20px;
            background-color: #3A6332;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        form.busca button:hover {
            background-color: #2e4f29;
        }

        .lista-produtos {
            max-height: 75vh;
            overflow-y: auto;
            padding-right: 10px;
            max-width: 840px;
            margin: 0 auto;
        }

        .card {
            background-color: #3A6332;
            color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .card img {
            width: 120px;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        .text {
            flex: 1;
            margin-right: 20px;
        }

        .botoes button {
            background-color: #ffc107;
            color: black;
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .botoes button:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>

    <form method="get" class="busca">
        <input type="hidden" name="page" value="alterar_produto">
        <input type="text" name="busca" placeholder="Buscar por nome ou categoria..." value="<?= htmlspecialchars($busca) ?>">
        <button type="submit">Buscar</button>
    </form>

    <div class="lista-produtos">
    <?php
    while ($produto = mysqli_fetch_assoc($resultado)) {
        $img = $produto['imagem'] ?: 'images/default.jpg';
        $valor = number_format($produto['valor'], 2, ',', '.');
        echo <<<HTML
        <div class="card">
            <div class="text">
                <h3>{$produto['nome']}</h3>
                <p>{$produto['descricao']}</p>
                <p><strong>R$ {$valor}</strong></p>
            </div>
            <div class="botoes">
HTML;
        echo '<a href="?page=editar_produto&id=' . $produto['id_cardapio'] . '">
                <button class="btn-alterar">Alterar</button>
              </a>';
        echo <<<HTML
            </div>
            <img src="{$img}" alt="{$produto['nome']}">
        </div>
HTML;
}
    ?>
    </div>

</body>
</html>
