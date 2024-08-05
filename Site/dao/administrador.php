<?php
require_once("conexao.php");
session_start(); // Inicia a sessão para guardar valores entre as páginas

// Limpa espaços em branco antes de depois do input e guarda na variável $curso
$nome = trim($_POST['admNome']);
$cpf = trim($_POST['admCpf']);
$endereço = trim($_POST['admEndereco']);
$cidade = trim($_POST['admCidade']);
$estado = trim($_POST['admEstado']);
$telefone = trim($_POST['admCelular']);
$senha = trim($_POST['admSenha']);
$status = trim($_POST['admStatus']);
$admId = $_POST['admId'];

$sql2 = $pdo->prepare("SELECT idtpu FROM tp_u WHERE descricao_tpu = 'Administrador'");
$sql2->execute();
$infoId = $sql2->fetchAll(PDO::FETCH_ASSOC);
$tipo = $infoId[0]['idtpu'];


// Verifica se o formulário de adicionar curso foi enviado
if (isset($_POST['adicionar-administrador'])){
    $sql = $pdo->prepare("SELECT * FROM administrador WHERE cpf_adm = ?");
    $sql->execute(array($cpf));

    $sql->fetchAll(PDO::FETCH_ASSOC); //Retorna um array com os resultados obtidos através da consulta no banco de dados

    if (empty($nome)){
        $_SESSION['error'] = 'Todos os atributos são obrigatórios!'; //Define a session para retornar uma mensagem na página curso fora do dao.
        header("Location: ../gerenciamento_administrador.php"); // Retorna para a página curso.
        exit();

    }else if($sql->rowCount() > 0){ // Verifica se já possui algum registro. Se não tiver, o curso digitado será inserido. 
        $_SESSION['duplicated'] = 'O Administrador '.$nome.' já está cadastrado! Insira outro administrador ou atualize o nome já existente.';
        header("Location: ../gerenciamento_administrador.php");
        exit();

    }else{
        $sql = $pdo->prepare("INSERT INTO administrador VALUES (null, ?, ?, ?, ?, ?, ?, ?, 1, ?)");
        $sql->execute(array($nome, $cpf, $endereço, $cidade, $estado, $telefone, $senha, $tipo));
        $_SESSION['success'] = 'Administrador ' . $nome.  ' inserido com sucesso!';
        header("Location: ../gerenciamento_administrador.php");
        exit();
    }

// Verifica se o formulário de atualizar curso foi enviado 
} else if (isset($_POST["atualizar-administrador"])){
    $sql = $pdo->prepare("SELECT * FROM administrador WHERE cpf_adm = ?");
    $sql->execute(array($cpf));
    $sql->fetchAll(PDO::FETCH_ASSOC);

    if (empty($nome)){
        $_SESSION['error'] = 'Todos os atributos são obrigatórios!';
        header("Location: ../gerenciamento_administrador.php");
        exit();

    }else if($sql->rowCount() > 0){
        $_SESSION['duplicated'] = 'O Administrador '.$nome.' já está cadastrado! Insira outro nome.';
        header("Location: ../gerenciamento_administrador.php");
        exit();

    }else{
        $sql = $pdo->prepare("UPDATE administrador SET nome_adm = ?, cpf_adm = ?, endereco_adm = ?, cidade_adm = ?, estado_adm = ?, telefone_adm = ?, senha_adm = ?, status_adm = ?  WHERE idadm = ?");
        $sql->execute(array($nome, $cpf, $endereço, $cidade, $estado, $telefone, $senha, $status, $admId));
        $_SESSION['success'] = 'Administrador atualizado com sucesso!';
        header("Location: ../gerenciamento_administrador.php");
        exit();
    }
}
?>