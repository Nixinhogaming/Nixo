<?php
session_start();
require_once "config_db.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== 'host') {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$theme = $_SESSION["theme"] ?? 'light';

// Obter ID do restaurante
$restaurant_id = null;
$sql = "SELECT id FROM restaurants WHERE host_id = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($rid);
    if ($stmt->fetch()) {
        $restaurant_id = $rid;
    }
    $stmt->close();
}

if (!$restaurant_id) {
    echo "Por favor, gere um código de restaurante primeiro no Dashboard.";
    exit;
}

// Adicionar mesa
if (isset($_POST['add_table'])) {
    $x = rand(0, 800);
    $y = rand(0, 500);
    $sql = "INSERT INTO restaurant_tables (restaurant_id, x_pos, y_pos) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("iii", $restaurant_id, $x, $y);
    $stmt->execute();
    header("location: floor_plan.php");
    exit;
}

// Obter mesas
$tables = [];
$sql = "SELECT id, x_pos, y_pos, status, tablet_id FROM restaurant_tables WHERE restaurant_id = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planta do Restaurante - AlgarveOrder</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .floor-plan-area {
            width: 100%;
            height: 600px;
            background-color: var(--light-blue);
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            border: 2px dashed var(--primary-blue);
        }
        .table-item {
            width: 80px;
            height: 80px;
            background-color: var(--white);
            border: 2px solid var(--primary-blue);
            border-radius: 50%;
            position: absolute;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 0.8rem;
            cursor: move;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table-status {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 5px;
        }
        .status-available { background-color: #28a745; }
        .status-occupied { background-color: #ffc107; }
        .status-delayed { background-color: #dc3545; animation: blink 1s infinite; }

        @keyframes blink {
            50% { opacity: 0; }
        }
    </style>
</head>
<body data-theme="<?php echo $theme; ?>">

    <nav class="navbar">
        <a href="host_dashboard.php" class="navbar-brand">AlgarveOrder Host</a>
        <div class="navbar-links">
            <a href="host_dashboard.php">Dashboard</a>
            <a href="floor_plan.php">Planta</a>
            <a href="logout.php" class="btn btn-primary">Sair</a>
        </div>
    </nav>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Planta do Restaurante</h1>
            <form method="POST">
                <button type="submit" name="add_table" class="btn btn-primary">+ Adicionar Mesa / Cadeira</button>
            </form>
        </div>

        <div class="floor-plan-area" id="floor-plan">
            <?php foreach ($tables as $table): ?>
                <div class="table-item" draggable="true" ondragend="updatePos(event, <?php echo $table['id']; ?>)" style="left: <?php echo $table['x_pos']; ?>px; top: <?php echo $table['y_pos']; ?>px;">
                    <strong>Mesa <?php echo $table['id']; ?></strong>
                    <div class="table-status status-<?php echo $table['status']; ?>"></div>
                </div>
            <?php endforeach; ?>
        </div>

        <script>
            function updatePos(e, id) {
                const rect = document.getElementById('floor-plan').getBoundingClientRect();
                const x = e.clientX - rect.left - 40;
                const y = e.clientY - rect.top - 40;
                e.target.style.left = x + 'px';
                e.target.style.top = y + 'px';
                // Aqui poderíamos fazer um fetch para salvar no BD
                console.log('Mesa ' + id + ' movida para: ' + x + ',' + y);
            }
        </script>

        <div style="margin-top: 2rem; display: flex; gap: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div class="table-status status-available"></div> Disponível
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div class="table-status status-occupied"></div> Ocupada
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div class="table-status status-delayed"></div> Atraso / Alerta
            </div>
        </div>
    </div>

</body>
</html>
