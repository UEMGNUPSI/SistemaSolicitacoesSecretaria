<?php
require_once("conexao.php");
session_start(); // Inicia a sessão para guardar valores entre as páginas

require_once("verifica_adm.php"); 

// Limpa espaços em branco antes de depois do input e guarda na variável $curso
$curso = trim($_POST['cursoNome']);

// Verifica se o formulário de adicionar curso foi enviado
if (isset($_POST['adicionar-curso'])){
    $sql = $pdo->prepare("SELECT * FROM curso WHERE nome_cur = ?");
    $sql->execute(array($curso));

    $sql->fetchAll(PDO::FETCH_ASSOC); //Retorna um array com os resultados obtidos através da consulta no banco de dados

    if (empty($curso)){
        $_SESSION['error'] = 'Valor inválido!'; //Define a session para retornar uma mensagem na página curso fora do dao.
        header("Location: ../curso.php"); // Retorna para a página curso.
        exit();

    }else if($sql->rowCount() > 0){ // Verifica se já possui algum registro. Se não tiver, o curso digitado será inserido. 
        $_SESSION['duplicated'] = 'O curso '.$curso.' já está cadastrado! Insira outro curso ou atualize o nome do curso já existente.';
        header("Location: ../curso.php");
        exit();

    }else{
        $sql = $pdo->prepare("INSERT INTO curso VALUES (null, ?)");
        $sql->execute(array($curso));
        $_SESSION['success'] = 'Curso ' . $curso.  ' inserido com sucesso!';
        header("Location: ../curso.php");
        exit();
    }

// Verifica se o formulário de atualizar curso foi enviado
} else if (isset($_POST["atualizar-curso"])){
    $cursoId = $_POST['cursoId'];
    $sql = $pdo->prepare("SELECT * FROM curso WHERE nome_cur = ?");
    $sql->execute(array($curso));
    $sql->fetchAll(PDO::FETCH_ASSOC);

    if (empty($curso)){
        $_SESSION['error'] = 'Valor inválido!';
        header("Location: ../curso.php");
        exit();

    }else if($sql->rowCount() > 0){
        $_SESSION['duplicated'] = 'O curso '.$curso.' já está cadastrado! Insira outro nome.';
        header("Location: ../curso.php");
        exit();

    }else{
        $sql = $pdo->prepare("UPDATE curso SET nome_cur = ? WHERE idcur = ?");
        $sql->execute(array($curso, $cursoId));
        $_SESSION['success'] = 'Curso atualizado com sucesso!';
        header("Location: ../curso.php");
        exit();
    }
}
?>