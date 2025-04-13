<?php
require('fpdf/fpdf.php');

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

$ranking = $pdo->query("
  SELECT l.titulo, COUNT(e.id) AS total
  FROM emprestimos e
  JOIN livros l ON e.livro_id = l.id
  GROUP BY l.titulo
  ORDER BY total DESC
  LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(190,10,utf8_decode("Ranking: Livros Mais Emprestados"), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(130,8,"Título",1);
$pdf->Cell(50,8,"Empréstimos",1);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
foreach ($ranking as $livro) {
    $pdf->Cell(130,7,utf8_decode($livro['titulo']),1);
    $pdf->Cell(50,7,$livro['total'],1);
    $pdf->Ln();
}

$pdf->Output();
exit;
