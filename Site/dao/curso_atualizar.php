<?php
require_once("conexao.php");
session_start();

$curso = trim($_POST['cursoNome']);
$cursoId = $_POST['cursoId'];

$sql = $pdo->prepare("SELECT * FROM curso WHERE nome = ?");
$sql->execute(array($curso));
$sql->fetchAll(PDO::FETCH_ASSOC);

if (empty($curso)){
    $_SESSION['error'] = 'Valor inválido!';
    header("Location: ../cadastro_curso.php");
    exit();

}else if($sql->rowCount() > 0){
    $_SESSION['duplicated'] = 'O curso '.$curso.' já está cadastrado! Insira outro nome.';
    header("Location: ../cadastro_curso.php");
    exit();

}else{
    $sql = $pdo->prepare("UPDATE curso SET nome = ? WHERE idcur = ?");
    $sql->execute(array($curso, $cursoId));
    $_SESSION['success'] = 'Curso atualizado com sucesso!';
    header("Location: ../cadastro_curso.php");
    exit();
}
?>