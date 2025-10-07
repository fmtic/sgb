<?php
class Emprestimo {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Listar histórico de empréstimos por usuário
    public function listarPorUsuario($usuarioId) {
        $sql = "SELECT e.*, l.titulo 
                FROM emprestimos e
                INNER JOIN livros l ON e.livro_id = l.id
                WHERE e.leitor_id = :usuario_id
                ORDER BY e.data_emprestimo DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar empréstimo
    public function cadastrar($dados) {
        $sql = "INSERT INTO emprestimos (livro_id, leitor_id, data_emprestimo, data_devolucao_prevista, status)
                VALUES (:livro_id, :leitor_id, :data_emprestimo, :data_devolucao_prevista, 'Emprestado')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($dados);
    }

    // Atualizar devolução
    public function devolver($id, $dataDevolucaoReal) {
    // Atualiza o empréstimo como devolvido
    $sql = "UPDATE emprestimos SET data_devolucao_real = :data_devolucao_real, status = 'Devolvido'
            WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $resultado = $stmt->execute([
        ':data_devolucao_real' => $dataDevolucaoReal,
        ':id' => $id
    ]);

    if ($resultado) {
        // Obtem o livro relacionado
        $sqlLivro = "SELECT livro_id FROM emprestimos WHERE id = :id";
        $stmtLivro = $this->conn->prepare($sqlLivro);
        $stmtLivro->execute([':id' => $id]);
        $livro = $stmtLivro->fetch(PDO::FETCH_ASSOC);

        if ($livro) {
            // Processa reservas automáticas
            $this->processarReservas($livro['livro_id']);
        }
    }

    return $resultado;
}

    // Listar Livros
    public function listarAtivos() {
    $sql = "SELECT e.*, l.titulo, u.nome, u.sobrenome
            FROM emprestimos e
            INNER JOIN livros l ON e.livro_id = l.id
            INNER JOIN usuarios u ON e.leitor_id = u.id
            WHERE e.status = 'Emprestado'
            ORDER BY e.data_emprestimo ASC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Listar empréstimos atrasados
public function listarAtrasados() {
    $sql = "SELECT e.*, l.titulo, u.nome, u.sobrenome, u.email
            FROM emprestimos e
            INNER JOIN livros l ON e.livro_id = l.id
            INNER JOIN usuarios u ON e.leitor_id = u.id
            WHERE e.status = 'Emprestado' AND e.data_devolucao_prevista < CURRENT_DATE
            ORDER BY e.data_devolucao_prevista ASC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Dentro da classe Emprestimo
public function processarReservas($livroId) {
    require_once 'Reserva.php';
    $reservaModel = new Reserva($this->conn);

    // Busca a primeira reserva ativa para este livro
    $reservas = $reservaModel->listar([
        'livro_id' => $livroId,
        'status' => 'ativa'
    ]);

    if (!empty($reservas)) {
        $reserva = $reservas[0];
        // Concluir a reserva (indica que já pode ser emprestado)
        $reservaModel->concluir($reserva['id']);
    }
}

// Esse método retorna empréstimos que vencem em até 2 dias, permitindo avisos antecipados
public function listarAlertas() {
    $sql = "SELECT e.*, l.titulo, u.nome, u.sobrenome
            FROM emprestimos e
            INNER JOIN livros l ON e.livro_id = l.id
            INNER JOIN usuarios u ON e.leitor_id = u.id
            WHERE e.status = 'Emprestado' 
              AND e.data_devolucao_prevista <= CURRENT_DATE + INTERVAL '2 days'
            ORDER BY e.data_devolucao_prevista ASC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Filtrar empréstimos por livro ou usuário
public function filtrar($filtros) {
    $sql = "SELECT e.*, l.titulo, u.nome, u.sobrenome
            FROM emprestimos e
            INNER JOIN livros l ON e.livro_id = l.id
            INNER JOIN usuarios u ON e.leitor_id = u.id
            WHERE 1=1";

    $params = [];

    if (!empty($filtros['livro'])) {
        $sql .= " AND l.titulo ILIKE :livro";
        $params[':livro'] = "%".$filtros['livro']."%";
    }

    if (!empty($filtros['usuario'])) {
        $sql .= " AND (u.nome ILIKE :usuario OR u.sobrenome ILIKE :usuario)";
        $params[':usuario'] = "%".$filtros['usuario']."%";
    }

    if (!empty($filtros['status'])) {
        $sql .= " AND e.status = :status";
        $params[':status'] = $filtros['status'];
    }

    if (!empty($filtros['data_inicio'])) {
        $sql .= " AND e.data_emprestimo >= :data_inicio";
        $params[':data_inicio'] = $filtros['data_inicio'];
    }

    if (!empty($filtros['data_fim'])) {
        $sql .= " AND e.data_emprestimo <= :data_fim";
        $params[':data_fim'] = $filtros['data_fim'];
    }

    $sql .= " ORDER BY e.data_emprestimo DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}
