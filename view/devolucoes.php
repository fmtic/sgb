<?php
require_once '../config/auth.php';
verificarPermissao(['admin', 'bibliotecario']);

require_once '../config/db.php';
require_once '../model/Emprestimo.php';

$emprestimoModel = new Emprestimo($conn);

// Registrar devolução
if (isset($_POST['devolver'])) {
    $id = (int)$_POST['emprestimo_id'];
    $emprestimoModel->devolver($id, date('Y-m-d'));
    header("Location: devolucoes.php");
    exit;
}

// Lista empréstimos ativos
$emprestimos = $emprestimoModel->listarAtivos();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar Devoluções</title>
    <link rel="stylesheet" href="../assets/css/livros.css">
</head>
<body>
<div class="container">
    <h1>Registrar Devoluções</h1>
    <a href="dashboard.php">Voltar ao Dashboard</a>
    <hr>

    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Usuário</th>
                <th>Data Empréstimo</th>
                <th>Data Prevista</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($emprestimos)): ?>
                <?php foreach ($emprestimos as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['titulo']) ?></td>
                        <td><?= htmlspecialchars($e['nome']) ?> <?= htmlspecialchars($e['sobrenome']) ?></td>
                        <td><?= htmlspecialchars($e['data_emprestimo']) ?></td>
                        <td><?= htmlspecialchars($e['data_devolucao_prevista']) ?></td>
                        <td><?= htmlspecialchars($e['status']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="emprestimo_id" value="<?= $e['id'] ?>">
                                <button type="submit" name="devolver">Registrar Devolução</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">Nenhum empréstimo ativo.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
