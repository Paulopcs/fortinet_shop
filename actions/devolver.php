<?php
require_once '../config/conexao.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("<p style='color:red'>Você precisa estar logado.<br><a href='login.php'>Entrar</a></p>");
}

$id_usuario = $_SESSION['usuario_id'];
$id_cosmetico = $_POST['id_cosmetico'] ?? null;

if (!$id_cosmetico) {
    die("<p style='color:red'>Item inválido.</p>");
}


$item = $pdo->prepare("SELECT * FROM cosmeticos WHERE id = ?");
$item->execute([$id_cosmetico]);
$item = $item->fetch();

if (!$item) {
    die("<p style='color:red'>Item não encontrado.</p>");
}


$possui = $pdo->prepare("SELECT COUNT(*) FROM usuarios_cosmeticos WHERE id_usuario = ? AND id_cosmetico = ?");
$possui->execute([$id_usuario, $id_cosmetico]);
if ($possui->fetchColumn() == 0) {
    die("<p style='color:orange'>Você não possui este item.</p>");
}

try {
    $pdo->beginTransaction();

  
    $stmt = $pdo->prepare("DELETE FROM usuarios_cosmeticos WHERE id_usuario = ? AND id_cosmetico = ?");
    $stmt->execute([$id_usuario, $id_cosmetico]);

  
    $stmt = $pdo->prepare("INSERT INTO historico (id_usuario, id_cosmetico, tipo, valor)
                           VALUES (?, ?, 'devolucao', ?)");
    $stmt->execute([$id_usuario, $id_cosmetico, $item['preco']]);

  
    $stmt = $pdo->prepare("UPDATE usuarios SET creditos = creditos + ? WHERE id = ?");
    $stmt->execute([$item['preco'], $id_usuario]);

    $pdo->commit();

 
    $_SESSION['creditos'] += $item['preco'];

    echo "<h3 style='color:green'>✅ Item devolvido com sucesso!</h3>";
    echo "<p>Você recebeu <b>{$item['preco']}</b> créditos de volta.</p>";
    echo "<p><a href='../views/meus_cosmeticos.php'>⬅️ Voltar para meus cosméticos</a></p>";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "<p style='color:red'>Erro na devolução: " . $e->getMessage() . "</p>";
}
