<?php
    session_start();

    $idalu = $_SESSION['idalu']; // Puxa o Id do email e Data Nascimento Inserido anteriormente
    date_default_timezone_set('America/Sao_Paulo');
    include ("dao/conexao.php");

    // RECEBER DADOS DO FORMULARIO
    if (isset($_POST['SendTrocaSenha'])) {
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];
    
        // Verificações de Segurança
        if (empty($nova_senha) || empty($confirmar_senha)) {
            $mensagem = "<p>Erro: Preencha todos os campos obrigatórios</p>";
        } elseif ($nova_senha != $confirmar_senha) {
            $mensagem = "<p>Erro: As senhas não coincidem</p>";
        } else {
            // Enviando a nova senha no padrão HASH
            $nova_senha_hash = password_hash($nova_senha,PASSWORD_DEFAULT);
            
            // QUERY PARA ATUALIZAR A SENHA DO USUARIO NO BANCO DE DADOS ATRIBUINDO VALOR A SENHA ALU PARA NOVA SENHA
            $query_atualizar_senha = "UPDATE aluno SET senha_alu=:nova_senha WHERE idalu=:idalu";
            $result_atualizar_senha = $pdo->prepare($query_atualizar_senha);
            $result_atualizar_senha->bindParam(':nova_senha', $nova_senha_hash);
            $result_atualizar_senha->bindParam(':idalu', $idalu);
    
            try {
                $result_atualizar_senha->execute();
                $mensagem = "<p>Sua senha foi atualizada com sucesso! Você vai ser redirecionado em 5 segundos!</p>";
                header('Refresh: 5; URL=index.php');
            } catch (PDOException $e) {
                $mensagem = "<p>Erro: Não foi possível atualizar a senha. Erro: " . $e->getMessage() . "</p>";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Troca de Senha</title>
    <link rel="stylesheet" type="text/css" href="bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
    <link rel="stylesheet" href="Estilos/estilo_rec_senha.css">
    <style>
        /* Adicionei um estilo para a mensagem de erro/sucesso */
        .mensagem {
            color: #00698f;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="right-content">
        <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> 
            <h1 id="h1-header">Troca de Senha</h1>
        </header>
    </div>

    <form id="updateForm" method="POST" action="">
        <div class="mb-3">
            <label for="Nova Senha" class="form-label"> Nova Senha: </label>
            <input type="password" name="nova_senha" placeholder="Insira a sua nova senha" class="form-control"><br>

            <label for="Confirmar Senha" class="form-label"> Confirmar Senha: </label>
            <input type="password" name="confirmar_senha" placeholder="Confirme a sua nova senha" class="form-control"><br>

            <button type="submit" name="SendTrocaSenha" class="btn btn-primary">Atualizar Senha</button>
        </div>
        <?php if (isset($mensagem)) { ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php } ?>
    </form> 