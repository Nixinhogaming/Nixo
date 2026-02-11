<?php
session_start();
require_once "config_db.php";

$restaurant_id = $_SESSION["client_restaurant_id"] ?? null;

if (!$restaurant_id) {
    header("location: login.php");
    exit;
}

// Obter nome do restaurante e código do host
$sql = "SELECT r.name, r.host_code FROM restaurants r WHERE r.id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$stmt->bind_result($restaurant_name, $correct_host_code);
$stmt->fetch();
$stmt->close();

// Sair do modo cliente
$erro_saida = "";
if (isset($_POST['exit_client_mode'])) {
    $input_code = $_POST['exit_code'];
    if ($input_code === $correct_host_code) {
        unset($_SESSION["client_restaurant_id"]);
        header("location: index.php");
        exit;
    } else {
        $erro_saida = "Código de Host incorreto. Por favor, solicite ajuda ao funcionário.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Digital - <?php echo htmlspecialchars($restaurant_name); ?></title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        body { background-color: var(--light-blue); }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        .menu-item {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .menu-item:hover { transform: translateY(-5px); }
        .menu-item img { width: 100%; height: 200px; object-fit: cover; }
        .menu-item-info { padding: 1.5rem; }
        .price { color: var(--primary-blue); font-weight: 700; font-size: 1.2rem; }
    </style>
</head>
<body>

    <nav class="navbar" style="justify-content: center; position: relative;">
        <span class="navbar-brand"><?php echo htmlspecialchars($restaurant_name); ?> - Menu Digital</span>
        <button onclick="document.getElementById('exit-modal').style.display='flex'" style="position: absolute; right: 2rem; background: none; border: 1px solid var(--primary-blue); color: var(--primary-blue); padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer;">Sair do Modo Cliente</button>
    </nav>

    <div class="container">
        <div style="text-align: center; margin-top: 3rem;">
            <h1>Ementa Recomendada</h1>
            <p>Escolha os seus pratos favoritos e desfrute da melhor experiência gastronómica do Algarve.</p>
        </div>

        <div class="menu-grid">
            <!-- Exemplos Pre-definidos bonitos -->
            <div class="menu-item">
                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&q=80&w=500" alt="Salada">
                <div class="menu-item-info">
                    <h3>Salada Mediterrânica</h3>
                    <p style="opacity: 0.7; font-size: 0.9rem;">Mix de folhas frescas, queijo feta, azeitonas e azeite extra virgem.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                        <span class="price">12.50€</span>
                        <button class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Pedir</button>
                    </div>
                </div>
            </div>

            <div class="menu-item">
                <img src="https://images.unsplash.com/photo-1567620905732-2d1ec7bb7445?auto=format&fit=crop&q=80&w=500" alt="Panquecas">
                <div class="menu-item-info">
                    <h3>Pequeno Almoço Algarve</h3>
                    <p style="opacity: 0.7; font-size: 0.9rem;">Panquecas fofas com mel local, frutas da época e sumo de laranja natural.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                        <span class="price">8.90€</span>
                        <button class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Pedir</button>
                    </div>
                </div>
            </div>

            <div class="menu-item">
                <img src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&q=80&w=500" alt="Carne">
                <div class="menu-item-info">
                    <h3>Bife da Vazia Especial</h3>
                    <p style="opacity: 0.7; font-size: 0.9rem;">Grelhado na brasa, acompanhado com batata rústica e molho de pimentas.</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                        <span class="price">18.00€</span>
                        <button class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Pedir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Saída -->
    <div id="exit-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; justify-content: center; align-items: center;">
        <div class="form-container" style="width: 400px; text-align: center;">
            <h2 style="color: var(--primary-blue);">Restrição de Acesso</h2>
            <p style="margin: 1.5rem 0; line-height: 1.6;">
                <strong>Atenção:</strong> Esta área é restrita a clientes. Se clicou por engano, por favor feche este aviso.
                Para sair, é necessário o código de autorização do Host.
            </p>
            <?php if ($erro_saida): ?> <p style="color: red;"><?php echo $erro_saida; ?></p> <?php endif; ?>
            <form method="POST">
                <div class="input-group">
                    <label>Código do Host</label>
                    <input type="password" name="exit_code" required>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="button" onclick="document.getElementById('exit-modal').style.display='none'" class="btn btn-outline" style="flex: 1;">Cancelar</button>
                    <button type="submit" name="exit_client_mode" class="btn btn-primary" style="flex: 1;">Confirmar Saída</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($erro_saida): ?>
        <script>document.getElementById('exit-modal').style.display='flex';</script>
    <?php endif; ?>

</body>
</html>
