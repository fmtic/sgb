<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

if (isset($_GET['devolver'])) {
    $id = $_GET['devolver'];
    $hoje = date('Y-m-d');
    $stmt = $pdo->prepare("UPDATE emprestimos SET data_devolucao_real = ?, status = 'devolvido' WHERE id = ?");
    $stmt->execute([$hoje, $id]);
    header("Location: emprestimos.php");
    registrar_log('Livro devolvido', "Empréstimo ID: $id");
    exit;
}

// Atualiza status de empréstimos vencidos
$hoje = date('Y-m-d');
$pdo->query("UPDATE emprestimos 
             SET status = 'atrasado' 
             WHERE status = 'ativo' AND data_devolucao_prevista < '$hoje'");

$emprestimos = $pdo->query("
    SELECT e.*, l.titulo, r.nome AS leitor_nome
    FROM emprestimos e
    JOIN livros l ON e.livro_id = l.id
    JOIN leitores r ON e.leitor_id = r.id
    ORDER BY e.data_emprestimo DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Empréstimos</title>
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #f2f2f2; }
    .btn { padding: 5px 10px; background: #3498db; color: #fff; border-radius: 4px; text-decoration: none; }
    .btn:hover { background: #2980b9; }
    .danger { background: #e74c3c; }
    .verde { color: green; font-weight: bold; }
    .vermelho { color: red; font-weight: bold; }
  </style>
</head>
<body>
  <h2>Empréstimos</h2>
  <a href="emprestimo_form.php" class="btn">➕ Novo Empréstimo</a>
  <br><br>
  <table>
    <tr>
      <th>Leitor</th>
      <th>Livro</th>
      <th>Empréstimo</th>
      <th>Prev. Devolução</th>
      <th>Devolvido</th>
      <th>Status</th>
      <th>Ações</th>
    </tr>
    <?php foreach ($emprestimos as $e): ?>
    <tr>
      <td><?= htmlspecialchars($e['leitor_nome']) ?></td>
      <td><?= htmlspecialchars($e['titulo']) ?></td>
      <td><?= $e['data_emprestimo'] ?></td>
      <td><?= $e['data_devolucao_prevista'] ?></td>
      <td><?= $e['data_devolucao_real'] ?? '-' ?></td>
      <td class="<?= $e['status'] == 'atrasado' ? 'vermelho' : 'verde' ?>"><?= ucfirst($e['status']) ?></td>
      <td>
        <?php if ($e['status'] == 'ativo' || $e['status'] == 'atrasado'): ?>
          <a class="btn" href="emprestimos.php?devolver=<?= $e['id'] ?>">✅ Marcar Devolução</a>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <br>
  <a href="dashboard.php">⬅ Voltar ao Painel</a>
</body>
</html>
