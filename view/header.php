<?php
session_start();

// Verifica se o usuário está logado
$logado = isset($_SESSION['usuario_id']);
$nomeUsuario = $logado ? $_SESSION['usuario_nome'] : '';
$googleLogin = isset($_SESSION['google_login']) && $_SESSION['google_login'] === true;
?>

<header class="main-header">
    <div class="logo">
        <h1>SGB - Biblioteca</h1>
    </div>

    <?php if($logado): ?>
        <div class="user-info">
            <span>Olá, <?= htmlspecialchars($nomeUsuario) ?></span>
            <a href="../controller/logout.php<?= $googleLogin ? '?google=1' : '' ?>">Sair</a>
        </div>
    <?php else: ?>
        <div class="login-link">
            <a href="login.php">Entrar</a>
        </div>
    <?php endif; ?>
</header>
