<?php
include("./conexao.php");

$msg = '';

// Processa exclusão com segurança
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cardapio'])) {
    $id = intval($_POST['id_cardapio']); // garante que é inteiro
    $delete = "DELETE FROM novo_cardapio WHERE id_cardapio = $id";
    if (mysqli_query($conn, $delete)) {
        echo "<script>alert('Produto excluído com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao excluir o produto.');</script>";
    }
}

// Escapa o parâmetro busca para evitar SQL Injection
$busca = isset($_GET['busca']) ? mysqli_real_escape_string($conn, $_GET['busca']) : '';
$sql = "SELECT * FROM novo_cardapio WHERE nome LIKE '%$busca%' OR categoria LIKE '%$busca%'";
$resultado = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Excluir Produto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
            margin: 0;
        }
        .msg {
            text-align: center;
            color: green;
            margin-bottom: 15px;
        }

        form.busca {
            text-align: center;
            margin-bottom: 20px;
            padding-right: 20px;
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
            max-height: 70vh;
            overflow-y: auto;
            padding-right: 10px;
            max-width: 840px;
            margin: 0 auto;
        }
        .card {
            background-color: #3A6332;
            color: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
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
        .card .text {
            flex: 1;
            margin-right: 20px;
        }
        .card form {
            margin-top: 10px;
        }
        .card button {
            background-color: red;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 5px;
            cursor: pointer;
        }
        .card button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

    <?php if (!empty($msg)) { ?>
        <div class="msg"><?= $msg ?></div>
    <?php } ?>

    <?php if (!empty($busca)): ?>
        <div style="text-align: right; margin-bottom: 10px; margin-top: 5px; max-width: 840px; margin-left:auto; margin-right:auto;">
            <a href="?page=excluir_produto" style="padding: 10px 20px; background-color: #3A6332; color: white;
                                                   text-decoration: none; border-radius: 8px;">Ver todos os produtos</a>
        </div>
    <?php endif; ?>

    <form method="get" class="busca">
        <input type="hidden" name="page" value="excluir_produto">
        <input type="text" name="busca" placeholder="Buscar por nome ou categoria..." value="<?= htmlspecialchars($busca) ?>">
        <button type="submit">Buscar</button>
    </form>

    <div class="lista-produtos">
    <?php
    while ($produto = mysqli_fetch_assoc($resultado)) {
        $img = (!empty($produto['imagem'])) ? $produto['imagem'] : 'images/default.jpg';
        $valor_formatado = number_format($produto['valor'], 2, ',', '.');
        echo <<<HTML
        <div class="card">
            <div class="text">
                <h3>{$produto['nome']}</h3>
                <p>{$produto['descricao']}</p>
                <p><strong>R$ {$valor_formatado}</strong></p>
                <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                    <input type="hidden" name="id_cardapio" value="{$produto['id_cardapio']}">
                    <button type="submit">Excluir</button>
                </form>
            </div>
            <img src="{$img}" alt="{$produto['nome']}">
        </div>
        HTML;
    }
    ?>
    </div>

</body>
</html>
