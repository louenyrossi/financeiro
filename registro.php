<?php
require_once 'config.php';
require_once 'mensagens.php';

// Se já estiver logado, redireciona para o index
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
    <title>Registro - Sistema Financeiro</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Poppins", Arial, sans-serif;
            background: linear-gradient(135deg, #f4e9ff, #e5d4f7);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #5c3d79;
            margin-top: 40px;
            font-size: 32px;
            text-align: center;
        }

        h2 {
            color: #6b4a8e;
            margin-bottom: 20px;
            margin-top: -5px;
            font-size: 22px;
            text-align: center;
        }

        .container-registro {
            background: #ffffff;
            padding: 35px;
            width: 400px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(90, 60, 120, 0.20);
            border: 1px solid #d8c6ef;
            margin-top: 10px;
        }

        .container-registro form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .container-registro form div {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            color: #4e3c62;
            margin-bottom: 5px;
        }

        input {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #c8b2e3;
            background-color: #f8f2ff;
            font-size: 14px;
            transition: 0.2s ease;
            width: 100%;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: #8b5fbf;
            box-shadow: 0 0 8px rgba(139, 95, 191, 0.3);
        }

        button {
            background-color: #8b5fbf;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: 0.2s;
        }

        button:hover {
            background-color: #7345a3;
        }

        .link-login {
            margin-top: 15px;
            text-align: center;
            font-size: 15px;
        }

        .link-login a {
            color: #6f499a;
            font-weight: bold;
            text-decoration: none;
        }

        .link-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <h1>Sistema Financeiro Pessoal</h1>
    <h2>Cadastro de Usuário</h2>

    <?php exibir_mensagem(); ?>

    <form action="registrar.php" method="POST">
        <div>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
        </div>

        <div>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required minlength="6">
        </div>

        <div>
            <label for="confirmar_senha">Confirmar Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6">
        </div>

        <div>
            <button type="submit">Cadastrar</button>
        </div>
    </form>

    <p>Já tem conta? <a href="login.php">Faça login aqui</a></p>
</body>

</html>