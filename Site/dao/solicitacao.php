<?php
require_once("conexao.php");
session_start(); 

$idUsuario =  $_SESSION['id-usuario'];
$cursoUsuario = $_SESSION['curso-idcur'];

if (isset($_POST['adicionar-solicitacao'])) {
    $solicitacao = trim($_POST['solicitacaoSolicitacao']);
    $justificativa = trim($_POST['solicitacaoJustificativa']);
    $sol_curso = trim($_POST['solicitacaoCurso']);
    $periodo = trim($_POST['solicitacaoPeriodo']);
    $sol_tipo = trim($_POST['solicitacaoTipo']);

    // Processamento dos arquivos
    if (!empty($_FILES['solicitacaoArquivo'])) {
        $arquivos = $_FILES['solicitacaoArquivo'];
        $nomesArquivos = array();

        foreach ($arquivos['name'] as $key => $nomeArquivo) {
            $tmpName = $arquivos['tmp_name'][$key];
            $error = $arquivos['error'][$key];
            $size = $arquivos['size'][$key];
            $type = $arquivos['type'][$key];

            // Verificar se o arquivo foi enviado sem erros
            if ($error == 0) {
                // Mover o arquivo para uma pasta específica
                $caminho = '../uploads/' . $nomeArquivo;
                move_uploaded_file($tmpName, $caminho);

                // Salvar o nome do arquivo no array
                $nomesArquivos[] = $nomeArquivo;
            }
        }

        // Converter o array de nomes de arquivos para uma string separada por vírgulas
        $nomesArquivosString = implode(',', $nomesArquivos);
    } else {
        $nomesArquivosString = '';
    }

    $sql = $pdo->prepare("INSERT INTO solicitacao VALUES (null,?,?,?,?,1,?,?,?,?)");
    $sql->execute(array($sol_curso,$periodo,$solicitacao,$justificativa,$nomesArquivosString,$sol_tipo,$cursoUsuario,$idUsuario));
    $_SESSION['success'] = 'Solicitação Enviada!';
    header("Location: ../solicitacao_aluno.php");
    exit();
}
?>