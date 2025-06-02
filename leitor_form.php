<?php
require_once 'conexao.php'; // Define $pdo (usando PDO)

$modoEdicao = false;
$leitor = [
    'id' => '', 'nome' => '', 'data_nascimento' => '', 'email' => '',
    'telefone' => '', 'telefone2' => '', 'cep' => '', 'endereco' => '', 'numero' => '',
    'bairro' => '', 'cidade' => '', 'foto' => ''
];

if (isset($_GET['id'])) {
    $modoEdicao = true;
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM leitores WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $leitor = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nome = $_POST['nome'];
    $data_nascimento = $_POST['data_nascimento'];
    $email = $_POST['email'];
    $telefone = preg_replace('/\D/', '', $_POST['telefone']);
    $telefone2 = isset($_POST['telefone2']) ? preg_replace('/\D/', '', $_POST['telefone2']) : null;
    $cep = $_POST['cep'];
	$endereco = $_POST['endereco'];
    $numero = $_POST['numero'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $caminhoFoto = $leitor['foto'] ?? '';


    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nomeFoto = uniqid('leitor_') . '.' . $extensao;
        $caminhoRelativo = "imagens/fotos_leitores/" . $nomeFoto;
        $caminhoAbsoluto = __DIR__ . '/' . $caminhoRelativo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoAbsoluto)) {
            $caminhoFoto = $caminhoRelativo;
        }
    }

    try {
        if ($id) {
            $sql = "UPDATE leitores SET nome = ?, data_nascimento = ?, email = ?, telefone = ?, telefone2 = ?, cep = ?,
                    endereco = ?, numero = ?, bairro = ?, cidade = ?, foto = ? WHERE id = ?";
            $params = [$nome, $data_nascimento, $email, $telefone, $telefone2, $cep, $endereco, $numero, $bairro, $cidade, $caminhoFoto, $id];
        } else {
            $sql = "INSERT INTO leitores (nome, data_nascimento, email, telefone, telefone2, cep, endereco, numero, bairro, cidade, foto)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$nome, $data_nascimento, $email, $telefone, $telefone2, $cep, $endereco, $numero, $bairro, $cidade, $caminhoFoto];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: leitores.php");
        exit;

    } catch (PDOException $e) {
        echo "<p>Erro ao salvar leitor: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?php echo $modoEdicao ? 'Editar Leitor' : 'Cadastrar Leitor'; ?></title>
    <link rel="stylesheet" href="css/estilo_leitor_forms.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="js/limitadorTelefone.js" defer></script>
    <script src="js/buscaCEP.js" defer></script>
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
            <input type="text" name="telefone" id="telefone" value="<?php echo htmlspecialchars($leitor['telefone']); ?>" required>

            <label for="telefone2">Telefone 2:</label>
            <input type="text" name="telefone2" id="telefone2" value="<?php echo htmlspecialchars($leitor['telefone2']); ?>">

            <label for="cep">CEP:</label>
            <input type="text" name="cep" id="cep" value="<?php echo htmlspecialchars($leitor['cep']); ?>" required>

            <label for="endereco">Endereço:</label>
            <input type="text" name="endereco" id="endereco" value="<?php echo htmlspecialchars($leitor['endereco']); ?>">

            <label for="numero">Número:</label>
            <input type="text" name="numero" value="<?php echo htmlspecialchars($leitor['numero']); ?>">

            <label for="bairro">Bairro:</label>
            <input type="text" name="bairro" id="bairro" value="<?php echo htmlspecialchars($leitor['bairro']); ?>">

            <label for="cidade">Cidade:</label>
            <input type="text" name="cidade" id="cidade" value="<?php echo htmlspecialchars($leitor['cidade']); ?>">

            <label for="foto">Foto:</label>
            <input type="file" name="foto" accept="image/*">

            <?php if ($modoEdicao && $leitor['foto']) : ?>
                <div style="margin-bottom: 15px;">
                    <img src="<?php echo htmlspecialchars($leitor['foto']); ?>" alt="Foto do Leitor" style="max-width: 150px; border-radius: 8px;">
                </div>
            <?php endif; ?>

            <input type="submit" value="<?php echo $modoEdicao ? 'Atualizar' : 'Cadastrar'; ?>">
        </form>

        <a href="leitores.php" class="botao-voltar">Voltar para lista</a>
    </div>
</body>

</html>