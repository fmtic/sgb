<?php
session_start();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    try {
        $pdo = new PDO("pgsql:host=localhost;dbname=SGB", "postgres", "24455535");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
            header("Location: " . ($usuario['tipo_usuario'] === 'admin' ? "dashboard.php" : "catalogo.php"));
            require_once 'log.php';
            registrar_log('Login realizado com sucesso', "Email: $email");
            exit;
        } else {
            $message = "Email ou senha incorretos.";
            registrar_log('Tentativa de login falhou', "Email: $email");
        }
    } catch (PDOException $e) {
        $message = "Erro de conexão: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - SGB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/estilo_login.css">
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <!-- FontAwesome CDN (ícones) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="login-container">
    <img src="Imagens/logo.png" alt="Logo da empresa" class="logo">
    <h2>Login SGB</h2>

    <?php if (!empty($message)): ?>
      <p class="mensagem-erro"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="Email" required>
      </div>

      <div class="input-group senha-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="senha" id="senha" placeholder="Senha" required>
        <span class="toggle-senha" onclick="toggleSenha()">
          <i class="fas fa-eye" id="icone-olho"></i>
        </span>
      </div>
      <input type="submit" value="Entrar">
    </form>
    <div id="g_id_onload"
     data-client_id="498166475431-2g921brj1lq1e6u89h23hrmlgnet4quo.apps.googleusercontent.com
"
     data-context="signin"
     data-callback="handleCredentialResponse"
     data-auto_prompt="false">
</div>
<div class="g_id_signin"
     data-type="standard"
     data-shape="rectangular"
     data-theme="outline"
     data-text="signin_with"
     data-size="large"
     data-logo_alignment="left">
</div>
  </div>
  <script>
  function toggleSenha() {
    const campoSenha = document.getElementById("senha");
    const icone = document.getElementById("icone-olho");
    if (campoSenha.type === "password") {
      campoSenha.type = "text";
      icone.classList.remove("fa-eye");
      icone.classList.add("fa-eye-slash");
    } else {
      campoSenha.type = "password";
      icone.classList.remove("fa-eye-slash");
      icone.classList.add("fa-eye");
    }
  }
  </script>
  <script>
function handleCredentialResponse(response) {
  fetch('login_google.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ credential: response.credential })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      window.location.href = data.redirect;
    } else {
      alert("Erro no login com Google: " + data.error);
    }
  });
}
</script>
</body>
</html>
