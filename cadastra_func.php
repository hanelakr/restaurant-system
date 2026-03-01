<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexao.php';
// Verifica se é admin
if (!isset($_SESSION['usuario_perfil']) || $_SESSION['usuario_perfil'] !== 'admin') {
    header('Location: home_func.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta dados do formulário
    $dados = [
        'nome' => $_POST['nome'] ?? '',
        'cep' => $_POST['cep'] ?? '',
        'n_cracha' => $_POST['n_cracha'] ?? '',
        'perfil' => $_POST['perfil'] ?? 'funcionario',
        'bairro' => $_POST['bairro'] ?? '',
        'numero' => $_POST['numero'] ?? '',
        'rua' => $_POST['rua'] ?? '',
        'cpf' => $_POST['cpf'] ?? '',
        'ddd' => $_POST['ddd'] ?? '',
        'telefone' => $_POST['telefone'] ?? ''

    ];

    // Extrai variáveis para uso nas queries
    extract($dados);

    // Validações
    $erros = [];
    foreach ($dados as $campo => $valor) {
        if (empty($valor)) {
            $erros[$campo] = ucfirst($campo) . " é obrigatório";
        }
    }

    // Verifica se o crachá já está cadastrado (somente se não houver outros erros)
    if (empty($erros)) {
        $verificaCracha = "SELECT * FROM funcionarios WHERE n_cracha = ?";
        $stmtCracha = mysqli_prepare($conn, $verificaCracha);
        mysqli_stmt_bind_param($stmtCracha, "s", $n_cracha);
        mysqli_stmt_execute($stmtCracha);
        mysqli_stmt_store_result($stmtCracha);

        if (mysqli_stmt_num_rows($stmtCracha) > 0) {
            $erros['n_cracha'] = "Este crachá já está cadastrado!";
        }
    }

    // Verifica se o CPF já está cadastrado (somente se não houver outros erros)
    if (empty($erros)) {
        $verificaCpf = "SELECT * FROM usuarios WHERE cpf = ?";
        $stmtCpf = mysqli_prepare($conn, $verificaCpf);
        mysqli_stmt_bind_param($stmtCpf, "s", $cpf);
        mysqli_stmt_execute($stmtCpf);
        mysqli_stmt_store_result($stmtCpf);

        if (mysqli_stmt_num_rows($stmtCpf) > 0) {
            $erros['cpf'] = "Este CPF já está cadastrado!";
        }
    }

    // Se houver erros, volta para o cadastro
    if (!empty($erros)) {
        $_SESSION['erros'] = $erros;
        $_SESSION['form_data'] = $dados;
        header("Location: cadastra_func.php");
        exit();
    }

    // Insere na tabela 'usuarios'
    $queryUsuario = "INSERT INTO usuarios (cpf, nome, telefone) VALUES (?, ?, ?)";
    $stmtUsuario = mysqli_prepare($conn, $queryUsuario);
    mysqli_stmt_bind_param($stmtUsuario, "sss", $cpf, $nome, $telefone);

    if (mysqli_stmt_execute($stmtUsuario)) {
        // Insere na tabela 'funcionarios'
        $queryFuncionarios = "INSERT INTO funcionarios (fk_cpf, n_cracha, perfil) VALUES (?, ?, ?)";
        $stmtFuncionarios = mysqli_prepare($conn, $queryFuncionarios);
        mysqli_stmt_bind_param($stmtFuncionarios, "sss", $cpf, $n_cracha, $perfil);

        if (mysqli_stmt_execute($stmtFuncionarios)) {
            // Insere na tabela 'enderecos'
            $tipo = "casa";
            $queryEndereco = "INSERT INTO enderecos (cep, numero, tipo, fk_cpf, bairro, rua) VALUES (?, ?, ?, ?, ?, ?)";
            $stmtEndereco = mysqli_prepare($conn, $queryEndereco);
            mysqli_stmt_bind_param($stmtEndereco, "ssssss", $cep, $numero, $tipo, $cpf, $bairro, $rua);

           if (mysqli_stmt_execute($stmtEndereco)) {
               $_SESSION['cadastro_sucesso'] = "Funcionário cadastrado com sucesso!";
               unset($_SESSION['form_data']);

               // Redireciona para a lista de funcionários ou para a página de cadastro novamente
               header("Location: cadastra_func.php");

               exit();
           }

             else {
                $_SESSION['mensagem'] = "Erro ao salvar endereço: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['mensagem'] = "Erro ao salvar dados do funcionário: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['mensagem'] = "Erro ao cadastrar usuário: " . mysqli_error($conn);
    }

    header("Location: cadastra_func.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionário</title>
    <link href="https://fonts.googleapis.com/css2?family=Italianno&display=swap" rel="stylesheet">
    <style>
        body {


            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
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
            left: 20px;
        }

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

        .container {
            width: 1000px;
            margin: 0px auto 0px;
        }

        .titulo {
            font-family: 'Italianno', cursive;
            font-size: 45px;
            color: #3A6332;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container {
            background-color: #3f6130;
            border-radius: 10px;
            padding: 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .form-section {
            width: 48%;
        }

        .form-section h {
            font-family: 'Italiana';
            font-size: 24px;
            display: block;
            margin-bottom: 15px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        .form-group input, select {
            width: 100%;
            height: 35px;
            padding: 5px 10px;
            border: none;
            border-radius: 8px;
            background-color: #D9D9D9;
            font-size: 15px;
            color: #333;
        }

        .form-group.inline-group {
            display: flex;
            gap: 10px;
        }

        .form-group.inline-group input {
            width: 48%;
        }

        .form-buttons {
            text-align: right;
            margin-top: 15px;
        }

        .form-buttons button {
            padding: 10px 20px;
            background-color: #2e4a27;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .form-buttons button:hover {
            background-color: #1c311a;
        }

        .mensagem-erro {
            color: #ff3860;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

<header>
    <?php if (isset($_SESSION['cadastro_sucesso'])): ?>
            <div style="background-color: #4CAF50; color: white; padding: 10px; text-align: center; position: fixed; top: 70px; width: 100%; z-index: 999;">
                <?= $_SESSION['cadastro_sucesso'] ?>
            </div>
            <?php unset($_SESSION['cadastro_sucesso']); ?>
        <?php endif; ?>

    <a href="home_func.php"><img src="logo.jpeg" alt="La Doce Vita"></a>
   <nav>
         <nav>
                <a href="home_func.php">Home</a>
                <a href="funcionario.php">Opções de cardápio</a>
                <a href="delivery.php">Delivery</a>
               <?php if (isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin'): ?>
                   <a href="lista_funcionarios.php">Lista de Funcionários</a>
               <?php endif; ?>
                <a href="perfil_func.php">Meu Perfil</a>
                </nav>
             </nav>
</header>

<div class="container">
    <div class="titulo">Cadastro de Funcionário</div>
    <form method="POST" action="cadastra_func.php">
        <div class="form-container">
            <div class="form-section">
                <h>Dados Pessoais</h>
                <div class="form-group">
                    <input type="text" name="nome" placeholder="Nome completo" required>
                </div>
                <div class="form-group">
                    <input type="text" name="cpf" placeholder="CPF" required>
                </div>
                <div class="form-group">
                    <input type="text" name="n_cracha" placeholder="Nº do crachá" required>
                </div>
                <div class="form-group">
                    <select name="perfil" required>
                        <option value="funcionario">Funcionário</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="form-group inline-group">
                    <input type="text" name="ddd" placeholder="DDD" required maxlength="2">
                    <input type="text" name="telefone" placeholder="Telefone" required maxlength="9">
                </div>
            </div>
            <div class="form-section">
                <h>Endereço</h>
                <div class="form-group">
                    <input type="text" name="cep" placeholder="CEP" required>
                </div>
                <div class="form-group inline-group">
                    <input type="text" name="bairro" placeholder="Bairro" required>
                    <input type="text" name="numero" placeholder="Número" required>
                </div>
                <div class="form-group">
                    <input type="text" name="rua" placeholder="Rua" required>
                </div>
            </div>
        </div>

        <div class="form-buttons">
            <button type="submit">Cadastrar Funcionário</button>
        </div>
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
            const camposObrigatorios = ['nome', 'n_cracha', 'cep', 'bairro', 'numero', 'rua', 'cpf', 'ddd', 'telefone'];

            camposObrigatorios.forEach(campoId => {
                const campo = document.getElementById(campoId);
                if (!campo.value.trim()) {
                    mostrarErro(campoId, "Este campo é obrigatório");
                    valido = false;
                } else {
                    removerErro(campoId);
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


        // Máscaras
        document.getElementById("telefone").addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/\D/g, "").replace(/(\d{4})(\d{4})/, "$1-$2");
        });

        document.getElementById("cpf").addEventListener("input", function(e) {
            e.target.value = e.target.value.replace(/\D/g, "").replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
        });
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 5000);
            }
        });

    </script>
</body>
</html>