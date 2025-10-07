<?php
session_start();
require_once '../config/db.php';
require_once '../model/Livro.php';

// Verifica autenticação
if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Permissões
$usuarioTipo = $_SESSION['usuario_tipo'];
$permitido = in_array($usuarioTipo, ['admin','bibliotecario','aluno','publico_externo']);
if(!$permitido) {
    die("Acesso negado.");
}

$livroModel = new Livro($conn);

// Receber filtros
$filtros = [
    'titulo' => $_GET['titulo'] ?? '',
    'autor'  => $_GET['autor'] ?? '',
    'isbn'   => $_GET['isbn'] ?? ''
];

$livros = $livroModel->listar($filtros);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Livros</title>
    <link rel="stylesheet" href="../assets/css/livros.css">
</head>
<body>
<div class="container">
    <h1>Livros Cadastrados</h1>
    <p>Tipo de usuário: <strong><?= htmlspecialchars($usuarioTipo) ?></strong></p>
    <a href="dashboard.php"><button>Voltar ao Dashboard</button></a>
    <a href="../controller/LogoutController.php"><button>Sair</button></a>

    <hr>

    <?php if(in_array($usuarioTipo, ['admin','bibliotecario'])): ?>
    <a href="livro_form.php"><button>Novo Livro</button></a>
    <?php endif; ?>

    <form method="GET" class="filtro-form">
        <label>Filtrar por Título:</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($filtros['titulo']) ?>">

        <label>Filtrar por Autor:</label>
        <input type="text" name="autor" value="<?= htmlspecialchars($filtros['autor']) ?>">

        <label>Filtrar por ISBN:</label>
        <input type="text" name="isbn" value="<?= htmlspecialchars($filtros['isbn']) ?>">

        <button type="submit">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Capa</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Editora</th>
                <th>Ano</th>
                <th>ISBN</th>
                <th>Gênero</th>
                <?php if(in_array($usuarioTipo, ['admin','bibliotecario'])): ?>
                <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($livros)): ?>
            <?php foreach($livros as $livro): ?>
            <tr>
                <td>
                    <?php if($livro['capa']): ?>
                        <img src="../uploads/capas/<?= htmlspecialchars($livro['capa']) ?>" alt="Capa" class="mini-capa">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($livro['titulo']) ?></td>
                <td><?= htmlspecialchars($livro['autor']) ?></td>
                <td><?= htmlspecialchars($livro['editora']) ?></td>
                <td><?= htmlspecialchars($livro['ano']) ?></td>
                <td><?= htmlspecialchars($livro['isbn']) ?></td>
                <td><?= htmlspecialchars($livro['genero']) ?></td>
                <?php if(in_array($usuarioTipo, ['admin','bibliotecario'])): ?>
                <td>
                    <a href="livro_form.php?id=<?= $livro['id'] ?>">Editar</a> |
                    <a href="../controller/LivroController.php?excluir=<?= $livro['id'] ?>" onclick="return confirm('Deseja realmente excluir este livro?')">Excluir</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align:center;">Nenhum livro encontrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
