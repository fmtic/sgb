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
    $campos = [
        'titulo', 'autor', 'editora', 'ano', 'isbn', 'edicao', 'genero', 'paginas', 'idioma',
        'tradutor', 'sinopse', 'capa', 'localizacao', 'palavras_chave', 'codigo_barras', 'ficha_catalografica'
    ];

    $dados = [];
    foreach ($campos as $campo) {
        $dados[$campo] = $_POST[$campo] ?? '';
    }
    $dados['id'] = $id;

    $sql = "UPDATE livros SET 
        titulo=:titulo, autor=:autor, editora=:editora, ano=:ano, isbn=:isbn, edicao=:edicao,
        genero=:genero, paginas=:paginas, idioma=:idioma, tradutor=:tradutor, sinopse=:sinopse,
        capa=:capa, localizacao=:localizacao, palavras_chave=:palavras_chave,
        codigo_barras=:codigo_barras, ficha_catalografica=:ficha_catalografica
        WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($dados);
    header("Location: livros.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Livro</title>
  <link rel="stylesheet" href="css/estilo_editar_excluir.css">
</head>
<body>
  <div class="container">
    <h2>Editar Livro</h2>
    <form method="POST">
      <?php foreach ($livro as $campo => $valor): ?>
        <?php if ($campo === 'id') continue; ?>
        <label><?= ucfirst(str_replace('_', ' ', $campo)) ?>:</label>
        <?php if (in_array($campo, ['sinopse', 'ficha_catalografica'])): ?>
          <textarea name="<?= $campo ?>"><?= htmlspecialchars($valor) ?></textarea>
        <?php else: ?>
          <input type="text" name="<?= $campo ?>" value="<?= htmlspecialchars($valor) ?>">
        <?php endif; ?>
      <?php endforeach; ?>
      <input type="submit" value="Salvar Alterações">
      <a href="livros.php" class="botao-voltar">Cancelar</a>
    </form>
  </div>
</body>
</html>
