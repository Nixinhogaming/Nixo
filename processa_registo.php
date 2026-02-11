<?php
session_start();
require_once "config_db.php";

$primeiro_nome = $ultimo_nome = $idade = $email = $senha = $confirmar_senha = $role = $experience = $skills = "";
$erros = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $primeiro_nome = trim($_POST["primeiro_nome"]);
    $ultimo_nome = trim($_POST["ultimo_nome"]);
    $idade = trim($_POST["idade"]);
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];
    $confirmar_senha = $_POST["confirmar_senha"];
    $role = $_POST["role"] ?? 'host';
    $experience = $_POST["experience"] ?? '';
    $skills = $_POST["skills"] ?? '';

    // Validações básicas
    if (empty($primeiro_nome)) $erros[] = "Primeiro nome é obrigatório.";
    if (empty($ultimo_nome)) $erros[] = "Último nome é obrigatório.";
    if (empty($email)) $erros[] = "Email é obrigatório.";
    if (strlen($senha) < 8) $erros[] = "A senha deve ter pelo menos 8 caracteres.";
    if ($senha !== $confirmar_senha) $erros[] = "As senhas não coincidem.";

    if (empty($erros)) {
        // Verificar se email já existe
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $erros[] = "Este email já está em uso.";
            }
            $stmt->close();
        }
    }

    if (empty($erros)) {
        $sql = "INSERT INTO usuarios (primeiro_nome, ultimo_nome, idade, email, senha, role, experience, skills) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $mysqli->prepare($sql)) {
            $hashed_password = password_hash($senha, PASSWORD_DEFAULT);
            $stmt->bind_param("ssisssss", $primeiro_nome, $ultimo_nome, $idade, $email, $hashed_password, $role, $experience, $skills);
            if ($stmt->execute()) {
                header("location: login.php?sucesso=Conta criada com sucesso!");
                exit;
            } else {
                $erros[] = "Erro ao criar conta. Tente novamente.";
            }
            $stmt->close();
        }
    }

    $_SESSION['erros_registo'] = $erros;
    header("location: registo.php");
    exit;
}
?>
