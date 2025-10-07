<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuário - Sistema de Biblioteca</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Crie sua conta</h2>
            <form id="formRegistro" method="POST" action="../controller/registro_controller.php">
                <div class="form-group">
                    <label for="nome">Nome completo</label>
                    <input type="text" id="nome" name="nome" required placeholder="Ex: Maria Silva">
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required placeholder="Ex: maria@email.com">
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required minlength="6" placeholder="Mínimo 6 caracteres">
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">Confirmar senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                </div>

                <input type="hidden" name="tipo" value="leitor">

                <button type="submit" class="btn-laranja">Registrar</button>

                <p class="auth-link">
                    Já tem conta? <a href="login_view.php">Faça login</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            const senha = document.getElementById('senha').value;
            const confirmar = document.getElementById('confirmar_senha').value;

            if (senha !== confirmar) {
                e.preventDefault();
                alert('As senhas não coincidem.');
            }
        });
    </script>
</body>
</html>
