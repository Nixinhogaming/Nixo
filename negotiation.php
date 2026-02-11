<?php
session_start();
require_once "config_db.php";

if (!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$role = $_SESSION["role"];
$theme = $_SESSION["theme"] ?? 'light';

// Ações de negociação
if (isset($_POST['apply'])) {
    $rid = $_POST['restaurant_id'];
    $msg = $_POST['message'];
    $sql = "INSERT INTO negotiations (staff_id, restaurant_id, message) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("iis", $user_id, $rid, $msg);
    $stmt->execute();
    header("location: negotiation.php?sucesso=Pedido enviado!");
    exit;
}

if (isset($_POST['update_status'])) {
    $nid = $_POST['negotiation_id'];
    $status = $_POST['status'];
    $sql = "UPDATE negotiations SET status = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("si", $status, $nid);
    if ($stmt->execute() && $status == 'accepted') {
        // Obter restaurant_id e staff_id
        $sql_info = "SELECT staff_id, restaurant_id FROM negotiations WHERE id = ?";
        $stmt_info = $mysqli->prepare($sql_info);
        $stmt_info->bind_param("i", $nid);
        $stmt_info->execute();
        $stmt_info->bind_result($sid, $rid);
        $stmt_info->fetch();
        $stmt_info->close();

        // Atualizar usuario
        $sql_user = "UPDATE usuarios SET current_restaurant_id = ? WHERE id = ?";
        $stmt_user = $mysqli->prepare($sql_user);
        $stmt_user->bind_param("ii", $rid, $sid);
        $stmt_user->execute();
    }
    header("location: negotiation.php");
    exit;
}

// Obter negociações
$negotiations = [];
if ($role == 'staff') {
    $sql = "SELECT n.id, r.name as restaurant_name, n.status, n.message, n.criado_em
            FROM negotiations n
            JOIN restaurants r ON n.restaurant_id = r.id
            WHERE n.staff_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $user_id);
} else {
    $sql = "SELECT n.id, u.primeiro_nome as staff_name, n.status, n.message, n.criado_em
            FROM negotiations n
            JOIN usuarios u ON n.staff_id = u.id
            JOIN restaurants r ON n.restaurant_id = r.id
            WHERE r.host_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) $negotiations[] = $row;
$stmt->close();

// Lista de restaurantes para staff se candidatar
$available_restaurants = [];
if ($role == 'staff') {
    $sql = "SELECT id, name FROM restaurants";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc()) $available_restaurants[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Negociação - AlgarveOrder</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body data-theme="<?php echo $theme; ?>">

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">AlgarveOrder</a>
        <div class="navbar-links">
            <a href="<?php echo $role == 'host' ? 'host_dashboard.php' : 'staff_dashboard.php'; ?>">Dashboard</a>
            <a href="logout.php" class="btn btn-primary">Sair</a>
        </div>
    </nav>

    <div class="container">
        <h1>Centro de Negociações</h1>

        <?php if ($role == 'staff'): ?>
            <div class="form-container" style="max-width: none; margin-top: 2rem;">
                <h2>Candidatar-se a um Restaurante</h2>
                <form method="POST" style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 1rem; align-items: end;">
                    <div class="input-group" style="margin: 0;">
                        <label>Escolha o Restaurante</label>
                        <select name="restaurant_id" required>
                            <?php foreach ($available_restaurants as $res): ?>
                                <option value="<?php echo $res['id']; ?>"><?php echo htmlspecialchars($res['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group" style="margin: 0;">
                        <label>Mensagem / Proposta</label>
                        <input type="text" name="message" placeholder="Olá, gostaria de trabalhar convosco..." required>
                    </div>
                    <button type="submit" name="apply" class="btn btn-primary">Enviar Proposta</button>
                </form>
            </div>
        <?php endif; ?>

        <div style="margin-top: 3rem;">
            <h2>Histórico de Negociações</h2>
            <div class="form-container" style="max-width: none;">
                <?php if (empty($negotiations)): ?>
                    <p>Nenhuma negociação encontrada.</p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--primary-blue); text-align: left;">
                                <th style="padding: 1rem;"><?php echo $role == 'staff' ? 'Restaurante' : 'Candidato'; ?></th>
                                <th style="padding: 1rem;">Mensagem</th>
                                <th style="padding: 1rem;">Estado</th>
                                <th style="padding: 1rem;">Data</th>
                                <?php if ($role == 'host'): ?> <th style="padding: 1rem;">Ações</th> <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($negotiations as $neg): ?>
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($role == 'staff' ? $neg['restaurant_name'] : $neg['staff_name']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($neg['message']); ?></td>
                                    <td style="padding: 1rem;">
                                        <span style="padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; background: var(--light-blue); color: var(--primary-blue);">
                                            <?php echo strtoupper($neg['status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;"><?php echo date("d/m/Y", strtotime($neg['criado_em'])); ?></td>
                                    <?php if ($role == 'host' && $neg['status'] == 'pending'): ?>
                                        <td style="padding: 1rem;">
                                            <form method="POST" style="display: flex; gap: 0.5rem;">
                                                <input type="hidden" name="negotiation_id" value="<?php echo $neg['id']; ?>">
                                                <input type="hidden" name="update_status" value="1">
                                                <button type="submit" name="status" value="accepted" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem; text-transform: none;">
                                                    Aceitar
                                                </button>
                                                <button type="submit" name="status" value="rejected" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.8rem; color: red; border-color: red; text-transform: none;">
                                                    Rejeitar
                                                </button>
                                            </form>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>
