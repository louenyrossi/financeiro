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

// Filtros
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_categoria = $_GET['categoria'] ?? '';

// Buscar todas as transações do usuário
$sql = "SELECT t.*, c.nome as categoria_nome 
        FROM transacao t 
        LEFT JOIN categoria c ON t.id_categoria = c.id_categoria 
        WHERE t.id_usuario = :usuario_id";

$params = [':usuario_id' => $usuario_id];

// Aplicar filtros
if ($filtro_tipo && in_array($filtro_tipo, ['receita', 'despesa'])) {
    $sql .= " AND t.tipo = :tipo";
    $params[':tipo'] = $filtro_tipo;
}

if ($filtro_categoria) {
    $sql .= " AND t.id_categoria = :categoria";
    $params[':categoria'] = $filtro_categoria;
}

$sql .= " ORDER BY t.data_transacao DESC, t.id_transacao DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$transacoes = $stmt->fetchAll();

// Buscar categorias para o filtro
$sql_categorias = "SELECT * FROM categoria WHERE id_usuario = :usuario_id ORDER BY nome";
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
    <title>Transações - Sistema Financeiro</title>
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
            gap: 25px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .navbar ul li a {
            color: #5c3d79 !important;
            font-weight: 600;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid #d2c0ed;
            border-radius: 12px;
            text-decoration: none !important;
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
            background: #7c5fc4;
            color: #fff;
            transition: 0.25s;
        }

        .navbar-right .nav-link[href="logout.php"]:hover {
            background: #4c3373;
        }

        .titulo {
            color: #4c3373;
        }

        h1,
        h2,
        h3 {
            color: var(--roxo-escuro);
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        div,
        form,
        table {
            margin: 0 auto 20px auto;
            max-width: 900px;
        }

        a[href*="transacoes_formulario.php"] {
            display: inline-block;
            background: #ede6ff;
            color: var(--roxo-escuro);
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 10px;
            border: 1px solid #d6c8ff;
            margin-bottom: 15px;
            transition: 0.25s;
            text-align: center;
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
            margin-top: 20px;
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

        .bnt-bv {
            text-align: center;
        }

        .new-transaction-container {
            text-align: center;
            margin-bottom: 25px;
        }

        .new-transaction-container a {
            display: inline-block;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <ul>
            <a class="titulo" class="navbar-brand" href="#">Financeiro</a>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="categorias_listar.php">Categorias</a></li>
            <li><a href="transacoes_listar.php">Transações</a></li>
        </ul>
        <div class="navbar-right">
            <span class="nav-link">🤖 Usuário: <?php echo htmlspecialchars($usuario_nome); ?></span>
            <a class="nav-link" href="logout.php">Sair</a>
        </div>
    </nav>

    <h1>Sistema Financeiro Pessoal</h1>

    <div class="bnt-bv">
        <p>Bem-vindo, <strong><?php echo htmlspecialchars($usuario_nome); ?></strong></p>
    </div>

    <?php exibir_mensagem(); ?>

    <h2>Transações</h2>

    <div class="new-transaction-container">
        <a href="transacoes_formulario.php">Nova Transação</a>
    </div>

    <h3>Filtros</h3>
    <form method="GET" action="transacoes_listar.php">
        <div>
            <label for="tipo">Tipo:</label>
            <select id="tipo" name="tipo">
                <option value="">Todos</option>
                <option value="receita" <?php echo $filtro_tipo === 'receita' ? 'selected' : ''; ?>>Receita</option>
                <option value="despesa" <?php echo $filtro_tipo === 'despesa' ? 'selected' : ''; ?>>Despesa</option>
            </select>
        </div>

        <div>
            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria['id_categoria']; ?>"
                        <?php echo $filtro_categoria == $categoria['id_categoria'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit">Filtrar</button>
            <a href="transacoes_listar.php">Limpar Filtros</a>
        </div>
    </form>

    <?php if (count($transacoes) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transacoes as $transacao): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($transacao['data_transacao'])); ?></td>
                        <td><?php echo htmlspecialchars($transacao['descricao']); ?></td>
                        <td><?php echo htmlspecialchars($transacao['categoria_nome'] ?? 'Sem categoria'); ?></td>
                        <td><?php echo ucfirst($transacao['tipo']); ?></td>
                        <td>R$ <?php echo number_format($transacao['valor'], 2, ',', '.'); ?></td>
                        <td>
                            <a href="transacoes_formulario.php?id=<?php echo $transacao['id_transacao']; ?>">Editar</a>
                            <a href="transacoes_excluir.php?id=<?php echo $transacao['id_transacao']; ?>"
                                onclick="return confirm('Tem certeza que deseja excluir esta transação?');">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhuma transação encontrada.</p>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

</body>

</html>