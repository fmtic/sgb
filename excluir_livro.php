<?php
require_once 'conexao.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID do livro não fornecido.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM livros WHERE id = :id");
$stmt->execute(['id' => $id]);
$livro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$livro) {
    echo "Livro não encontrado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'sim') {
        $stmt = $pdo->prepare("DELETE FROM livros WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: livros.php");
        exit;
    } else {
        header("Location: livros.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Excluir Livro</title>
  <link rel="stylesheet" href="css/estilo_editar_excluir.css">
</head>
<body>
  <div class="container">
    <h2>Confirmar Exclusão</h2>
    <p>Tem certeza que deseja excluir o livro <strong><?= htmlspecialchars($livro['titulo']) ?></strong>?</p>
    <form method="POST">
      <input type="hidden" name="id" value="<?= $id ?>">
      <button type="submit" name="confirmar" value="sim">Sim, excluir</button>
      <a href="livros.php" class="botao-voltar">Cancelar</a>
    </form>
  </div>
</body>
</html>
