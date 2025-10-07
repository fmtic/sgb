<?php
class Usuario {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Buscar usuário por ID
    public function buscarPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar usuário por email
    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Listar todos os usuários (opcional, pode ser filtrado depois)
    public function listar($filtros = []) {
        $sql = "SELECT * FROM usuarios WHERE 1=1";
        $params = [];

        if (!empty($filtros['nome'])) {
            $sql .= " AND nome ILIKE :nome";
            $params[':nome'] = "%{$filtros['nome']}%";
        }
        if (!empty($filtros['email'])) {
            $sql .= " AND email ILIKE :email";
            $params[':email'] = "%{$filtros['email']}%";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cadastrar usuário
    public function cadastrar($dados) {
        $sql = "INSERT INTO usuarios (nome, sobrenome, telefone, whatsapp, email, senha, foto, tipo)
                VALUES (:nome, :sobrenome, :telefone, :whatsapp, :email, :senha, :foto, :tipo)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($dados);
    }

    // Atualizar usuário
    public function atualizar($dados) {
        $sql = "UPDATE usuarios SET 
                    nome = :nome,
                    sobrenome = :sobrenome,
                    telefone = :telefone,
                    whatsapp = :whatsapp,
                    email = :email,
                    senha = :senha,
                    foto = :foto,
                    tipo = :tipo
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($dados);
    }

    // Excluir usuário
    public function excluir($id) {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
?>
