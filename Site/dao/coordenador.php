<?php
require_once("conexao.php");
session_start(); // Inicia a sessão para guardar valores entre as páginas

// Limpa espaços em branco antes de depois do input e guarda na variável $curso
$coordenador = trim($_POST['coordenadorNome']);
$cpf_crd = trim($_POST['coordenadorCpf']);
$senha_crd = trim($_POST['coordenadorSenha']);
$masp_crd = trim($_POST['coordenadorMasp']);
$curso = trim($_POST['coordenadorCurso']);
$celular = trim($_POST['coodenadorCelular']);




$sql2 = $pdo->prepare("SELECT idtpu FROM tp_u WHERE descricao_tpu = 'Coordenador'");
$sql2->execute();
$infoId = $sql2->fetchAll(PDO::FETCH_ASSOC);
$tipo = $infoId[0]['idtpu'];


// Verifica se o formulário de adicionar Coordenador foi enviado
if (isset($_POST['adicionar-coordenador'])){
    $sql = $pdo->prepare("SELECT * FROM coordenador WHERE cpf_crd = ?");
    $sql->execute(array($coordenador));

    $sql->fetchAll(PDO::FETCH_ASSOC); //Retorna um array com os resultados obtidos através da consulta no banco de dados

    if (empty($coordenador)){
        $_SESSION['error'] = 'Valor inválido!'; //Define a session para retornar uma mensagem na página curso fora do dao.
        header("Location: ../gerenciamento_coordenador.php"); // Retorna para a página Coordenador.
        exit();

    }else if($sql->rowCount() > 0){ // Verifica se já possui algum registro. Se não tiver, o curso digitado será inserido. 
        $_SESSION['duplicated'] = 'O coordenador '.$coordenador.' já está cadastrado! Insira outro coordenador ou atualize o nome do coordenador já existente.';
        header("Location: ../gerenciamento_coordenador.php");
        exit();

    }else{
        $sql = $pdo->prepare("INSERT INTO coordenador VALUES (null, ?, ?, ?, 1, ?, ?, ?, ?)");
        $sql->execute(array($coordenador, $cpf_crd, $senha_crd, $masp_crd, $curso, $tipo, $celular));
        $_SESSION['success'] = 'Coordenador ' . $coordenador.  ' inserido com sucesso!';
        header("Location: ../gerenciamento_coordenador.php");
        exit();
    }

// Verifica se o formulário de atualizar curso foi enviado
} else if (isset($_POST["atualizar-coordenador"])){
    $coordenadorId = $_POST['coordenadorId'];
    $sql = $pdo->prepare("SELECT * FROM coordenador WHERE nome_crd = ?  AND idcrd <> ?");
    $sql->execute(array($coordenador, $coordenadorId));
    $sql->fetchAll(PDO::FETCH_ASSOC);
    $status_crd = $_POST['status'];

    if (empty($coordenador)){
        $_SESSION['error'] = 'Valor inválido!';
        header("Location: ../gerenciamento_coordenador.php");
        exit();

    }else if($sql->rowCount() > 0){
        $_SESSION['duplicated'] = 'O coordenador '.$coordenador.' já está cadastrado! Insira outro nome.';
        header("Location: ../gerenciamento_coordenador.php");
        exit();

    }else{
        $sql = $pdo->prepare("UPDATE coordenador SET nome_crd = ?, cpf_crd = ?, senha_crd = ?, status_crd = ?, masp_crd = ?, curso_idcur = ?, tp_u_idtpu = ? WHERE idcrd = ?");
        $sql->execute(array($coordenador, $cpf_crd, $senha_crd, $status_crd, $masp_crd, $curso, $tipo, $coordenadorId));
        $_SESSION['success'] = 'Coordenador atualizado com sucesso!';
        header("Location: ../gerenciamento_coordenador.php");
        exit();
    }
}
?>