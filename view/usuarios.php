<?php
require_once '../config/db.php';
require_once '../model/Usuario.php';

$usuarioModel = new Usuario($conn);

// Receber filtros
$filtros = [
    'nome' => $_GET['nome'] ?? '',
    'email' => $_GET['email'] ?? '',
    'tipo' => $_GET['tipo'] ?? ''
];

$usuarios = $usuarioModel->listar($filtros);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuários</title>
    <link rel="stylesheet" href="../assets/css/usuarios.css">
</head>
<body>
<div class="container">
    <h1>Usuários Cadastrados</h1>

    <form method="GET" class="filtro-form">
        <label>Filtrar por Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($filtros['nome']) ?>">

        <label>Filtrar por E-mail:</label>
        <input type="text" name="email" value="<?= htmlspecialchars($filtros['email']) ?>">

        <label>Filtrar por Tipo:</label>
        <select name="tipo">
            <option value="">Todos</option>
            <option value="admin" <?= $filtros['tipo']=='admin'?'selected':'' ?>>Admin</option>
            <option value="bibliotecario" <?= $filtros['tipo']=='bibliotecario'?'selected':'' ?>>Bibliotecário</option>
            <option value="aluno" <?= $filtros['tipo']=='aluno'?'selected':'' ?>>Aluno</option>
            <option value="publico_externo" <?= $filtros['tipo']=='publico_externo'?'selected':'' ?>>Público Externo</option>
        </select>

        <button type="submit">Filtrar</button>
        <a href="usuario_form.php"><button type="button">Novo Usuário</button></a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nome</th>
                <th>Sobrenome</th>
                <th>Telefone</th>
                <th>WhatsApp</th>
                <th>E-mail</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if(!empty($usuarios)): ?>
            <?php foreach($usuarios as $usuario): ?>
            <tr>
                <td>
                    <?php if($usuario['foto']): ?>
                        <img src="../uploads/fotos/<?= htmlspecialchars($usuario['foto']) ?>" alt="Foto" class="mini-foto">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                <td><?= htmlspecialchars($usuario['sobrenome']) ?></td>
                <td><?= htmlspecialchars($usuario['telefone']) ?></td>
                <td><?= htmlspecialchars($usuario['whatsapp']) ?></td>
                <td><?= htmlspecialchars($usuario['email']) ?></td>
                <td><?= htmlspecialchars($usuario['tipo']) ?></td>
                <td>
                    <a href="usuario_form.php?id=<?= $usuario['id'] ?>">Editar</a> |
                    <a href="../controller/UsuarioController.php?excluir=<?= $usuario['id'] ?>" onclick="return confirm('Deseja realmente excluir este usuário?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="8" style="text-align:center;">Nenhum usuário encontrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
