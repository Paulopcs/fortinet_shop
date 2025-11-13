<?php
require_once 'config/conexao.php';

include 'views/header.php';

//consulta que irá atualizar creditos em tempo real
if (isset($_SESSION['usuario_id'])) {
    $stmt = $pdo->prepare("SELECT creditos FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $_SESSION['creditos'] = $stmt->fetchColumn();
}

$itens_por_pagina = 40;
$pagina = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($pagina < 1) $pagina = 1;

$offset = ($pagina - 1) * $itens_por_pagina;


$where = [];
$params = [];

if (!empty($_GET['nome'])) {
    $where[] = "nome LIKE :nome";
    $params[':nome'] = '%' . $_GET['nome'] . '%';
}

if (!empty($_GET['tipo'])) {
    $where[] = "tipo = :tipo";
    $params[':tipo'] = $_GET['tipo'];
}

if (!empty($_GET['raridade'])) {
    $where[] = "raridade = :raridade";
    $params[':raridade'] = $_GET['raridade'];
}

if (isset($_GET['novos'])) {
    $where[] = "is_novo = 1";
}

if (isset($_GET['venda'])) {
    $where[] = "is_venda = 1";
}

$sql_total = "SELECT COUNT(*) FROM cosmeticos";
if ($where) {
    $sql_total .= " WHERE " . implode(" AND ", $where);
}
$stmt_total = $pdo->prepare($sql_total);
$stmt_total->execute($params);
$total_registros = $stmt_total->fetchColumn();

$total_paginas = ceil($total_registros / $itens_por_pagina);


$sql = "SELECT * FROM cosmeticos";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC LIMIT :offset, :limit";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);

$stmt->execute();
$cosmeticos = $stmt->fetchAll(PDO::FETCH_ASSOC);


$meus_ids = [];
if (isset($_SESSION['usuario_id'])) {
    $stmt2 = $pdo->prepare("SELECT id_cosmetico FROM usuarios_cosmeticos WHERE id_usuario = ?");
    $stmt2->execute([$_SESSION['usuario_id']]);
    $meus_ids = $stmt2->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Fortnite Shop</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/modal.css">
</head>
<body>

<h1>🛍️ Fortnite Shop</h1>


<form method="GET" class="filtro">

    <input type="text" name="nome" placeholder="Buscar por nome" 
           value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>">

    <select name="tipo">
        <option value="">Tipo</option>
        <option value="outfit" <?= (@$_GET['tipo']=='outfit')?'selected':'' ?>>Outfit</option>
        <option value="backpack" <?= (@$_GET['tipo']=='backpack')?'selected':'' ?>>Backpack</option>
        <option value="pickaxe" <?= (@$_GET['tipo']=='pickaxe')?'selected':'' ?>>Pickaxe</option>
        <option value="glider" <?= (@$_GET['tipo']=='glider')?'selected':'' ?>>Glider</option>
        <option value="wrap" <?= (@$_GET['tipo']=='wrap')?'selected':'' ?>>Wrap</option>
    </select>

    <select name="raridade">
        <option value="">Raridade</option>
        <option value="common" <?= (@$_GET['raridade']=='common')?'selected':'' ?>>Comum</option>
        <option value="uncommon" <?= (@$_GET['raridade']=='uncommon')?'selected':'' ?>>Incomum</option>
        <option value="rare" <?= (@$_GET['raridade']=='rare')?'selected':'' ?>>Rara</option>
        <option value="epic" <?= (@$_GET['raridade']=='epic')?'selected':'' ?>>Épica</option>
        <option value="legendary" <?= (@$_GET['raridade']=='legendary')?'selected':'' ?>>Lendária</option>
    </select>

    <label><input type="checkbox" name="novos" <?= isset($_GET['novos'])?'checked':'' ?>> 🆕 Novos</label>
    <label><input type="checkbox" name="venda" <?= isset($_GET['venda'])?'checked':'' ?>> 🛒 À venda</label>

    <button type="submit">Filtrar</button>
    <a href="index.php" class="btn-limpar">Limpar</a>
</form>


<div class="grid">
<?php foreach ($cosmeticos as $item): ?>
    <?php $ja_tem = in_array($item['id'], $meus_ids); ?>

    <div class="item">

        <div class="badges">
            <?php if ($item['is_novo']): ?><span class="badge novo">🆕</span><?php endif; ?>
            <?php if ($item['is_venda']): ?><span class="badge venda">🛒</span><?php endif; ?>
            <?php if ($ja_tem): ?><span class="badge possui">✅</span><?php endif; ?>
        </div>

        <a href="views/detalhes.php?id=<?= $item['id'] ?>">
            <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>">
        </a>

        <h4>
            <a href="views/detalhes.php?id=<?= $item['id'] ?>" style="color:#FFD700; text-decoration:none;">
                <?= htmlspecialchars($item['nome']) ?>
            </a>
        </h4>

        <small><?= ucfirst($item['raridade']) ?> - <?= ucfirst($item['tipo']) ?></small><br>
        <span class="preco"><?= number_format($item['preco'], 0, ',', '.') ?> VBucks</span><br>

        <?php if (isset($_SESSION['usuario_id']) && !$ja_tem): ?>
        <form action="actions/comprar.php" method="POST" 
              onsubmit="return confirmarCompra('<?= htmlspecialchars($item['nome']) ?>', this, event)">
            <input type="hidden" name="id_cosmetico" value="<?= $item['id'] ?>">
            <button type="submit" class="btn">Comprar</button>
        </form>
        <?php elseif ($ja_tem): ?>
            <small style="color:#FFD700">Você já possui</small>
        <?php endif; ?>

    </div>

<?php endforeach; ?>
</div>



<div class="pagination">

    <?php if ($pagina > 1): ?>
        <a class="pag-btn" href="?<?= http_build_query(array_merge($_GET, ['page'=>$pagina-1])) ?>">Anterior</a>
    <?php else: ?>
        <span class="pag-btn disabled">Anterior</span>
    <?php endif; ?>


    <?php
    $range = 2;

    for ($i = 1; $i <= $total_paginas; $i++) {

        if ($i == 1 && $pagina > ($range + 2)) {
            echo '<a class="pag-btn" href="?'. http_build_query(array_merge($_GET, ['page'=>1])) .'">1</a>';
            echo '<span class="ellipsis">...</span>';
            continue;
        }

        if ($i == $total_paginas && $pagina < ($total_paginas - ($range + 1))) {
            echo '<span class="ellipsis">...</span>';
            echo '<a class="pag-btn" href="?'. http_build_query(array_merge($_GET, ['page'=>$total_paginas])) .'">'.$total_paginas.'</a>';
            continue;
        }

        if ($i >= ($pagina - $range) && $i <= ($pagina + $range)) {
            if ($i == $pagina) {
                echo '<span class="pag-btn active">'.$i.'</span>';
            } else {
                echo '<a class="pag-btn" href="?'. http_build_query(array_merge($_GET, ['page'=>$i])) .'">'.$i.'</a>';
            }
        }
    }
    ?>

    <!-- PRÓXIMO -->
    <?php if ($pagina < $total_paginas): ?>
        <a class="pag-btn" href="?<?= http_build_query(array_merge($_GET, ['page'=>$pagina+1])) ?>">Próximo</a>
    <?php else: ?>
        <span class="pag-btn disabled">Próximo</span>
    <?php endif; ?>

</div>

<div id="modalCompra" class="modal">
  <div class="modal-content">
    <h3>Confirmar compra</h3>
    <p id="textoItem">Você deseja adquirir este item?</p>
    <div class="modal-buttons">
      <button id="confirmarBtn">Sim, comprar</button>
      <button id="cancelarBtn">Cancelar</button>
    </div>
  </div>
</div>

<script>
let formTemp = null;

function confirmarCompra(nomeItem, form, event) {
  event.preventDefault();
  formTemp = form;

  const modal = document.getElementById("modalCompra");
  const texto = document.getElementById("textoItem");
  texto.textContent = `Você deseja adquirir o item "${nomeItem}"?`;

  modal.style.display = "flex";
}

document.addEventListener("DOMContentLoaded", function() {
  const confirmarBtn = document.getElementById("confirmarBtn");
  const cancelarBtn = document.getElementById("cancelarBtn");
  const modal = document.getElementById("modalCompra");

  confirmarBtn.addEventListener("click", () => {
    modal.style.display = "none";
    if (formTemp) formTemp.submit();
  });

  cancelarBtn.addEventListener("click", () => {
    modal.style.display = "none";
    formTemp = null;
  });
});
</script>

</body>
</html>
