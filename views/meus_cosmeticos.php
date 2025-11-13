<?php
require_once '../config/conexao.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("<p style='color:red'>Você precisa estar logado para acessar esta página.<br><a href='login.php'>Fazer login</a></p>");
}

$id_usuario = $_SESSION['usuario_id'];


$sql = "SELECT c.*, uc.data_compra 
        FROM usuarios_cosmeticos uc
        INNER JOIN cosmeticos c ON c.id = uc.id_cosmetico
        WHERE uc.id_usuario = :id_usuario
        ORDER BY uc.data_compra DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id_usuario' => $id_usuario]);
$cosmeticos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Meus Cosméticos</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #0b0b0b;
    color: white;
    text-align: center;
}
h1 {
    color: #FFD700;
}
.grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}
.item {
    background: #1b1b1b;
    border: 1px solid #333;
    margin: 10px;
    padding: 10px;
    width: 160px;
    border-radius: 10px;
}
img {
    width: 100%;
    border-radius: 8px;
}
button {
    background: #ff5555;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 6px;
}
button:hover {
    background: #ff2222;
}
a {
    color: #FFD700;
    text-decoration: none;
}
</style>
</head>
<body>

<h1>🎒 Meus Cosméticos</h1>
<p><a href="../index.php">⬅️ Voltar para a loja</a></p>

<?php if (empty($cosmeticos)): ?>
    <p>Você ainda não possui nenhum item.</p>
<?php else: ?>
<div class="grid">
    <?php foreach ($cosmeticos as $item): ?>
        <div class="item">
            <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>">
            <h4><?= htmlspecialchars($item['nome']) ?></h4>
            <small><?= ucfirst($item['raridade']) ?> - <?= ucfirst($item['tipo']) ?></small><br>
            <span><?= number_format($item['preco'], 0, ',', '.') ?> VBucks</span><br>
            <form method="POST" action="../actions/devolver.php">
                <input type="hidden" name="id_cosmetico" value="<?= $item['id'] ?>">
                <button type="submit">Devolver</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

</body>
</html>
