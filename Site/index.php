<?php
    session_start();
    if (isset($_SESSION['ERROR'])) {
        echo "<script>alert('".$_SESSION['ERROR']."');</script>";
        unset($_SESSION['ERROR']);
    }

    if (isset($_SESSION['acesso-negado'])) {
        echo "<script>alert('". $_SESSION['acesso-negado']."');</script>";
        unset($_SESSION['acesso-negado']);
    }

    if (isset($_SESSION['success'])) {
        echo "<script>alert('". $_SESSION['success']."');</script>";
        unset($_SESSION['success']);
    }




?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

        <form method="POST" action="dao/login.php">
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
            <div id="espaco"></div><div class="cadastro">
                <p>Esqueceu sua senha? <a href="../recuperar_senha.php"> Clique Aqui </a></p>
            </div>
            <br>
            <div id="box-opcoes">
                <label for="opcoes">Perfil:</label>
                <select name="opcoes" class="form-select" id="opcoes" required>
                    <option value="" selected disabled>Selecione uma opção</option>
                    <option value="Aluno">Aluno</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Coordenador">Coordenador</option>
                </select>

            </div>

            <div id="button">
                <button type="submit" name="acao" value="login">Entrar</button>
            </div>
            <div class="cadastro">
                <p>É aluno e não tem conta? <a href="cadastro_aluno.php">Cadastre-se</a></p>
            </div>
        </form>
    </main>

    <script src="https://kit.fontawesome.com/5086c6dc28.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>