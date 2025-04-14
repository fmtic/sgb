<?php
require_once 'conexao.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    exit('ID inválido');
}

$id = (int) $_GET['id'];
$thumb = isset($_GET['thumb']);

$stmt = $pdo->prepare("SELECT foto FROM leitores WHERE id = ?");
$stmt->execute([$id]);
$leitor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$leitor || empty($leitor['foto'])) {
    http_response_code(404);
    exit('Foto não encontrada');
}

$fotoData = $leitor['foto'];

// Se for thumb, redimensiona usando GD
if ($thumb) {
    $img = imagecreatefromstring($fotoData);
    if ($img === false) {
        http_response_code(500);
        exit('Erro ao processar imagem');
    }

    $largura = 50;
    $altura = 50;

    $thumbImg = imagecreatetruecolor($largura, $altura);
    imagecopyresampled($thumbImg, $img, 0, 0, 0, 0, $largura, $altura, imagesx($img), imagesy($img));

    header("Content-Type: image/jpeg");
    imagejpeg($thumbImg);
    imagedestroy($img);
    imagedestroy($thumbImg);
    exit;
}

// Imagem original
header("Content-Type: image/jpeg");
echo $fotoData;
