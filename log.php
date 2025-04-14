<?php
function registrar_log($acao, $detalhes = '') {
    $arquivo = __DIR__ . '/logs/sistema.log';
    $data = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP desconhecido';
    $usuario = $_SESSION['usuario_id'] ?? 'Visitante';

    $linha = "[$data] - IP: $ip - Usuário: $usuario - Ação: $acao";

    if (!empty($detalhes)) {
        $linha .= " - Detalhes: $detalhes";
    }

    $linha .= PHP_EOL;

    file_put_contents($arquivo, $linha, FILE_APPEND);
}
