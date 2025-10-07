<?php
session_start();
require_once '../config/db.php';
require_once '../model/Usuario.php';

$usuarioModel = new Usuario($conn);

$mensagem = "";
$usuario = null;
$editando = false;

// Se id está na URL, estamos editando
if(isset($_GET['id'])) {
    $usuario = $usuarioModel->obterPorId((int)$_GET['id']);
    if($usuario) {
        $editando = true;
    }
}

// Processa cadastro/edição
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        ':nome' => trim($_POST['nome']),
        ':sobrenome' => trim($_POST['sobrenome']),
        ':email' => trim($_POST['email']),
        ':telefone' => preg_replace('/\D/', '', $_POST['telefone']),
        ':whatsapp' => preg_replace('/\D/', '', $_POST['whatsapp']),
        ':foto' => null,
        ':tipo' => $_POST['tipo'] ?? 'aluno'
    ];

    // Senha (apenas para cadastro)
    if(!$editando) {
        $dados[':senha'] = $_POST['senha'];
    }

    // Upload de foto
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nomeFoto = uniqid('usuario_') . '.' . $ext;
        $destino = '../uploads/fotos/' . $nomeFoto;
        if(move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $dados[':foto'] = $nomeFoto;
        }
    } elseif($editando) {
        $dados[':foto'] = $_POST['foto_antiga'] ?? null;
    }

    if($editando) {
        $dados[':id'] = (int)$_POST['id'];
        if($usuarioModel->atualizar($dados)) {
            $mensagem = "Usuário atualizado com sucesso!";
        } else {
            $mensagem = "Erro ao atualizar usuário.";
        }
    } else {
        if($usuarioModel->cadastrar($dados)) {
            $mensagem = "Usuário cadastrado com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar usuário.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $editando ? 'Editar' : 'Cadastrar' ?> Usuário</title>
    <link rel="stylesheet" href="../assets/css/livros.css">
</head>
<body>
<div class="container">
    <h1><?= $editando ? 'Editar' : 'Cadastrar' ?> Usuário</h1>
    <a href="dashboard.php"><button>Voltar ao Dashboard</button></a>

    <?php if($mensagem): ?>
        <p style="color:green; font-weight:bold;"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <?php if($editando): ?>
            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
            <input type="hidden" name="foto_antiga" value="<?= htmlspecialchars($usuario['foto']) ?>">
        <?php endif; ?>

        <label>Nome:</label>
        <input type="text" name="nome" required value="<?= $editando ? htmlspecialchars($usuario['nome']) : '' ?>">

        <label>Sobrenome:</label>
        <input type="text" name="sobrenome" required value="<?= $editando ? htmlspecialchars($usuario['sobrenome']) : '' ?>">

        <label>Email:</label>
        <input type="email" name="email" required value="<?= $editando ? htmlspecialchars($usuario['email']) : '' ?>">

        <?php if(!$editando): ?>
            <label>Senha:</label>
            <input type="password" name="senha" required>
        <?php endif; ?>

        <label>Telefone:</label>
        <input type="text" name="telefone" maxlength="11" value="<?= $editando ? htmlspecialchars($usuario['telefone']) : '' ?>">

        <label>WhatsApp:</label>
        <input type="text" name="whatsapp" maxlength="11" value="<?= $editando ? htmlspecialchars($usuario['whatsapp']) : '' ?>">

        <label>Foto:</label>
        <input type="file" name="foto" accept="image/*">
        <div id="previewFoto">
            <?php if($editando && $usuario['foto']): ?>
                <img id="imgPreview" src="../uploads/fotos/<?= htmlspecialchars($usuario['foto']) ?>" alt="Foto" style="max-width:200px;">
            <?php else: ?>
                <img id="imgPreview" src="#" alt="Preview" style="display:none; max-width:200px;">
            <?php endif; ?>
        </div>

        <label>Tipo:</label>
        <select name="tipo" required>
            <?php 
            $tipos = ['admin'=>'Admin','bibliotecario'=>'Bibliotecário','aluno'=>'Aluno','publico_externo'=>'Público Externo'];
            foreach($tipos as $key => $val):
            ?>
            <option value="<?= $key ?>" <?= ($editando && $usuario['tipo']==$key) ? 'selected' : '' ?>><?= $val ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
    </form>
</div>

<script>
const inputFoto = document.querySelector('input[name="foto"]');
const imgPreview = document.getElementById('imgPreview');

inputFoto.addEventListener('change', function() {
    const file = this.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(e){
            imgPreview.src = e.target.result;
            imgPreview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        imgPreview.style.display = 'none';
    }
});
</script>
</body>
</html>
