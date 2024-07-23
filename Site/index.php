<?php
session_start();
$resposta = null;
require_once("dao/conexao.php");

if (isset($_POST['acao'])){
    $nome = $_POST['usuario'];
    $senha = $_POST['senha'];

    $sql = $pdo->prepare("SELECT * FROM administrador where nome_adm = ? and senha_adm = ?");
    $sql->execute(array($nome, $senha));
    
    if ($sql->rowCount() > 0) {
        $_SESSION['usuario'] = $nome;
        header('Location: curso.php');
        exit();
    }else{
        $resposta = "Usuário ou senha incorretos.";
        die();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Estilos/estiloLogin.css">
    <link rel="shortcut icon" href="assets/icone logo.ico" type="image/x-icon">
    <title>SOLICITAÇÕES UEMG-FRUTAL</title>
</head>

<body>

    <main>

        <div id="banneruemg">
            <img src="assets/Banner uemg.png" alt="">
        </div>

        <div id="texto">
            <h1>SOLICITAÇÕES ACADÊMICAS</h1>
        </div>

        <form method="POST" action="index.php">
            <div>
                <?php if ($resposta){
                    echo "<p style='color: #f00;'> $resposta </p>";
                } ?>
            </div>
            <div id="box-usuario">
                <label for="usuario"><i class="fa-solid fa-user"></i></label>   
                <input type="text" name="usuario" id="usuario" placeholder="CPF" required>
            </div>
            <hr>

            <div id="box-senha">
                <label for="senha"><i class="fa-solid fa-lock"></i></label>
                <input type="password" name="senha" id="senha" placeholder="Senha" required>
            </div>
            <hr>
            <div id="espaco"></div>
            <div id="box-opcoes">
                <label for="opcoes">Perfil:</label>
                <select name="opcoes" id="opcoes" required>
                    <option value="" selected disabled>Selecione uma opção</option>
                    <option value="aluno">Aluno</option>
                    <option value="administrador">Administrador</option>
                    <option value="coordenador">Coordenador</option>
                </select>

            </div>

            <div id="button">
                <button type="submit" name="acao" value="login">Entrar</button>
            </div>
        </form>
    </main>

    <script src="https://kit.fontawesome.com/5086c6dc28.js" crossorigin="anonymous"></script>
</body>

</html>