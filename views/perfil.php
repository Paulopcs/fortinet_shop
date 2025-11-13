<?php
require_once '../config/conexao.php';

include 'header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("<p style='color:red; text-align:center;'>Usuário inválido.</p>");
}


$user = $pdo->prepare("SELECT id, nome, email, creditos FROM usuarios WHERE id = ?");
$user->execute([$id]);
$user = $user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("<p style='color:red; text-align:center;'>Usuário não encontrado.</p>");
}


$sql = "SELECT c.* FROM usuarios_cosmeticos uc
        INNER JOIN cosmeticos c ON c.id = uc.id_cosmetico
        WHERE uc.id_usuario = ?
        ORDER BY c.nome ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$cosmeticos = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total_itens = count($cosmeticos);

$stmt_gasto = $pdo->prepare("
    SELECT SUM(c.preco)
    FROM usuarios_cosmeticos uc
    JOIN cosmeticos c ON c.id = uc.id_cosmetico
    WHERE uc.id_usuario = ?
");
$stmt_gasto->execute([$id]);
$total_gasto = $stmt_gasto->fetchColumn() ?: 0;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Perfil de <?= htmlspecialchars($user['nome']) ?></title>
<link rel="stylesheet" href="../assets/perfil.css">
</head>
<body>

<div class="perfil-container">

    <div class="perfil-card">

        <div class="avatar">
            👤
        </div>

        <h1><?= htmlspecialchars($user['nome']) ?></h1>
        <p class="email"><?= htmlspecialchars($user['email']) ?></p>

        <div class="stats">
            <span>💰 Saldo: <strong><?= number_format($user['creditos'], 0, ',', '.') ?> VBucks</strong></span>
            <span>🎒 Itens adquiridos: <strong><?= $total_itens ?></strong></span>
            <span>🧾 Total gasto: <strong><?= number_format($total_gasto, 0, ',', '.') ?> VBucks</strong></span>
        </div>

        <div class="links">
            <a href="usuarios.php">⬅️ Lista de Jogadores</a>
            <a href="../index.php">🏠 Loja</a>
        </div>

    </div>


    <h2 class="subtitulo">Itens deste jogador</h2>

    <?php if (empty($cosmeticos)): ?>
        <p class="nao-tem">Este jogador ainda não possui cosméticos.</p>
    <?php else: ?>
    <div class="grid">
        <?php foreach ($cosmeticos as $item): ?>
        <div class="item">
            <img src="<?= htmlspecialchars($item['imagem']) ?>">
            <h4><?= htmlspecialchars($item['nome']) ?></h4>
            <span><?= ucfirst($item['raridade']) ?> - <?= ucfirst($item['tipo']) ?></span><br>
            <span class="preco"><?= number_format($item['preco'], 0, ',', '.') ?> VBucks</span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

</body>
</html>
