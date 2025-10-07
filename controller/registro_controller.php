<?php
session_start();
require_once '../config/db.php';
require_once '../model/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar_senha'];
    $tipo = $_POST['tipo'] ?? 'leitor'; // padrão leitor

    // Validação básica
    if (empty($nome) || empty($email) || empty($senha)) {
        die("Erro: todos os campos são obrigatórios.");
    }

    if ($senha !== $confirmar) {
        die("Erro: as senhas não coincidem.");
    }

    // Verifica duplicidade de e-mail
    $usuarioModel = new Usuario($conn);
    if ($usuarioModel->buscarPorEmail($email)) {
        die("Erro: e-mail já cadastrado.");
    }

    // Criptografar senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Preparar dados para inserção
    $dados = [
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senhaHash,
        ':tipo' => $tipo,
        ':telefone' => null,
        ':whatsapp' => null,
        ':foto' => null
    ];

    $resultado = $usuarioModel->cadastrar($dados);

    if ($resultado) {
        $_SESSION['usuario_id'] = $conn->lastInsertId(); // guardar id na sessão
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_tipo'] = $tipo;

        header('Location: ../view/dashboard.php'); // redireciona para dashboard
        exit();
    } else {
        die("Erro ao cadastrar usuário.");
    }
} else {
    header('Location: registro_view.php');
    exit();
}
