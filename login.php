<?php
require_once 'config.php';
require_once 'mensagens.php';

// Verificar se o usuário já está logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Financeiro</title>
    <style>
body {
    margin: 0;
    padding: 0;
    font-family: "Poppins", Arial, sans-serif;
    background: linear-gradient(135deg, #f4e9ff, #e5d4f7);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

h1 {
    color: #5c3d79;
    margin-bottom: 25px;
    font-size: 32px;
    text-align: center;
}

.container-login {
    background: #ffffff;
    padding: 40px;
    width: 380px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(90, 60, 120, 0.20);
    border: 1px solid #d8c6ef;
}

form div {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: bold;
    color: #4e3c62;
    margin-bottom: 6px;
}

input[type="email"],
input[type="password"] {
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #c8b2e3;
    background-color: #f8f2ff;
    font-size: 14px;
    transition: 0.2s ease;
}

input:focus {
    outline: none;
    border-color: #8b5fbf;
    box-shadow: 0 0 8px rgba(139, 95, 191, 0.3);
}

button {
    margin-top: 18px;
    background-color: #8b5fbf;
    color: white;
    border: none;
    padding: 13px;
    width: 100%;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    transition: 0.2s ease;
}

button:hover {
    background-color: #7345a3;
}

.link-registro {
    margin-top: 15px;
    text-align: center;
    font-size: 14px;
}

.link-registro a {
    color: #6f499a;
    font-weight: bold;
    text-decoration: none;
}

.link-registro a:hover {
    text-decoration: underline;
}

.mensagem {
    background: #eadcff;
    border-left: 4px solid #8b5fbf;
    padding: 10px;
    width: 300px;
    margin-bottom: 15px;
    border-radius: 8px;
    text-align: center;
}

    </style>
</head>

<body>
    <h1>Login - Sistema Financeiro</h1>

    <?php exibir_mensagem(); ?>

    <form action="autenticar.php" method="post">
        <div>
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <br>
        <div>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>
        </div>
        <br>
        <div>
            <button type="submit">Entrar</button>
        </div>
    </form>

    <p>Não tem conta? <a href="registro.php">Cadastre-se aqui.</a></p>
</body>

</html>