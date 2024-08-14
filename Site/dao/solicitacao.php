<?php
require_once("conexao.php");
session_start(); 
$idUsuario = $_SESSION['id-usuario'];
$cursoUsuario = $_SESSION['curso-usuario'];
$solicitacao = trim($_POST['solicitacaoSolicitacao']);
$justificativa = trim($_POST['solicitacaoJustificativa']);
$arquivo = trim($_POST['solicitacaoArquivo']);
$sol_curso = trim($_POST['solicitacaoCurso']);
$periodo = trim($_POST['solicitacaoPeriodo']);
$sol_tipo = trim($_POST['solicitacaoTipo']);

if (isset($_POST['adicionar-solicitacao'])){
    $sql = $pdo->prepare("INSERT INTO solicitacao VALUES (null,?,?,?,?,1,?,?,?,?)");
    $sql->execute(array($sol_curso,$periodo,$solicitacao,$justificativa,$arquivo,$sol_tipo,$cursoUsuario,$idUsuario));
    $_SESSION['success'] = 'Solicitação Enviada!';
    header("Location: ../solicitacao_aluno.php");
    exit();

    }
?>