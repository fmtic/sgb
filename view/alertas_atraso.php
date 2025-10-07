<?php
require_once '../config/db.php';
require_once '../model/Emprestimo.php';
require_once '../auth/auth.php';

verificarLogin();

// Somente bibliotecário e admin podem ver alertas
if(!in_array($_SESSION['usuario_tipo'], ['admin','bibliotecario'])) {
    die("Acesso negado.");
}

$emprestimoModel = new Emprestimo($conn);

// Listar todos empréstimos pendentes
$emprestimos = $emprestimoModel->listar();
$hoje = new DateTime();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alertas de Atraso</title>
    <link rel="stylesheet" href="../assets/css/livros.css">
    <style>
        .atrasado { background-color: #f8d7da; } /* vermelho claro */
    </style>
</head>
<body>
<h1>Empréstimos em Atraso</h1>

<table>
    <thead>
        <tr>
            <th>Usuário</th>
            <th>Livro</th>
            <th>Data Empréstimo</th>
            <th>Data Devolução Prevista</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $temAtraso = false;
        foreach($emprestimos as $e): 
            $dataPrevista = new DateTime($e['data_devolucao_prevista']);
            $atrasado = ($e['status'] === 'pendente' && $dataPrevista < $hoje);
            if($atrasado) $temAtraso = true;
        ?>
        <tr class="<?= $atrasado ? 'atrasado' : '' ?>">
            <td><?= htmlspecialchars($e['nome'].' '.$e['sobrenome']) ?></td>
            <td><?= htmlspecialchars($e['titulo'].' - '.$e['autor']) ?></td>
            <td><?= htmlspecialchars($e['data_emprestimo']) ?></td>
            <td><?= htmlspecialchars($e['data_devolucao_prevista']) ?></td>
            <td><?= htmlspecialchars($e['status']) ?><?= $atrasado ? ' (ATRASADO)' : '' ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($emprestimos)): ?>
            <tr><td colspan="5" style="text-align:center;">Nenhum empréstimo registrado.</td></tr>
        <?php elseif(!$temAtraso): ?>
            <tr><td colspan="5" style="text-align:center;">Nenhum empréstimo em atraso.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<a href="emprestimos.php">Voltar aos empréstimos</a>
</body>
</html>
