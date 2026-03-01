<?php
// Verifica se a sessão já está ativa antes de iniciar
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['usuario_cpf'])) {
    header("Location: home.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão com banco de dados
require 'conexao.php';

// Verifica mensagem de cadastro bem-sucedido
$mensagem = '';
$erro = '';
if (isset($_SESSION['cadastro_sucesso'])) {
    $mensagem = $_SESSION['cadastro_sucesso'];
    unset($_SESSION['cadastro_sucesso']);
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$tipo_usuario = $_POST['tipo_usuario'] ?? 'cliente';
   if ($tipo_usuario === 'cliente') {
       $email = $_POST['email'] ?? '';
       $senha = $_POST['senha'] ?? '';
   } else {
       $cpf = $_POST['cpf'] ?? '';
       $senha = $_POST['cracha'] ?? ''; // Sim, "cracha" vai para a senha
   }



    try {
        // Validações básicas
        if ($tipo_usuario === 'cliente') {
            if (empty($email) || empty($senha)) {
                throw new Exception("Por favor, preencha todos os campos");
            }
        } else {
            if (empty($cpf) || empty($senha)) {
                throw new Exception("Por favor, preencha todos os campos");
            }
        }


        // Busca usuário no banco de acordo com o tipo
        if ($tipo_usuario === 'cliente') {
            $stmt = $conn->prepare("SELECT u.cpf, u.nome, u.senha
                                    FROM usuarios u
                                    JOIN clientes c ON u.cpf = c.fk_cpf
                                    WHERE c.email = ?");
            $stmt->bind_param("s", $email);
        } else {

            $stmt = $conn->prepare("SELECT u.cpf, u.nome, f.n_cracha AS senha, f.perfil
                                    FROM usuarios u
                                    JOIN funcionarios f ON u.cpf = f.fk_cpf
                                    WHERE u.cpf = ?");
            $stmt->bind_param("s", $cpf);

        }



        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Credenciais inválidas");
        }

        $usuario = $result->fetch_assoc();



        if ($tipo_usuario === 'cliente') {
            if (!password_verify($senha, $usuario['senha'])) {
                throw new Exception("Senha incorreta");
            }
        } else {
            // Funcionário: crachá não está em hash
            if ($senha !== $usuario['senha']) {
                throw new Exception("Senha incorreta");
            }
        }


        // Login bem-sucedido
       $_SESSION['usuario_cpf'] = $usuario['cpf'];           // ← ESTA LINHA FALTAVA
       $_SESSION['usuario_perfil'] = $usuario['perfil'];

       if ($usuario['perfil'] === 'admin' || $usuario['perfil'] === 'funcionario') {
           $redirect = 'home_func.php';
       } else {
           $redirect = 'home.php'; // ou outro destino para clientes ou usuários comuns
       }


        if (!empty($_SESSION['redirect_to'])) {
            $redirect = $_SESSION['redirect_to'];
            unset($_SESSION['redirect_to']);
        }

        header("Location: " . $redirect);
        exit();

    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}



?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Restaurante</title>
    <link href="https://fonts.googleapis.com/css2?family=Italianno&display=swap" rel="stylesheet">
    <style>
        :root {
            --cor-primaria: #3A6332;
            --cor-secundaria: #2e4a27;
            --cor-texto: #333;
            --cor-borda: #ddd;
            --cor-sucesso: #d4edda;
            --cor-erro: #f8d7da;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f8f8f8;
            color: var(--cor-texto);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            background-size: cover;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .titulo {
            font-family: 'Italianno', cursive;
            font-size: 2.8rem;
            color: var(--cor-primaria);
            margin-bottom: 1.5rem;
            font-weight: 400;
        }

        .login-container input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 1rem;
            border: 1px solid var(--cor-borda);
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .login-container input:focus {
            outline: none;
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 2px rgba(58, 99, 50, 0.2);
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: var(--cor-primaria);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .login-container button:hover {
            background-color: var(--cor-secundaria);
            transform: translateY(-2px);
        }

        .login-container button:active {
            transform: translateY(0);
        }

        .login-container button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .links-container {
            margin-top: 1.5rem;
        }

        .login-container a {
            color: var(--cor-primaria);
            text-decoration: none;
            display: block;
            margin: 0.5rem 0;
            transition: color 0.3s;
        }

        .login-container a:hover {
            color: var(--cor-secundaria);
            text-decoration: underline;
        }

        .mensagem {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            font-weight: bold;
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .mensagem.sucesso {
            background-color: var(--cor-sucesso);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensagem.erro {
            background-color: var(--cor-erro);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }

            .titulo {
                font-size: 2.2rem;
            }
         .radio-group {
                    margin: 15px 0;
                    display: flex;
                    justify-content: center;
                    gap: 20px;
                }

                .radio-option {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }

                .radio-option input[type="radio"] {
                    accent-color: var(--cor-primaria);
                }

                .radio-option label {
                    color: var(--cor-texto);
                    cursor: pointer;
                }
            .tipo-usuario-container {
                display: flex;
                gap: 20px; /* espaço entre as opções (ajuste se quiser) */
                align-items: center; /* alinha verticalmente, se necessário */
            }
        }
    .tipo-usuario-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 15px 0;
    }

    .tipo-option {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .tipo-option input[type="radio"] {
        accent-color: var(--cor-primaria);
    }

    .tipo-option label {
        color: var(--cor-texto);
        cursor: pointer;
        user-select: none;
    }
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
                         width: 50px;
                                   height: 50px;
                                   border-radius: 50%;
                                   position: absolute;
                                   top: 5px;
                                   left: 20px;;
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

                    .hidden {
                        display: none;
                    }

    </style>
</head>
<body>
    <header>
        <!-- Logo -->
        <a href="?page=home"><img src="logo.jpeg" class="imagem-circular" alt="La Doce Vita"></a>

        <!-- Menu horizontal -->
         <nav>
              <a href="?page=home">Home</a>
              <a href="?page=cardapio">Cardapio</a>
              <a href="?page=delivery">Delivery</a>
              <a href="?page=historia">Nossa História</a>
              <a href="?page=contato">Contato</a>
              <?php if(isset($_SESSION['usuario_cpf'])): ?>
                  <!-- Mostra apenas "Meu Perfil" quando logado -->
                  <a href="perfil.php" class="entrar">Meu Perfil</a>
              <?php else: ?>
                  <!-- Mostra "Entrar" quando não logado -->
                  <a href="login.php" class="entrar">Entrar</a>
              <?php endif; ?>
          </nav>
      </header>
    <div class="login-container">
        <div class="titulo">Login</div>

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem sucesso"><?= $mensagem ?></div>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <div class="mensagem erro"><?= $erro ?></div>
        <?php endif; ?>

       <form method="POST">
           <!-- Campo E-mail (Cliente) -->
           <div class="form-group" id="campo-email">
               <input type="email" name="email" placeholder="E-mail" autocomplete="username">
           </div>

           <!-- Campo CPF (Funcionário) -->
           <div class="form-group hidden" id="campo-cpf">
               <input type="text" name="cpf" placeholder="CPF">
           </div>

           <!-- Campo Senha (Cliente) -->
           <div class="form-group password-container" id="campo-senha">
               <input type="password" name="senha" placeholder="Senha" autocomplete="current-password">
               <span class="toggle-password" onclick="togglePasswordVisibility(this)">👁️</span>
           </div>

           <!-- Campo Crachá (Funcionário) -->
           <div class="form-group hidden" id="campo-cracha">
               <input type="text" name="cracha" placeholder="Nº do Crachá">

           </div>

           <div class="tipo-usuario-container">
               <div class="tipo-option">
                   <label><input type="radio" name="tipo_usuario" value="cliente" checked onchange="trocarTipoLogin()"> Cliente</label>
               </div>
               <div class="tipo-option">
                   <label><input type="radio" name="tipo_usuario" value="funcionario" onchange="trocarTipoLogin()"> Funcionário</label>
               </div>
           </div>

           <button type="submit" class="btn">Entrar</button>
       </form>


        <div class="links-container" id="links-cliente">
            <a href="cadastro.php">Não tem uma conta? Cadastre-se</a>
            <a href="recupera_senha.php">Esqueci minha senha</a>
        </div>
    </div>

    <script>
        // Função para alternar visibilidade da senha
        function togglePasswordVisibility(icon) {
            const senhaInput = icon.previousElementSibling;
            if (senhaInput.type === 'password') {
                senhaInput.type = 'text';
                icon.textContent = '👁️';
            } else {
                senhaInput.type = 'password';
                icon.textContent = '👁️';
            }
        }

        // Foco automático no campo de email
        document.addEventListener('DOMContentLoaded', function() {
        trocarTipoLogin();
            document.querySelector('input[name="email"]').focus();
        });
        // Adicione isto ao seu script existente
        document.querySelectorAll('.tipo-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove a seleção de todos os radios
                document.querySelectorAll('input[name="tipo_usuario"]').forEach(radio => {
                    radio.checked = false;
                });
                // Seleciona o radio dentro desta opção
                this.querySelector('input').checked = true;
            });
        });



        function trocarTipoLogin() {
            const tipo = document.querySelector('input[name="tipo_usuario"]:checked').value;

            const email = document.getElementById('campo-email');
            const senha = document.getElementById('campo-senha');
            const cpf = document.getElementById('campo-cpf');
            const cracha = document.getElementById('campo-cracha');
            const linksCliente = document.getElementById('links-cliente');

            if (tipo === 'cliente') {
                email.classList.remove('hidden');
                senha.classList.remove('hidden');
                cpf.classList.add('hidden');
                cracha.classList.add('hidden');
                linksCliente.classList.remove('hidden');
            } else {
                email.classList.add('hidden');
                senha.classList.add('hidden');
                cpf.classList.remove('hidden');
                cracha.classList.remove('hidden');
                linksCliente.classList.add('hidden');
            }
        }


    </script>
</body>
</html>