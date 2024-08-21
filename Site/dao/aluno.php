<?php
require_once("conexao.php");
session_start(); 

$nome = trim($_POST['alunoNome']);
$cpf = trim($_POST['alunoCpf']);
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

    if (empty($nome)){
        $_SESSION['error'] = 'Valor inválido!'; 
        header("Location: ../gerenciamento_aluno.php"); 
        exit();

    }else if($sql->rowCount() > 0){ 
        $_SESSION['duplicated'] = 'O aluno '.$nome.' já está cadastrado!';
        header("Location: ../gerenciamento_aluno.php");
        exit();

    }else{
        $sql = $pdo->prepare("INSERT INTO aluno VALUES (null, ?,?,?,?,?,?,1,?,?,?)");
        $sql->execute(array($nome,$cpf,$ra,$email,$celular,$turno,$senha,$curso,$tipo));
        $_SESSION['success'] = 'Aluno ' . $nome.  ' inserido com sucesso!';
        header("Location: ../gerenciamento_aluno.php");
        exit();
    }

// Verifica se o formulário de atualizar curso foi enviado
} else if (isset($_POST["atualizar-aluno"])){
    $status = trim($_POST['alunoStatus']);
    $alunoId = trim($_POST['alunoId']);
    $sql = $pdo->prepare("SELECT * FROM aluno WHERE cpf_alu = ?  AND idalu <> ?");
    $sql->execute(array($cpf, $alunoId));
    $sql->fetchAll(PDO::FETCH_ASSOC);

    if (empty($nome)){
        $_SESSION['error'] = 'Valor inválido!';
        header("Location: ../gerenciamento_aluno.php");
        exit();

    }else if($sql->rowCount() > 0){
        $_SESSION['duplicated'] = 'O aluno (CPF) já está cadastrado! Insira outro nome.';
        header("Location: ../gerenciamento_aluno.php");
        exit();

    }else{
        $sql = $pdo->prepare("UPDATE aluno SET nome_alu = ?, cpf_alu = ?, ra_alu = ?, email_alu = ?, celular_alu = ?, turno_alu = ?, status_alu = ?, senha_alu = ?, curso_idcur = ?, tp_u_idtpu = ? WHERE idalu = ?");
        $sql->execute(array($nome,$cpf,$ra,$email,$celular,$turno,$status,$senha,$curso,$tipo,$alunoId));
        $_SESSION['success'] = 'Aluno atualizado com sucesso!';
        header("Location: ../gerenciamento_aluno.php");
        exit();
    }
}
?>