<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

$livros = $pdo->query("SELECT id, titulo FROM livros ORDER BY titulo")->fetchAll(PDO::FETCH_ASSOC);
$leitores = $pdo->query("SELECT id, nome FROM leitores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $livro_id = $_POST['livro_id'];
    $leitor_id = $_POST['leitor_id'];
    $data_emprestimo = $_POST['data_emprestimo'];
    $data_prev = $_POST['data_devolucao_prevista'];

    $stmt = $pdo->prepare("INSERT INTO emprestimos (livro_id, leitor_id, data_emprestimo, data_devolucao_prevista, status)
                           VALUES (?, ?, ?, ?, 'ativo')");
    $stmt->execute([$livro_id, $leitor_id, $data_emprestimo, $data_prev]);
    header("Location: emprestimos.php");
    registrar_log('Empréstimo realizado', "Livro ID: $livro_id, Leitor ID: $leitor_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Novo Empréstimo</title>
  <link rel="stylesheet" href="css/form_emprestimo.css">
</head>
<body>
  <h2>Registrar Empréstimo</h2>
  <form method="POST">
    <label>Leitor:</label><br>
    <select name="leitor_id" required>
      <option value="">Selecione</option>
      <?php foreach ($leitores as $l): ?>
        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nome']) ?></option>
      <?php endforeach; ?>
    </select><br><br>

    <label>Livro:</label><br>
    <select name="livro_id" required>
      <option value="">Selecione</option>
      <?php foreach ($livros as $l): ?>
        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['titulo']) ?></option>
      <?php endforeach; ?>
    </select><br><br>

    <label>Data de Empréstimo:</label><br>
    <input type="date" name="data_emprestimo" value="<?= date('Y-m-d') ?>" required><br><br>

    <label>Data Prevista de Devolução:</label><br>
    <input type="date" name="data_devolucao_prevista" required><br><br>

    <input type="submit" value="Registrar">
  </form>
  <br>
  <a href="emprestimos.php">⬅ Voltar</a>
</body>
</html>
