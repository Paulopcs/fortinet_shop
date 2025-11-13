<?php
require_once '../config/conexao.php';
include 'header.php';

if (!isset($_SESSION['usuario_id'])) {
    die("<p style='color:red'>Você precisa estar logado para ver o histórico.<br><a href='login.php'>Fazer login</a></p>");
}

$id_usuario = $_SESSION['usuario_id'];


$sql = "SELECT h.*, c.nome AS nome_item, c.imagem
        FROM historico h
        INNER JOIN cosmeticos c ON c.id = h.id_cosmetico
        WHERE h.id_usuario = :id_usuario
        ORDER BY h.data DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id_usuario' => $id_usuario]);
$historico = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Histórico de Transações</title>
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
table {
    margin: 0 auto;
    border-collapse: collapse;
    width: 80%;
    background: #1b1b1b;
}
th, td {
    border: 1px solid #333;
    padding: 10px;
}
th {
    background: #222;
}
.compra {
    color: #00FF00;
}
.devolucao {
    color: #FF5555;
}
img {
    width: 60px;
    border-radius: 5px;
}
a {
    color: #FFD700;
    text-decoration: none;
}
</style>
</head>
<body>

<h1>📜 Histórico de Compras e Devoluções</h1>
<p><a href="../index.php">⬅️ Voltar à loja</a> | <a href="meus_cosmeticos.php">🎒 Meus cosméticos</a></p>

<?php if (empty($historico)): ?>
    <p>Você ainda não realizou nenhuma transação.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Imagem</th>
            <th>Item</th>
            <th>Tipo</th>
            <th>Valor</th>
            <th>Data</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($historico as $item): ?>
            <tr>
                <td><img src="<?= htmlspecialchars($item['imagem']) ?>" alt=""></td>
                <td><?= htmlspecialchars($item['nome_item']) ?></td>
                <td class="<?= $item['tipo'] ?>"><?= ucfirst($item['tipo']) ?></td>
                <td><?= number_format($item['valor'], 0, ',', '.') ?> VBucks</td>
                <td><?= date('d/m/Y H:i', strtotime($item['data'])) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>
