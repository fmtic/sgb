<?php
require_once '../config/db.php';
require_once '../model/Livro.php';
require_once '../model/Emprestimo.php';
require_once '../model/Reserva.php';

$livroModel = new Livro($conn);
$emprestimoModel = new Emprestimo($conn);
$reservaModel = new Reserva($conn);

// Recebe filtros
$livroFiltro = $_GET['livro'] ?? '';
$usuarioFiltro = $_GET['usuario'] ?? '';
$statusFiltro = $_GET['status'] ?? '';
$dataInicio = $_GET['data_inicio'] ?? '';
$dataFim = $_GET['data_fim'] ?? '';

// Buscar empréstimos
$emprestimos = $emprestimoModel->filtrar([
    'livro' => $livroFiltro,
    'usuario' => $usuarioFiltro,
    'status' => $statusFiltro,
    'data_inicio' => $dataInicio,
    'data_fim' => $dataFim
]);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatórios da Biblioteca</title>
    <link rel="stylesheet" href="../assets/css/relatorios.css">
</head>
<body>
<div class="container">
    <h1>Relatórios de Empréstimos</h1>

    <form method="GET" class="filtro-form">
        <label>Livro:</label>
        <input type="text" name="livro" value="<?= htmlspecialchars($livroFiltro) ?>">

        <label>Usuário:</label>
        <input type="text" name="usuario" value="<?= htmlspecialchars($usuarioFiltro) ?>">

        <label>Status:</label>
        <select name="status">
            <option value="">Todos</option>
            <option value="Emprestado" <?= $statusFiltro=='Emprestado'?'selected':'' ?>>Emprestado</option>
            <option value="Devolvido" <?= $statusFiltro=='Devolvido'?'selected':'' ?>>Devolvido</option>
        </select>

        <label>Data Início:</label>
        <input type="date" name="data_inicio" value="<?= htmlspecialchars($dataInicio) ?>">

        <label>Data Fim:</label>
        <input type="date" name="data_fim" value="<?= htmlspecialchars($dataFim) ?>">

        <button type="submit">Filtrar</button>
        <button type="button" onclick="window.location.href='relatorios.php'">Resetar</button>
        <button type="button" onclick="window.location.href='exportar_relatorio.php?<?= http_build_query($_GET) ?>'">Exportar CSV</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Livro</th>
                <th>Usuário</th>
                <th>Data Empréstimo</th>
                <th>Data Devolução Prevista</th>
                <th>Data Devolução Real</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($emprestimos)): ?>
            <?php foreach ($emprestimos as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['titulo']) ?></td>
                <td><?= htmlspecialchars($e['nome'].' '.$e['sobrenome']) ?></td>
                <td><?= date('d/m/Y', strtotime($e['data_emprestimo'])) ?></td>
                <td><?= date('d/m/Y', strtotime($e['data_devolucao_prevista'])) ?></td>
                <td><?= $e['data_devolucao_real'] ? date('d/m/Y', strtotime($e['data_devolucao_real'])) : '-' ?></td>
                <td><?= htmlspecialchars($e['status']) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">Nenhum resultado encontrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
