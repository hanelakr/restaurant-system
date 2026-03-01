<?php
include ("./conexao.php");

$busca = $_GET['busca'] ?? '';

$diaIngles = strtolower(date('l'));
$traducao = [
    'sunday' => 'domingo',
    'monday' => 'segunda',
    'tuesday' => 'terca',
    'wednesday' => 'quarta',
    'thursday' => 'quinta',
    'friday' => 'sexta',
    'saturday' => 'sabado'
];
$diaAtual = $traducao[$diaIngles];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cardápio</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      font-family: Italianno, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f7f7f7;
    }

    header {
      background-color: #3f6130;
      color: white;
      padding: 6px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
    }

    nav {
      display: flex;
      justify-content: space-evenly;
      width: 100%;
      padding: 4px 0;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-size: 16px;
      padding: 6px;
    }

    nav a:hover {
      text-decoration: underline;
    }

    /* Formulário de busca */
    .form-busca {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      margin-top: 20px;
      margin-bottom: 10px;
    }

    .form-busca input[type="text"] {
      padding: 10px;
      width: 280px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }

    .form-busca button {
      padding: 10px 20px;
      background-color: #3A6332;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }

    .menu {
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #3A6332;
      padding: 6px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      margin: 20px auto;
      max-width: fit-content;
      gap: 8px;
    }

    .menu button {
      background: none;
      border: none;
      color: #FFFFFF;
      font-weight: bold;
      font-size: 14px;
      cursor: pointer;
    }

    .menu button:hover {
      text-decoration: underline;
    }

    .menu a {
      margin: 0 10px;
      text-decoration: none;
      color: #3A6332;
      background-color: #FFFFFF;
      font-weight: bold;
      font-size: 14px;
      padding: 5px 10px;
      border-radius: 5px;
    }

    .category-section {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      max-width: 1200px;
      margin: 20px auto;
      padding: 16px;
      box-sizing: border-box;
    }

    .left {
      flex: 1;
      max-width: calc(100% - 400px);
      overflow-y: auto;
      max-height: calc(100vh - 180px);
      display: flex;
      flex-direction: column;
      gap: 15px;
      padding: 20px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card {
      background-color: #3A6332;
      color: white;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card .text {
      flex: 1;
      margin-right: 15px;
    }

    .card img {
      width: 120px;
      height: auto;
      border-radius: 5px;
    }

    .right {
      width: 380px;
      background-color: #597b51;
      color: white;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      box-sizing: border-box;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      align-self: flex-start;
    }

    .right img {
      max-width: 45%;
      border-radius: 5px;
      margin: 10px 0;
    }

    .add-cart {
      background-color: #FFFFFF;
      color: black;
      border: none;
      padding: 6px 14px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
    }

    .add-cart:hover {
      background-color: #3A6332;
      color: white;
    }

  </style>
</head>

<body>

<header>
  <nav>
    <a href="?page=home">Home</a>
    <a href="?page=cardapio">Cardápio</a>
    <a href="?page=delivery">Delivery</a>
    <a href="?page=historia">Nossa História</a>
    <a href="?page=contato">Contato</a>
    <?php if(isset($_SESSION['usuario_id'])): ?>
        <a href="perfil.php" class="entrar">Meu Perfil</a>
    <?php endif; ?>
  </nav>
</header>

<!-- Formulário de busca -->
<form method="get" class="form-busca">
  <input type="hidden" name="page" value="cardapio">
  <input
    type="text"
    name="busca"
    placeholder="Buscar produto..."
    value="<?= htmlspecialchars($busca) ?>"
  >
  <button type="submit">Buscar</button>
</form>

<div class="menu">
  <button onclick="showSection('massas')">Massas</button>
  <button onclick="showSection('tradicionais')">Tradicionais Italianos</button>
  <button onclick="showSection('entradas')">Entradas e Saídas</button>
  <button onclick="showSection('vinhos')">Vinhos</button>
  <button onclick="showSection('sobremesas')">Sobremesas</button>
  <button onclick="showSection('bebidas')">Bebidas</button>
  <a href="?page=delivery" class="view-cart"><i class="fas fa-shopping-cart"></i> Ver Carrinho</a>
</div>

<?php
function exibirProdutos($conn, $categoria) {
  global $busca;
  $buscaEscapado = mysqli_real_escape_string($conn, $busca);
  $sql = "SELECT * FROM novo_cardapio WHERE categoria = '$categoria'";
  if (!empty($buscaEscapado)) {
    $sql .= " AND (nome LIKE '%$buscaEscapado%' OR descricao LIKE '%$buscaEscapado%')";
  }
  $query = mysqli_query($conn, $sql);
  if ($query && mysqli_num_rows($query) > 0) {
    while ($dados = mysqli_fetch_assoc($query)) {
      echo "<div class='card'>";
      echo "<div class='text'>";
      echo "<h3>{$dados['nome']}</h3>";
      echo "<p>{$dados['descricao']}</p>";
      echo "<p>R$ {$dados['valor']}</p>";
      echo "<button class='add-cart'
              data-id='{$dados['id_cardapio']}'
              data-nome='{$dados['nome']}'
              data-valor='{$dados['valor']}'
              data-imagem='{$dados['imagem']}'
              data-descricao='{$dados['descricao']}'>
              + <i class='fas fa-cart-plus'></i>
            </button>";
      echo "</div>";
      $imagem = !empty($dados['imagem']) ? $dados['imagem'] : 'images/default.jpg';
      echo "<img src='{$imagem}' alt='{$dados['nome']}'>";
      echo "</div>";
    }
  } else {
    echo "<p style='color: gray;'>Nenhum produto encontrado nesta categoria.</p>";
  }
}

function exibirPratoDoDia($conn, $categoria) {
  global $diaAtual;
  $sql = "SELECT * FROM novo_cardapio WHERE pt_dia = 1 AND categoria = '$categoria' AND dia_da_semana = '$diaAtual' LIMIT 1";
  $query = mysqli_query($conn, $sql);
  if ($query && mysqli_num_rows($query) > 0) {
    while ($dados = mysqli_fetch_assoc($query)) {
      echo "<h3>Prato do Dia - {$categoria}</h3>";
      echo "<b>{$dados['nome']}</b><br>";
      if (!empty($dados['imagem'])) {
        echo "<img src='{$dados['imagem']}' alt='{$dados['nome']}' style='width:100%; max-width:200px; border-radius:8px;'><br>";
      }
      echo "<p>{$dados['descricao']}</p>";
      echo "<p><strong>R$ {$dados['valor']}</strong></p>";
      echo "<button class='add-cart'
              data-id='{$dados['id_cardapio']}'
              data-nome='{$dados['nome']}'
              data-valor='{$dados['valor']}'
              data-imagem='{$dados['imagem']}'
              data-descricao='{$dados['descricao']}'>
              + <i class='fas fa-cart-plus'></i>
            </button>";
    }
  } else {
    echo "<p>Nenhum prato do dia para hoje nesta categoria.</p>";
  }
}
?>

<!-- Categorias -->
<div id="massas" class="category-section" style="display:block;">
  <div class="left"><?php exibirProdutos($conn, 'Massas'); ?></div>
  <div class="right"><?php exibirPratoDoDia($conn, 'Massas'); ?></div>
</div>

<div id="tradicionais" class="category-section" style="display:none;">
  <div class="left"><?php exibirProdutos($conn, 'Tradicionais'); ?></div>
  <div class="right"><?php exibirPratoDoDia($conn, 'Tradicionais'); ?></div>
</div>

<div id="entradas" class="category-section" style="display:none;">
  <div class="left"><?php exibirProdutos($conn, 'Entradas'); ?></div>
  <div class="right"><?php exibirPratoDoDia($conn, 'Entradas'); ?></div>
</div>

<div id="vinhos" class="category-section" style="display:none;">
  <div class="left"><?php exibirProdutos($conn, 'Vinhos'); ?></div>
  <div class="right"><?php exibirPratoDoDia($conn, 'Vinhos'); ?></div>
</div>

<div id="sobremesas" class="category-section" style="display:none;">
  <div class="left"><?php exibirProdutos($conn, 'Sobremesas'); ?></div>
  <div class="right"><?php exibirPratoDoDia($conn, 'Sobremesas'); ?></div>
</div>

<div id="bebidas" class="category-section" style="display:none;">
  <div class="left"><?php exibirProdutos($conn, 'Bebidas'); ?></div>
  <div class="right"><?php exibirPratoDoDia($conn, 'Bebidas'); ?></div>
</div>

<script>
  function showSection(sectionId) {
    const sections = document.querySelectorAll('.category-section');
    sections.forEach(section => section.style.display = 'none');
    const selected = document.getElementById(sectionId);
    if (selected) selected.style.display = 'flex';
  }

  document.addEventListener("DOMContentLoaded", function () {
    showSection('massas');
    const botoesAddCart = document.querySelectorAll('.add-cart');
    botoesAddCart.forEach(botao => {
      botao.addEventListener('click', adicionarAoCarrinho);
    });
  });

  function adicionarAoCarrinho(event) {
    const botao = event.currentTarget;
    const produto = {
      id: botao.dataset.id,
      nome: botao.dataset.nome,
      valor: parseFloat(botao.dataset.valor),
      imagem: botao.dataset.imagem,
      descricao: botao.dataset.descricao,
      quantidade: 1
    };

    let carrinho = JSON.parse(sessionStorage.getItem('carrinho') || '[]');
    const existente = carrinho.findIndex(item => item.id === produto.id);

    if (existente >= 0) {
      carrinho[existente].quantidade++;
    } else {
      carrinho.push(produto);
    }

    sessionStorage.setItem('carrinho', JSON.stringify(carrinho));
    mostrarAlerta("Item adicionado ao carrinho!");
  }

  function mostrarAlerta(mensagem) {
    const alerta = document.getElementById("alerta");
    alerta.textContent = mensagem;
    alerta.style.display = "block";

    setTimeout(() => {
      alerta.style.display = "none";
    }, 3000);
  }
</script>

<div id="alerta" style="display:none;
                position: fixed;
                top: 80%;
                left: 50%;
                transform: translate(-50%,-50%);
                background-color: #FFFFFF;
                color: #3A6332;
                padding: 16px 24px;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                z-index: 1000;">
  Item adicionado ao Carrinho!
</div>

</body>
</html>
