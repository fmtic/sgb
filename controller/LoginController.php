<?php
session_start();
require_once '../config/db.php';
require_once '../model/Usuario.php';

$usuarioModel = new Usuario($conn);

// Verifica se o formulário foi enviado
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Buscar usuário pelo e-mail
    $usuario = $usuarioModel->buscarPorEmail($email);

    if ($usuario) {
        // Verifica senha (assumindo que a senha está hashada com password_hash)
        if (password_verify($senha, $usuario['senha'])) {
            // Criar sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'] . ' ' . $usuario['sobrenome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            // Redirecionar conforme tipo
            header("Location: ../view/dashboard.php");
            exit;
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado.";
    }
}

// Futuro login com Google
/*
if (isset($_GET['google_login'])) {
    // Aqui entraria a lógica de integração com a API do Google
    // Gerar token, buscar usuário, criar sessão, etc.
}
*/

// Se houver erro, redireciona de volta para o login com mensagem
if (!empty($erro)) {
    $_SESSION['login_erro'] = $erro;
    header("Location: ../view/login.php");
    exit;
}
?>
