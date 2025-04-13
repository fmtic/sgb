<?php
$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

$dataInicio = $_GET['inicio'] ?? '';
$dataFim = $_GET['fim'] ?? '';

$where = "WHERE e.status = 'devolvido'";
$params = [];

if ($dataInicio && $dataFim) {
    $where .= " AND e.data_devolucao_real BETWEEN ? AND ?";
    $params = [$dataInicio, $dataFim];
}

$stmt = $pdo->prepare("
    SELECT r.nome AS leitor, l.titulo, e.data_emprestimo, e.data_devolucao_prevista, e.data_devolucao_real
    FROM emprestimos e
    JOIN livros l ON e.livro_id = l.id
    JOIN leitores r ON e.leitor_id = r.id
    $where
    ORDER BY e.data_devolucao_real DESC
");
$stmt->execute($params);
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cabeçalhos para download
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=relatorio_devolucoes.csv");

$output = fopen('php://output', 'w');
fputcsv($output, ['Leitor', 'Livro', 'Empréstimo', 'Prev. Devolução', 'Devolução Real']);

foreach ($dados as $linha) {
    fputcsv($output, array_values($linha));
}

fclose($output);
exit;
