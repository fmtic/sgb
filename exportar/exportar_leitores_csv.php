<?php
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=leitores.csv");

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");
$leitores = $pdo->query("SELECT * FROM leitores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$output = fopen('php://output', 'w');
fputcsv($output, ['Nome', 'Data Nascimento', 'Endereço', 'Telefone', 'Email']);

foreach ($leitores as $l) {
    fputcsv($output, [
        $l['nome'], $l['data_nascimento'], $l['endereco'], $l['telefone'], $l['email']
    ]);
}
fclose($output);
exit;
