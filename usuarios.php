<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
    header("Location: usuarios.php");
    exit;
}

$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Usuários</title>
  <style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #eee; }
    .btn { padding: 5px 10px; background: #3498db; color: #fff; border-radius: 4px; text-decoration: none; }
    .btn:hover { background: #2980b9; }
    .danger { background: #e74c3c; }
  </style>
</head>
<body>
  <h2>👥 Usuários do Sistema</h2>
  <a href="usuario_form.php" class="btn">➕ Novo Usuário</a>
  <br><br>
  <table>
    <tr>
      <th>Nome</th>
      <th>Email</th>
      <th>Tipo</th>
      <th>Ações</th>
    </tr>
    <?php foreach ($usuarios as $u): ?>
    <tr>
      <td><?= htmlspecialchars($u['nome']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= ucfirst($u['tipo_usuario']) ?></td>
      <td>
        <a class="btn" href="usuario_form.php?id=<?= $u['id'] ?>">✏️ Editar</a>
        <a class="btn danger" href="usuarios.php?excluir=<?= $u['id'] ?>" onclick="return confirm('Excluir este usuário?')">🗑️ Excluir</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <br>
  <a href="dashboard.php">⬅ Voltar</a>
</body>
</html>
