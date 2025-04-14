<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Excluir leitor
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $pdo->prepare("DELETE FROM leitores WHERE id = ?")->execute([$id]);
    header("Location: leitores.php");
    exit;
}

// Função para calcular idade
function calcularIdade($data_nascimento) {
    $nascimento = new DateTime($data_nascimento);
    $hoje = new DateTime();
    return $nascimento->diff($hoje)->y;
}

// Função para verificar campos obrigatórios pendentes
function camposPendentes($leitor) {
    return empty($leitor['telefone']) || empty($leitor['foto']) || empty($leitor['endereco']) || empty($leitor['data_nascimento']);
}

// Filtros
$nomeFiltro = $_GET['nome'] ?? '';
$cidadeFiltro = $_GET['cidade'] ?? '';
$idadeMin = $_GET['idade_min'] ?? '';

// Paginação
$porPagina = 10;
$paginaAtual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $porPagina;

// Monta a query com filtros
$where = [];
$params = [];

if (!empty($nomeFiltro)) {
    $where[] = 'nome ILIKE :nome';
    $params[':nome'] = '%' . $nomeFiltro . '%';
}

if (!empty($cidadeFiltro)) {
    $where[] = 'cidade ILIKE :cidade';
    $params[':cidade'] = '%' . $cidadeFiltro . '%';
}

if (!empty($idadeMin)) {
    $dataLimite = (new DateTime())->modify("-$idadeMin years")->format('Y-m-d');
    $where[] = 'data_nascimento <= :dataLimite';
    $params[':dataLimite'] = $dataLimite;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Total de registros para paginação
$total = $pdo->prepare("SELECT COUNT(*) FROM leitores $whereSQL");
$total->execute($params);
$totalRegistros = $total->fetchColumn();
$totalPaginas = ceil($totalRegistros / $porPagina);

// Busca os leitores com limite/paginação
$sql = "SELECT * FROM leitores $whereSQL ORDER BY nome LIMIT :limite OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $chave => $valor) {
    $stmt->bindValue($chave, $valor);
}
$stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$leitores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Leitores</title>
  <style>
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background: #f2f2f2; }
    .btn { padding: 6px 10px; text-decoration: none; background: #3498db; color: white; border-radius: 4px; }
    .btn:hover { background: #2980b9; }
    .danger { background: #e74c3c; }
    .alerta { background-color: #f8d7da; color: #721c24; font-weight: bold; }
    .filtro-box { margin: 15px 0; padding: 10px; background: #eef; border-radius: 6px; }
  </style>
</head>
<body>
  <h2>Leitores</h2>

  <div class="filtro-box">
    <form method="GET">
      <label>Nome: <input type="text" name="nome" value="<?= htmlspecialchars($nomeFiltro) ?>"></label>
      <label>Cidade: <input type="text" name="cidade" value="<?= htmlspecialchars($cidadeFiltro) ?>"></label>
      <label>Idade mínima: <input type="number" name="idade_min" min="0" value="<?= htmlspecialchars($idadeMin) ?>"></label>
      <button type="submit" class="btn">🔍 Filtrar</button>
      <a href="leitores.php" class="btn danger">❌ Limpar</a>
    </form>
  </div>

  <a href="leitor_form.php" class="btn">➕ Novo Leitor</a>
  <br><br>

  <table>
    <tr>
    <th>Foto</th>  
    <th>Nome</th>
      <th>Idade</th>
      <th>Email</th>
      <th>Telefone</th>
      <th>Ações</th>
    </tr>

    <?php foreach ($leitores as $leitor): ?>
      <tr class="<?= camposPendentes($leitor) ? 'alerta' : '' ?>">
  <td>
    <?php if (!empty($leitor['foto'])): ?>
      <img src="exibir_foto.php?id=<?= $leitor['id'] ?>&thumb=1" 
           alt="Foto" 
           onclick="mostrarFoto(<?= $leitor['id'] ?>)"
           style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; cursor: pointer; border: 2px solid #3498db;">
    <?php else: ?>
      <span style="color: #aaa;">Sem foto</span>
    <?php endif; ?>
  </td>
  <td><?= htmlspecialchars($leitor['nome']) ?></td>
  <td><?= calcularIdade($leitor['data_nascimento']) ?></td>
  <td><?= htmlspecialchars($leitor['email']) ?></td>
  <td><?= htmlspecialchars($leitor['telefone']) ?></td>
  <td>
    <a class="btn" href="leitor_form.php?id=<?= $leitor['id'] ?>">✏️ Editar</a>
    <a class="btn danger" href="leitores.php?excluir=<?= $leitor['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este leitor?')">🗑️ Excluir</a>
  </td>
</tr>
    <?php endforeach; ?>
  </table>

  <!-- Paginação -->
  <div style="margin-top: 20px;">
    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
      <a class="btn <?= $i == $paginaAtual ? 'danger' : '' ?>" href="?pagina=<?= $i ?>&nome=<?= urlencode($nomeFiltro) ?>&cidade=<?= urlencode($cidadeFiltro) ?>&idade_min=<?= $idadeMin ?>">
        <?= $i ?>
      </a>
    <?php endfor; ?>
  </div>

  <br><br>
  <a href="dashboard.php">⬅ Voltar ao Painel</a>

  <!-- Modal da Foto -->
  <div id="modalFoto" style="
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.85);
      justify-content: center;
      align-items: center;
      z-index: 1000;
  ">
    <div style="position: relative;">
      <img id="imgFoto" src="" style="
        max-width: 90vw;
        max-height: 90vh;
        border: 6px solid #FFA500;
        border-radius: 12px;
        box-shadow: 0 0 20px #3498db;
      ">
      <button onclick="fecharModal()" style="
        position: absolute;
        top: -20px;
        right: -20px;
        background: #3498db;
        color: white;
        border: none;
        padding: 6px 10px;
        border-radius: 50%;
        font-size: 18px;
        cursor: pointer;
        box-shadow: 0 0 10px black;
      ">×</button>
    </div>
  </div>

  <script>
    function mostrarFoto(id) {
      const modal = document.getElementById('modalFoto');
      const img = document.getElementById('imgFoto');
      img.src = 'exibir_foto.php?id=' + id + '&cache=' + new Date().getTime(); // força atualização
      modal.style.display = 'flex';
    }

    function fecharModal() {
      document.getElementById('modalFoto').style.display = 'none';
    }
  </script>
</body>
</html>
