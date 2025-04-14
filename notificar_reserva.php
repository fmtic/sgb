<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reserva_id'])) {
    $stmt = $pdo->prepare("UPDATE reservas SET status = 'notificado' WHERE id = ?");
    $stmt->execute([$_POST['reserva_id']]);
}

header("Location: notificacoes.php");
exit;
