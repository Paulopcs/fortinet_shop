<?php
require_once '../config/conexao.php';


$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if ($nome == '' || $email == '' || $senha == '') {
        $erro = "Preencha todos os campos!";
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        try {
            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => $senhaHash]);
            $sucesso = "✅ Usuário cadastrado com sucesso! <a href='login.php'>Fazer login</a>";
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $erro = "Este email já está cadastrado.";
            } else {
                $erro = "Erro: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro - Fortnite Shop</title>
<link rel="icon" href="https://fortnite-api.com/assets/img/logo_128.png" type="image/png">
<link rel="stylesheet" href="../assets/auth.css">
</head>
<body>

<div class="container">
    <img src="https://fortnite-api.com/assets/img/logo_128.png" alt="Fortnite Logo" width="80">
    <h2>📝 Criar Conta</h2>

    <?php if (!empty($erro)): ?>
        <p class="mensagem erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <?php if (!empty($sucesso)): ?>
        <p class="mensagem sucesso"><?= $sucesso ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nome" placeholder="Nome completo" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <button type="submit">Cadastrar</button>
    </form>

    <p>Já tem conta? <a href="../views/login.php">Entrar</a></p>
    <p><a href="../index.php">⬅️ Voltar à loja</a></p>
</div>

</body>
</html>
