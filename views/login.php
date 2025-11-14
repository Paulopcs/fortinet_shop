<?php
require_once '../config/conexao.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['creditos'] = $user['creditos'];

        header("Location: ../index.php");
        exit;
    } else {
        $erro = "Email ou senha incorretos!";
    }
}
?>

<link rel="stylesheet" href="../assets/auth.css">

<div class="auth-container">
    <h2>Entrar</h2>

    <?php if (!empty($erro)): ?>
        <p style="color:red"><?= $erro ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>

    <p>Não tem conta? <a href="../actions/registrar.php">Cadastrar-se</a></p>
</div>
