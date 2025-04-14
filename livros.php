<?php
require_once 'conexao.php';

// Mensagem de erro caso algum campo obrigatório não seja preenchido
$erro = '';

// Processamento do formulário (inserção)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $camposObrigatorios = ['isbn', 'titulo', 'autor', 'editora', 'ano'];
  $dados = [];
  foreach ($camposObrigatorios as $campo) {
    if (empty($_POST[$campo])) {
      $erro = "⚠️ Por favor, preencha todos os campos obrigatórios.";
      break;
    }
    $dados[$campo] = trim($_POST[$campo]);
  }

  if (!$erro) {
    $stmt = $pdo->prepare("INSERT INTO livros (
      isbn, titulo, autor, editora, ano, edicao, genero, paginas, idioma, tradutor, sinopse,
      capa, localizacao, palavras_chave, codigo_barras, ficha_catalografica
    ) VALUES (
      :isbn, :titulo, :autor, :editora, :ano, :edicao, :genero, :paginas, :idioma, :tradutor, :sinopse,
      :capa, :localizacao, :palavras_chave, :codigo_barras, :ficha_catalografica
    )");

    $stmt->execute([
      ':isbn' => $_POST['isbn'],
      ':titulo' => $_POST['titulo'],
      ':autor' => $_POST['autor'],
      ':editora' => $_POST['editora'],
      ':ano' => is_numeric($_POST['ano']) ? (int)$_POST['ano'] : null,
      ':edicao' => $_POST['edicao'] ?? null,
      ':genero' => $_POST['genero'] ?? null,
      ':paginas' => is_numeric($_POST['paginas']) ? (int)$_POST['paginas'] : null,
      ':idioma' => $_POST['idioma'] ?? null,
      ':tradutor' => $_POST['tradutor'] ?? null,
      ':sinopse' => $_POST['sinopse'] ?? null,
      ':capa' => $_POST['capa'] ?? null,
      ':localizacao' => $_POST['localizacao'] ?? null,
      ':palavras_chave' => $_POST['palavras_chave'] ?? null,
      ':codigo_barras' => $_POST['codigo_barras'] ?? null,
      ':ficha_catalografica' => $_POST['ficha_catalografica'] ?? null
    ]);

    header('Location: relatorio_livros.php');
    exit;
  }
}

// Consulta para exibir livros existentes
$filtro = $_GET['filtro'] ?? '';
$sql = "SELECT * FROM livros WHERE titulo ILIKE :filtro OR autor ILIKE :filtro OR isbn ILIKE :filtro ORDER BY titulo";
$stmt = $pdo->prepare($sql);
$stmt->execute(['filtro' => "%$filtro%"]);
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro e Consulta de Livros</title>
  <link rel="stylesheet" href="css/estilo_livros.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/livros.js"></script>
</head>
<body>
<div class="container">
  <h2>Cadastro de Livros</h2>
  <?php if ($erro): ?>
    <p class="erro-msg"><?= $erro ?></p>
  <?php endif; ?>
  <form method="POST" action="" id="form-livro">
    <p class="legenda-obrigatorio">Campos marcados com <span class="obrigatorio">*</span> são obrigatórios</p>
    <fieldset>
      <legend>Dados Básicos</legend>
      <label>ISBN: <span class="obrigatorio">*</span></label>
      <input type="text" name="isbn" id="isbn" required>
      <button type="button" id="btnBuscarISBN">Buscar por ISBN</button> <span id="isbn-status"></span><br>

      <label>Título: <span class="obrigatorio">*</span></label>
      <input type="text" name="titulo" id="titulo" required>

      <label>Autor(es): <span class="obrigatorio">*</span></label>
      <input type="text" name="autor" id="autor" required>

      <label>Editora: <span class="obrigatorio">*</span></label>
      <input type="text" name="editora" id="editora" required>

      <label>Ano de Publicação: <span class="obrigatorio">*</span></label>
      <input type="number" name="ano" id="ano" required>

      <label>Edição:</label>
      <input type="text" name="edicao" id="edicao">
    </fieldset>

    <fieldset>
      <legend>Dados Complementares</legend>
      <label>Gênero/Assunto:</label>
      <input type="text" name="genero" id="genero">

      <label>Número de Páginas:</label>
      <input type="number" name="paginas" id="paginas">

      <label>Idioma:</label>
      <input type="text" name="idioma" id="idioma">

      <label>Tradutor(es):</label>
      <input type="text" name="tradutor" id="tradutor">

      <label>Sinopse/Resumo:</label>
      <textarea name="sinopse" id="sinopse"></textarea>

      <label>Capa (URL):</label>
      <input type="text" name="capa" id="capa">
      <div id="preview-capa"></div>

      <label>Localização:</label>
      <input type="text" name="localizacao" id="localizacao">

      <label>Palavras-chave:</label>
      <input type="text" name="palavras_chave" id="palavras_chave">

      <label>Código de Barras:</label>
      <input type="text" name="codigo_barras" id="codigo_barras">

      <label>Ficha Catalográfica:</label>
      <textarea name="ficha_catalografica" id="ficha_catalografica"></textarea>
    </fieldset>

    <input type="submit" value="Cadastrar Livro">
    <a href="dashboard.php" class="botao-voltar">Voltar ao Painel</a>
  </form>

  <h2>Consultar Livros</h2>
  <form method="GET">
    <input type="text" name="filtro" placeholder="Buscar por título, autor ou ISBN" value="<?= htmlspecialchars($filtro) ?>">
    <input type="submit" value="Buscar">
  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Autor</th>
        <th>ISBN</th>
        <th>Ano</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($livros as $livro): ?>
        <tr>
          <td><?= $livro['id'] ?></td>
          <td><?= htmlspecialchars($livro['titulo']) ?></td>
          <td><?= htmlspecialchars($livro['autor']) ?></td>
          <td><?= $livro['isbn'] ?></td>
          <td><?= $livro['ano'] ?></td>
          <td>
            <a href="editar_livro.php?id=<?= $livro['id'] ?>">Editar</a> |
            <a href="excluir_livro.php?id=<?= $livro['id'] ?>" onclick="return confirm('Deseja excluir este livro?');">Excluir</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
