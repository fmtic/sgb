<?php
require('fpdf/fpdf.php');

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");
$leitores = $pdo->query("SELECT * FROM leitores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(190,10,utf8_decode("Relatório de Leitores"), 0, 1, 'C');
$pdf->Ln(5);

// Cabeçalho
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50,7,"Nome",1);
$pdf->Cell(30,7,"Nascimento",1);
$pdf->Cell(70,7,"Endereço",1);
$pdf->Cell(40,7,"Telefone",1);
$pdf->Ln();

// Conteúdo
$pdf->SetFont('Arial','',9);
foreach ($leitores as $l) {
    $pdf->Cell(50,6,utf8_decode($l['nome']),1);
    $pdf->Cell(30,6,$l['data_nascimento'],1);
    $pdf->Cell(70,6,utf8_decode($l['endereco']),1);
    $pdf->Cell(40,6,$l['telefone'],1);
    $pdf->Ln();
}

$pdf->Output();
exit;
