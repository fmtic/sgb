<?php
session_start();
if (!isset($_SESSION['usuario_id']) || 
   !in_array($_SESSION['tipo_usuario'], ['aluno', 'publico_externo'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Área do Usuário</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
<div class="dashboard">
    <header>
        <h1>📙 Área do Usuário</h1>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
            <a href="../controller/logout.php" class="logout">Sair</a>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="livros.php">🔍 Consultar Livros</a></li>
            <li><a href="reservas.php">📅 Minhas Reservas</a></li>
            <li><a href="historico.php">📖 Histórico de Empréstimos</a></li>
        </ul>
    </nav>

    <main>
        <h2>Olá, <?= explode(' ', $_SESSION['usuario_nome'])[0] ?>!</h2>
        <p>Bem-vindo à sua área pessoal da biblioteca.</p>
    </main>
</div>
</body>
</html>
