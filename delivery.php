<?php
$usuario_logado = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho De Compras Delivery</title>
    <link rel="stylesheet" href="delivery.css">
    <link href="https://fonts.googleapis.com/css2?family=Italianno&family=Italiana:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <a href="?page=home"><img src="logo.jpeg" alt="Logo" class="imagem-circular"></a>
        <nav>
             <a href="?page=home">Home</a>
             <a href="?page=cardapio">Cardapio</a>
             <a href="?page=delivery">Delivery</a>
             <a href="?page=historia">Nossa História</a>
             <a href="?page=contato">Contato</a>
             <?php if(isset($_SESSION['usuario_id'])): ?>
                 <!-- Mostra apenas "Meu Perfil" quando logado -->
                 <a href="perfil.php" class="entrar">Meu Perfil</a>
             <?php else: ?>
                 <!-- Mostra "Entrar" quando não logado -->
                 <a href="login.php" class="entrar">Entrar</a>
             <?php endif; ?>
         </nav>
     </header>

    <div class="titulo-principal">
        <h1>Carrinho De Compras Delivery</h1>
    </div>

    <div class="container">
        <div class="titulo-produtos">Produtos</div>
        
        <div class="conteudo-principal">
            <div class="produtos" id="produtos-carrinho"></div>

            <div class="endereco">
                <h2>Endereço</h2>
                <form action="salvar_endereco.php" method="POST">
                    <input type="text" name="cep" placeholder="Digite seu CEP" required>
                    <input type="text" name="numero" placeholder="Digite o Nº da residência" required>
                    <input type="text" name="tipo" placeholder="Tipo da residência" required>
                    <button type="submit" onclick="salvarEnderecoLocalmente()">Confirmar</button>
                </form>
            </div>
        </div>

        <div class="rodape">
            <div class="valor-total">
                <h3>Valor Total com Entrega</h3>
                <p id="valor-total-com-entrega">R$ 0.00 + R$ 7,00</p>
            </div>

            <div class="botoes">
                <button class="limpar">Limpar Carrinho</button>
                <?php if ($usuario_logado): ?>
                    <button class="realizar" onclick="window.location.href='pagamento.php'">Realizar Pedido</button>
                <?php else: ?>
                    <button class="realizar" onclick="alert('Você precisa estar logado para realizar o pedido!')">Realizar Pedido</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        atualizarCarrinho();
    });

    function atualizarCarrinho() {
        const carrinho = JSON.parse(sessionStorage.getItem('carrinho') || '[]');
        const produtosCarrinho = document.getElementById('produtos-carrinho');
        produtosCarrinho.innerHTML = '';
        let total = 0;

        if (carrinho.length === 0) {
            produtosCarrinho.innerHTML = `
                <p style="color:white; font-size: 18px;">Seu carrinho está vazio.</p>
                <button onclick="window.location.href='?page=cardapio'" style="
                    margin-top: 10px;
                    padding: 10px 20px;
                    font-size: 16px;
                    background-color: #28a745;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                ">
                    Ir para o Cardápio
                </button>
            `;
            document.getElementById("valor-total-com-entrega").textContent = "R$ 0.00 + R$ 7.00";
            return;
        }

        carrinho.forEach(item => {
            const produtoDiv = document.createElement('div');
            produtoDiv.classList.add('produto');
            produtoDiv.innerHTML = `
                <img src="${item.imagem}" alt="${item.nome}" width="50">
                <div class="info-produto">
                    <h3>${item.nome}</h3>
                    <p>${item.descricao}</p>
                </div>
                <div class="quantidade">
                    <button class="diminuir" data-id="${item.id}">-</button>
                    <span class="quantidade-item" data-id="${item.id}">${item.quantidade}</span>
                    <button class="aumentar" data-id="${item.id}">+</button>
                </div>
                <span class="preco-item" data-id="${item.id}">R$ ${(item.valor * item.quantidade).toFixed(2)}</span>
            `;
            produtosCarrinho.appendChild(produtoDiv);
            total += item.valor * item.quantidade;

            produtoDiv.querySelector(".diminuir").addEventListener('click', diminuirQuantidade);
            produtoDiv.querySelector(".aumentar").addEventListener('click', aumentarQuantidade);
        });


	const botaoContinuar = document.createElement('button');
	botaoContinuar.textContent = 'Continuar Comprando';
	botaoContinuar.style = `
		margin-top: 20px;
		padding: 10px 20px;
		font-size: 16px;
		background-color: #28a745;
		color: white;
		border: none;
		border-radius: 8px;
		cursor: pointer;
	`;
	botaoContinuar.onclick = function () {
		window.location.href = '?page=cardapio';
	};
	produtosCarrinho.appendChild(botaoContinuar);


        document.getElementById('valor-total-com-entrega').textContent = `R$ ${total.toFixed(2)} + R$ 7.00`;
    }

    function diminuirQuantidade(event) {
        const itemId = event.target.dataset.id;
        let carrinho = JSON.parse(sessionStorage.getItem('carrinho') || '[]');
        const itemIndex = carrinho.findIndex(item => item.id == itemId);
        if (carrinho[itemIndex].quantidade > 1) {
            carrinho[itemIndex].quantidade--;
        } else {
            carrinho.splice(itemIndex, 1);
        }
        sessionStorage.setItem('carrinho', JSON.stringify(carrinho));
        atualizarCarrinho();
    }

    function aumentarQuantidade(event) {
        const itemId = event.target.dataset.id;
        let carrinho = JSON.parse(sessionStorage.getItem('carrinho') || '[]');
        const itemIndex = carrinho.findIndex(item => item.id == itemId);
        carrinho[itemIndex].quantidade++;
        sessionStorage.setItem('carrinho', JSON.stringify(carrinho));
        atualizarCarrinho();
    }

    function salvarEnderecoLocalmente() {
        const cep = document.querySelector('input[name="cep"]').value;
        const numero = document.querySelector('input[name="numero"]').value;
        const tipo = document.querySelector('input[name="tipo"]').value;

        const endereco = { cep, numero, tipo };
        localStorage.setItem("enderecoEntrega", JSON.stringify(endereco));
    }

    document.querySelector('.limpar').addEventListener('click', function() {
        sessionStorage.removeItem('carrinho');
        atualizarCarrinho();
    });
</script>
</body>
</html>
