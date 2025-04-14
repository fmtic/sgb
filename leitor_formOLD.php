<?php
require_once 'conexao.php';

$modoEdicao = isset($_GET['id']);
$leitor = [
    'nome' => '',
    'email' => '',
    'telefone' => '',
    'endereco' => '',
    'complemento' => '',
    'cep' => '',
    'numero' => '',
    'bairro' => '',
    'cidade' => '',
    'foto' => '',
    'tipo_foto' => '',
    'data_cadastro' => '',
    'data_nascimento' => ''
];

if ($modoEdicao) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM leitores WHERE id = :id");
        $stmt->execute(['id' => $_GET['id']]);
        $leitor = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar leitor: " . $e->getMessage());
        echo "Erro ao buscar leitor. Por favor, tente novamente.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $complemento = $_POST['complemento'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? '';

    // Processa data
    $data_nascimento_str = '';
    if ($data_nascimento) {
        $data_obj = DateTime::createFromFormat('Y-m-d', $data_nascimento);
        if ($data_obj) {
            $data_nascimento_str = $data_obj->format('Y-m-d');
        } else {
            echo "Data de nascimento inválida!";
            exit;
        }
    }

    // Processa imagem, se foi enviada
    $foto = null;
    $tipo_foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
        $tipo_foto = $_FILES['foto']['type'];
    }

    try {
        if ($modoEdicao) {
            if ($foto !== null && $tipo_foto !== null) {
                $stmt = $pdo->prepare("UPDATE leitores SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco, complemento = :complemento, cep = :cep, numero = :numero, bairro = :bairro, cidade = :cidade, foto = :foto, tipo_foto = :tipo_foto, data_nascimento = :data_nascimento WHERE id = :id");
                $stmt->bindParam(':foto', $foto, PDO::PARAM_LOB);
                $stmt->bindParam(':tipo_foto', $tipo_foto);
            } else {
                $stmt = $pdo->prepare("UPDATE leitores SET nome = :nome, email = :email, telefone = :telefone, endereco = :endereco, complemento = :complemento, cep = :cep, numero = :numero, bairro = :bairro, cidade = :cidade, data_nascimento = :data_nascimento WHERE id = :id");
            }

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':complemento', $complemento);
            $stmt->bindParam(':cep', $cep);
            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':bairro', $bairro);
            $stmt->bindParam(':cidade', $cidade);
            $stmt->bindParam(':data_nascimento', $data_nascimento_str);
            $stmt->bindParam(':id', $_GET['id']);
            $stmt->execute();
        } else {
            $stmt = $pdo->prepare("INSERT INTO leitores (nome, email, telefone, endereco, complemento, cep, numero, bairro, cidade, foto, tipo_foto, data_nascimento) VALUES (:nome, :email, :telefone, :endereco, :complemento, :cep, :numero, :bairro, :cidade, :foto, :tipo_foto, :data_nascimento)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':complemento', $complemento);
            $stmt->bindParam(':cep', $cep);
            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':bairro', $bairro);
            $stmt->bindParam(':cidade', $cidade);
            $stmt->bindParam(':foto', $foto, PDO::PARAM_LOB);
            $stmt->bindParam(':tipo_foto', $tipo_foto);
            $stmt->bindParam(':data_nascimento', $data_nascimento_str);
            $stmt->execute();
        }

        header('Location: leitores.php');
        exit;
    } catch (PDOException $e) {
        error_log("Erro ao salvar leitor: " . $e->getMessage());
        echo "Erro ao salvar leitor. Por favor, tente novamente.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicao ? 'Editar Leitor' : 'Novo Leitor' ?></title>
    <link rel="stylesheet" href="css/estilo_forms.css">
    <script>
        function buscarEndereco() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.erro) {
                            alert('CEP não encontrado!');
                            return;
                        }
                        document.getElementById('endereco').value = data.logradouro || '';
                        document.getElementById('bairro').value = data.bairro || '';
                        document.getElementById('cidade').value = data.localidade || '';
                    })
                    .catch(error => alert('Erro ao buscar o CEP!'));
            }
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2><?= $modoEdicao ? 'Editar Leitor' : 'Novo Leitor' ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($leitor['nome']) ?>" required>

            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" name="data_nascimento" value="<?= htmlspecialchars($leitor['data_nascimento']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($leitor['email']) ?>" required>

            <label for="telefone">Telefone:</label>
            <input type="text" name="telefone" value="<?= htmlspecialchars($leitor['telefone']) ?>">

            <label for="foto">Foto:</label>
            <input type="file" name="foto" accept="image/*">

            <?php if ($modoEdicao && !empty($leitor['foto']) && !empty($leitor['tipo_foto'])): ?>
                <div style="margin-top: 10px;">
                    <strong>Foto atual:</strong><br>
                    <img src="data:<?= htmlspecialchars($leitor['tipo_foto']) ?>;base64,<?= base64_encode($leitor['foto']) ?>" 
                         alt="Foto do leitor" style="max-width: 150px; max-height: 150px; border-radius: 8px; margin-top: 5px;">
                </div>
            <?php endif; ?>

            <label for="cep">CEP:</label>
            <input type="text" id="cep" name="cep" value="<?= htmlspecialchars($leitor['cep']) ?>" onblur="buscarEndereco()">

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($leitor['endereco']) ?>" required>

            <label for="numero">Número:</label>
            <input type="text" name="numero" value="<?= htmlspecialchars($leitor['numero']) ?>">

            <label for="bairro">Bairro:</label>
            <input type="text" id="bairro" name="bairro" value="<?= htmlspecialchars($leitor['bairro']) ?>" required>

            <label for="cidade">Cidade:</label>
            <input type="text" id="cidade" name="cidade" value="<?= htmlspecialchars($leitor['cidade']) ?>" required>

            <label for="complemento">Complemento:</label>
            <input type="text" name="complemento" value="<?= htmlspecialchars($leitor['complemento']) ?>">

            <br/>
            <input type="submit" value="<?= $modoEdicao ? 'Atualizar' : 'Cadastrar' ?>" class="botao-principal">
            <a href="leitores.php" class="botao-voltar">Voltar</a>
        </form>
    </div>
</body>
</html>