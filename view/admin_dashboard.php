<?php
session_start();

// Verifica se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Permissões por tipo
$tipo = $_SESSION['usuario_tipo'];

// Menu básico por tipo de usuário
$menus = [];

switch ($tipo) {
    case 'admin':
        $menus = [
            'Gerenciar Usuários' => 'admin_dashboard.php',
            'Gerenciar Livros' => 'livros.php',
            'Relatórios' => 'relatorios.php'
        ];
        break;

    case 'bibliotecario':
        $menus = [
            'Gerenciar Livros' => 'livros.php',
            'Registrar Empréstimos' => 'emprestimos.php',
            'Registrar Devoluções' => 'devolucoes.php',
            'Cadastrar Alunos e Público' => 'usuario_form.php'
        ];
        break;

    case 'aluno':
    case 'publico_externo':
        $menus = [
            'Consultar Livros' => 'livros.php',
            'Reservar Livros' => 'reservas.php',
            'Histórico de Empréstimos' => 'historico.php'
        ];
        break;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Biblioteca</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <header>
        <h1>Bem-vindo(a), <?= htmlspecialchars($_SESSION['usuario_nome']) ?></h1>
        <a href="../controller/logout.php">Sair</a>
    </header>

    <nav>
        <ul>
            <?php foreach ($menus as $nome => $link): ?>
                <li><a href="<?= $link ?>"><?= $nome ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <main>
        <h2>Painel de Controle</h2>
        <p>Use o menu para acessar suas funcionalidades disponíveis.</p>
    </main>
</body>
</html>
