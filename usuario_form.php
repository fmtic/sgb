<?php
require_once 'conexao.php';

$modoEdicao = isset($_GET['id']);
$usuario = [
    'nome' => '',
    'email' => '',
    'tipo_usuario' => 'leitor'
];

if ($modoEdicao) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $_GET['id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $tipo_usuario = $_POST['tipo_usuario'] ?? 'leitor';

    if ($modoEdicao) {
        if (!empty($senha)) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, tipo_usuario = :tipo WHERE id = :id");
            $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senhaHash, 'tipo' => $tipo_usuario, 'id' => $_GET['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, tipo_usuario = :tipo WHERE id = :id");
            $stmt->execute(['nome' => $nome, 'email' => $email, 'tipo' => $tipo_usuario, 'id' => $_GET['id']]);
        }

        // Se o tipo for leitor, atualiza também na tabela leitores
        if ($tipo_usuario === 'leitor') {
            // Verifica se já existe um leitor vinculado a esse usuário
            $stmtCheck = $pdo->prepare("SELECT id FROM leitores WHERE usuario_id = :usuario_id");
            $stmtCheck->execute(['usuario_id' => $_GET['id']]);
            $leitorExistente = $stmtCheck->fetchColumn();

            if ($leitorExistente) {
                // Atualiza os dados do leitor
                $stmtUpdateLeitor = $pdo->prepare("UPDATE leitores SET nome = :nome, email = :email WHERE usuario_id = :usuario_id");
                $stmtUpdateLeitor->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'usuario_id' => $_GET['id']
                ]);
            } else {
                // Caso não exista, cria um novo leitor
                $stmtCreateLeitor = $pdo->prepare("INSERT INTO leitores (nome, email, usuario_id) VALUES (:nome, :email, :usuario_id)");
                $stmtCreateLeitor->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'usuario_id' => $_GET['id']
                ]);
            }
        }
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, :tipo)");
        $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senhaHash, 'tipo' => $tipo_usuario]);

        $usuario_id = $pdo->lastInsertId(); // Pega o id do usuário recém-criado

        // Se o tipo for leitor, cria um novo leitor
        if ($tipo_usuario === 'leitor') {
            $stmtCreateLeitor = $pdo->prepare("INSERT INTO leitores (nome, email, usuario_id) VALUES (:nome, :email, :usuario_id)");
            $stmtCreateLeitor->execute([
                'nome' => $nome,
                'email' => $email,
                'usuario_id' => $usuario_id
            ]);
        }
    }

    header('Location: usuarios.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicao ? 'Editar Usuário' : 'Novo Usuário' ?></title>
    <link rel="stylesheet" href="css/estilo_forms.css">
    <style>
        .senha-container {
            position: relative;
        }

        .toggle-senha {
            position: absolute;
            right: 10px;
            top: 8px;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2><?= $modoEdicao ? 'Editar Usuário' : 'Novo Usuário' ?></h2>
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
            <div id="feedback-nome"></div>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            <div id="feedback-email"></div>

            <label for="senha">Senha:</label>
            <div class="senha-container">
                <input type="password" name="senha" id="senha" <?= $modoEdicao ? '' : 'required' ?>>
                <button type="button" class="toggle-senha" onclick="toggleSenha()">👁️</button>
            </div>
            <div id="feedback-senha"></div>

            <label for="tipo_usuario">Tipo de Usuário:</label>
            <select name="tipo_usuario">
                <option value="admin" <?= $usuario['tipo_usuario'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="leitor" <?= $usuario['tipo_usuario'] === 'leitor' ? 'selected' : '' ?>>Leitor</option>
            </select>
            <br><br>
            <input type="submit" value="<?= $modoEdicao ? 'Atualizar' : 'Cadastrar' ?>">
            <a href="usuarios.php" class="botao-voltar">Voltar</a>
        </form>
    </div>

    <script>
    function toggleSenha() {
        const input = document.getElementById("senha");
        input.type = input.type === "password" ? "text" : "password";
    }

    document.addEventListener("DOMContentLoaded", function () {
        const senhaInput = document.querySelector('input[name="senha"]');
        const nomeInput = document.querySelector('input[name="nome"]');
        const emailInput = document.querySelector('input[name="email"]');
        const form = document.querySelector("form");

        const feedbackSenha = document.getElementById("feedback-senha");
        const feedbackEmail = document.getElementById("feedback-email");
        const feedbackNome = document.getElementById("feedback-nome");

        function validarSenha(senha) {
            const erros = [];
            if (senha.length < 8) erros.push("mínimo de 8 caracteres");
            if (!/[a-z]/.test(senha)) erros.push("uma letra minúscula");
            if (!/[A-Z]/.test(senha)) erros.push("uma letra maiúscula");
            if (!/[0-9]/.test(senha)) erros.push("um número");
            if (!/[\W_]/.test(senha)) erros.push("um caractere especial");
            return erros;
        }

        function validarEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        senhaInput.addEventListener("input", function () {
            const senha = senhaInput.value;
            const erros = validarSenha(senha);

            if (!senha) {
                feedbackSenha.textContent = "";
                return;
            }

            if (erros.length === 0) {
                feedbackSenha.innerHTML = "✔️ Senha forte";
                feedbackSenha.style.color = "green";
            } else {
                feedbackSenha.innerHTML = "⚠️ A senha precisa de: " + erros.join(", ");
                feedbackSenha.style.color = "red";
            }
        });

        emailInput.addEventListener("input", function () {
            if (!emailInput.value) {
                feedbackEmail.textContent = "";
                return;
            }

            if (validarEmail(emailInput.value)) {
                feedbackEmail.innerHTML = "✔️ E-mail válido";
                feedbackEmail.style.color = "green";
            } else {
                feedbackEmail.innerHTML = "❌ E-mail inválido";
                feedbackEmail.style.color = "red";
            }
        });

        nomeInput.addEventListener("input", function () {
            if (!nomeInput.value.trim()) {
                feedbackNome.innerHTML = "❌ Nome é obrigatório";
                feedbackNome.style.color = "red";
            } else {
                feedbackNome.innerHTML = "✔️ Nome preenchido";
                feedbackNome.style.color = "green";
            }
        });

        form.addEventListener("submit", function (e) {
            const senha = senhaInput.value;
            const errosSenha = validarSenha(senha);
            const emailValido = validarEmail(emailInput.value);
            const nomeValido = nomeInput.value.trim().length > 0;

            if ((senha || senhaInput.required) && errosSenha.length > 0) {
                e.preventDefault();
                alert("Senha fraca:\n- " + errosSenha.join("\n- "));
                return;
            }

            if (!emailValido) {
                e.preventDefault();
                alert("E-mail inválido.");
                return;
            }

            if (!nomeValido) {
                e.preventDefault();
                alert("O campo nome é obrigatório.");
                return;
            }
        });
    });
    </script>
</body>
</html>
