<?php

session_start();

require_once('verificacao_login.php');
require_once('conexao.php');


if (isset($_POST['confirmar-atualizacoes'])){
    $nome = $_POST['Nome'];
    $cpf = trim($_POST['Cpf']);
    $ra = trim($_POST['Ra']);
    $email = trim($_POST['Email']);
    $celular = trim($_POST['Celular']);
    $turno = trim($_POST['Turno']);
    $curso = trim($_POST['Curso']);
    $alunoId =  $_SESSION['id-usuario'];

    $sql = $pdo->prepare("UPDATE aluno SET nome_alu = ?, cpf_alu = ?, ra_alu = ?, email_alu = ?, celular_alu = ?, turno_alu = ?, curso_idcur = ? WHERE idalu = ?");
    $sql->execute(array($nome,$cpf,$ra,$email,$celular,$turno,$curso,$alunoId));
    $_SESSION['success'] = 'Aluno atualizado com sucesso!';
    header("Location: ../dados_aluno.php");
    exit();

}else{
    Header("Location: ../dados_aluno.php");
exit();

}




?>