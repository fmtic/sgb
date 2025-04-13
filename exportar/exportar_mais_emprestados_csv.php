<?php
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=livros_mais_emprestados.csv");

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

$ranking = $pdo->query("
  SELECT l.titulo, COUNT(e.id) AS total
  FROM emprestimos e
  JOIN livros l ON e.livro_id = l.id
  GROUP BY l.titulo
  ORDER BY total DESC
  LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

$output = fopen('php://output', 'w');
fputcsv($output, ['Título', 'Total de Empréstimos']);
foreach ($ranking as $livro) {
    fputcsv($output, [$livro['titulo'], $livro['total']]);
}
fclose($output);
exit;
