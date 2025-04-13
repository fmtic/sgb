<?php
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=livros.csv");

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");
$livros = $pdo->query("SELECT * FROM livros ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);

$output = fopen('php://output', 'w');
fputcsv($output, ['Título', 'Autor', 'ISBN', 'Editora', 'Ano', 'Gênero']);

foreach ($livros as $l) {
    fputcsv($output, [
        $l['titulo'], $l['autor'], $l['isbn'], $l['editora'], $l['ano'], $l['genero']
    ]);
}
fclose($output);
exit;
