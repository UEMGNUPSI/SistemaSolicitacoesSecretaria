<?php

require_once("conexao.php");
session_start(); 

$nome = trim($_POST['alunoNome']);
$cpf = trim($_POST['alunoCpf']);
$dt_nasc = trim($_POST['alunoDt_nasc']);
$ra = trim($_POST['alunoRa']);
$email = trim($_POST['alunoEmail']);
$celular = trim($_POST['alunoCelular']);
$turno = trim($_POST['alunoTurno']);
$senha = password_hash($_POST['alunoSenha'], PASSWORD_DEFAULT);
$curso = trim($_POST['alunoCurso']);


$sql2 = $pdo->prepare("SELECT idtpu FROM tp_u WHERE descricao_tpu = 'aluno'");
$sql2->execute();
$infoId = $sql2->fetchAll(PDO::FETCH_ASSOC);
$tipo = $infoId[0]['idtpu'];

if (isset($_POST['adicionar-aluno'])){
    $sql = $pdo->prepare("SELECT * FROM aluno WHERE cpf_alu = ?");
    $sql->execute(array($cpf));

    $sql->fetchAll(PDO::FETCH_ASSOC); 

    if($sql->rowCount() > 0){ 
        $_SESSION['duplicated'] = 'Cpf já está cadastrado!';
        header("Location: ../index.php");
        exit();
    } else {
        // Insere a data formatada no banco de dados
        $sql = $pdo->prepare("INSERT INTO aluno VALUES (null, ?,?,?,?,?,?,?,1,?,?,?)");
        $sql->execute(array($nome, $cpf, $dt_nasc, $ra, $email, $celular, $turno, $senha, $curso, $tipo));
        $_SESSION['success'] = "Cadastro efetuado com sucesso!";
        header("Location: ../index.php");
        exit();
    }
}
?>
?>