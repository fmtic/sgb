<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'leitor') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("
  SELECT e.*, l.titulo 
  FROM emprestimos e
  JOIN livros l ON e.livro_id = l.id
  WHERE e.leitor_id = (
    SELECT id FROM leitores WHERE usuario_id = ?
  )
  ORDER BY e.data_emprestimo DESC
");
$stmt->execute([$usuario_id]);
$emprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Histórico de Empréstimos</title>
  <style>
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #eee; }
  </style>
</head>
<body>
  <h2>📜 Meu Histórico de Empréstimos</h2>
  <?php if (count($emprestimos) === 0): ?>
    <p>Você ainda não fez nenhum empréstimo.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Título</th>
        <th>Data Empréstimo</th>
        <th>Prev. Devolução</th>
        <th>Data Devolução</th>
        <th>Status</th>
      </tr>
      <?php foreach ($emprestimos as $e): ?>
        <tr>
          <td><?= htmlspecialchars($e['titulo']) ?></td>
          <td><?= $e['data_emprestimo'] ?></td>
          <td><?= $e['data_devolucao_prevista'] ?></td>
          <td><?= $e['data_devolucao_real'] ?? '-' ?></td>
          <td><?= ucfirst($e['status']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <br>
  <a href="catalogo.php">⬅ Voltar ao Catálogo</a>
</body>
</html>
