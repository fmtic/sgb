<?php
require('fpdf/fpdf.php');

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");
$livros = $pdo->query("SELECT * FROM livros ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,utf8_decode("Relatório de Livros"), 0, 1, 'C');
$pdf->Ln(5);

// Cabeçalho
$pdf->SetFont('Arial','B',10);
$pdf->Cell(60,7,utf8_decode("Título"),1);
$pdf->Cell(40,7,utf8_decode("Autor"),1);
$pdf->Cell(30,7,"Ano",1);
$pdf->Cell(60,7,utf8_decode("Gênero"),1);
$pdf->Ln();

// Conteúdo
$pdf->SetFont('Arial','',10);
foreach ($livros as $l) {
    $pdf->Cell(60,6,utf8_decode($l['titulo']),1);
    $pdf->Cell(40,6,utf8_decode($l['autor']),1);
    $pdf->Cell(30,6,$l['ano'],1);
    $pdf->Cell(60,6,utf8_decode($l['genero']),1);
    $pdf->Ln();
}

$pdf->Output();
exit;
