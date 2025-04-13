<?php
require('fpdf/fpdf.php');

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");
$dados = $pdo->query("
  SELECT r.nome AS leitor, l.titulo, e.data_emprestimo, e.data_devolucao_prevista,
         e.data_devolucao_real, e.status
  FROM emprestimos e
  JOIN livros l ON e.livro_id = l.id
  JOIN leitores r ON e.leitor_id = r.id
  ORDER BY e.data_emprestimo DESC
")->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,utf8_decode("Relatório de Empréstimos"), 0, 1, 'C');
$pdf->Ln(5);

// Cabeçalho
$pdf->SetFont('Arial','B',9);
$pdf->Cell(40,7,"Leitor",1);
$pdf->Cell(50,7,"Livro",1);
$pdf->Cell(25,7,"Empréstimo",1);
$pdf->Cell(25,7,"Prev. Dev.",1);
$pdf->Cell(25,7,"Dev. Real",1);
$pdf->Cell(25,7,"Status",1);
$pdf->Ln();

// Conteúdo
$pdf->SetFont('Arial','',8);
foreach ($dados as $d) {
    $pdf->Cell(40,6,utf8_decode($d['leitor']),1);
    $pdf->Cell(50,6,utf8_decode($d['titulo']),1);
    $pdf->Cell(25,6,$d['data_emprestimo'],1);
    $pdf->Cell(25,6,$d['data_devolucao_prevista'],1);
    $pdf->Cell(25,6,$d['data_devolucao_real'] ?? '-',1);
    $pdf->Cell(25,6,utf8_decode($d['status']),1);
    $pdf->Ln();
}

$pdf->Output();
exit;
