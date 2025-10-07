<?php
require_once '../config/db.php';
require_once '../model/Emprestimo.php';

$emprestimoModel = new Emprestimo($conn);

// Registrar empréstimo
if (isset($_POST['cadastrar'])) {
    $dados = [
        ':livro_id' => (int)$_POST['livro_id'],
        ':leitor_id' => (int)$_POST['leitor_id'],
        ':data_emprestimo' => date('Y-m-d'),
        ':data_devolucao_prevista' => $_POST['data_devolucao_prevista']
    ];

    if ($emprestimoModel->cadastrar($dados)) {
        header("Location: ../view/emprestimos.php");
        exit;
    } else {
        die("Erro ao registrar empréstimo.");
    }
}

// Registrar devolução
if (isset($_POST['devolver'])) {
    $id = (int)$_POST['id'];
    $dataDevolucaoReal = $_POST['data_devolucao_real'];

    if ($emprestimoModel->devolver($id, $dataDevolucaoReal)) {
        header("Location: ../view/emprestimos.php");
        exit;
    } else {
        die("Erro ao registrar devolução.");
    }
}
?>
