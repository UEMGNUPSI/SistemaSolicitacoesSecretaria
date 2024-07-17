<?php
require_once("conexao.php");
session_start();

$curso = trim($_POST['cursoNome']);


$sql = $pdo->prepare("SELECT * FROM curso WHERE nome = ?");
$sql->execute(array($curso));
$sql->fetchAll(PDO::FETCH_ASSOC);

if (empty($curso)){
    $_SESSION['error'] = 'Valor inválido!';
    header("Location: ../cadastro_curso.php");
    exit();

}else if($sql->rowCount() > 0){
    $_SESSION['duplicated'] = 'O curso '.$curso.' já está cadastrado! Insira outro curso ou atualize o nome do curso já existente.';
    header("Location: ../cadastro_curso.php");
    exit();

}else{
    $sql = $pdo->prepare("INSERT INTO curso VALUES (null, ?)");
    $sql->execute(array($curso));
    $_SESSION['success'] = 'Curso ' . $curso.  ' inserido com sucesso!';
    header("Location: ../cadastro_curso.php");
    exit();
}
?>