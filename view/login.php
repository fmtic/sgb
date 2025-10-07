<?php
session_start();

// Redireciona usuário logado
if (isset($_SESSION['usuario_id'])) {
    switch ($_SESSION['usuario_tipo']) {
        case 'admin':
            header('Location: admin_dashboard.php');
            break;
        case 'bibliotecario':
            header('Location: bibliotecario_dashboard.php');
            break;
        case 'aluno':
        case 'publico_externo':
            header('Location: dashboard.php');
            break;
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Biblioteca</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
<div class="login-container">
    <h1>Login Biblioteca</h1>
    
    <form method="POST" action="../controller/LoginController.php">
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required>
        
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required>
        
        <button type="submit">Entrar</button>
    </form>

    <hr>
    <div class="google-login">
        <a href="../controller/LoginController.php?google_login=1">
            <img src="../assets/img/google_login.png" alt="Login com Google">
        </a>
    </div>
</div>
</body>
</html>
