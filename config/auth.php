<?php
session_start();

function verificarLogin() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: ../view/login.php");
        exit;
    }
}

function verificarPermissao($tiposPermitidos = []) {
    verificarLogin();
    $tipoUsuario = $_SESSION['usuario']['tipo'] ?? null;

    if (!in_array($tipoUsuario, $tiposPermitidos)) {
        die("Acesso negado: você não tem permissão para acessar esta página.");
    }
}
?>
