<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Biblioteca</title>
  <link rel="stylesheet" href="css/estilo_dashboard.css">
</head>
<body>
  <div class="painel">
    <h2>Bem-vindo(a) à Biblioteca</h2>
    <a href="livros.php" class="botao">📚 Gerenciar Livros</a>
    <a href="leitores.php" class="botao">👤 Gerenciar Leitores</a>
    <a href="usuarios.php" class="botao">👤 Gerenciar Usuários</a>
    <a href="emprestimos.php" class="botao">🔁 Empréstimos</a>
    <a href="devolucoes.php" class="botao">✅ Devoluções</a>
    <a href="relatorios.php" class="botao">📊 Relatórios</a>
    <a href="logout.php" class="botao" style="background: #e74c3c;">🚪 Sair</a>
  </div>
<footer>
    <a href="https://fmtic.com.br">&#169;FMtic</a> - Sistema de Gestão de Bibliotecas SGB
</footer>
</body>
</html>
