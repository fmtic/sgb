<?php
session_start();

// Caminho base (ajuste se necessário)
define('BASE_PATH', __DIR__);

// Autoload simples (modelos e controladores)
spl_autoload_register(function ($classe) {
    $paths = [
        BASE_PATH . '/model/' . $classe . '.php',
        BASE_PATH . '/controller/' . $classe . '.php',
    ];
    foreach ($paths as $arquivo) {
        if (file_exists($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});

// Conexão com o banco
require_once BASE_PATH . '/config/db.php';

// Se o usuário não estiver logado, redireciona para login
if (!isset($_SESSION['usuario_id']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header("Location: view/login.php");
    exit;
}

// Página padrão
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';

// Roteamento simples
switch ($pagina) {
    case 'home':
        require_once 'view/home.php';
        break;
    case 'livros':
        require_once 'view/livros.php';
        break;
    case 'emprestimos':
        require_once 'view/emprestimos.php';
        break;
    case 'devolucoes':
        require_once 'view/devolucoes.php';
        break;
    case 'reservas':
        require_once 'view/reservas.php';
        break;
    case 'leitores':
        require_once 'view/leitores.php';
        break;
    case 'usuarios':
        require_once 'view/usuarios.php';
        break;
    case 'logout':
        session_destroy();
        header("Location: view/login.php");
        break;
    default:
        require_once 'view/404.php';
        break;
}

?>
