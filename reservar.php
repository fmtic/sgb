<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'leitor') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

// Encontrar o ID do leitor vinculado a esse usuário
$stmt = $pdo->prepare("SELECT id FROM leitores WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$leitor_id = $stmt->fetchColumn();

$livro_id = $_POST['livro_id'] ?? null;
$data_reserva = date('Y-m-d');

if ($livro_id && $leitor_id) {
    // Verifica se já há uma reserva pendente do mesmo leitor para o mesmo livro
    $verifica = $pdo->prepare("SELECT 1 FROM reservas WHERE livro_id = ? AND leitor_id = ? AND status = 'pendente'");
    $verifica->execute([$livro_id, $leitor_id]);

    if (!$verifica->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO reservas (livro_id, leitor_id, data_reserva, status) VALUES (?, ?, ?, 'pendente')");
        $stmt->execute([$livro_id, $leitor_id, $data_reserva]);
    }
}

header("Location: catalogo.php");
exit;
