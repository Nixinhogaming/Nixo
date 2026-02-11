<?php
session_start();
$primeiro_nome = isset($_SESSION["primeiro_nome"]) ? htmlspecialchars($_SESSION["primeiro_nome"]) : null;
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlgarveOrder - Soluções Profissionais para Restaurantes</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body data-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">

    <nav class="navbar">
        <a href="index.php" class="navbar-brand">AlgarveOrder</a>
        <div class="navbar-links">
            <a href="index.php">Início</a>
            <a href="#funcionalidades">Funcionalidades</a>
            <?php if ($primeiro_nome): ?>
                <a href="<?php echo $_SESSION['role'] == 'host' ? 'host_dashboard.php' : 'staff_dashboard.php'; ?>">Minha Área</a>
                <a href="logout.php" class="btn btn-outline" style="margin-left: 1rem;">Sair</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
                <a href="registo.php" class="btn btn-primary">Criar Conta</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container" style="text-align: center; padding: 5rem 1rem;">
        <h1 style="font-size: 3.5rem; color: var(--primary-blue);">Gestão Inteligente para o seu Restaurante</h1>
        <p style="font-size: 1.2rem; max-width: 800px; margin: 2rem auto; line-height: 1.8;">
            A AlgarveOrder oferece uma plataforma completa e gratuita para modernizar o seu estabelecimento.
            Desde plantas interativas até gestão de staff e pedidos em tempo real.
        </p>
        <div style="margin-top: 3rem;">
            <a href="registo.php" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2.5rem;">Começar Agora</a>
            <a href="#funcionalidades" class="btn btn-outline" style="font-size: 1.2rem; padding: 1rem 2.5rem; margin-left: 1rem;">Saiba Mais</a>
        </div>
    </div>

    <div id="funcionalidades" class="container" style="padding: 4rem 1rem;">
        <h2 style="text-align: center; margin-bottom: 4rem;">Porquê escolher a AlgarveOrder?</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div class="form-container" style="max-width: none;">
                <h3>Para Restaurantes (Host)</h3>
                <p>Crie a sua planta, gira mesas e acompanhe o status de cada pedido em tempo real com o nosso sistema de tablets.</p>
            </div>
            <div class="form-container" style="max-width: none;">
                <h3>Para Profissionais (Staff)</h3>
                <p>Encontre oportunidades, negoceie condições e gira o seu histórico profissional numa plataforma dedicada.</p>
            </div>
            <div class="form-container" style="max-width: none;">
                <h3>Para Clientes</h3>
                <p>Aceda ao menu digital instantaneamente através de um código simples e desfrute de uma experiência moderna.</p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p style="font-weight: 700; color: var(--primary-blue); font-size: 1.5rem; margin-bottom: 1rem;">AlgarveOrder</p>
            <p>&copy; <?php echo date("Y"); ?> AlgarveOrder International Group. Todos os direitos reservados.</p>
            <p style="font-size: 0.9rem; opacity: 0.7; margin-top: 1rem;">
                Sediada no Algarve, servindo o mundo. A maior plataforma gratuita de gestão de restauração.
                <br>
                Termos de Uso | Política de Privacidade | Suporte 24/7
            </p>
        </div>
    </footer>

</body>
</html>
