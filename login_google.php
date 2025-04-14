<?php
session_start();

// Caminhos para os arquivos da Google API
require_once 'google-api-php-client/src/Google/autoload.php';

// Configurações do Google Cloud
$clientID = '498166475431-2g921brj1lq1e6u89h23hrmlgnet4quo.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/sgb-2/login_google.php'; // ajuste conforme sua URL real

// Configuração do cliente Google
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

$service = new Google_Service_Oauth2($client);

// Passo 1: redireciona para o Google
if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit;
} else {
    // Passo 2: recebe o token de autenticação
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $client->setAccessToken($_SESSION['access_token']);

    // Passo 3: pega dados do usuário
    $userInfo = $service->userinfo->get();

    $email = $userInfo->email;
    $nome = $userInfo->name;

    // Conectar ao banco e verificar/cadastrar usuário
    try {
        $pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            // Usuário novo – cadastrar como leitor
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (:nome, :email, '', 'leitor')");
            $stmt->execute(['nome' => $nome, 'email' => $email]);
            $usuarioId = $pdo->lastInsertId();
        } else {
            $usuarioId = $usuario['id'];
        }

        // Login
        $_SESSION['usuario_id'] = $usuarioId;
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'] ?? 'leitor';
        header("Location: " . ($_SESSION['tipo_usuario'] === 'admin' ? "dashboard.php" : "catalogo.php"));
        exit;

    } catch (PDOException $e) {
        echo "Erro ao conectar ao banco: " . $e->getMessage();
        exit;
    }
}
