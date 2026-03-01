<?php
include("conexao.php");

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Busca os dados do produto
    $sql = "SELECT * FROM novo_cardapio WHERE id_cardapio = $id";
    $resultado = mysqli_query($conn, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $produto = mysqli_fetch_assoc($resultado);
    } else {
        echo "<p>Produto não encontrado.</p>";
        exit;
    }

    // Se o formulário foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];
        $valor = floatval($_POST['valor']);
        $imagem = $_POST['imagem'];
        $categoria = $_POST['categoria'];
        $pt_dia = isset($_POST['pt_dia']) ? 1 : 0;
        $dia_da_semana = $_POST['dia_da_semana'] ?: NULL;

        // Escapa strings para segurança
        $nome = mysqli_real_escape_string($conn, $nome);
        $descricao = mysqli_real_escape_string($conn, $descricao);
        $imagem = mysqli_real_escape_string($conn, $imagem);
        $categoria = mysqli_real_escape_string($conn, $categoria);
        $dia_da_semana = $dia_da_semana ? "'" . mysqli_real_escape_string($conn, $dia_da_semana) . "'" : "NULL";

        // Atualiza os dados
        $sqlUpdate = "UPDATE novo_cardapio SET
                        nome = '$nome',
                        descricao = '$descricao',
                        valor = $valor,
                        imagem = '$imagem',
                        categoria = '$categoria',
                        pt_dia = $pt_dia,
                        dia_da_semana = $dia_da_semana
                      WHERE id_cardapio = $id";

        if (mysqli_query($conn, $sqlUpdate)) {
            $_SESSION['mensagem'] = "Produto atualizado com sucesso!";
            header("Location: funcionario.php?page=alterar_produto");
            exit;
        } else {
            echo "<p style='color:red;'>Erro ao atualizar produto: " . mysqli_error($conn) . "</p>";
        }
    }
} else {
    echo "<p>ID do produto não especificado.</p>";
    exit;
}
?>

<div class="form-container">
    <h2>Editar Produto</h2>

    <form method="POST">
        <label>Nome do Produto</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>

        <label>Descrição Do Produto:</label>
        <textarea name="descricao" rows="3" required><?= htmlspecialchars($produto['descricao']) ?></textarea>

        <label>Insira o Valor (R$):</label>
        <input type="text" name="valor" value="<?= $produto['valor'] ?>" required>

        <label>Insira a Imagem (URL):</label>
        <input type="text" name="imagem" value="<?= htmlspecialchars($produto['imagem']) ?>">

        <label>Categoria:</label>
        <select name="categoria" required>
            <?php
            $categorias = ["massas", "tradicionais", "entradas", "vinhos", "sobremesas", "bebidas"];
            foreach ($categorias as $cat) {
                $selected = ($produto['categoria'] == $cat) ? 'selected' : '';
                echo "<option value='$cat' $selected>" . ucfirst($cat) . "</option>";
            }
            ?>
        </select>

        <div class="checkbox">
            <input type="checkbox" name="pt_dia" id="pt_dia" <?= $produto['pt_dia'] ? 'checked' : '' ?>>
            <label for="pt_dia">Prato do dia?</label>
        </div>

        <label>Dia da Semana:</label>
        <select name="dia_da_semana">
            <option value="">--Não é prato do dia--</option>
            <?php
            $dias = ["segunda", "terca", "quarta", "quinta", "sexta", "sabado", "domingo"];
            foreach ($dias as $d) {
                $sel = ($produto['dia_da_semana'] == $d) ? 'selected' : '';
                echo "<option value='$d' $sel>" . ucfirst($d) . "</option>";
            }
            ?>
        </select>

        <input type="submit" value="Salvar Alterações">
    </form>
</div>

<style>
    .form-container {
        max-height: 75vh;
        overflow-y: auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        max-width: 600px;
        margin: 0 auto;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        font-weight: bold;
        color: #3A6332;
        margin-bottom: 5px;
    }

    input[type="text"],
    textarea,
    select {
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #ccc;
        margin-bottom: 15px;
        width: 100%;
    }

    .checkbox {
        margin: 10px 0;
        display: flex;
        align-items: center;
    }

    .checkbox input[type="checkbox"] {
        margin-right: 8px;
    }

    input[type="submit"] {
        background-color: #3A6332;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        align-self: flex-start;
    }

    input[type="submit"]:hover {
        background-color: #2c4f27;
    }
</style>
