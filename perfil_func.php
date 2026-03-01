<?php
// Inicia a sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_cpf'])) {
    header("Location: login.php");
    exit();
}

// Define variáveis de perfil consistentemente
$isLogado = isset($_SESSION['usuario_cpf']);
$isAdmin = isset($_SESSION['usuario_perfil']) && $_SESSION['usuario_perfil'] === 'admin';

require 'conexao.php';

try {
    $stmt = $conn->prepare("
        SELECT u.nome AS nome_usuario,
               u.telefone,
               f.n_cracha AS cracha,
               e.cep, e.numero, e.tipo
        FROM usuarios u
        LEFT JOIN funcionarios f ON u.cpf = f.fk_cpf
        LEFT JOIN enderecos e ON u.cpf = e.fk_cpf
        WHERE u.cpf = ?
    ");

    $stmt->bind_param("s", $_SESSION['usuario_cpf']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Usuário não encontrado");
    }

    $usuario = $result->fetch_assoc();

    // Define valores padrão
    $usuario['nome_usuario'] = $usuario['nome_usuario'] ?? '';
    $usuario['telefone'] = $usuario['telefone'] ?? '';
    $usuario['cracha'] = $usuario['cracha'] ?? '';
    $usuario['cep'] = $usuario['cep'] ?? '';
    $usuario['numero'] = $usuario['numero'] ?? '';
    $usuario['tipo'] = $usuario['tipo'] ?? 'Casa';

} catch (Exception $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - La Doce Vita</title>
    <style>
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

        .perfil-container {
            max-width: 600px;
            margin: 100px auto 30px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn-salvar {
            background: #3A6332;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-sair {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .mensagem {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .sucesso {
            background: #d4edda;
            color: #155724;
        }

        .erro {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <header>
        <a href="?page=home"><img src="logo.jpeg" class="imagem-circular" alt="La Doce Vita"></a>

           <!-- Menu horizontal -->
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

    <div class="perfil-container">
        <h1>Meu Perfil</h1>

        <?php if(isset($_SESSION['mensagem'])): ?>
            <div class="mensagem sucesso"><?= htmlspecialchars($_SESSION['mensagem']) ?></div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['erro'])): ?>
            <div class="mensagem erro"><?= htmlspecialchars($_SESSION['erro']) ?></div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>

        <form method="POST" action="atualizar_perfil.php">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" value="<?= htmlspecialchars($usuario['nome_usuario']) ?>" readonly>
            </div>

            <div class="form-group">
                <label>Crachá</label>
                <input type="text" value="<?= htmlspecialchars($usuario['cracha']) ?>" readonly>
            </div>

            <div class="form-group">
                <label>Telefone</label>
                <input type="tel" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" required>
            </div>

            <h2>Endereço</h2>

            <div class="form-group">
                <label>CEP</label>
                <input type="text" name="cep" value="<?= htmlspecialchars($usuario['cep']) ?>" required>
            </div>

            <div class="form-group">
                <label>Número</label>
                <input type="text" name="numero" value="<?= htmlspecialchars($usuario['numero']) ?>" required>
            </div>

            <div class="form-group">
                <label>Tipo de Residência</label>
                <select name="tipo" required>
                    <option value="Casa" <?= $usuario['tipo'] == 'Casa' ? 'selected' : '' ?>>Casa</option>
                    <option value="Apartamento" <?= $usuario['tipo'] == 'Apartamento' ? 'selected' : '' ?>>Apartamento</option>
                    <option value="Comercial" <?= $usuario['tipo'] == 'Comercial' ? 'selected' : '' ?>>Comercial</option>
                </select>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn-salvar">Salvar Alterações</button>
                <a href="logout.php" class="btn-sair">Sair</a>
            </div>
        </form>
    </div>
</body>
</html>