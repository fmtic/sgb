<?php
session_start();
require_once '../config/db.php';
require_once '../model/Reserva.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../view/login.php");
    exit;
}

$reservaModel = new Reserva($conn);
$usuarioId = $_SESSION['usuario_id'];

// 1. Cadastrar nova reserva
if (isset($_POST['cadastrar'])) {
    $livroId = (int)$_POST['livro_id'];

    // Evitar duplicidade: usuário não pode reservar o mesmo livro ativo
    $reservasAtivas = $reservaModel->listarAtivasPorUsuarioLivro($usuarioId, $livroId);
    if (!empty($reservasAtivas)) {
        die("Você já possui uma reserva ativa para este livro.");
    }

    $dados = [
        ':livro_id' => $livroId,
        ':usuario_id' => $usuarioId,
        ':data_reserva' => date('Y-m-d H:i:s'),
        ':status' => 'ativa'
    ];

    $resultado = $reservaModel->cadastrar($dados);
    if ($resultado) {
        header("Location: ../view/reservas.php");
        exit;
    } else {
        die("Erro ao cadastrar reserva.");
    }
}

// 2. Cancelar reserva
if (isset($_GET['cancelar'])) {
    $reservaId = (int)$_GET['cancelar'];

    // Verifica se a reserva pertence ao usuário
    $reserva = $reservaModel->buscarPorId($reservaId);
    if (!$reserva || $reserva['usuario_id'] != $usuarioId) {
        die("Ação não permitida.");
    }

    $reservaModel->atualizarStatus($reservaId, 'cancelada');
    header("Location: ../view/reservas.php");
    exit;
}
