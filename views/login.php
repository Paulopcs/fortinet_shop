<?php
session_start(); 
require_once '../config/conexao.php';

$erro = '';

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
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login - Fortnite Shop</title>
<link rel="icon" href="https://fortnite-api.com/assets/img/logo_128.png" type="image/png">
<link rel="stylesheet" href="../assets/auth.css">
</head>
<body>

<div class="container">
    <img src="https://fortnite-api.com/assets/img/logo_128.png" alt="Fortnite Logo" width="80">
    <h2>🔐 Acessar Conta</h2>

    <?php if ($erro): ?>
        <p class="mensagem erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <button type="submit">Entrar</button>
    </form>

    <p>Não tem conta? <a href="registrar.php">Cadastre-se aqui</a></p>
    <p><a href="../views/index.php">⬅️ Voltar à loja</a></p>
</div>

</body>
</html>
