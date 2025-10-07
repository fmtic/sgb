<?php
session_start();
require_once '../config/db.php';
require_once '../model/Emprestimo.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuarioId = $_SESSION['usuario_id'];
$emprestimoModel = new Emprestimo($conn);
$emprestimos = $emprestimoModel->listarPorUsuario($usuarioId);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Empréstimos</title>
    <link rel="stylesheet" href="../assets/css/livros.css">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-emprestado { color: blue; }
        .status-devolvido { color: green; }
        .status-atrasado { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Histórico de Empréstimos</h1>
        <a href="logout.php">Sair</a>
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Data do Empréstimo</th>
                    <th>Data Prevista</th>
                    <th>Data de Devolução</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($emprestimos)): ?>
                    <?php foreach ($emprestimos as $e): ?>
                        <?php
                        // Define a classe do status
                        $statusClass = '';
                        if ($e['status'] === 'Emprestado' && strtotime($e['data_devolucao_prevista']) < time()) {
                            $statusClass = 'status-atrasado';
                        } elseif ($e['status'] === 'Emprestado') {
                            $statusClass = 'status-emprestado';
                        } else {
                            $statusClass = 'status-devolvido';
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($e['titulo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($e['data_emprestimo'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($e['data_devolucao_prevista'])) ?></td>
                            <td><?= $e['data_devolucao_real'] ? date('d/m/Y', strtotime($e['data_devolucao_real'])) : '-' ?></td>
                            <td class="<?= $statusClass ?>"><?= htmlspecialchars($e['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;">Nenhum empréstimo encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
