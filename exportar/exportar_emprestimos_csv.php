<?php
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=emprestimos.csv");

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");
$dados = $pdo->query("
  SELECT r.nome AS leitor, l.titulo, e.data_emprestimo, e.data_devolucao_prevista,
         e.data_devolucao_real, e.status
  FROM emprestimos e
  JOIN livros l ON e.livro_id = l.id
  JOIN leitores r ON e.leitor_id = r.id
  ORDER BY e.data_emprestimo DESC
")->fetchAll(PDO::FETCH_ASSOC);

$output = fopen('php://output', 'w');
fputcsv($output, ['Leitor', 'Livro', 'Empréstimo', 'Prev. Devolução', 'Devolução Real', 'Status']);

foreach ($dados as $linha) {
    fputcsv($output, array_values($linha));
}
fclose($output);
exit;
