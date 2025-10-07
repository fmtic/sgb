<?php
require_once '../config/db.php';
require_once '../model/Usuario.php';

$usuarioModel = new Usuario($conn);

session_start();

// Exclusão
if(isset($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    $resultado = $usuarioModel->excluir($id);
    if($resultado) {
        header("Location: ../view/usuarios.php");
        exit;
    } else {
        die("Erro ao excluir usuário.");
    }
}

// Cadastro / Edição via POST
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nome = trim($_POST['nome']);
    $sobrenome = trim($_POST['sobrenome']);
    $telefone = preg_replace('/\D/', '', $_POST['telefone']);
    $whatsapp = preg_replace('/\D/', '', $_POST['whatsapp']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? 'aluno';

    // Upload de foto
    $foto = $_POST['foto_antiga'] ?? null;
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nomeFoto = uniqid('usuario_') . '.' . $ext;
        $caminho = '../uploads/fotos/' . $nomeFoto;
        if(move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
            $foto = $nomeFoto;
        }
    }

    // Hash da senha
    if($senha) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    } elseif($id) {
        $usuarioExistente = $usuarioModel->buscarPorId((int)$id);
        $senhaHash = $usuarioExistente['senha'];
    } else {
        die("Senha é obrigatória.");
    }

    $dados = [
        ':nome' => $nome,
        ':sobrenome' => $sobrenome,
        ':telefone' => $telefone,
        ':whatsapp' => $whatsapp,
        ':email' => $email,
        ':senha' => $senhaHash,
        ':tipo' => $tipo,
        ':foto' => $foto
    ];

    if($id) {
        $dados[':id'] = $id;
        $resultado = $usuarioModel->atualizar($dados);
    } else {
        $resultado = $usuarioModel->cadastrar($dados);
    }

    if($resultado) {
        header("Location: ../view/usuarios.php");
        exit;
    } else {
        die("Erro ao salvar usuário. Verifique se o e-mail já está cadastrado.");
    }
}
