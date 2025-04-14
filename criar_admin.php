<?php
$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

$nome = "Administrador";
$email = "admin@biblioteca.com";
$senha = password_hash("admin123", PASSWORD_DEFAULT);
$tipo = "admin";

$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (?, ?, ?, ?)");
$stmt->execute([$nome, $email, $senha, $tipo]);

echo "Usuário administrador criado!";
