<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/conexao.php'; 
?>

<style>
.menu {
    background: #111;
    padding: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: Arial, sans-serif;
    color: white;
}
.menu a {
    color: #FFD700;
    text-decoration: none;
    margin: 0 10px;
    font-weight: bold;
}
.menu a:hover {
    text-decoration: underline;
}
.menu .right a {
    color: #fff;
    background: #007bff;
    padding: 6px 10px;
    border-radius: 6px;
}
.menu .right a.logout {
    background: #ff3333;
}
</style>
<head>
<meta charset="UTF-8">
<title>Fortnite Shop</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="https://fortnite-api.com/assets/img/logo_128.png" type="image/png">
<link rel="icon" href="https://fortnite-api.com/assets/img/logo_128.png" type="image/png">
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="../assets/modal.css">
</head>
<div class="menu">
    <div class="left">
        <a href="<?= $base_url ?>/index.php">🏠 Loja</a>
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="<?= $base_url ?>/views/meus_cosmeticos.php">🎒 Meus Itens</a>
            <a href="<?= $base_url ?>/views/historico.php">📜 Histórico</a>
        <?php endif; ?>
        <a href="<?= $base_url ?>/views/usuarios.php">👥 Jogadores</a>
    </div>

    <div class="right">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <span>
                Olá, <b><?= htmlspecialchars($_SESSION['nome']) ?></b> |
                💰 <?= number_format($_SESSION['creditos'], 0, ',', '.') ?> VBucks
            </span>
            <a class="logout" href="<?= $base_url ?>/actions/logout.php">Sair</a>
        <?php else: ?>
            <a href="<?= $base_url ?>/views/login.php">Entrar</a>
            <a href="<?= $base_url ?>/actions/registrar.php">Cadastrar</a>
        <?php endif; ?>
    </div>
</div>
