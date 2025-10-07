<?php
session_start();
require_once '../config/db.php';
require_once '../model/Reserva.php';
require_once '../model/Livro.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$reservaModel = new Reserva($conn);
$livroModel = new Livro($conn);
$usuarioId = $_SESSION['usuario_id'];

// Buscar reservas do usuário
$reservas = $reservaModel->listarPorUsuario($usuarioId);

// Buscar livros disponíveis para reserva
$livrosDisponiveis = $livroModel->listar([]);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Minhas Reservas</title>
    <link rel="stylesheet" href="../assets/css/reservas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
    <h1>Minhas Reservas</h1>

    <!-- Formulário de nova reserva -->
    <h2><i class="fa-solid fa-plus icon"></i>Nova Reserva</h2>
    <form method="POST">
        <label for="livro_id">Livro:</label>
        <select name="livro_id" required>
            <option value="">Selecione um livro</option>
            <?php foreach ($livrosDisponiveis as $livro): ?>
                <option value="<?= $livro['id'] ?>"><?= htmlspecialchars($livro['titulo']) ?> - <?= htmlspecialchars($livro['autor']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="cadastrar"><i class="fa-solid fa-book-bookmark icon"></i>Reservar</button>
    </form>

    <!-- Tabela de reservas -->
    <h2><i class="fa-solid fa-list icon"></i>Minhas Reservas Atuais</h2>
    <table>
        <thead>
            <tr>
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
                // Determinar se está atrasada
                $statusClass = $reserva['status'];
                if ($reserva['status'] === 'ativa' && strtotime($reserva['data_reserva']) < strtotime('-7 days')) {
                    $statusClass = 'atrasada';
                }
                ?>
                <tr>
                    <td><?= htmlspecialchars($reserva['titulo']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($reserva['data_reserva'])) ?></td>
                    <td><span class="badge <?= $statusClass ?>"><?= ucfirst($statusClass) ?></span></td>
                    <td>
                        <?php if ($reserva['status'] === 'ativa' || $reserva['status'] === 'atrasada'): ?>
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
            <tr><td colspan="4" style="text-align:center;">Nenhuma reserva encontrada.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
