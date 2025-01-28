<?php
session_start();
require "conexao.php";

if (isset($_POST["create_usuario"])) {
    $nome = mysqli_real_escape_string($conexao, trim($_POST['nome']));
    $idade = mysqli_real_escape_string($conexao, trim($_POST['idade']));
    $senha = isset($_POST['senha']) ? mysqli_real_escape_string($conexao, password_hash(trim($_POST['senha']), PASSWORD_DEFAULT)) : '';

    $sql = "INSERT INTO usuarios (nome, idade, senha) VALUES ('$nome', '$idade', '$senha')";
    mysqli_query($conexao, $sql);

    if (mysqli_affected_rows($conexao) > 0) {
        $_SESSION["mensagem"] = "Usuário criado com sucesso";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION["mensagem"] = "Usuário não registrado";
        header("Location: index.php");
        exit;
    }
}

if (isset($_POST["update_usuario"])) {
    $usuario_id = mysqli_real_escape_string($conexao, $_POST["usuario_id"]);
    $usuario_id = intval($usuario_id); // Convert to integer

    $nome = mysqli_real_escape_string($conexao, trim($_POST['nome']));
    $idade = mysqli_real_escape_string($conexao, trim($_POST['idade']));
    $senha = mysqli_real_escape_string($conexao, trim($_POST['senha']));

    $sql = "UPDATE usuarios SET nome = ?, idade = ?";
    $params = array($nome, $idade);

    if (!empty($senha)) {
        $sql .= ", senha = ?";
        $params[] = password_hash($senha, PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id = ?";
    $params[] = $usuario_id;

    $stmt = mysqli_prepare($conexao, $sql);

    if ($stmt) {
        $types = str_repeat('s', count($params) - 1) . 'i'; // 'ssi' or 'sssi'
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
    
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION["mensagem"] = "Usuário editado com sucesso";
        } else {
            $_SESSION["mensagem"] = "Nenhuma alteração feita ou usuário não encontrado";
        }
    
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION["mensagem"] = "Erro ao preparar a consulta";
    }

    header("Location: index.php");
    exit;
}

if (isset($_POST["delete_usuario"])) {
    $usuario_id = mysqli_real_escape_string($conexao, $_POST["delete_usuario"]);

    $sql = "DELETE FROM usuarios WHERE id = '$usuario_id'";

    mysqli_query($conexao,$sql);

    if (mysqli_affected_rows($conexao) > 0) {
        $_SESSION["message"] = "Usuário deletado com sucesso";
        header('Location: index.php');
        exit;
    } else {
        $_SESSION["message"] = "Usuário não foi deletado";
        header('Location: index.php');
        exit;
    }
}

?>

