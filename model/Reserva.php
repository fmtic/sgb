<?php
class Reserva {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Cadastrar nova reserva
    public function cadastrar($dados) {
        $sql = "INSERT INTO reservas (livro_id, usuario_id, data_reserva, status)
                VALUES (:livro_id, :usuario_id, :data_reserva, :status)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($dados);
    }

    // Atualizar status da reserva (ex: cancelada, concluida)
    public function atualizarStatus($id, $status) {
        $sql = "UPDATE reservas SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }

    // Buscar reserva por ID
    public function buscarPorId($id) {
        $sql = "SELECT * FROM reservas WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listar reservas ativas de um usuário para um livro específico
    public function listarAtivasPorUsuarioLivro($usuarioId, $livroId) {
        $sql = "SELECT * FROM reservas WHERE usuario_id = :usuario_id 
                AND livro_id = :livro_id AND status = 'ativa'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':livro_id' => $livroId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar reservas com filtros (ex: livro_id, status)
    public function listar($filtros = []) {
        $sql = "SELECT * FROM reservas WHERE 1=1";
        $params = [];

        if (!empty($filtros['livro_id'])) {
            $sql .= " AND livro_id = :livro_id";
            $params[':livro_id'] = $filtros['livro_id'];
        }

        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }

        if (!empty($filtros['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filtros['status'];
        }

        $sql .= " ORDER BY data_reserva ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Concluir reserva (quando o livro é emprestado)
    public function concluir($id) {
        $sql = "UPDATE reservas SET status = 'concluida' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Listar reservas disponíveis para notificação
    public function listarDisponiveis() {
        $sql = "SELECT r.*, l.titulo 
                FROM reservas r
                INNER JOIN livros l ON r.livro_id = l.id
                WHERE r.status = 'ativa'
                ORDER BY r.data_reserva ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
