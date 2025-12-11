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
$id_transacao = $_GET['id'] ?? null;
$transacao = null;

if ($id_transacao) {
    // Buscar transação para editar
    $sql = "SELECT * FROM transacao WHERE id_transacao = :id_transacao AND id_usuario = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_transacao', $id_transacao);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $transacao = $stmt->fetch();

    // Se não encontrou ou não pertence ao usuário, redireciona
    if (!$transacao) {
        set_mensagem('Transação não encontrada.', 'erro');
        header('Location: transacoes_listar.php');
        exit;
    }
}

// Buscar categorias do usuário
$sql_categorias = "SELECT * FROM categoria WHERE id_usuario = :usuario_id ORDER BY tipo, nome";
$stmt_categorias = $conn->prepare($sql_categorias);
$stmt_categorias->bindParam(':usuario_id', $usuario_id);
$stmt_categorias->execute();
$categorias = $stmt_categorias->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $transacao ? 'Editar' : 'Nova'; ?> Transação - Sistema Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>

        :root {
            --roxo-escuro: #4c3373;
            --roxo-medio: #7c5fc4;
            --roxo-claro: #d9ccff;
            --lilas: #f3edff;
            --branco: #ffffff;
            --cinza-texto: #4b4453;
        }

        body {
            margin: 0;
            padding: 20px;
            font-family: "Poppins", sans-serif;
            color: var(--cinza-texto);
            background: linear-gradient(160deg, #f7f3ff, #e9e2ff);
            text-align: center;
        }

        a {
            text-decoration: none !important;
            color: var(--roxo-escuro);
        }

        .navbar {
            width: 100%;
            padding: 18px 40px;
            background: rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(150, 120, 200, 0.25);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-radius: 12px;
        }

        .navbar ul {
            display: flex;
            gap: 15px;
            padding: 0;
            margin: 0;
            list-style: none;
            align-items: center;
        }

    
        .navbar .titulo {
            font-weight: 700;
            font-size: 22px;
            color: var(--roxo-escuro);
            padding: 0;
            background: none;
            border: none;
            pointer-events: none;
        }

        .navbar ul li a {
            display: inline-block;
            padding: 10px 20px;
            font-weight: 600;
            text-decoration: none !important;
            color: #5c3d79 !important;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid #d2c0ed;
            border-radius: 12px;
            transition: 0.25s ease;
        }

        .navbar ul li a:hover {
            background: #e9dafe;
            transform: translateY(-2px);
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar-right .nav-link {
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 10px;
        }

        .navbar-right .nav-link[href="logout.php"] {
            background: var(--roxo-medio);
            color: #fff;
            transition: 0.25s;
        }

        .navbar-right .nav-link[href="logout.php"]:hover {
            background: var(--roxo-escuro);
        }

        h1,
        h2,
        h3 {
            color: var(--roxo-escuro);
            font-weight: 600;
            margin-bottom: 20px;
        }

        a[href*="transacoes_formulario.php"] {
            display: block;
            margin: 0 auto 25px auto;
            background: #ede6ff;
            color: var(--roxo-escuro);
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 10px;
            border: 1px solid #d6c8ff;
            text-align: center;
            transition: 0.25s;
        }

        a[href*="transacoes_formulario.php"]:hover {
            background: var(--roxo-claro);
        }

        input,
        select,
        textarea,
        button,
        input[type="submit"] {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #dcd0ff;
            margin-bottom: 15px;
            background: #faf8ff;
            transition: 0.25s;
            font-size: 14px;
            box-sizing: border-box;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--roxo-medio);
            background: #f7f3ff;
        }

        button,
        input[type="submit"] {
            background: var(--roxo-medio);
            color: #fff;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        button:hover,
        input[type="submit"]:hover {
            background: var(--roxo-escuro);
        }

        table {
            width: 100%;
            max-width: 900px;
            background: var(--branco);
            border-radius: 14px;
            border: 1px solid #e6ddff;
            overflow: hidden;
            margin: 0 auto 20px auto;
        }

        thead {
            background: var(--roxo-claro);
            color: var(--roxo-escuro);
            font-weight: 600;
        }

        th,
        td {
            padding: 12px 14px;
            border-bottom: 1px solid #efe7ff;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background: #faf7ff;
        }

        tbody tr:hover {
            background: #f2eaff;
        }

        td:nth-child(5) {
            font-weight: 600;
            color: var(--roxo-escuro);
        }

        table td a {
            display: inline-block;
            width: 110px;
            text-align: center;
            padding: 7px 0;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: 0.25s;
        }

        a[href*="formulario"] {
            background: #ede6ff;
            border: 1px solid #d6c8ff;
            color: var(--roxo-escuro);
        }

        a[href*="formulario"]:hover {
            background: var(--roxo-claro);
        }

        a[href*="excluir"] {
            background: #ffe3ec;
            border: 1px solid #ffc9da;
            color: #7a3b54;
        }

        a[href*="excluir"]:hover {
            background: #ffd4e3;
        }

        .mensagem {
            background: var(--lilas);
            border-left: 6px solid var(--roxo-medio);
            padding: 12px 16px;
            border-radius: 10px;
            font-weight: 600;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <!-- Lado esquerdo: título e links -->
        <ul>
            <li class="titulo">Financeiro</li>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="categorias_listar.php">Categorias</a></li>
            <li><a href="transacoes_listar.php">Transações</a></li>
        </ul>

        <!-- Lado direito: usuário e logout -->
        <div class="navbar-right">
            <span class="nav-link">🤖 Usuário: <?php echo htmlspecialchars($usuario_nome); ?></span>
            <a class="nav-link" href="logout.php">Sair</a>
        </div>
    </nav>


    <h1>Sistema Financeiro Pessoal</h1>

    <div>
        <p>Bem-vindo, <strong><?php echo htmlspecialchars($usuario_nome); ?></strong></p>
    </div>

    <?php exibir_mensagem(); ?>

    <h2><?php echo $transacao ? 'Editar' : 'Nova'; ?> Transação</h2>

    <?php if (count($categorias) === 0): ?>
        <p><strong>Atenção:</strong> Você precisa cadastrar pelo menos uma categoria antes de criar transações.</p>
        <p><a href="categorias_formulario.php">Cadastrar Categoria</a></p>
    <?php else: ?>
        <form action="transacoes_salvar.php" method="POST">
            <?php if ($transacao): ?>
                <input type="hidden" name="id_transacao" value="<?php echo $transacao['id_transacao']; ?>">
            <?php endif; ?>

            <div>
                <label for="descricao">Descrição:</label>
                <input type="text" id="descricao" name="descricao"
                    value="<?php echo $transacao ? htmlspecialchars($transacao['descricao']) : ''; ?>"
                    required>
            </div>

            <div>
                <label for="valor">Valor:</label>
                <input type="number" id="valor" name="valor" step="0.01" min="0.01"
                    value="<?php echo $transacao ? number_format($transacao['valor'], 2, '.', '') : ''; ?>"
                    required>
            </div>

            <div>
                <label for="data_transacao">Data:</label>
                <input type="date" id="data_transacao" name="data_transacao"
                    value="<?php echo $transacao ? $transacao['data_transacao'] : date('Y-m-d'); ?>"
                    required>
            </div>

            <div>
                <label for="tipo">Tipo:</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Selecione...</option>
                    <option value="receita" <?php echo ($transacao && $transacao['tipo'] === 'receita') ? 'selected' : ''; ?>>Receita</option>
                    <option value="despesa" <?php echo ($transacao && $transacao['tipo'] === 'despesa') ? 'selected' : ''; ?>>Despesa</option>
                </select>
            </div>

            <div>
                <label for="id_categoria">Categoria:</label>
                <select id="id_categoria" name="id_categoria" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id_categoria']; ?>"
                            <?php echo ($transacao && $transacao['id_categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['nome']) . ' (' . ucfirst($categoria['tipo']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button type="submit">Salvar</button>
                <a href="transacoes_listar.php">Cancelar</a>
            </div>
        </form>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>

</html>