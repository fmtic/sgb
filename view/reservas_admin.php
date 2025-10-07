<?php
session_start();
require_once '../config/db.php';
require_once '../model/Reserva.php';
require_once '../model/Usuario.php';
require_once '../model/Livro.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Verifica se usuário é admin ou bibliotecário
if (!in_array($_SESSION['tipo'], ['admin', 'bibliotecario'])) {
    die("Acesso negado.");
}

$reservaModel = new Reserva($conn);
$usuarioModel = new Usuario($conn);
$livroModel = new Livro($conn);

// Filtros
$filtros = [
    'usuario' => $_GET['usuario'] ?? '',
    'status' => $_GET['status'] ?? '',
    'livro' => $_GET['livro'] ?? ''
];

$reservas = $reservaModel->listarTodosComFiltros($filtros);

// Listas para filtros
$usuarios = $usuarioModel->listar();
$livros = $livroModel->listar([]);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Reservas - Administração</title>
    <link rel="stylesheet" href="../assets/css/reservas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
    <h1>Reservas - Painel Administrativo</h1>

    <!-- Filtros -->
    <form method="GET" class="filtro-form">
        <label>Usuário:</label>
        <select name="usuario">
            <option value="">Todos</option>
            <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $filtros['usuario'] == $u['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['nome']) ?> <?= htmlspecialchars($u['sobrenome']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Livro:</label>
        <select name="livro">
            <option value="">Todos</option>
            <?php foreach ($livros as $l): ?>
                <option value="<?= $l['id'] ?>" <?= $filtros['livro'] == $l['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($l['titulo']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Status:</label>
        <select name="status">
            <option value="">Todos</option>
            <option value="ativa" <?= $filtros['status'] === 'ativa' ? 'selected' : '' ?>>Ativa</option>
            <option value="concluida" <?= $filtros['status'] === 'concluida' ? 'selected' : '' ?>>Concluída</option>
            <option value="cancelada" <?= $filtros['status'] === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
            <option value="atrasada" <?= $filtros['status'] === 'atrasada' ? 'selected' : '' ?>>Atrasada</option>
        </select>

        <button type="submit"><i class="fa-solid fa-filter icon"></i>Filtrar</button>
    </form>

    <!-- Tabela de reservas -->
    <table>
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Livro</th>
                <th>Data da Reserva</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($reservas)): ?>
            <?php foreach ($reservas as $reserva): ?>
                <?php
                $statusClass = $reserva['status'];
                if ($reserva['status'] === 'ativa' && strtotime($reserva['data_reserva']) < strtotime('-7 days')) {
                    $statusClass = 'atrasada';
                }
                ?>
                <tr>
                    <td><?= htmlspecialchars($reserva['usuario_nome']) ?> <?= htmlspecialchars($reserva['usuario_sobrenome']) ?></td>
                    <td><?= htmlspecialchars($reserva['titulo']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($reserva['data_reserva'])) ?></td>
                    <td><span class="badge <?= $statusClass ?>"><?= ucfirst($statusClass) ?></span></td>
                    <td>
                        <?php if (in_array($reserva['status'], ['ativa', 'atrasada'])): ?>
                            <a href="../controller/ReservaController.php?cancelar=<?= $reserva['id'] ?>">
                                <i class="fa-solid fa-xmark"></i> Cancelar
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">Nenhuma reserva encontrada.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
