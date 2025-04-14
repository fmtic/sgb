<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatórios</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f7f7;
      padding: 30px;
    }
    h2 {
      color: #2c3e50;
    }
    ul {
      list-style: none;
      padding: 0;
    }
    li {
      background: #ffffff;
      padding: 15px;
      margin: 10px 0;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0,0,0,0.05);
      font-size: 16px;
    }
    a {
      text-decoration: none;
      color: #3498db;
      font-weight: bold;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <h2>📊 Relatórios Disponíveis</h2>
  <ul>
    <li>📚 <a href="relatorio_livros.php">Relatório de Livros Cadastrados</a></li>
    <li>🔁 <a href="relatorio_emprestimos.php">Relatório de Empréstimos</a></li>
    <li>📘 <a href="relatorio_devolucoes.php">Relatório de Devoluções</a></li>
    <li>👤 <a href="relatorio_leitores.php">Relatório de Leitores</a></li>
    <li>🏆 <a href="relatorio_mais_emprestados.php">Livros Mais Emprestados</a></li>
  </ul>

  <br>
  <a href="dashboard.php">⬅ Voltar ao Painel</a>

</body>
</html>
