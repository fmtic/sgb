<?php
require_once '../config/db.php';
require_once '../model/Emprestimo.php';

$emprestimoModel = new Emprestimo($conn);
$emprestimos = $emprestimoModel->filtrar($_GET);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=relatorio_emprestimos.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Livro', 'Usuário', 'Data Empréstimo', 'Data Devolução Prevista', 'Data Devolução Real', 'Status']);

foreach ($emprestimos as $e) {
    fputcsv($output, [
        $e['titulo'],
        $e['nome'].' '.$e['sobrenome'],
        date('d/m/Y', strtotime($e['data_emprestimo'])),
        date('d/m/Y', strtotime($e['data_devolucao_prevista'])),
        $e['data_devolucao_real'] ? date('d/m/Y', strtotime($e['data_devolucao_real'])) : '-',
        $e['status']
    ]);
}
fclose($output);
exit;
?>
