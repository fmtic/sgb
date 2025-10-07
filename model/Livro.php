<?php
class Livro {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function listar($filtros = []) {
        $sql = "SELECT * FROM livros WHERE 1=1";
        $params = [];
        if (!empty($filtros['titulo'])) {
            $sql .= " AND titulo ILIKE :titulo";
            $params[':titulo'] = "%{$filtros['titulo']}%";
        }
        if (!empty($filtros['autor'])) {
            $sql .= " AND autor ILIKE :autor";
            $params[':autor'] = "%{$filtros['autor']}%";
        }
        if (!empty($filtros['isbn'])) {
            $sql .= " AND isbn = :isbn";
            $params[':isbn'] = $filtros['isbn'];
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function cadastrar($dados) {
        $sql = "INSERT INTO livros (titulo, autor, editora, ano, isbn, genero, capa)
                VALUES (:titulo, :autor, :editora, :ano, :isbn, :genero, :capa)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($dados);
    }

    public function atualizar($dados) {
        $sql = "UPDATE livros SET titulo=:titulo, autor=:autor, editora=:editora, 
                ano=:ano, isbn=:isbn, genero=:genero, capa=:capa WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($dados);
    }

    public function excluir($id) {
        $stmt = $this->conn->prepare("DELETE FROM livros WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM livros WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
?>
