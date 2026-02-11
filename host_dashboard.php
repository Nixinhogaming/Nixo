<?php
session_start();
require_once "config_db.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== 'host') {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$theme = $_SESSION["theme"] ?? 'light';

// Lógica para alternar tema
if (isset($_GET['toggle_theme'])) {
    $new_theme = ($theme == 'light') ? 'dark' : 'light';
    $sql = "UPDATE usuarios SET theme = ? WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("si", $new_theme, $user_id);
        $stmt->execute();
        $_SESSION["theme"] = $new_theme;
        header("location: host_dashboard.php");
        exit;
    }
}

// Obter código do restaurante
$host_code = "NÃO GERADO";
$sql = "SELECT host_code FROM restaurants WHERE host_id = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($code);
    if ($stmt->fetch()) {
        $host_code = $code;
    }
    $stmt->close();
}

// Gerar código se solicitado
if (isset($_POST['generate_code'])) {
    $new_code = "ALG-" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    // Se já tem restaurante, atualiza. Se não, cria.
    if ($host_code == "NÃO GERADO") {
        $sql = "INSERT INTO restaurants (name, host_id, host_code) VALUES ('Meu Restaurante', ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("is", $user_id, $new_code);
    } else {
        $sql = "UPDATE restaurants SET host_code = ? WHERE host_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $new_code, $user_id);
    }
    $stmt->execute();
    header("location: host_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Host - AlgarveOrder</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body data-theme="<?php echo $theme; ?>">

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">AlgarveOrder Host</a>
        <div class="navbar-links">
            <a href="host_dashboard.php">Dashboard</a>
            <a href="floor_plan.php">Planta do Restaurante</a>
            <a href="host_dashboard.php?toggle_theme=1" class="btn btn-outline"><?php echo $theme == 'light' ? 'Modo Escuro' : 'Modo Claro'; ?></a>
            <a href="logout.php" class="btn btn-primary">Sair</a>
        </div>
    </nav>

    <div class="container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION["primeiro_nome"]); ?>!</h1>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <div class="form-container" style="max-width: none;">
                <h2>Acesso de Clientes</h2>
                <p style="font-size: 1.1rem; margin-bottom: 1.5rem;">Partilhe este código com os seus clientes para que possam aceder ao menu digital.</p>
                <div style="background: var(--light-blue); padding: 1.5rem; text-align: center; border-radius: 10px;">
                    <span style="font-size: 2.5rem; font-weight: 700; color: var(--primary-blue); letter-spacing: 3px;">
                        <?php echo $host_code; ?>
                    </span>
                </div>
                <form method="POST" style="margin-top: 1.5rem; text-align: center;">
                    <button type="submit" name="generate_code" class="btn btn-outline">Gerar Novo Código</button>
                </form>
            </div>

            <div class="form-container" style="max-width: none;">
                <h2>Estatísticas Rápidas</h2>
                <div style="display: flex; justify-content: space-around; text-align: center; margin-top: 1rem;">
                    <div>
                        <p style="font-size: 2rem; font-weight: 700; color: var(--primary-blue); margin: 0;">0</p>
                        <p>Mesas Ativas</p>
                    </div>
                    <div>
                        <p style="font-size: 2rem; font-weight: 700; color: var(--primary-blue); margin: 0;">0</p>
                        <p>Funcionários</p>
                    </div>
                </div>
                <a href="floor_plan.php" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem; text-align: center;">Ver Planta Interativa</a>
            </div>
        </div>

        <div class="form-container" style="max-width: none; margin-top: 2rem;">
            <h2>Negociações com Staff</h2>
            <p>Acompanhe aqui os pedidos de adesão de novos funcionários.</p>
            <a href="negotiation.php" class="btn btn-outline">Ver Pedidos Pendentes</a>
        </div>
    </div>

</body>
</html>
