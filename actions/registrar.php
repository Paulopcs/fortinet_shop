<?php
require_once '../config/conexao.php';
include '../views/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$nome, $email, $senha]);
        header("Location: login.php?cadastrado=1");
        exit;
    } catch (PDOException $e) {
        $erro = "Email já cadastrado!";
    }
}
?>

<link rel="stylesheet" href="../assets/auth.css">

<div class="auth-container">
    <h2>Cadastrar</h2>

    <?php if (!empty($erro)): ?>
        <p style="color:red"><?= $erro ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nome" placeholder="Nome completo" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Criar conta</button>
    </form>

    <p>Já tem conta? <a href="../views/login.php">Entrar</a></p>
</div>
