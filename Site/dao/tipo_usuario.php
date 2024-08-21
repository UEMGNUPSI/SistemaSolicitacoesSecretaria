<?php
require_once("conexao.php");
session_start();
require_once("verifica_adm.php"); 

$tpu = trim($_POST['idtpu']);

if (isset($_POST['adicionar-tpu'])){
    $sql = $pdo->prepare("SELECT * FROM tp_u WHERE descricao_tpu = ?");
    $sql->execute(array($tpu));
    $sql->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tpu)){
        $_SESSION['error'] = 'Valor inválido!';
        header("Location: ../gerenciamento_tpu.php");
        exit();

    }else if($sql->rowCount() > 0){
        $_SESSION['duplicated'] = 'O Tipo Usuario '.$tpu.' já está cadastrado! Insira outro Tipo de Usuario ou atualize o Tipo de Usuario já existente.';
        header("Location: ../gerenciamento_tpu.php");
        exit();

    }else{
        $sql = $pdo->prepare("INSERT INTO tp_u VALUES (null, ?)");
        $sql->execute(array($tpu));
        $_SESSION['success'] = 'Tipo Usuario ' . $tpu.  ' inserido com sucesso!';
        header("Location: ../gerenciamento_tpu.php");
        exit();
    }
    
} else if (isset($_POST["atualizar-tpu"])){
    $tpuid = $_POST['tipoid'];
    $sql = $pdo->prepare("SELECT * FROM tp_u WHERE descricao_tpu = ?");
    $sql->execute(array($tpu));
    $sql->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tpu)){
        $_SESSION['error'] = 'Valor inválido!';
        header("Location: ../gerenciamento_tpu.php");
        exit();

    }else if($sql->rowCount() > 0){
        $_SESSION['duplicated'] = 'O Tipo Usuario '.$tpu.' já está cadastrado! Insira outro Tipo de Usuario.';
        header("Location: ../gerenciamento_tpu.php");
        exit();

    }else{
        $sql = $pdo->prepare("UPDATE tp_u SET descricao_tpu = ? WHERE idtpu = ?");
        $sql->execute(array($tpu, $tpuid));
        $_SESSION['success'] = 'Tipo de Usuario atualizado com sucesso!';
        header("Location: ../gerenciamento_tpu.php");
        exit();
    }
}
?>