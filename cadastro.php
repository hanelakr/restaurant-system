<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmar-senha'];
    $cep = $_POST['cep'];
    $bairro = $_POST['bairro'];
    $numero = $_POST['numero'];
    $rua = $_POST['rua'];
    $cpf = $_POST['cpf'];
    $ddd = $_POST['ddd'];
    $telefone = $_POST['telefone'];

    // Guardar os valores em sessão para reaproveitar no formulário
    $_SESSION['form_data'] = [
        'nome' => $nome,
        'email' => $email,
        'cep' => $cep,
        'bairro' => $bairro,
        'numero' => $numero,
        'rua' => $rua,
        'cpf' => $cpf,
        'ddd' => $ddd,
        'telefone' => $telefone
    ];

    $erros = [];

    // Validações básicas
    if (empty($nome)) $erros['nome'] = "Nome é obrigatório";
    if (empty($email)) $erros['email'] = "E-mail é obrigatório";
    if (empty($senha)) $erros['senha'] = "Senha é obrigatória";
    if (empty($confirmarSenha)) $erros['confirmar-senha'] = "Confirme sua senha";
    if (empty($cep)) $erros['cep'] = "CEP é obrigatório";
    if (empty($bairro)) $erros['bairro'] = "Bairro é obrigatório";
    if (empty($numero)) $erros['numero'] = "Número é obrigatório";
    if (empty($rua)) $erros['rua'] = "Rua é obrigatório";
    if (empty($cpf)) $erros['cpf'] = "CPF é obrigatório";
    if (empty($ddd)) $erros['ddd'] = "DDD é obrigatório";
    if (empty($telefone)) $erros['telefone'] = "Telefone é obrigatório";

    if ($senha !== $confirmarSenha) {
        $erros['senha'] = "As senhas não coincidem!";
    }

    if (empty($cpf)) $erros['cpf'] = "CPF é obrigatório";

    // Verifica se o e-mail já está cadastrado
    $verificaEmail = "SELECT * FROM clientes WHERE email = ?";
    $stmtEmail = mysqli_prepare($conn, $verificaEmail);
    mysqli_stmt_bind_param($stmtEmail, "s", $email);
    mysqli_stmt_execute($stmtEmail);
    mysqli_stmt_store_result($stmtEmail);

    if (mysqli_stmt_num_rows($stmtEmail) > 0) {
        $erros['email'] = "Este e-mail já está cadastrado!";
    }

    // Verifica se o CPF já está cadastrado
    $verificaCpf = "SELECT * FROM usuarios WHERE cpf = ?";
    $stmtCpf = mysqli_prepare($conn, $verificaCpf);
    mysqli_stmt_bind_param($stmtCpf, "s", $cpf);
    mysqli_stmt_execute($stmtCpf);
    mysqli_stmt_store_result($stmtCpf);

    if (mysqli_stmt_num_rows($stmtCpf) > 0) {
        $erros['cpf'] = "Este CPF já está cadastrado!";
    }

    // Se houver erros, volta para o cadastro
    if (!empty($erros)) {
        $_SESSION['erros'] = $erros;
        header("Location: cadastro.php");
        exit();
    }

    // Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere na tabela 'usuarios'
    $queryUsuario = "INSERT INTO usuarios (cpf, nome, senha, telefone) VALUES (?, ?, ?, ?)";
    $stmtUsuario = mysqli_prepare($conn, $queryUsuario);
    mysqli_stmt_bind_param($stmtUsuario, "ssss", $cpf, $nome, $senhaHash, $telefone);

    if (mysqli_stmt_execute($stmtUsuario)) {
        // Insere na tabela 'clientes'
        $queryClientes = "INSERT INTO clientes (fk_cpf, email) VALUES (?, ?)";
        $stmtClientes = mysqli_prepare($conn, $queryClientes);
        mysqli_stmt_bind_param($stmtClientes, "ss", $cpf, $email);

        if (mysqli_stmt_execute($stmtClientes)) {
            // Insere na tabela 'enderecos'
            $tipo = "casa"; // Defina um valor padrão ou pegue do formulário
            $queryEndereco = "INSERT INTO enderecos (cep, numero, tipo, fk_cpf, bairro, rua) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtEndereco = mysqli_prepare($conn, $queryEndereco);
            mysqli_stmt_bind_param($stmtEndereco, "ssssss", $cep, $numero, $tipo, $cpf, $bairro, $rua);

            if (mysqli_stmt_execute($stmtEndereco)) {
                $_SESSION['cadastro_sucesso'] = "Cadastro realizado com sucesso! Faça login para continuar.";
                unset($_SESSION['form_data']);
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['mensagem'] = "Erro ao salvar endereço: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['mensagem'] = "Erro ao salvar dados do cliente: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['mensagem'] = "Erro ao cadastrar usuário: " . mysqli_error($conn);
    }

    header("Location: cadastro.php");
    exit();
    }

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente</title>
    <link href="https://fonts.googleapis.com/css2?family=Italianno&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 1000px;
            padding: 2px;
            margin-top: 10px;
        }

        .titulo {
            width: 100%;
            font-family: 'Italianno', cursive;
            font-weight: 400;
            font-size: 45px;
            line-height: 100%;
            color: #3A6332;
            text-align: center;
            margin-bottom: 20px;
        }

        h {
            font-family: 'Italiana';
            width: 450px;
            height: 80px;
            top: 50px;
            left: 500px;
            font-weight: 200;
            font-size: 28px;
            line-height: 100%;
            letter-spacing: 0px;
            color: #FFFFFF;
            text-align: center;
            margin-top: -10px;
        }

        .form-container {
            background-color: #3f6130;
            border-radius: 10px;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            flex-wrap: nowrap;
            max-width: 1200px;
            color: white;
            margin: 20px auto;
            gap: 20px;
        }

        .form-section {
            width: 400px;
            padding: 20px;
            margin: 0 auto;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group input {
            width: 90%;
            height: 25px;
            padding: 10px;
            border: none;
            border-radius: 10px;
            background-color: #D9D9D9;
            font-size: 16px;
            font-style: italic;
        }

        .form-group.inline-group {
            display: flex;
            gap: 5px;
        }

        .form-group.inline-group input {
            width: 45%;
        }

        .form-buttons {
            text-align: right;
            margin-top: 10px;
        }

        .form-buttons button {
            padding: 10px 20px;
            background-color: #3A6332;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            font-family: 'Italiana';
        }

        .form-buttons button:hover {
            background-color: #2e4a27;
        }

        /* Estilos de validação */
        .erro-campo {
            border: 2px solid #ff3860 !important;
        }

        .mensagem-erro {
            color: #ff3860;
            font-size: 12px;
            margin-top: 5px;
            text-align: left;
            padding-left: 5px;
        }

        .senha-invalida {
            border: 2px solid #ff3860 !important;
        }

        .senha-valida {
            border: 2px solid #3A6332 !important;
        }

        .senha-group span {
            font-size: 18px;
            cursor: pointer;
            margin-left: -30px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);

        }
    .divider {
                position: absolute;
                top: 100px;
                bottom: 90px;
                left: 50%;
                width: 3px;
                background-color: white;
                transform: translateX(-50%);
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

    </style>
</head>
<body>
     <header>
            <!-- Logo -->
            <a href="?page=home"><img src="logo.jpeg" class="imagem-circular" alt="La Doce Vita"></a>

            <!-- Menu horizontal -->
            <nav>
                <a href="home.php">Home</a>
                <a href="cardapio.php">Cardapio</a>
                <a href="delivery.php">Delivery</a>
                <a href="historia.php">Nossa História</a>
                <a href="contato.php">Contato</a>
                <?php if(isset($_SESSION['usuario_id'])): ?>
                    <!-- Mostra apenas "Meu Perfil" quando logado -->
                    <a href="perfil.php" class="entrar">Meu Perfil</a>
                <?php else: ?>
                    <!-- Mostra "Entrar" quando não logado -->
                    <a href="login.php" class="entrar">Entrar</a>
                <?php endif; ?>
            </nav>
        </header>
    <div id="formCadastro" class="container">
        <div class="titulo">Painel de Cadastro do Cliente</div>
        <form method="POST" action="cadastro.php" id="formCadastro" class="container">
            <div class="form-container">
                 <div class="divider"></div>
                <div class="form-section">
                    <h>Dados Pessoais</h>
                    <div class="form-group">
                        <input type="text" id="nome" placeholder="Insira seu nome completo" name="nome" required
                               value="<?= isset($_SESSION['form_data']['nome']) ? $_SESSION['form_data']['nome'] : '' ?>">
                        <div id="erro-nome" class="mensagem-erro"></div>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" placeholder="Insira seu e-mail" name="email" required
                               value="<?= isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : '' ?>">
                        <div id="erro-email" class="mensagem-erro"></div>
                    </div>
                    <div class="form-group senha-group">
                        <input type="password" id="senha" placeholder="Digite uma senha" name="senha" required>
                        <span onclick="toggleSenha('senha')">👁️</span>
                    </div>
                    <div class="form-group senha-group">
                        <input type="password" id="confirmar-senha" placeholder="Confirme sua senha" name="confirmar-senha" required>
                        <span onclick="toggleSenha('confirmar-senha')">👁️</span>
                        <div id="erro-senha" class="mensagem-erro"></div>
                    </div>
                </div>

                <div class="form-section">
                    <h>Dados Residenciais</h>
                    <div class="form-group">
                        <input type="text" id="cep" placeholder="Digite seu CEP" name="cep" required
                               value="<?= isset($_SESSION['form_data']['cep']) ? $_SESSION['form_data']['cep'] : '' ?>">
                        <div id="erro-cep" class="mensagem-erro"></div>
                    </div>
                    <div class="form-group inline-group">
                        <input type="text" id="bairro" placeholder="Digite seu bairro" name="bairro" required style="width: 68%;"
                               value="<?= isset($_SESSION['form_data']['bairro']) ? $_SESSION['form_data']['bairro'] : '' ?>">
                        <input type="text" id="numero" placeholder="Nº da casa" name="numero" required style="width: 20%;"
                               value="<?= isset($_SESSION['form_data']['numero']) ? $_SESSION['form_data']['numero'] : '' ?>">
                        <div id="erro-bairro" class="mensagem-erro"></div>
                        <div id="erro-numero" class="mensagem-erro"></div>
                    </div>
                    <div class="form-group">
                        <input type="text" id="cpf" placeholder="Insira seu CPF" name="cpf" required
                               value="<?= isset($_SESSION['form_data']['cpf']) ? $_SESSION['form_data']['cpf'] : '' ?>">
                        <div id="erro-cpf" class="mensagem-erro"></div>
                    </div>
                <div class="form-group">
                                        <input type="text" id="rua" placeholder="Informe sua rua" name="rua" required
                                               value="<?= isset($_SESSION['form_data']['rua']) ? $_SESSION['form_data']['rua'] : '' ?>">
                                        <div id="erro-ruaf" class="mensagem-erro"></div>
                                    </div>
                    <div class="form-group inline-group">
                        <input type="text" id="ddd" placeholder="DDD" name="ddd" required maxlength="2" style="width: 15%;"
                               value="<?= isset($_SESSION['form_data']['ddd']) ? $_SESSION['form_data']['ddd'] : '' ?>">
                        <input type="text" id="telefone" placeholder="Digite seu número" name="telefone" required maxlength="9" style="width: 75%;"
                               value="<?= isset($_SESSION['form_data']['telefone']) ? $_SESSION['form_data']['telefone'] : '' ?>">
                        <div id="erro-ddd" class="mensagem-erro"></div>
                        <div id="erro-telefone" class="mensagem-erro"></div>
                    </div>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit">Realizar cadastro</button>
            </div>
            <a href="login.php">Já tem uma conta? Faça login</a>
        </form>
    </div>

    <script>
        // Função para mostrar erros nos campos
        function mostrarErro(campoId, mensagem) {
            const campo = document.getElementById(campoId);
            const erroElement = document.getElementById(`erro-${campoId}`);

            campo.classList.add('erro-campo');
            erroElement.textContent = mensagem;
            erroElement.style.display = 'block';
        }

        // Função para remover erros
        function removerErro(campoId) {
            const campo = document.getElementById(campoId);
            const erroElement = document.getElementById(`erro-${campoId}`);

            campo.classList.remove('erro-campo');
            erroElement.style.display = 'none';
        }

        // Validação de senha em tempo real
        document.getElementById('confirmar-senha').addEventListener('input', function() {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = this.value;
            const senhaInput = this;

            if (confirmarSenha && senha !== confirmarSenha) {
                senhaInput.classList.add('senha-invalida');
                senhaInput.classList.remove('senha-valida');
                document.getElementById('erro-senha').textContent = "As senhas não coincidem";
                document.getElementById('erro-senha').style.display = 'block';
            } else if (confirmarSenha) {
                senhaInput.classList.remove('senha-invalida');
                senhaInput.classList.add('senha-valida');
                document.getElementById('erro-senha').style.display = 'none';
            }
        });

        // Validação ao enviar o formulário
        document.getElementById('formCadastro').addEventListener('submit', function(e) {
            let valido = true;

            // Verifica campos obrigatórios
            const camposObrigatorios = ['nome', 'email', 'senha', 'confirmar-senha', 'cep', 'bairro', 'numero', 'cpf', 'ddd', 'telefone'];

            camposObrigatorios.forEach(campoId => {
                const campo = document.getElementById(campoId);
                if (!campo.value.trim()) {
                    mostrarErro(campoId, "Este campo é obrigatório");
                    valido = false;
                } else {
                    removerErro(campoId);
                }
            });

            // Verifica se as senhas coincidem
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar-senha').value;

            if (senha !== confirmarSenha) {
                mostrarErro('senha', "As senhas não coincidem");
                mostrarErro('confirmar-senha', "As senhas não coincidem");
                valido = false;
            }

            if (!valido) {
                e.preventDefault();
            }
        });

        // Validação em tempo real para outros campos
        document.querySelectorAll('input[required]').forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    mostrarErro(this.id, "Este campo é obrigatório");
                } else {
                    removerErro(this.id);
                }
            });
        });

        // Mostrar erros do servidor
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['erros'])): ?>
                <?php foreach ($_SESSION['erros'] as $campo => $mensagem): ?>
                    mostrarErro('<?= $campo ?>', '<?= addslashes($mensagem) ?>');
                <?php endforeach; ?>
                <?php unset($_SESSION['erros']); ?>
            <?php endif; ?>
        });

        function toggleSenha(id) {
            const input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
            } else {
                input.type = "password";
            }
        }

        // Máscaras
        document.getElementById("telefone").addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/\D/g, "").replace(/(\d{4})(\d{4})/, "$1-$2");
        });

        document.getElementById("cpf").addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
        });
    </script>
</body>
</html>