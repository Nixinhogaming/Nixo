<?php
session_start();
require_once "config_db.php";

if (!isset($_SESSION["loggedin"])) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["id"];
    $experience = $_POST["experience"] ?? '';
    $skills = $_POST["skills"] ?? '';

    $sql = "UPDATE usuarios SET experience = ?, skills = ? WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ssi", $experience, $skills, $user_id);
        if ($stmt->execute()) {
            header("location: staff_dashboard.php?sucesso=Perfil atualizado!");
        } else {
            header("location: staff_dashboard.php?erro=Erro ao atualizar perfil.");
        }
        $stmt->close();
    }
    exit;
}
?>
