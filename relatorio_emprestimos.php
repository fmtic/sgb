<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");
$filtro = $_GET['status'] ?? '';

$sql = "SELECT e.*, l.titulo, r.nome AS leitor_nome 
        FROM emprestimos e
        JOIN livros l ON l.id = e.livro_id
        JOIN leitores r ON r.id = e.leitor_id";

if ($filtro) {
    $sql .= " WHERE e.status = " . $pdo->quote($filtro);
}

$sql .= " ORDER BY e.data_emprestimo DESC";
$emprestimos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Empréstimos</title>
  <style>
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 6px; }
    th { background: #eee; }
    select { padding: 5px; }
  </style>
</head>
<body>
  <h2>🔁 Relatório de Empréstimos</h2>
  <form method="GET">
    <label>Filtrar por status:</label>
    <select name="status" onchange="this.form.submit()">
      <option value="">-- Todos --</option>
      <option value="ativo" <?= $filtro === 'ativo' ? 'selected' : '' ?>>Ativo</option>
      <option value="devolvido" <?= $filtro === 'devolvido' ? 'selected' : '' ?>>Devolvido</option>
      <option value="atrasado" <?= $filtro === 'atrasado' ? 'selected' : '' ?>>Atrasado</option>
    </select>
  </form>
  <br>
  <p>
  <a href="exportar/exportar_emprestimos_csv.php" class="btn">📥 Exportar CSV</a>
  <a href="exportar/exportar_emprestimos_pdf.php" class="btn" target="_blank">📄 Exportar PDF</a>
</p>
  <table>
    <tr>
      <th>Leitor</th>
      <th>Livro</th>
      <th>Data Empréstimo</th>
      <th>Prev. Devolução</th>
      <th>Devolução Real</th>
      <th>Status</th>
    </tr>
    <?php foreach ($emprestimos as $e): ?>
    <tr>
      <td><?= htmlspecialchars($e['leitor_nome']) ?></td>
      <td><?= htmlspecialchars($e['titulo']) ?></td>
      <td><?= $e['data_emprestimo'] ?></td>
      <td><?= $e['data_devolucao_prevista'] ?></td>
      <td><?= $e['data_devolucao_real'] ?? '-' ?></td>
      <td><?= ucfirst($e['status']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <br>
  <a href="relatorios.php">⬅ Voltar</a>
</body>
</html>
