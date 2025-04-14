<?php
require_once 'conexao.php';

// Obter o ISBN via GET
$isbn = $_GET['isbn'] ?? '';

// Verificar se o ISBN foi enviado e não está vazio
if (!empty($isbn)) {
    // Verificar se o ISBN já existe na tabela de livros
    $sql = "SELECT COUNT(*) FROM livros WHERE isbn = :isbn";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['isbn' => $isbn]);
    $count = $stmt->fetchColumn();

    // Retornar a resposta em formato JSON
    echo json_encode(['duplicado' => $count > 0]);
} else {
    // Caso o ISBN não seja fornecido
    echo json_encode(['duplicado' => false]);
}
?>
