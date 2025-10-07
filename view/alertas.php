<?php
require_once '../config/db.php';
require_once '../model/Emprestimo.php';

$emprestimoModel = new Emprestimo($conn);
$alertas = $emprestimoModel->listarAlertas();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alertas de Devolução</title>
    <link rel="stylesheet" href="../assets/css/painel.css">
</head>
<body>
<div class="container">
    <h1>Alertas de Devolução Próxima</h1>

    <?php if (!empty($alertas)): ?>
        <table>
            <thead>
                <tr>
                    <th>Livro</th>
                    <th>Usuário</th>
                    <th>Data de Devolução Prevista</th>
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
