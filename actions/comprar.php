<?php
require_once '../config/conexao.php';
include '../views/header.php';


if (!isset($_SESSION['usuario_id'])) {
    die("<p style='color:red'>Você precisa estar logado para comprar.<br><a href='../views/login.php'>Fazer login</a></p>");
}


$id_usuario = $_SESSION['usuario_id'];
$id_cosmetico = $_POST['id_cosmetico'] ?? null;

if (!$id_cosmetico) {
    die("<p style='color:red'>ID do item não informado.</p>");
}


$user = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$user->execute([$id_usuario]);
$user = $user->fetch();

$item = $pdo->prepare("SELECT * FROM cosmeticos WHERE id = ?");
$item->execute([$id_cosmetico]);
$item = $item->fetch();

if (!$item) {
    die("<p style='color:red'>Item não encontrado.</p>");
}


$check = $pdo->prepare("SELECT COUNT(*) FROM usuarios_cosmeticos WHERE id_usuario = ? AND id_cosmetico = ?");
$check->execute([$id_usuario, $id_cosmetico]);
if ($check->fetchColumn() > 0) {
    die("<p style='color:orange'>Você já possui este item!<br><a href='../index.php'>Voltar</a></p>");
}


if ($user['creditos'] < $item['preco']) {
    die("<p style='color:red'>Créditos insuficientes!<br><a href='../index.php'>Voltar</a></p>");
}


try {
    $pdo->beginTransaction();

    
    $stmt = $pdo->prepare("INSERT INTO usuarios_cosmeticos (id_usuario, id_cosmetico) VALUES (?, ?)");
    $stmt->execute([$id_usuario, $id_cosmetico]);

    $stmt = $pdo->prepare("INSERT INTO historico (id_usuario, id_cosmetico, tipo, valor) VALUES (?, ?, 'compra', ?)");
    $stmt->execute([$id_usuario, $id_cosmetico, $item['preco']]);


    $stmt = $pdo->prepare("UPDATE usuarios SET creditos = creditos - ? WHERE id = ?");
    $stmt->execute([$item['preco'], $id_usuario]);


    $pdo->commit();

 
    $_SESSION['creditos'] = $user['creditos'] - $item['preco'];

    echo "<h3 style='color:green'>✅ Compra realizada com sucesso!</h3>";
    echo "<p>Você comprou: <b>{$item['nome']}</b> por <b>{$item['preco']}</b> créditos.</p>";
    echo "<p><a href='../index.php'>⬅️ Voltar à loja</a></p>";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "<p style='color:red'>Erro na compra: " . $e->getMessage() . "</p>";
}
