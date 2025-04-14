<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}
$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

$reservas = $pdo->query("
  SELECT r.*, l.titulo, le.nome AS leitor_nome
  FROM reservas r
  JOIN livros l ON r.livro_id = l.id
  JOIN leitores le ON r.leitor_id = le.id
  WHERE r.status = 'pendente'
  ORDER BY r.data_reserva
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Reservas de Livros</title>
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #eee; }
    .btn { padding: 5px 10px; background: #3498db; color: #fff; border-radius: 4px; text-decoration: none; }
    .btn:hover { background: #2980b9; }
  </style>
</head>
<body>
  <h2>📚 Reservas Pendentes</h2>
  <?php if (count($reservas) === 0): ?>
    <p>Não há reservas pendentes.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Leitor</th>
        <th>Livro</th>
        <th>Data da Reserva</th>
        <th>Status</th>
      </tr>
      <?php foreach ($reservas as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['leitor_nome']) ?></td>
        <td><?= htmlspecialchars($r['titulo']) ?></td>
        <td><?= $r['data_reserva'] ?></td>
        <td><?= ucfirst($r['status']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
  <br>
  <a href="dashboard.php">⬅ Voltar</a>
</body>
</html>
