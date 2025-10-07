<?php
require_once '../config/db.php';
require_once '../model/Livro.php';
require_once '../model/Usuario.php';
require_once '../auth/auth.php';

verificarLogin();

// Só bibliotecário e admin podem registrar empréstimos
if(!in_array($_SESSION['usuario_tipo'], ['admin','bibliotecario'])) {
    die("Acesso negado.");
}

$livroModel = new Livro($conn);
$usuarioModel = new Usuario($conn);

// Buscar livros disponíveis (não emprestados)
$livros = $livroModel->listarDisponiveis(); // você pode criar esse método no model
// Buscar usuários do tipo aluno ou público externo
$usuarios = $usuarioModel->listarPorTipo(['aluno','publico_externo']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar Empréstimo</title>
</head>
<body>
<h1>Registrar Empréstimo</h1>

<form method="POST" action="../controller/EmprestimoController.php">
    <label>Livro:</label>
    <select name="livro_id" required>
        <option value="">Selecione um livro</option>
        <?php foreach($livros as $livro): ?>
            <option value="<?= $livro['id'] ?>"><?= htmlspecialchars($livro['titulo']) ?> - <?= htmlspecialchars($livro['autor']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Usuário:</label>
    <select name="usuario_id" required>
        <option value="">Selecione um usuário</option>
        <?php foreach($usuarios as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nome'].' '.$u['sobrenome']) ?> (<?= $u['tipo'] ?>)</option>
        <?php endforeach; ?>
    </select>

    <label>Dias de empréstimo:</label>
    <input type="number" name="dias_emprestimo" value="7" min="1" max="30" required>

    <button type="submit" name="registrar">Registrar Empréstimo</button>
</form>

<a href="emprestimos.php">Voltar para lista de empréstimos</a>
</body>
</html>
