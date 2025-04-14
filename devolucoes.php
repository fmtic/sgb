<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'log.php';
$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['devolver'])) {
    foreach ($_POST['emprestimos'] as $id) {
        // Atualizar empréstimo
        $stmt = $pdo->prepare("UPDATE emprestimos SET data_devolucao_real = CURRENT_DATE, status = 'devolvido' WHERE id = ?");
        $stmt->execute([$id]);

        // Buscar o livro devolvido
        $stmtLivro = $pdo->prepare("SELECT livro_id FROM emprestimos WHERE id = ?");
        $stmtLivro->execute([$id]);
        $livro_id = $stmtLivro->fetchColumn();

        // Atualizar reserva (primeira pendente)
        $stmtRes = $pdo->prepare("
            SELECT id FROM reservas 
            WHERE livro_id = ? AND status = 'pendente' 
            ORDER BY data_reserva ASC LIMIT 1
        ");
        $stmtRes->execute([$livro_id]);
        $reserva_id = $stmtRes->fetchColumn();

        if ($reserva_id) {
            $pdo->prepare("UPDATE reservas SET status = 'notificado' WHERE id = ?")->execute([$reserva_id]);
            registrar_log('Reserva liberada', "Reserva ID: $reserva_id, Livro ID: $livro_id");
        }

        registrar_log('Livro devolvido', "Empréstimo ID: $id, Livro ID: $livro_id");
    }

    header("Location: devolucoes.php");
    exit;
}

$emprestimos = $pdo->query("
  SELECT e.id, e.data_emprestimo, e.data_devolucao_prevista, e.status,
         l.titulo, r.nome AS leitor_nome
  FROM emprestimos e
  JOIN livros l ON e.livro_id = l.id
  JOIN leitores r ON e.leitor_id = r.id
  WHERE e.status IN ('ativo', 'atrasado')
  ORDER BY e.data_emprestimo ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Devoluções</title>
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f2f2f2; }
    .btn { padding: 6px 12px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; }
    .btn:hover { background: #219150; }
    .dias-atraso { color: #e74c3c; font-weight: bold; }
  </style>
</head>
<body>
  <h2>📘 Devoluções Pendentes</h2>

  <?php if (count($emprestimos) === 0): ?>
    <p>Nenhum empréstimo pendente de devolução.</p>
  <?php else: ?>
    <form method="POST">
      <table>
        <tr>
          <th></th>
          <th>Leitor</th>
          <th>Livro</th>
          <th>Data Empréstimo</th>
          <th>Prev. Devolução</th>
          <th>Status</th>
          <th>Dias de Atraso</th>
        </tr>
        <?php foreach ($emprestimos as $e): ?>
          <?php
            $diasAtraso = '';
            if ($e['status'] === 'atrasado') {
                $prevista = new DateTime($e['data_devolucao_prevista']);
                $hoje = new DateTime();
                $diasAtraso = $prevista->diff($hoje)->days . ' dias';
            }
          ?>
          <tr>
            <td><input type="checkbox" name="emprestimos[]" value="<?= $e['id'] ?>"></td>
            <td><?= htmlspecialchars($e['leitor_nome']) ?></td>
            <td><?= htmlspecialchars($e['titulo']) ?></td>
            <td><?= $e['data_emprestimo'] ?></td>
            <td><?= $e['data_devolucao_prevista'] ?></td>
            <td><?= ucfirst($e['status']) ?></td>
            <td class="dias-atraso"><?= $diasAtraso ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
      <br>
      <input class="btn" type="submit" name="devolver" value="📥 Registrar Devoluções Selecionadas">
    </form>
  <?php endif; ?>

  <br>
  <a href="relatorio_devolucoes.php">📄 Ver relatório de devoluções</a> |
  <a href="dashboard.php">⬅ Voltar</a>
</body>
</html>