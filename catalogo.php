<?php
require_once 'conexao.php';

try {
    $stmt = $pdo->query("SELECT * FROM livros ORDER BY titulo ASC");
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar livros: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Livros</title>
    <link rel="stylesheet" href="css/estilo_catalogo.css">
</head>
<body>
    <div class="catalogo-container">
        <h1>Catálogo de Livros</h1>
        <?php
        if (!empty($livros)) {
            echo "<div class='livros-container'>";
            foreach ($livros as $livro) {
                echo "<div class='livro-card'>";
                echo "<h3>" . htmlspecialchars($livro['titulo']) . "</h3>";
                echo "<p><strong>Autor:</strong> " . htmlspecialchars($livro['autor']) . "</p>";
                echo "<p><strong>Editora:</strong> " . htmlspecialchars($livro['editora']) . "</p>";
                echo "<p><strong>Ano:</strong> " . htmlspecialchars($livro['ano']) . "</p>";
                echo "<p><strong>ISBN:</strong> " . htmlspecialchars($livro['isbn']) . "</p>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<p>Biblioteca em criação.<br/></p>";
        }
        ?>
    </div>
</body>
</html>
