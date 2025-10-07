<?php
require_once '../config/db.php';
require_once '../model/Usuario.php';
require_once '../auth/auth.php';

session_start();

if(!isset($_POST['id_token'])) {
    die("Token não recebido.");
}

$id_token = $_POST['id_token'];
$usuarioModel = new Usuario($conn);

// Validar o token via Google
$googleClientId = 'SEU_CLIENT_ID_AQUI';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/tokeninfo?id_token=".$id_token);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if(!isset($data['aud']) || $data['aud'] !== $googleClientId) {
    die("Token inválido.");
}

// Token válido, extrair dados
$googleId = $data['sub'];
$nome = $data['given_name'] ?? '';
$sobrenome = $data['family_name'] ?? '';
$email = $data['email'] ?? '';
$foto = $data['picture'] ?? '';

// Verificar se usuário já existe
$usuario = $usuarioModel->buscarPorEmail($email);

if(!$usuario) {
    // Criar usuário novo com tipo padrão "publico_externo"
    $senhaAleatoria = bin2hex(random_bytes(8));
    $dados = [
        ':nome' => $nome,
        ':sobrenome' => $sobrenome,
        ':email' => $email,
        ':senha' => password_hash($senhaAleatoria, PASSWORD_DEFAULT),
        ':tipo' => 'publico_externo',
        ':telefone' => null,
        ':whatsapp' => null,
        ':foto' => $foto
    ];
    $usuario_id = $usuarioModel->cadastrar($dados);
} else {
    $usuario_id = $usuario['id'];
}

// Autenticar sessão
$_SESSION['usuario_id'] = $usuario_id;
$_SESSION['usuario_tipo'] = $usuario['tipo'] ?? 'publico_externo';
$_SESSION['usuario_nome'] = $nome;

// Redirecionar para dashboard ou página inicial
header("Location: ../view/dashboard.php");
exit;
?>
