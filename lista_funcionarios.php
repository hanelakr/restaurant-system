<?php
session_start();
if (!isset($_SESSION['usuario_perfil']) || $_SESSION['usuario_perfil'] !== 'admin') {
    header('Location: home_func.php');
    exit();
}

include 'conexao.php';

// Exclui funcionário, se solicitado
if (isset($_GET['excluir']) && !empty($_GET['excluir'])) {
    $cpf = $_GET['excluir'];

    mysqli_query($conn, "DELETE FROM enderecos WHERE fk_cpf = '$cpf'");
    mysqli_query($conn, "DELETE FROM funcionarios WHERE fk_cpf = '$cpf'");
    mysqli_query($conn, "DELETE FROM usuarios WHERE cpf = '$cpf'");

    header('Location: lista_funcionarios.php');
    exit();
}

// Altera o perfil, se solicitado
if (isset($_GET['cpf_alterar']) && isset($_GET['novo_perfil'])) {
    $cpf_alterar = $_GET['cpf_alterar'];
    $novo_perfil = $_GET['novo_perfil'] === 'admin' ? 'admin' : 'funcionario';

    $stmt = mysqli_prepare($conn, "UPDATE funcionarios SET perfil = ? WHERE fk_cpf = ?");
    mysqli_stmt_bind_param($stmt, "ss", $novo_perfil, $cpf_alterar);
    mysqli_stmt_execute($stmt);

    header('Location: lista_funcionarios.php');
    exit();
}

// Consulta para listar funcionários
$query = "SELECT u.nome, u.cpf, f.n_cracha, f.perfil
          FROM usuarios u
          JOIN funcionarios f ON u.cpf = f.fk_cpf";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Funcionários</title>
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

        h1 {
            text-align: center;
            color: #3f6130;
        }

        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #3f6130;
            color: white;
        }

        .btn-excluir, .btn-alterar {
            background-color: transparent;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-excluir {
            color: red;
        }

        .btn-excluir:hover {
            color: darkred;
        }

        .btn-alterar {
            color: #007bff;
        }

        .btn-alterar:hover {
            color: #0056b3;
        }

        .novo-func {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #3f6130;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }

        .novo-func:hover {
            background-color: #2e4a27;
        }
    </style>
</head>
<body>
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

    <h1>Lista de Funcionários</h1>
    <table>
        <tr>
            <th>Nome</th>
            <th>CPF</th>
            <th>Crachá</th>
            <th>Perfil</th>
            <th>Ações</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($row['nome']) ?></td>
            <td><?= htmlspecialchars($row['cpf']) ?></td>
            <td><?= htmlspecialchars($row['n_cracha']) ?></td>
            <td><?= htmlspecialchars($row['perfil']) ?></td>
            <td>
                <!-- Excluir -->
                <form method="GET" style="display:inline;" onsubmit="return confirm('Deseja excluir este funcionário?');">
                    <input type="hidden" name="excluir" value="<?= htmlspecialchars($row['cpf']) ?>">
                    <button type="submit" class="btn-excluir" title="Excluir funcionário">🗑️</button>
                </form>

                <!-- Alterar perfil -->
                <form method="GET" style="display:inline;">
                    <input type="hidden" name="cpf_alterar" value="<?= htmlspecialchars($row['cpf']) ?>">
                    <input type="hidden" name="novo_perfil" value="<?= $row['perfil'] === 'admin' ? 'funcionario' : 'admin' ?>">
                    <button type="submit" class="btn-alterar" title="Alterar perfil">
                        <?= $row['perfil'] === 'admin' ? '⬇️ Rebaixar' : '⬆️ Tornar Admin' ?>
                    </button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="cadastra_func.php" class="novo-func">+ Cadastrar Novo Funcionário</a>
</body>
</html>
