<?php
session_start();
require_once '../config/google_config.php'; // Defina GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI
require_once '../model/Usuario.php';
require_once '../config/db.php';

$usuarioModel = new Usuario($conn);

require_once '../vendor/autoload.php'; // Google API Client

$client = new Google\Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope("email");
$client->addScope("profile");

if (!isset($_GET['code'])) {
    // Redireciona para login Google
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit;
} else {
    // Recebe o código do Google
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $oauth = new Google\Service\Oauth2($client);
        $googleUser = $oauth->userinfo->get();

        $email = $googleUser->email;
        $nome = $googleUser->givenName;
        $sobrenome = $googleUser->familyName;
        $foto = $googleUser->picture;

        // Verifica se usuário já existe
        $usuario = $usuarioModel->listarPorEmail($email);

        if (!$usuario) {
            // Cria novo usuário do tipo "aluno" por padrão
            $dados = [
                ':nome' => $nome,
                ':sobrenome' => $sobrenome,
                ':email' => $email,
                ':telefone' => null,
                ':whatsapp' => null,
                ':foto' => null, // opcional: baixar imagem do Google e salvar local
                ':tipo' => 'aluno',
                ':senha' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT) // senha aleatória
            ];
            $usuarioModel->cadastrar($dados);
            $usuario = $usuarioModel->listarPorEmail($email);
        }

        // Cria sessão
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'tipo' => $usuario['tipo']
        ];

        // Redireciona por tipo
        switch($usuario['tipo']) {
            case 'admin':
            case 'bibliotecario':
                header("Location: dashboard.php");
                break;
            case 'aluno':
            case 'publico_externo':
                header("Location: livros_disponiveis.php");
                break;
        }
        exit;
    } else {
        die("Erro no login Google: " . htmlspecialchars($token['error']));
    }
}
?>
