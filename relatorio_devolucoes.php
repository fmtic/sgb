<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

// Filtro de datas
$dataInicio = $_GET['inicio'] ?? '';
$dataFim = $_GET['fim'] ?? '';

$where = "WHERE e.status = 'devolvido'";
$params = [];

if ($dataInicio && $dataFim) {
    $where .= " AND e.data_devolucao_real BETWEEN ? AND ?";
    $params = [$dataInicio, $dataFim];
}

$stmt = $pdo->prepare("
    SELECT e.data_emprestimo, e.data_devolucao_prevista, e.data_devolucao_real,
           l.titulo, r.nome AS leitor_nome
    FROM emprestimos e
    JOIN livros l ON e.livro_id = l.id
    JOIN leitores r ON e.leitor_id = r.id
    $where
    ORDER BY e.data_devolucao_real DESC
");
$stmt->execute($params);
$devolucoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatório de Devoluções</title>
  <style>
    body { font-family: Arial, sans-serif; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .btn { padding: 6px 12px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
    .btn:hover { background: #2980b9; }
    form { margin-bottom: 15px; }
  </style>
</head>
<body>
  <h2>📄 Relatório de Devoluções</h2>

  <form method="GET">
    <label>De: <input type="date" name="inicio" value="<?= htmlspecialchars($dataInicio) ?>"></label>
    <label>Até: <input type="date" name="fim" value="<?= htmlspecialchars($dataFim) ?>"></label>
    <button type="submit" class="btn">🔎 Filtrar</button>
    <a href="relatorio_devolucoes.php" class="btn" style="background:#e67e22;">🔁 Limpar</a>
  </form>

  <p>
    <a href="exportar_devolucoes_csv.php?inicio=<?= $dataInicio ?>&fim=<?= $dataFim ?>" class="btn">📥 Exportar CSV</a>
    <a href="exportar_devolucoes_pdf.php?inicio=<?= $dataInicio ?>&fim=<?= $dataFim ?>" class="btn" target="_blank">📄 Exportar PDF</a>
  </p>

  <?php if (count($devolucoes) === 0): ?>
    <p>Nenhuma devolução encontrada no período.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Leitor</th>
        <th>Livro</th>
        <th>Empréstimo</th>
        <th>Prev. Devolução</th>
        <th>Devolução Real</th>
      </tr>
      <?php foreach ($devolucoes as $d): ?>
        <tr>
          <td><?= htmlspecialchars($d['leitor_nome']) ?></td>
          <td><?= htmlspecialchars($d['titulo']) ?></td>
          <td><?= $d['data_emprestimo'] ?></td>
          <td><?= $d['data_devolucao_prevista'] ?></td>
          <td><?= $d['data_devolucao_real'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <br>
  <a href="relatorios.php">⬅ Voltar</a>
</body>
</html>
