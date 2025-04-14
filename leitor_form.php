<?php
include 'conexao.php'; // Certifique-se de que este arquivo define $conn

$modoEdicao = false;
$leitor = [
    'id' => '', 'nome' => '', 'data_nascimento' => '', 'email' => '', 'telefone' => '',
    'endereco' => '', 'numero' => '', 'bairro' => '', 'cidade' => '', 'foto' => null
];

if (isset($_GET['id'])) {
    $modoEdicao = true;
    $id = $_GET['id'];
    $result = pg_query_params($conn, "SELECT * FROM leitores WHERE id = $1", [$id]);
    if ($row = pg_fetch_assoc($result)) {
        $leitor = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];

    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoTempPath = $_FILES['foto']['tmp_name'];
        $foto = file_get_contents($fotoTempPath);
        $foto = pg_escape_bytea($foto);
    }

    if ($id) {
        if ($foto) {
            $sql = "UPDATE leitores SET nome=$1, data_nascimento=$2, email=$3, telefone=$4, endereco=$5, numero=$6, bairro=$7, cidade=$8, foto=decode($9, 'escape') WHERE id=$10";
            $params = [$nome, $data_nascimento, $email, $telefone, $endereco, $numero, $bairro, $cidade, $foto, $id];
        } else {
            $sql = "UPDATE leitores SET nome=$1, data_nascimento=$2, email=$3, telefone=$4, endereco=$5, numero=$6, bairro=$7, cidade=$8 WHERE id=$9";
            $params = [$nome, $data_nascimento, $email, $telefone, $endereco, $numero, $bairro, $cidade, $id];
        }
    } else {
        $sql = "INSERT INTO leitores (nome, data_nascimento, email, telefone, endereco, numero, bairro, cidade, foto) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,decode($9,'escape'))";
        $params = [$nome, $data_nascimento, $email, $telefone, $endereco, $numero, $bairro, $cidade, $foto];
    }

    $res = pg_query_params($conn, $sql, $params);
    if ($res) {
        header("Location: leitores.php");
        exit;
    } else {
        echo "<p>Erro ao salvar leitor.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo $modoEdicao ? 'Editar Leitor' : 'Cadastrar Leitor'; ?></title>
    <link rel="stylesheet" href="css/leitor_form.css">
</head>
<body>
    <div class="form-container">
        <h2><?php echo $modoEdicao ? 'Editar Leitor' : 'Cadastrar Leitor'; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($leitor['id']); ?>">

            <label for="nome">Nome:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($leitor['nome']); ?>" required>

            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" name="data_nascimento" value="<?php echo htmlspecialchars($leitor['data_nascimento']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($leitor['email']); ?>" required>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" value="<?php echo htmlspecialchars($leitor['telefone']); ?>">

            <label for="endereco">Endereço:</label>
            <input type="text" name="endereco" value="<?php echo htmlspecialchars($leitor['endereco']); ?>">

            <label for="numero">Número:</label>
            <input type="text" name="numero" value="<?php echo htmlspecialchars($leitor['numero']); ?>">

            <label for="bairro">Bairro:</label>
            <input type="text" name="bairro" value="<?php echo htmlspecialchars($leitor['bairro']); ?>">

            <label for="cidade">Cidade:</label>
            <input type="text" name="cidade" value="<?php echo htmlspecialchars($leitor['cidade']); ?>">

            <label for="foto">Foto:</label>
            <input type="file" name="foto" accept="image/*">

            <?php if ($modoEdicao && $leitor['foto']) : ?>
                <div style="margin-bottom: 15px;">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode(pg_unescape_bytea($leitor['foto'])); ?>" alt="Foto do Leitor" style="max-width: 150px; border-radius: 8px;">
                </div>
            <?php endif; ?>

            <input type="submit" value="<?php echo $modoEdicao ? 'Atualizar' : 'Cadastrar'; ?>">
        </form>

        <a href="leitores.php" class="botao-voltar">Voltar para lista</a>
    </div>
</body>
</html>
