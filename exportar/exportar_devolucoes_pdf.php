<?php
require('fpdf/fpdf.php');

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
$devolucoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(190, 10, utf8_decode("Relatório de Devoluções"), 0, 1, 'C');
$pdf->Ln(5);

// Cabeçalho
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 7, "Leitor", 1);
$pdf->Cell(50, 7, "Livro", 1);
$pdf->Cell(30, 7, "Empréstimo", 1);
$pdf->Cell(30, 7, "Prev. Dev.", 1);
$pdf->Cell(30, 7, "Dev. Real", 1);
$pdf->Ln();

// Conteúdo
$pdf->SetFont('Arial', '', 9);
foreach ($devolucoes as $d) {
    $pdf->Cell(50, 6, utf8_decode($d['leitor']), 1);
    $pdf->Cell(50, 6, utf8_decode($d['titulo']), 1);
    $pdf->Cell(30, 6, $d['data_emprestimo'], 1);
    $pdf->Cell(30, 6, $d['data_devolucao_prevista'], 1);
    $pdf->Cell(30, 6, $d['data_devolucao_real'], 1);
    $pdf->Ln();
}

$pdf->Output();
exit;
