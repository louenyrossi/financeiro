<?php
require_once 'config.php';
require_once 'mensagens.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

// Verificar se está editando
$id_categoria = $_GET['id'] ?? null;
$categoria = null;

if ($id_categoria) {
    // Buscar categoria para editar
    $sql = "SELECT * FROM categoria WHERE id_categoria = :id_categoria AND id_usuario = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_categoria', $id_categoria);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $categoria = $stmt->fetch();

    // Se não encontrou ou não pertence ao usuário, redireciona
    if (!$categoria) {
        set_mensagem('Categoria não encontrada.', 'erro');
        header('Location: categorias_listar.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Sistema Financeiro</title>
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

        .navbar {
            width: 100%;
            padding: 18px 0;
            background: rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(150, 120, 200, 0.25);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: center;
        }

        .navbar ul {
            display: flex;
            gap: 25px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .navbar ul li a {
            text-decoration: none;
            color: #5c3d79;
            font-weight: 600;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid #d2c0ed;
            border-radius: 12px;
            transition: 0.25s ease;
        }

        .navbar ul li a:hover {
            background: #e8d7ff;
            transform: translateY(-2px);
        }

        h1 {
            margin-top: 40px !important;
            color: #5c3d79;
            font-size: 32px;
            text-align: center;
        }

        .user-box {
            margin-top: 10px;
            text-align: center;
        }

        .user-box p {
            color: #4e3c62;
            font-size: 16px;
        }

        .user-box a {
            color: #7345a3;
            font-weight: bold;
            text-decoration: none;
        }

        .user-box a:hover {
            text-decoration: underline;
        }

        .submenu {
            margin-top: 25px;
            display: flex;
            gap: 20px;
        }

        .submenu a {
            text-decoration: none;
            color: #5c3d79;
            font-weight: 600;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid #d2c0ed;
            border-radius: 12px;
            transition: 0.25s ease;
        }

        .submenu a:hover {
            background: #e8d7ff;
            transform: translateY(-2px);
        }

        h2 {
            margin-top: 35px;
            color: #6b4a8e;
            text-align: center;
            font-size: 24px;
        }

        form {
            background: #ffffff;
            width: 450px;
            padding: 35px;
            margin-top: 20px;
            border-radius: 18px;
            border: 1px solid #d8c6ef;
            box-shadow: 0 8px 20px rgba(90, 60, 120, 0.18);
            margin-left: auto;
            margin-right: auto;
        }

        form div {
            display: flex;
            flex-direction: column;
            margin-bottom: 18px;
        }

        label {
            font-weight: bold;
            color: #4e3c62;
            margin-bottom: 5px;
        }

        input,
        select {
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #c8b2e3;
            background-color: #f8f2ff;
            font-size: 14px;
            transition: 0.25s;
        }

        input:hover,
        select:hover {
            background-color: #f3eaff;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #8b5fbf;
            box-shadow: 0 0 10px rgba(139, 95, 191, 0.3);
        }

        button {
            background-color: #8b5fbf;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.25s ease;
        }

        button:hover {
            background-color: #7345a3;
            transform: translateY(-2px);
        }

        form a {
            color: #6b4a8e;
            font-weight: bold;
            margin-top: 8px;
            display: inline-block;
        }

        form a:hover {
            text-decoration: underline;
        }

        div p {
            text-align: center;
            width: 100%;
            display: flex;
            justify-content: center;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container body-formularios">
        <h1>Sistema Financeiro</h1>

        <div>
            <p class="bem-vindo">Bem-vindo, <strong> <?php echo $usuario_nome ?> </strong></p>
        </div>

        <?php exibir_mensagem(); ?>

        <h2><?php echo $categoria ? 'Editar' : 'Nova'; ?> Categoria</h2>

        <form action="categorias_salvar.php" method="POST">
            <?php if ($categoria): ?>
                <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">
            <?php endif; ?>

            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome"
                    value="<?php echo $categoria ? htmlspecialchars($categoria['nome']) : ''; ?>"
                    required>
            </div>

            <div>
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Selecione...</option>
                    <option value="receita" <?php echo ($categoria && $categoria['tipo'] === 'receita') ? 'selected' : ''; ?>>Receita</option>
                    <option value="despesa" <?php echo ($categoria && $categoria['tipo'] === 'despesa') ? 'selected' : ''; ?>>Despesa</option>
                </select>
            </div>

            <div>
                <button type="submit">Salvar</button>
                <a href="categorias_listar.php">Cancelar</a>
            </div>
        </form>
    </div>

</body>

</html>