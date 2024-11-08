<?php
date_default_timezone_set('America/Sao_Paulo');
include_once "dao/conexao.php";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha para Alunos</title>
    <link rel="stylesheet" type="text/css" href="bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
    <link rel="stylesheet" href="Estilos/estilo_rec_senha.css">
</head>
<body>
    <?php
    // RECEBER DADOS DO FORMULÁRIO
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // ACESSAR O IF QUANDO O USUÁRIO CLICAR NO BOTÃO DE RECUPERAR SENHA DO FORMULÁRIO
    if (!empty($dados['SendRecupSenha'])) {
        // QUERY PARA RECUPERAR OS DADOS DO USUÁRIO NO BANCO DE DADOS
        $query_usuario = "SELECT idalu, email_alu, senha_alu, dt_nasc_alu FROM aluno WHERE email_alu=:email_alu AND dt_nasc_alu=:dt_nasc_alu LIMIT 1";
        $result_usuario = $pdo->prepare($query_usuario);
        $result_usuario->bindParam(':email_alu', $dados['email_alu']);
        $result_usuario->bindParam(':dt_nasc_alu', $dados['dt_nasc_alu']); // Corrigido para usar o nome correto
        $result_usuario->execute();
    
        if ($result_usuario->rowCount() > 0) {
            $row_usuario = $result_usuario->fetch(PDO::FETCH_ASSOC);
            // Data de nascimento e email corretos, redirecionar para página de troca de senha
            session_start(); // Inicia a sessão
            $_SESSION['idalu'] = $row_usuario['idalu']; // Armazena o idalu na sessão
            header('Location: troca_senha.php');
            exit;
        } else {
            $erro = "<p>ERRO: Email ou data de nascimento incorretos</p>";
        }
    }
    ?>

    <div class="right-content">
        <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> 
            <h1 id="h1-header">Recuperação de Senha</h1>
        </header>
    </div>

    <form id="updateForm" method="POST" action="">
        <div class="mb-3">
            <label for="Email" class="form-label">Email:</label>
            <input type="text" name="email_alu" class="form-control" placeholder="Insira o email de recuperação da sua conta" required>

            <label for="Data de Nascimento" class="form-label">Data de Nascimento:</label>
            <input type="date" name="dt_nasc_alu" class="form-control" required>
        </div>    
    
        <button type="submit" name="SendRecupSenha" class="btn btn-primary" id="button-adicionar" value="Recuperar">Recuperar Senha</button>

        <?php if (isset($erro)) { echo $erro; } ?>

        <p>Lembrou a senha? <a href="../index.php">Clique aqui para logar</a></p>
    </form>
</body>
</html>