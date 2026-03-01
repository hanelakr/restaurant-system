<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Compra Confirmada</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #3A6332; /* fundo verde escuro */
      color: #333;
    }

    .container {
      padding: 40px 30px;
      max-width: 600px;
      margin: 60px auto;
      background-color: white;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      text-align: center;
    }

    h1 {
      color: #3A6332;
      font-size: 2.5em;
      margin-bottom: 10px;
    }

    p {
      font-size: 1.2em;
      color: #333;
    }

    .pedido, .endereco {
      text-align: left;
      margin-top: 30px;
      padding: 20px;
      background-color: #fff;
      border: 2px solid #3A6332;
      border-radius: 12px;
    }

    .produto {
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #ccc;
    }

    .produto:last-child {
      border-bottom: none;
    }

    .botao-voltar {
      margin-top: 40px;
      padding: 12px 24px;
      font-size: 18px;
      background-color: #3A6332;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .botao-voltar:hover {
      background-color: #2a4a25;
    }
  </style>
</head>
<body>

  <div class="container">
    <h1>Compra Confirmada!</h1>
    <p>Obrigado pelo seu pedido. Aqui está o resumo:</p>

    <div class="pedido" id="resumo-pedido"></div>
    <div class="endereco" id="endereco-entrega"></div>

    <button class="botao-voltar" onclick="voltarInicio()">Voltar para o Início</button>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const dados = JSON.parse(localStorage.getItem("dadosConfirmacao") || '{}');
      const carrinho = dados.carrinho || [];
      const endereco = dados.endereco || {};

      const resumo = document.getElementById("resumo-pedido");
      const entrega = document.getElementById("endereco-entrega");

      let total = 0;
      carrinho.forEach(item => {
        resumo.innerHTML += `
          <div class="produto">
            <strong>${item.nome}</strong> - ${item.quantidade}x<br>
            <small>${item.descricao}</small><br>
            <span>R$ ${(item.valor * item.quantidade).toFixed(2)}</span>
          </div>
        `;
        total += item.valor * item.quantidade;
      });

      resumo.innerHTML += `<strong>Total + Entrega: R$ ${(total + 7).toFixed(2)}</strong>`;

      entrega.innerHTML = `
        <h3>Endereço de Entrega:</h3>
        <p>CEP: ${endereco.cep}</p>
        <p>Nº: ${endereco.numero}</p>
        <p>Tipo: ${endereco.tipo}</p>
      `;

      sessionStorage.removeItem('carrinho');
    });

    function voltarInicio() {
      window.location.href = "home.php";
    }
  </script>
</body>
</html>
