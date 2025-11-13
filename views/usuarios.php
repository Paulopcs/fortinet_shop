<?php
require_once '../config/conexao.php';

include 'header.php';


$stmt = $pdo->query("SELECT id, nome, email, creditos FROM usuarios ORDER BY nome ASC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);


function getUserStats($pdo, $id_usuario)
{
   
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios_cosmeticos WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $total_itens = $stmt->fetchColumn() ?: 0;

  
    $stmt2 = $pdo->prepare("
        SELECT SUM(c.preco) 
        FROM usuarios_cosmeticos uc
        JOIN cosmeticos c ON uc.id_cosmetico = c.id
        WHERE uc.id_usuario = ?
    ");
    $stmt2->execute([$id_usuario]);
    $total_gasto = $stmt2->fetchColumn() ?: 0;

    return [
        'total_itens' => $total_itens,
        'total_gasto' => $total_gasto
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Jogadores</title>
<link rel="stylesheet" href="../assets/usuarios.css">
</head>
<body>

<h1>👥 Jogadores</h1>

<div class="usuarios-grid">

<?php foreach ($usuarios as $user): ?>

    <?php $stats = getUserStats($pdo, $user['id']); ?>

    <div class="card-user">

        <div class="avatar">👤</div>

        <h3><?= htmlspecialchars($user['nome']) ?></h3>
        <p class="email"><?= htmlspecialchars($user['email']) ?></p>

        <div class="info">
            <p>🎒 <b><?= $stats['total_itens'] ?></b> itens</p>
            <p>💰 <b><?= number_format($user['creditos'], 0, ',', '.') ?></b> VBucks</p>
            <p>🧾 Total gasto: 
                <b><?= number_format($stats['total_gasto'], 0, ',', '.') ?> VBucks</b>
            </p>
        </div>

        <a href="perfil.php?id=<?= $user['id'] ?>" class="btn-perfil">
            Ver Perfil
        </a>

    </div>

<?php endforeach; ?>

</div>

</body>
</html>
