<?php
session_start();
require_once "config_db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];
    $host_code = $_POST["host_code"] ?? null;

    if ($host_code) {
        // Redirecionar direto para o restaurante se o código for válido
        $sql = "SELECT id FROM restaurants WHERE host_code = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $host_code);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($restaurant_id);
                $stmt->fetch();
                $_SESSION["client_restaurant_id"] = $restaurant_id;
                header("location: client_mode.php");
                exit;
            } else {
                header("location: login.php?erro=Código de restaurante inválido.");
                exit;
            }
        }
    }

    $sql = "SELECT id, primeiro_nome, email, senha, role, theme FROM usuarios WHERE email = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $primeiro_nome, $email_db, $hashed_password, $role, $theme);
                if ($stmt->fetch()) {
                    if (password_verify($senha, $hashed_password)) {
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["primeiro_nome"] = $primeiro_nome;
                        $_SESSION["role"] = $role;
                        $_SESSION["theme"] = $theme;

                        if ($role == 'host') {
                            header("location: host_dashboard.php");
                        } elseif ($role == 'staff') {
                            header("location: staff_dashboard.php");
                        } else {
                            header("location: index.php");
                        }
                        exit;
                    } else {
                        header("location: login.php?erro=Senha incorreta.");
                    }
                }
            } else {
                header("location: login.php?erro=Usuário não encontrado.");
            }
        }
        $stmt->close();
    }
    exit;
}
?>
