<?php
require_once '../config/db.php';
require_once '../model/Emprestimo.php';

$emprestimoModel = new Emprestimo($conn);

if (isset($_POST['enviarEmail'])) {
    $id = (int)$_POST['emprestimo_id'];

    // Buscar dados do empréstimo
    $stmt = $conn->prepare("SELECT e.*, u.email, u.nome, u.sobrenome, l.titulo
                            FROM emprestimos e
                            INNER JOIN usuarios u ON e.leitor_id = u.id
                            INNER JOIN livros l ON e.livro_id = l.id
                            WHERE e.id = :id");
    $stmt->execute([':id' => $id]);
    $emprestimo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($emprestimo) {
        $to = $emprestimo['email'];
        $subject = "Alerta de Atraso: Livro " . $emprestimo['titulo'];
        $message = "Olá " . $emprestimo['nome'] . ",\n\n" .
                   "O livro '" . $emprestimo['titulo'] . "' está atrasado desde " . $emprestimo['data_devolucao_prevista'] . ".\n" .
                   "Por favor, realize a devolução o quanto antes.\n\nAtenciosamente,\nBiblioteca";

        // Envio de email (funcao mail do PHP)
        if (mail($to, $subject, $message)) {
            echo "Email enviado para " . $to;
        } else {
            echo "Erro ao enviar email para " . $to;
        }
    }

    header("Location: ../view/alertas.php");
    exit;
}
