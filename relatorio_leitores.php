<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");
$leitores = $pdo->query("SELECT * FROM leitores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

function calcularIdade($data_nasc) {
    $nasc = new DateTime($data_nasc);
    $hoje = new DateTime();
    return $nasc->diff($hoje)->y;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatório de Leitores</title>
  <style>
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f2f2f2; }
    .btn { padding: 6px 12px; text-decoration: none; background: #3498db; color: white; border-radius: 4px; margin-right: 10px; }
    .btn:hover { background: #2980b9; }
  </style>
</head>
<body>
  <h2>👤 Relatório de Leitores Cadastrados</h2>

  <p>
    <a href="exportar/exportar_leitores_csv.php" class="btn">📥 Exportar CSV</a>
    <a href="exportar/exportar_leitores_pdf.php" class="btn" target="_blank">📄 Exportar PDF</a>
  </p>

  <?php if (count($leitores) === 0): ?>
    <p>Nenhum leitor encontrado.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>Nome</th>
        <th>Data de Nascimento</th>
        <th>Idade</th>
        <th>Endereço</th>
        <th>Telefone</th>
        <th>Email</th>
      </tr>
      <?php foreach ($leitores as $l): ?>
        <tr>
          <td><?= htmlspecialchars($l['nome']) ?></td>
          <td><?= $l['data_nascimento'] ?></td>
          <td><?= calcularIdade($l['data_nascimento']) ?> anos</td>
          <td><?= htmlspecialchars($l['endereco']) ?></td>
          <td><?= htmlspecialchars($l['telefone']) ?></td>
          <td><?= htmlspecialchars($l['email']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <br>
  <a href="relatorios.php">⬅ Voltar</a>
</body>
</html>
