<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once("dao/verificacao_login.php");
require_once("dao/verifica_adm.php");
include('dao/conexao.php');


$solicitante = $_POST['solicitante'];
$tipo_sol = $_POST['solicitacaoTipo'];
$data_sol = htmlspecialchars(date('d/m/Y', strtotime($_POST['datasol'])));
$alunoId = $_POST['alunoId'];
$solicitacaoAluno = $_POST['solicitacaoSolicitacao'];
$justificativaAluno = $_POST['solicitacaoJustificativa'];
$curso_sol = $_POST['cursoSolicitado'];

$sql = $pdo->prepare("SELECT nome_cur FROM curso WHERE idcur = ?");
$sql->execute(array($curso_sol));
$info = $sql->fetch();

$curso_solicitado = $info["nome_cur"];

$sql = $pdo->prepare("SELECT ra_alu FROM aluno WHERE idalu = ?");
$sql->execute(array($alunoId));
$info = $sql->fetch();

$alunoRA = $info['ra_alu'];








$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML("<h1>Solicitações Academicas UEMG</h1>
                        <h2><strong>Solicitante: </strong>$solicitante - RA: $alunoRA</h2>
                        <h3>Data: $data_sol</h3>
                        <p><b>Tipo de solicitação: </b>$tipo_sol</p>
                        <p><b>Curso a qual se destina: </b>$curso_solicitado</p>
                        <p><b>Solicitação do Aluno: </b>$solicitacaoAluno</p>
                        <p><b>Justificativa do Aluno: </b>$justificativaAluno</p>
                        
");

$mpdf->Output();

?>