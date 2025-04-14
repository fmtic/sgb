<?php
// Conexão com banco
$conn = pg_connect("host=localhost dbname=seubanco user=seuusuario password=suasenha");

$mensagem = '';
$foto_preview = '';
$edicao = false;

// Carrega dados se estiver editando
if (isset($_GET['id'])) {
    $edicao = true;
    $id = $_GET['id'];
    $query = "SELECT * FROM leitores WHERE id = $1";
    $resultado = pg_query_params($conn, $query, array($id));
    $leitor = pg_fetch_assoc($resultado);

    if ($leitor && $leitor['foto']) {
        $foto_preview = 'data:image/jpeg;base64,' . base64_encode($leitor['foto']);
    }
}

// Ao enviar o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $telefone = $_POST["telefone"];
    $data_nascimento = $_POST["data_nascimento"];
    $endereco = $_POST["endereco"];
    $usuario_id = $_POST["usuario_id"];
    $foto_bin = null;

    // Se uma nova foto foi enviada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_bin = file_get_contents($_FILES['foto']['tmp_name']);
    }

    if ($edicao) {
        if ($foto_bin !== null) {
            $query = "UPDATE leitores SET nome = $1, email = $2, telefone = $3, data_nascimento = $4, endereco = $5, usuario_id = $6, foto = $7 WHERE id = $8";
            $params = array($nome, $email, $telefone, $data_nascimento, $endereco, $usuario_id, $foto_bin, $id);
        } else {
            $query = "UPDATE leitores SET nome = $1, email = $2, telefone = $3, data_nascimento = $4, endereco = $5, usuario_id = $6 WHERE id = $7";
            $params = array($nome, $email, $telefone, $data_nascimento, $endereco, $usuario_id, $id);
        }
        $resultado = pg_query_params($conn, $query, $params);
        $mensagem = "Leitor atualizado com sucesso!";
    } else {
        $query = "INSERT INTO leitores (nome, email, telefone, data_nascimento, endereco, usuario_id, foto) VALUES ($1, $2, $3, $4, $5, $6, $7)";
        $params = array($nome, $email, $telefone, $data_nascimento, $endereco, $usuario_id, $foto_bin);
        $resultado = pg_query_params($conn, $query, $params);
        $mensagem = "Leitor cadastrado com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo $edicao ? "Editar" : "Cadastrar"; ?> Leitor</title>
    <link rel="stylesheet" href="css/leitor_form.css">
</head>
<body>
<div class="form-container">
    <h2><?php echo $edicao ? "Editar" : "Cadastrar"; ?> Leitor</h2>

    <?php if (!empty($mensagem)) echo "<p>$mensagem</p>"; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" value="<?= $leitor['nome'] ?? '' ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= $leitor['email'] ?? '' ?>">

        <label for="telefone">Telefone:</label>
        <input type="text" name="telefone" id="telefone" value="<?= $leitor['telefone'] ?? '' ?>">

        <label for="data_nascimento">Data de Nascimento:</label>
        <input type="date" name="data_nascimento" id="data_nascimento" value="<?= $leitor['data_nascimento'] ?? '' ?>">

        <label for="endereco">Endereço:</label>
        <input type="text" name="endereco" id="endereco" value="<?= $leitor['endereco'] ?? '' ?>">

        <label for="usuario_id">ID do Usuário:</label>
        <input type="number" name="usuario_id" id="usuario_id" value="<?= $leitor['usuario_id'] ?? '' ?>" required>

        <label for="foto">Foto:</label>
        <input type="file" name="foto" id="foto" accept="image/*">

        <?php if ($foto_preview): ?>
            <div style="margin-bottom:15px;">
                <strong>Foto atual:</strong><br>
                <img src="<?= $foto_preview ?>" alt="Foto do leitor" style="max-width: 200px; border-radius: 10px;">
            </div>
        <?php endif; ?>

        <input type="submit" value="<?php echo $edicao ? 'Atualizar' : 'Cadastrar'; ?>">
    </form>

    <a href="leitores.php" class="botao-voltar">← Voltar para listagem</a>
</div>
</body>
</html>
