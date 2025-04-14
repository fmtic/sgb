<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

$ranking = $pdo->query("
  SELECT l.titulo, COUNT(e.id) AS total
  FROM emprestimos e
  JOIN livros l ON e.livro_id = l.id
  GROUP BY l.titulo
  ORDER BY total DESC
  LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Mais Emprestados</title>
  <style>
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 6px; }
    th { background: #eee; }
  </style>
</head>
<body>
  <h2>🏆 Livros Mais Emprestados</h2>
  <p>
  <a href="exportar/exportar_mais_emprestados_csv.php" class="btn">📥 Exportar CSV</a>
  <a href="exportar/exportar_mais_emprestados_pdf.php" class="btn" target="_blank">📄 Exportar PDF</a>
</p>
  <table>
    <tr>
      <th>Título</th>
      <th>Qtd. Empréstimos</th>
    </tr>
    <?php foreach ($ranking as $livro): ?>
    <tr>
      <td><?= htmlspecialchars($livro['titulo']) ?></td>
      <td><?= $livro['total'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <br>
  <a href="relatorios.php">⬅ Voltar</a>
</body>
</html>
