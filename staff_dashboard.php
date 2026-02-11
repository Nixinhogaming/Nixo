<?php
session_start();
require_once "config_db.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== 'staff') {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$theme = $_SESSION["theme"] ?? 'light';

// Obter detalhes do funcionário
$sql = "SELECT experience, skills, current_restaurant_id FROM usuarios WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($experience, $skills, $current_restaurant_id);
$stmt->fetch();
$stmt->close();

// Obter nome do restaurante atual se existir
$restaurant_name = "Nenhum no momento";
if ($current_restaurant_id) {
    $sql = "SELECT name FROM restaurants WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $current_restaurant_id);
    $stmt->execute();
    $stmt->bind_result($rname);
    if ($stmt->fetch()) $restaurant_name = $rname;
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff - AlgarveOrder</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body data-theme="<?php echo $theme; ?>">

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">AlgarveOrder Staff</a>
        <div class="navbar-links">
            <a href="staff_dashboard.php">Meu Perfil</a>
            <a href="negotiation.php">Procurar Restaurante</a>
            <a href="logout.php" class="btn btn-primary">Sair</a>
        </div>
    </nav>

    <div class="container">
        <h1>Olá, <?php echo htmlspecialchars($_SESSION["primeiro_nome"]); ?>!</h1>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
            <div class="form-container" style="max-width: none;">
                <h2>Estado Atual</h2>
                <p style="font-size: 1.1rem;">Restaurante: <strong><?php echo htmlspecialchars($restaurant_name); ?></strong></p>
                <div style="margin-top: 2rem;">
                    <h3>Habilidades</h3>
                    <p style="opacity: 0.8;"><?php echo nl2br(htmlspecialchars($skills)); ?></p>
                </div>
                <a href="negotiation.php" class="btn btn-outline" style="width: 100%; margin-top: 1rem; text-align: center;">Procurar Novas Oportunidades</a>
            </div>

            <div class="form-container" style="max-width: none;">
                <h2>Configurações de Perfil</h2>
                <form action="processa_configuracoes_perfil.php" method="POST">
                    <div class="input-group">
                        <label for="experience">Experiência Atualizada</label>
                        <textarea id="experience" name="experience" style="height: 100px;"><?php echo htmlspecialchars($experience); ?></textarea>
                    </div>
                    <div class="input-group">
                        <label for="skills">Minhas Habilidades</label>
                        <textarea id="skills" name="skills" style="height: 100px;"><?php echo htmlspecialchars($skills); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Atualizar Perfil Profissional</button>
                </form>
            </div>
        </div>

        <div class="form-container" style="max-width: none; margin-top: 2rem;">
            <h2>Histórico de Trabalho</h2>
            <p>Em breve poderá ver aqui todos os restaurantes onde já trabalhou.</p>
        </div>
    </div>

</body>
</html>
