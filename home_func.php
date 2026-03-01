<?php
    session_start();
$isLogado = isset($_SESSION['usuario_cpf']);
$isAdmin = $isLogado && isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin';

    // Identifica a página atual
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';

    // Inclui o conteúdo correto baseado na página
    switch ($page) {
        case 'home':
          break;

        case 'funcionario':
            include('funcionario.php');
            break;

        case 'delivery':
            include('delivery.php');
            break;

        case 'cadastra_func':
           include('cadastra_func.php');
            break;

        case 'contato':
            echo "<h1>Contato</h1>";
            echo "<p>Entre em contato conosco pelo telefone ou e-mail.</p>";
            break;

 case 'perfil_func':
            include('perfil_func.php');
            break;

case 'lista_funcionarios':
            include('lista_funcionarios.php');
            break;

    }
    ?>

    <!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Doce Vita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px 20px;
            padding-top: 70px; /* Compensa a altura do cabeçalho */
            color: #333;
        }

        /* Estilo para a página "Home" */
        body.home {
            background-image: url('homers.jpeg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* Estilo para outras páginas */
        body.funcionario,
        body.delivery,
        body.,
        body.contato,
        body.cadastra_func,

        body.recuperar {
            background-color: white;
        }

        /* Estilo para o cabeçalho */
        header {
            background-color: #3f6130;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        header a {
            text-decoration: none;
            color: white;
        }

        header img {
            height: 50px;
            cursor: pointer;
        }

        /* Menu horizontal */
        nav {
            display: flex;
            justify-content: space-evenly;
            width: 100%;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px;
        }

        nav a:hover {
            text-decoration: underline;
        }

       .entrar {
      background-color: #2c4624; /* Um fundo mais escuro para o botão "Entrar" */
      padding: 10px 20px; /* Adiciona espaçamento dentro do botão */
      border-radius: 5px; /* Torna o botão arredondado */
  }

  .entrar:hover {
      background-color: #1e
      }
       .container {
         text-align: center;
         padding: 80px 20px 20px; /* Ajustei o padding superior para dar espaço ao cabeçalho fixo */
       }

       .login-container {
         max-width: 400px;
         margin: 50px auto;
         padding: 20px;
         background-color: white;
         box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
         border-radius: 8px;
       }

       .login-container h2 {
         margin-bottom: 20px;
       }

       .login-container form input {
         width: 100%;
         padding: 10px;
         margin: 10px 0;
         border: 1px solid #ccc;
         border-radius: 5px;
       }

       .login-container form button {
         width: 100%;
         padding: 10px;
         background-color: #3f6130;
         color: white;
         border: none;
         border-radius: 5px;
         cursor: pointer;
       }

       .login-container button:hover {
         background-color: #2c4624;
       }

       .login-container .options {
         margin-top: 15px;
         text-align: center;
       }

       .login-container .options a {
         display: block;
         color: #3f6130;
         text-decoration: none;
         margin-top: 10px;
         font-size: 14px;
       }

       .login-container .options a:hover {
         text-decoration: underline;
       }

        .imagem-circular {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            position: absolute;
            top: 5px;
            left: 20px;
        }
    .entrar, .sair {
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: all 0.3s;
    }

    .entrar {
        background-color: #3A6332; /* Verde do seu tema */
        color: white;
    }

    .entrar:hover {
        background-color: #2e4a27; /* Verde mais escuro */
    }

    </style>
</head>
<body class="<?php echo isset($_GET['page']) ? $_GET['page'] : 'home'; ?>">

<header>
    <!-- Logo -->
    <a href="?page=home"><img src="logo.jpeg" class="imagem-circular" alt="La Doce Vita"></a>

    <!-- Menu horizontal -->
   <nav>
       <a href="?page=home">Home</a>
       <a href="?page=funcionario">Opções de cardápio</a>
       <a href="?page=delivery">Delivery</a>

       <?php if (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin'): ?>
           <a href="?page=lista_funcionarios">Lista de Funcionários</a>
       <?php endif; ?>

       <a href="?page=perfil_func">Meu Perfil</a>
   </nav>

    </nav>
</header>

<div class="container">

</div>

</body>
</html>
