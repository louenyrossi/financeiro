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

// Buscar todas as categorias do usuário
$sql = "SELECT * FROM categoria WHERE id_usuario = :usuario_id ORDER BY tipo, nome";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id);
$stmt->execute();
$categorias = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Sistema Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #ffffff; /* FUNDO BRANCO */
    color: #4b3b5c;
    margin: 0;
    padding: 20px;
}

.body-formularios {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    justify-content: center;
}

h1,
h2,
h3 {
    color: #6d4ea6;
}

a {
    color: #000000 !important;
    text-decoration: none !important;
    font-weight: bold;
}

a:hover,
a:visited,
a:active,
a:focus {
    color: #000000 !important;
    text-decoration: none !important;
}


div > div {
    background-color: #e8d9ff;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    box-shadow: 0 2px 6px rgba(130, 100, 190, 0.2);
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: #f8f1ff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(130, 100, 190, 0.15);
}

table thead {
    background-color: #d8c1ff;
    color: #3d2a52;
}

table th,
table td {
    padding: 10px;
    border-bottom: 1px solid #d1b9ff;
}

table tr:nth-child(even) {
    background-color: #f2e7ff;
}

table tr:hover {
    background-color: #e4d2ff;
}

p a {
    background-color: #d1b6ff;
    padding: 8px 12px;
    border-radius: 6px;
    color: #000 !important;
}

p a:hover {
    background-color: #b391ff;
}


.btn-sair {
    background-color: #b87ccd;
    color: #ffffff !important;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: bold;
    display: inline-block;
    text-decoration: none !important;
    transition: 0.2s;
}

.btn-sair:hover {
    background-color: #c8a0d1ff;
    transform: translateY(-2px);
}

a[href*="nova"], 
a[href*="categoria"], 
button[href*="nova"], 
button[href*="categoria"] {
    background-color: #f3eaff !important; 
    color: #5c3d79 !important;
    border: 1px solid #dacbff !important;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    display: inline-block;
    transition: 0.25s;
}

a[href*="nova"]:hover,
a[href*="categoria"]:hover {
    background-color: #e6d6ff !important;
    transform: translateY(-2px);
}

a[href*="editar"],
a[href*="excluir"] {
    background-color: #f3eaff !important;
    color: #5c3d79 !important;
    border: 1px solid #dacbff !important;
    padding: 10px 16px;          
    border-radius: 8px;
    display: inline-block;
    min-width: 90px;           
    text-align: center;        
    font-weight: 600;
    transition: 0.2s;
}

a[href*="editar"]:hover,
a[href*="excluir"]:hover {
    background-color: #e6d6ff !important;
    transform: translateY(-2px);
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
    margin-bottom: 30px;
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

    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">

        <?php exibir_mensagem(); ?>

        <h2>Categorias</h2>

        <div>
            <a class="btn btn-primary" href="categorias_formulario.php">Nova Categoria</a>
        </div>

        <?php if (count($categorias) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                            <td><?php echo ucfirst($categoria['tipo']); ?></td>
                            <td>
                                <a class="btn btn-success" href="categorias_formulario.php?id=<?php echo $categoria['id_categoria']; ?>">Editar</a>
                                <a class="btn btn-danger" href="categorias_excluir.php?id=<?php echo $categoria['id_categoria']; ?>"
                                    onclick="return confirm('Tem certeza que deseja excluir esta categoria?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhuma categoria cadastrada ainda.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>