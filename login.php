<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AlgarveOrder</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <div class="container">
        <div style="margin-bottom: 2rem; text-align: center;">
            <a href="index.php" class="btn btn-outline">← Voltar ao Início</a>
        </div>

        <div class="form-container">
            <h2 style="text-align: center; color: var(--primary-blue); margin-bottom: 2rem;">Aceder à Plataforma</h2>

            <?php
            if (isset($_GET['erro'])) echo '<div style="color: red; margin-bottom: 1rem; text-align: center;">' . htmlspecialchars($_GET['erro']) . '</div>';
            if (isset($_GET['sucesso'])) echo '<div style="color: green; margin-bottom: 1rem; text-align: center;">' . htmlspecialchars($_GET['sucesso']) . '</div>';
            ?>

            <div class="tabs">
                <button class="tab-link active" onclick="openTab(event, 'host-tab')">Host</button>
                <button class="tab-link" onclick="openTab(event, 'staff-tab')">Staff</button>
                <button class="tab-link" onclick="openTab(event, 'client-tab')">Entrar com Código</button>
            </div>

            <div id="host-tab" class="tab-content active">
                <form action="processa_login.php" method="POST">
                    <div class="input-group">
                        <label for="email_host">Email do Host</label>
                        <input type="email" id="email_host" name="email" required>
                    </div>
                    <div class="input-group">
                        <label for="senha_host">Senha</label>
                        <input type="password" id="senha_host" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar como Host</button>
                </form>
            </div>

            <div id="staff-tab" class="tab-content">
                <form action="processa_login.php" method="POST">
                    <div class="input-group">
                        <label for="email_staff">Email do Funcionário</label>
                        <input type="email" id="email_staff" name="email" required>
                    </div>
                    <div class="input-group">
                        <label for="senha_staff">Senha</label>
                        <input type="password" id="senha_staff" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar como Staff</button>
                </form>
            </div>

            <div id="client-tab" class="tab-content">
                <form action="processa_login.php" method="POST">
                    <p style="margin-bottom: 1.5rem; text-align: center; opacity: 0.8;">
                        Introduza o código fornecido pelo restaurante para aceder ao menu digital.
                    </p>
                    <div class="input-group">
                        <label for="host_code">Código do Restaurante</label>
                        <input type="text" id="host_code" name="host_code" placeholder="Ex: ALG-1234" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar no Restaurante</button>
                </form>
            </div>

            <p style="text-align: center; margin-top: 1.5rem;">
                Ainda não tem conta? <a href="registo.php" style="color: var(--primary-blue); font-weight: 600;">Crie uma conta gratuita</a>
            </p>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tab-link");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
    </script>
</body>
</html>
