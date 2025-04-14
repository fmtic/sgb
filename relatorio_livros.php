<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");

// Filtros
$filtros = [];
$condicoes = [];

if (!empty($_GET['titulo'])) {
    $condicoes[] = "titulo ILIKE :titulo";
    $filtros[':titulo'] = '%' . $_GET['titulo'] . '%';
}
if (!empty($_GET['autor'])) {
    $condicoes[] = "autor ILIKE :autor";
    $filtros[':autor'] = '%' . $_GET['autor'] . '%';
}
if (!empty($_GET['ano'])) {
    $condicoes[] = "ano = :ano";
    $filtros[':ano'] = $_GET['ano'];
}
if (!empty($_GET['genero'])) {
    $condicoes[] = "genero ILIKE :genero";
    $filtros[':genero'] = '%' . $_GET['genero'] . '%';
}
if (!empty($_GET['editora'])) {
    $condicoes[] = "editora ILIKE :editora";
    $filtros[':editora'] = '%' . $_GET['editora'] . '%';
}
if (!empty($_GET['isbn'])) {
    $condicoes[] = "isbn = :isbn";
    $filtros[':isbn'] = $_GET['isbn'];
}

$sql = "SELECT * FROM livros";
if ($condicoes) {
    $sql .= " WHERE " . implode(" AND ", $condicoes);
}
$sql .= " ORDER BY titulo ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($filtros);
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatório de Livros</title>
  <link rel="stylesheet" href="css/estilo_relatorio_livros.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f2f8fc; margin: 0; padding: 20px; }
    .container { max-width: 1100px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h1 { color: #2cb2ff; margin-bottom: 10px; }
    form.filtros { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
    .filtros label { flex: 1 1 200px; }
    .filtros input { width: 100%; padding: 6px; border-radius: 6px; border: 1px solid #ccc; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
    th { background: #2cb2ff; color: white; }
    tr:hover { background: #f0f8ff; }
    .botao { background: #f99b3b; border: none; padding: 8px 16px; border-radius: 6px; color: white; cursor: pointer; }
    .botao:hover { background: #e58a22; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Relatório de Livros</h1>

    <form class="filtros" method="get">
      <label>Título:<br><input type="text" name="titulo" value="<?= htmlspecialchars($_GET['titulo'] ?? '') ?>"></label>
      <label>Autor:<br><input type="text" name="autor" value="<?= htmlspecialchars($_GET['autor'] ?? '') ?>"></label>
      <label>Ano:<br><input type="number" name="ano" value="<?= htmlspecialchars($_GET['ano'] ?? '') ?>"></label>
      <label>Gênero:<br><input type="text" name="genero" value="<?= htmlspecialchars($_GET['genero'] ?? '') ?>"></label>
      <label>Editora:<br><input type="text" name="editora" value="<?= htmlspecialchars($_GET['editora'] ?? '') ?>"></label>
      <label>ISBN:<br><input type="text" name="isbn" value="<?= htmlspecialchars($_GET['isbn'] ?? '') ?>"></label>
      <label style="align-self: end;"><button class="botao" type="submit">Filtrar</button></label>
    </form>
    <a href="relatorios.php" class="botao-voltar">⬅ Voltar</a>
    <table>
      <thead>
        <tr>
          <th>Título</th>
          <th>Autor</th>
          <th>Editora</th>
          <th>Ano</th>
          <th>Gênero</th>
          <th>ISBN</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($livros)): ?>
          <tr><td colspan="6">Nenhum livro encontrado.</td></tr>
        <?php else: ?>
          <?php foreach ($livros as $livro): ?>
            <tr>
              <td><?= htmlspecialchars($livro['titulo']) ?></td>
              <td><?= htmlspecialchars($livro['autor']) ?></td>
              <td><?= htmlspecialchars($livro['editora']) ?></td>
              <td><?= htmlspecialchars($livro['ano']) ?></td>
              <td><?= htmlspecialchars($livro['genero']) ?></td>
              <td><?= htmlspecialchars($livro['isbn']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
