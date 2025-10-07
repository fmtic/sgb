<?php
// controller/autenticar.php
session_start();
require_once __DIR__ . '/../config/db.php'; // espera $conn PDO

// Recebe dados
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// validação básica
if ($email === '' || $senha === '') {
    $_SESSION['mensagem'] = 'Preencha e-mail e senha.';
    header('Location: ../view/login.php');
    exit;
}

try {
    $sql = "SELECT id, nome, sobrenome, email, senha, tipo_usuario
            FROM usuarios
            WHERE email = :email
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // usuário encontrado?
    if (!$user) {
        // não vaze informação sensível — mensagem genérica
        $_SESSION['mensagem'] = 'E-mail ou senha incorretos.';
        header('Location: ../view/login.php');
        exit;
    }

    // verifica hash
    if (!password_verify($senha, $user['senha'])) {
        $_SESSION['mensagem'] = 'E-mail ou senha incorretos.';
        header('Location: ../view/login.php');
        exit;
    }

    // sucesso: criar sessão mínima e segura
    // aconselhável regenerar id da sessão
    session_regenerate_id(true);

    $_SESSION['usuario'] = [
        'id' => (int)$user['id'],
        'nome' => $user['nome'],
        'sobrenome' => $user['sobrenome'],
        'email' => $user['email'],
        'tipo_usuario' => $user['tipo_usuario']
    ];

    // redireciona por tipo
    switch ($user['tipo_usuario']) {
        case 'admin':
            header('Location: ../view/admin_dashboard.php');
            break;
        case 'bibliotecario':
            header('Location: ../view/bibliotecario_dashboard.php');
            break;
        case 'aluno':
        case 'publico_externo':
            header('Location: ../view/usuario_dashboard.php');
            break;
        default:
            // fallback: dashboard genérico
            header('Location: ../view/dashboard.php');
            break;
    }
    exit;

} catch (PDOException $e) {
    // logue esse erro no servidor; não exiba detalhes pro usuário
    error_log('Erro autenticar: ' . $e->getMessage());
    $_SESSION['mensagem'] = 'Erro interno. Contate o administrador.';
    header('Location: ../view/login.php');
    exit;
}
