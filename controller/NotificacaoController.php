<?php
session_start();

require '../vendor/autoload.php'; // PHPMailer via Composer
require_once '../config/db.php';
require_once '../model/Emprestimo.php';
require_once '../model/Reserva.php';
require_once '../model/Usuario.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Instanciando models
$emprestimoModel = new Emprestimo($conn);
$reservaModel = new Reserva($conn);
$usuarioModel = new Usuario($conn);

/**
 * Função para enviar e-mail usando PHPMailer
 */
function enviarEmail($destinatario, $assunto, $mensagem) {
    $mail = new PHPMailer(true);
    try {
        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.seudominio.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'seuemail@seudominio.com';
        $mail->Password   = 'suasenha';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Remetente e destinatário
        $mail->setFrom('biblioteca@seudominio.com', 'Biblioteca');
        $mail->addAddress($destinatario);

        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $mensagem;

        $mail->send();
        return true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * 1. Notificar empréstimos atrasados
 */
$atrasados = $emprestimoModel->listarAtrasados();
foreach ($atrasados as $emprestimo) {
    $usuarioEmail = $emprestimo['email'];
    $assunto = "Empréstimo Atrasado: {$emprestimo['titulo']}";
    $mensagem = "
        <p>Olá {$emprestimo['nome']} {$emprestimo['sobrenome']},</p>
        <p>O livro <strong>{$emprestimo['titulo']}</strong> que você emprestou está atrasado.</p>
        <p>Data de devolução prevista: {$emprestimo['data_devolucao_prevista']}</p>
        <p>Por favor, devolva o quanto antes para evitar restrições.</p>
    ";
    enviarEmail($usuarioEmail, $assunto, $mensagem);
}

/**
 * 2. Notificar reservas disponíveis
 */
$reservasDisponiveis = $reservaModel->listarDisponiveis();
foreach ($reservasDisponiveis as $reserva) {
    $usuario = $usuarioModel->buscarPorId($reserva['usuario_id']);
    $usuarioEmail = $usuario['email'];
    $assunto = "Reserva Disponível: {$reserva['titulo']}";
    $mensagem = "
        <p>Olá {$usuario['nome']} {$usuario['sobrenome']},</p>
        <p>O livro <strong>{$reserva['titulo']}</strong> que você reservou está disponível para retirada.</p>
        <p>Data da reserva: {$reserva['data_reserva']}</p>
    ";
    enviarEmail($usuarioEmail, $assunto, $mensagem);
}
