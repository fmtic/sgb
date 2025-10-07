<?php
require_once '../config/db.php';
require_once '../model/Emprestimo.php';
require_once '../model/Livro.php';
require_once '../model/Usuario.php';

$emprestimoModel = new Emprestimo($conn);
$livroModel = new Livro($conn);
$usuarioModel = new Usuario($conn);

// Listar empréstimos ativos e atrasados
$ativos = $emprestimoModel->listarAtivos();
$atrasados = $emprestimoModel->listarAtrasados();

// Listar livros disponíveis
$livros = $livroModel->listar(['disponivel' => true]);

// Listar leitores
$usuarios = $usuarioModel->listar(['tipo' => ['aluno','externo']]);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Empréstimos</title>
    <link rel="stylesheet" href="../assets/css/emprestimos.css">
</head>
<body>
<div class="container">
    <h1>Empréstimos</h1>

    <h2>Registrar Empréstimo</h2>
    <form method="POST" action="../controller/EmprestimoController.php">
        <label>Livro:</label>
        <select name="livro_id" required>
            <option value="">Selecione um livro</option>
            <?php foreach($livros as $livro): ?>
                <option value="<?= $livro['id'] ?>"><?= htmlspecialchars($livro['titulo']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Leitor:</label>
        <select name="leitor_id" required>
            <option value="">Selecione um leitor</option>
            <?php foreach($usuarios as $usuario): ?>
                <option value="<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['nome'] . ' ' . $usuario['sobrenome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Data Prevista de Devolução:</label>
        <input type="date" name="data_devolucao_prevista" required>

        <button type="submit" name="cadastrar">Registrar Empréstimo</button>
    </form>

    <h2>Empréstimos Ativos</h2>
    <table>
        <thead>
            <tr>
                <th>Livro</th>
                <th>Leitor</th>
                <th>Data Empréstimo</th>
                <th>Data Prevista</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($ativos as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e['titulo']) ?></td>
                    <td><?= htmlspecialchars($e['nome'] . ' ' . $e['sobrenome']) ?></td>
                    <td><?= $e['data_emprestimo'] ?></td>
                    <td><?= $e['data_devolucao_prevista'] ?></td>
                    <td>
                        <form method="POST" action="../controller/EmprestimoController.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $e['id'] ?>">
                            <input type="date" name="data_devolucao_real" value="<?= date('Y-m-d') ?>" required>
                            <button type="submit" name="devolver">Devolver</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Empréstimos Atrasados</h2>
    <table>
        <thead>
            <tr>
                <th>Livro</th>
                <th>Leitor</th>
                <th>Data Prevista</th>
                <th>Dias de Atraso</th>
                <th>Email do Leitor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($atrasados as $a): 
                $diasAtraso = (new DateTime($a['data_devolucao_prevista']))->diff(new DateTime())->days;
            ?>
                <tr style="background-color:#f8d7da;">
                    <td><?= htmlspecialchars($a['titulo']) ?></td>
                    <td><?= htmlspecialchars($a['nome'] . ' ' . $a['sobrenome']) ?></td>
                    <td><?= $a['data_devolucao_prevista'] ?></td>
                    <td><?= $diasAtraso ?></td>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
