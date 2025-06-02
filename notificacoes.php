<?php
session_start();
include 'conexao.php';
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Empréstimos atrasados
$atrasos = $pdo->query("
  SELECT e.*, l.titulo, r.nome AS leitor_nome
  FROM emprestimos e
  JOIN livros l ON e.livro_id = l.id
  JOIN leitores r ON e.leitor_id = r.id
  WHERE e.status = 'atrasado'
  ORDER BY e.data_devolucao_prevista
")->fetchAll(PDO::FETCH_ASSOC);

// Reservas pendentes de livros disponíveis
$reservas = $pdo->query("
  SELECT re.*, l.titulo, le.nome AS leitor_nome
  FROM reservas re
  JOIN livros l ON re.livro_id = l.id
  JOIN leitores le ON re.leitor_id = le.id
  WHERE re.status = 'pendente' AND NOT EXISTS (
    SELECT 1 FROM emprestimos e WHERE e.livro_id = re.livro_id AND e.status IN ('ativo', 'atrasado')
  )
  ORDER BY re.data_reserva
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Notificações</title>
  <style>
    h3 { margin-top: 30px; }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  <h2>🔔 Notificações</h2>

  <h3>📌 Empréstimos Atrasados</h3>
  <?php if (count($atrasos) === 0): ?>
    <p>Nenhum empréstimo atrasado.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Leitor</th>
        <th>Livro</th>
        <th>Data Prevista</th>
        <th>Dias de Atraso</th>
      </tr>
      <?php foreach ($atrasos as $a): ?>
      <tr>
        <td><?= htmlspecialchars($a['leitor_nome']) ?></td>
        <td><?= htmlspecialchars($a['titulo']) ?></td>
        <td><?= $a['data_devolucao_prevista'] ?></td>
        <td>
          <?php
            $dias = (new DateTime())->diff(new DateTime($a['data_devolucao_prevista']))->days;
            echo $dias;
          ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <h3>📬 Livros Reservados Disponíveis</h3>
  <?php if (count($reservas) === 0): ?>
    <p>Sem reservas com livros disponíveis.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Leitor</th>
        <th>Livro</th>
        <th>Data da Reserva</th>
        <th>Ação</th>
      </tr>
      <?php foreach ($reservas as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['leitor_nome']) ?></td>
        <td><?= htmlspecialchars($r['titulo']) ?></td>
        <td><?= $r['data_reserva'] ?></td>
        <td>
          <form method="POST" action="notificar_reserva.php">
            <input type="hidden" name="reserva_id" value="<?= $r['id'] ?>">
            <input type="submit" value="📤 Marcar como notificado">
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <br>
  <a href="dashboard.php">⬅ Voltar</a>
</body>
</html>
