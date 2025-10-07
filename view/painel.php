<?php
require_once '../config/db.php';
require_once '../model/Livro.php';
require_once '../model/Emprestimo.php';
require_once '../model/Reserva.php';

$livroModel = new Livro($conn);
$emprestimoModel = new Emprestimo($conn);
$reservaModel = new Reserva($conn);

// Dados gerais
$totalLivros = count($livroModel->listar([]));
$totalEmprestimos = count($emprestimoModel->listarAtivos());
$totalReservas = count($reservaModel->listarAtivas());
$alertas = $emprestimoModel->listarAlertas();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="../assets/css/painel.css">
</head>
<body>
<div class="container">
    <h1>Dashboard da Biblioteca</h1>

    <div class="cards">
        <div class="card">
            <h3>Total de Livros</h3>
            <p><?= $totalLivros ?></p>
        </div>
        <div class="card">
            <h3>Empréstimos Ativos</h3>
            <p><?= $totalEmprestimos ?></p>
        </div>
        <div class="card">
            <h3>Reservas Ativas</h3>
            <p><?= $totalReservas ?></p>
        </div>
        <div class="card">
            <h3>Alertas de Devolução</h3>
            <p><?= count($alertas) ?></p>
        </div>
    </div>

    <h2>Últimos Alertas</h2>
    <?php if (!empty($alertas)): ?>
        <table>
            <thead>
                <tr>
                    <th>Livro</th>
                    <th>Usuário</th>
                    <th>Data Prevista</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alertas as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['titulo']) ?></td>
                    <td><?= htmlspecialchars($a['nome'] . ' ' . $a['sobrenome']) ?></td>
                    <td><?= date('d/m/Y', strtotime($a['data_devolucao_prevista'])) ?></td>
                    <td><?= htmlspecialchars($a['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum alerta no momento.</p>
    <?php endif; ?>

</div>
</body>
</html>
