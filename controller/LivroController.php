<?php
require_once '../config/db.php';
require_once '../model/Livro.php';

$livroModel = new Livro($conn);

// Exclusão de livro
if (isset($_GET['excluir'])) {
    $livroModel->excluir((int)$_GET['excluir']);
    header("Location: ../view/livros.php");
    exit;
}

// Cadastro/edição de livro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    // Upload da capa
    $capaNome = null;
    if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION);
        $tiposPermitidos = ['jpg','jpeg','png','gif'];
        if (!in_array(strtolower($ext), $tiposPermitidos)) {
            die("Erro: formato de arquivo não permitido.");
        }
        $capaNome = uniqid('capa_') . '.' . $ext;
        move_uploaded_file($_FILES['capa']['tmp_name'], "../uploads/capas/$capaNome");
    } elseif (!empty($_POST['capa_antiga'])) {
        $capaNome = $_POST['capa_antiga'];
    }

    // Dados do livro
    $dados = [
        ':titulo' => trim($_POST['titulo']),
        ':autor'  => trim($_POST['autor']),
        ':editora'=> trim($_POST['editora'] ?? null),
        ':ano'    => !empty($_POST['ano']) ? (int)$_POST['ano'] : null,
        ':isbn'   => trim($_POST['isbn']),
        ':genero' => trim($_POST['genero'] ?? null),
        ':capa'   => $capaNome
    ];

    if ($id) {
        // Verifica duplicidade de ISBN em outro registro
        $livrosExistentes = $livroModel->listar(['isbn'=>$dados[':isbn']]);
        if (!empty($livrosExistentes) && $livrosExistentes[0]['id'] != $id) {
            die("Erro: ISBN já cadas
