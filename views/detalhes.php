<?php
require_once '../config/conexao.php';

include 'header.php';

if (!isset($_GET['id'])) {
    echo "<p style='color:red; text-align:center;'>❌ ID do item não informado.</p>";
    exit;
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM cosmeticos WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "<p style='color:red; text-align:center;'>❌ Item não encontrado.</p>";
    exit;
}

$ja_tem = false;
if (isset($_SESSION['usuario_id'])) {
    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM usuarios_cosmeticos WHERE id_usuario = ? AND id_cosmetico = ?");
    $stmt2->execute([$_SESSION['usuario_id'], $id]);
    $ja_tem = $stmt2->fetchColumn() > 0;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Detalhes - <?= htmlspecialchars($item['nome']) ?></title>
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="../assets/modal.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #0b0b0b;
    color: white;
    text-align: center;
}
.container {
    max-width: 600px;
    margin: 30px auto;
    background: #1b1b1b;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 0 15px rgba(0,0,0,0.5);
}
.container img {
    width: 300px;
    border-radius: 10px;
    margin-bottom: 15px;
}
.info {
    text-align: left;
    margin-top: 15px;
}
.info span {
    display: block;
    margin: 4px 0;
}
h2 {
    color: #FFD700;
}
.btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
}
.btn:hover {
    background: #0056b3;
}
.voltar {
    margin-top: 20px;
    display: inline-block;
    color: #ccc;
    text-decoration: none;
}
.voltar:hover {
    color: #fff;
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="container">
    <img src="<?= htmlspecialchars($item['imagem'] ?: 'https://fortnite-api.com/assets/img/placeholder.png') ?>" alt="<?= htmlspecialchars($item['nome']) ?>">

    <h2><?= htmlspecialchars($item['nome']) ?></h2>

    <div class="info">
        <span><b>Raridade:</b> <?= ucfirst($item['raridade']) ?></span>
        <span><b>Tipo:</b> <?= ucfirst($item['tipo']) ?></span>
        <span><b>Preço:</b> <?= number_format($item['preco'], 0, ',', '.') ?> VBucks</span>
        <span><b>Data de inclusão:</b> <?= date('d/m/Y', strtotime($item['data_inclusao'])) ?></span>
       <span><b>Descrição:</b>
    <?= !empty($item['descricao'])
        ? htmlspecialchars($item['descricao'])
        : 'Sem descrição disponível.' ?>
</span>

    </div>

    <?php if (isset($_SESSION['usuario_id'])): ?>
        <?php if ($ja_tem): ?>
            <p style="color:#FFD700; margin-top:10px;">✅ Você já possui este item!</p>
        <?php else: ?>
            <form action="../actions/comprar.php" method="POST">
                <input type="hidden" name="id_cosmetico" value="<?= $item['id'] ?>">
                <button type="submit" class="btn">Comprar</button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p style="color:#aaa;">🔒 Faça login para comprar este item.</p>
    <?php endif; ?>

    <a href="<?= $base_url ?>/index.php" class="voltar">⬅️ Voltar à Loja</a>
</div>

</body>
</html>
