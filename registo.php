<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - AlgarveOrder</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <div class="container">
        <div style="margin-bottom: 2rem; text-align: center;">
            <a href="index.php" class="btn btn-outline">← Voltar ao Início</a>
        </div>

        <div class="form-container">
            <h2 style="text-align: center; color: var(--primary-blue); margin-bottom: 2rem;">Criar Nova Conta</h2>

            <?php
            if (isset($_SESSION['erros_registo'])) {
                echo '<div style="color: red; margin-bottom: 1rem; text-align: center;">';
                foreach ($_SESSION['erros_registo'] as $erro) echo htmlspecialchars($erro) . '<br>';
                echo '</div>';
                unset($_SESSION['erros_registo']);
            }
            ?>

            <form action="processa_registo.php" method="POST">
                <div class="input-group">
                    <label for="role">Tipo de Conta</label>
                    <select name="role" id="role" onchange="toggleStaffFields()">
                        <option value="host">Restaurante (Host)</option>
                        <option value="staff">Funcionário (Staff)</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="primeiro_nome">Primeiro Nome</label>
                    <input type="text" id="primeiro_nome" name="primeiro_nome" required>
                </div>

                <div class="input-group">
                    <label for="ultimo_nome">Último Nome</label>
                    <input type="text" id="ultimo_nome" name="ultimo_nome" required>
                </div>

                <div class="input-group">
                    <label for="idade">Idade</label>
                    <input type="number" id="idade" name="idade" required>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div id="staff-fields" style="display: none;">
                    <div class="input-group">
                        <label for="experience">Experiência Profissional</label>
                        <textarea id="experience" name="experience" placeholder="Descreva a sua experiência..."></textarea>
                    </div>
                    <div class="input-group">
                        <label for="skills">Habilidades / O que deve colocar</label>
                        <textarea id="skills" name="skills" placeholder="O que acha que um funcionário deve colocar..."></textarea>
                    </div>
                </div>

                <div class="input-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required minlength="8">
                </div>

                <div class="input-group">
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="8">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Criar Conta Gratuita</button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem;">
                Já tem uma conta? <a href="login.php" style="color: var(--primary-blue); font-weight: 600;">Faça login</a>
            </p>
        </div>
    </div>

    <script>
        function toggleStaffFields() {
            const role = document.getElementById('role').value;
            const staffFields = document.getElementById('staff-fields');
            staffFields.style.display = role === 'staff' ? 'block' : 'none';
        }
    </script>
</body>
</html>
