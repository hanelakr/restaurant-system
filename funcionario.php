<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexao.php';
?>


     <header>
             <a href="home_func.php"><img src="logo.jpeg" alt="La Doce Vita"></a>
             <nav>
                             <a href="home_func.php">Home</a>
                             <a href="funcionario.php">Opções de cardápio</a>
                             <a href="delivery.php">Delivery</a>
                            <?php if (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin'): ?>
                                <a href="lista_funcionarios.php">Lista de Funcionários</a>
                            <?php endif; ?>
                             <a href="perfil_func.php">Meu Perfil</a>
                             </nav>
         </header>
 <div style="display: flex; height: 100vh;">
     <!-- LEFTBAR -->
     <div class="leftbar">
         <h3>Menu</h3>
         <ul style="list-style: none; padding: 0;">

            <li><a href="?page=funcionario&acao=cardapio">Cardápio</a></li>
            <li><a href="?page=funcionario&acao=incluir">Incluir Produto</a></li>
            <li><a href="?page=funcionario&acao=alterar">Alterar Produto</a></li>
            <li><a href="?page=funcionario&acao=excluir">Excluir Produto</a></li>
         </ul>
     </div>

     <!-- CONTENT -->
     <div class="content">
         <?php
        $acao = $_GET['acao'] ?? '';

        switch ($acao) {
            case 'cardapio':
                include('cardapio_funcionario.php');
                break;
            case 'incluir':
                include('incluir_produto.php');
                break;
            case 'alterar':
                include('alterar_produto.php');
                break;
            case 'excluir':
                include('excluir_produto.php');
                break;
            default:
                echo "<h2>Bem-vindo à área do funcionário.</h2>";
        }
         ?>
     </div>
 </div>


<style>
    .leftbar {
        width: 200px;
        background-color: #2e4f29;
        color: white;
        padding: 20px;
        border-radius: 15px;
    }

    .leftbar h3 {
        margin-top: 0;
    }

    .leftbar a {
        color: white;
        text-decoration: none;
        display: block;
        margin: 10px 0;
        padding: 5px;
        border-radius: 6px;
    }

    .leftbar a:hover {
        background-color: #3A6332;
    }

    .content {
        flex-grow: 1;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        margin: 20px;
    }
</style>
