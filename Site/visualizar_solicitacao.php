<?php
session_start();

include('dao/conexao.php');
require_once("dao/verificacao_login.php");

// Verifica se sessões foram setadas antes de entrar nesta página
if (isset($_SESSION['success'])) {
    echo "<script>alert('".$_SESSION['success']."');</script>";
    unset($_SESSION['success']);
} else if (isset($_SESSION['error'])) {
    echo "<script>alert('".$_SESSION['error']."');</script>";
    unset($_SESSION['error']);
} else if (isset($_SESSION["duplicated"])) {
    echo "<script>alert('".$_SESSION['duplicated']."');</script>"; 
    unset($_SESSION['duplicated']);
}

$idUsuario = $_SESSION['id-usuario'];

// Limpa o POST (quando usuário pressionar "Filtrar")
$post_array = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

// Verifica se filtro foi definido
$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : "";
if ($filtro === "") {
    $filtro = null;
}

/* Paginação da tabela: */

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10; // DEFINE A QUANTIDADE DE REGISTROS POR PÁGINA DE TABELA.
$offset = ($page - 1) * $records_per_page;

// Obter o total de registros
// if (isset($filtro)) {
//     $where .= " AND lower(aluno.nome_alu) like '%" . mb_strtolower($filtro) . "%'";
// }
// $total_sql = $pdo->prepare("SELECT COUNT(*) 
//                              FROM analise a 
//                              INNER JOIN encaminhamento e ON a.encaminhamento_idenc = e.idenc 
//                              INNER JOIN solicitacao s ON e.solicitacao_idsol = s.idsol 
//                              INNER JOIN aluno al ON s.aluno_idalu = al.idalu 
//                              WHERE al.idalu = :idalu");
// $total_sql->bindParam(':idalu', $idUsuario, PDO::PARAM_INT);

// $total_sql->execute();
// $total_records = $total_sql->fetchColumn();
// $total_pages = ceil($total_records / $records_per_page);

// $sql = $pdo->prepare("SELECT s.*, aluno.nome_alu FROM solicitacao s INNER JOIN aluno ON s.aluno_idalu = aluno.idalu  ORDER BY idsol LIMIT :limit OFFSET :offset"); 

$sql = $pdo->prepare("
    SELECT s.*, 
           aluno.nome_alu, 
           curso.nome_cur,
           a.justificativa_ana AS justificativa_coordenador,
           a.resultado_ana AS resultado_analise
    FROM solicitacao s 
    INNER JOIN aluno ON s.aluno_idalu = aluno.idalu 
    LEFT JOIN curso ON s.nome_curso_sol = curso.idcur
    LEFT JOIN encaminhamento e ON s.idsol = e.solicitacao_idsol 
    LEFT JOIN analise a ON e.idenc = a.encaminhamento_idenc 
    WHERE aluno.idalu = :idalu 
    ORDER BY s.idsol DESC
    LIMIT :limit OFFSET :offset
");

$sql->bindParam(':idalu', $idUsuario, PDO::PARAM_INT);
$sql->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$sql->bindValue(':offset', $offset, PDO::PARAM_INT);
$sql->execute();     
$res = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualização de Solicitações</title>
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .campos-modal-consulta-solicitacao {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    
    <?php include_once("sidebar.php");?>      

<!-- Cabeçalho -->
        <div class="right-content">
            <header>
                <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
                <h1 id="h1-header">Suas Solicitações</h1>
            </header>
            <main class="container">
                <!-- Formulário de Filtro -->
               <p>Para solicitações concluídas, a resposta e a justificativa do coordenador são exibidas no topo das informações ao clicar em visualizar.</p>
    
                <div class="table-wrapper">
                    <div class="row table-responsive">
                        <table class="table table-striped " >
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 20%;">Id Solicitação</th>
                                    <th scope="col" style="width: 20%;">Nome do Solicitante</th>
                                    <th scope="col" style="width: 20%;">Solicitação</th>
                                    <th scope="col" style="width: 20%;">Data da Solicitação</th>
                                    <th scope="col" style="width: 20%;">Status</th>
                                    <th scope="col" style="width: 50%;">Visualizar</th>
                                   <!-- <th scope="col" style="width: 6%;"></th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($res as $key => $value) {
                                        $aluno_idalu = $value['aluno_idalu'];
                                        $aluno_query = "SELECT nome_alu FROM aluno WHERE idalu = ?";
                                        $aluno_stmt = $pdo->prepare($aluno_query);
                                        $aluno_stmt->execute([$aluno_idalu]);
                                        $aluno_nome = $aluno_stmt->fetchColumn();
                                        
                                        echo '<tr>';
                                        echo '<th scope="row">' . htmlspecialchars($value['idsol']) . '</th>';
                                        echo '<td>' . htmlspecialchars($aluno_nome) . '</td>';
                                        echo '<td>' . htmlspecialchars($value['tipo_sol']) . '</td>';
                                        echo '<td>' . htmlspecialchars(date('d/m/Y', strtotime($value['data_sol']))) . '</td>';
                                        echo '<td>' . htmlspecialchars($value['status_sol']) . '</td>';
                                        echo '<td width=250>';
                                        echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modal-atualizar' 
                                                title='Atualizar " . $value['idsol'] . "' 
                                                data-bs-id='" . $value['idsol'] . "' 
                                                data-bs-curso='" . $value['nome_cur'] . "' 
                                                data-bs-sol='" . $value['solicitacao'] . "' 
                                                data-bs-just='" . $value['justificativa_sol'] . "' 
                                                data-bs-anexo='" . htmlspecialchars($value['anexo_sol']) . "' 
                                                data-bs-tipo='" . $value['tipo_sol'] . "' 
                                                data-bs-status='" . $value['status_sol'] . "' 
                                                data-bs-resposta-coordenador='" . (isset($value['resultado_analise']) ? $value['resultado_analise'] : '') . "'
                                                data-bs-justificativa-coordenador='" . (isset($value['justificativa_coordenador']) ? $value['justificativa_coordenador'] : '') . "'
                                                style='background-color: #46697F; width: 42px; height: 38px;'>
                                                <i class='fa-solid fa-eye'></i> 
                                            </button>
                                        ";
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

    
    <div class="modal fade" id="modal-atualizar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Informações</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/solicitacao.php" method="POST">
                        <input type="hidden" id="idsol" name="idsol"> <!-- !Importante!  input invisível apenas para enviar o Id do curso no formulario -->
                        <div class="campos-modal-consulta-solicitacao">
                            <div class="mb-3">         
                                <b></b><div id="resposta-analise" class="mb-3"></div></b>
                                <b></b><div id="justificativa-coordenador"></div><b></b>
                            </div>
                            <div class="mb-3">
                                <label for="solicitacaoTipo" class="form-label">Tipo de Solicitação:</label>
                                <input class = "form-control" type="text" name="solicitacaoTipo" id="solicitacaoTipo" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="solicitacaoSolicitacao" class="form-label">Solicitação do aluno:</label>
                                <textarea class="form-control" id="solicitacaoSolicitacao" placeholder="Informe a justificativa" maxlength="255" name="solicitacaoSolicitacao" required disabled></textarea>                            
                            </div>
                            
                            <div class="mb-3">
                                <label for="solicitacaoJustificativa" class="form-label">Justificativa do Aluno:</label>
                                <textarea class="form-control" id="solicitacaoInputAdicionar" placeholder="Informe a justificativa" maxlength="255" name="solicitacaoJustificativa" required disabled></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="solicitacaoCurso" class="form-label">Curso a qual se destina:</label>
                                <input type="text" class="form-control" name="solicitacaoCurso" id="solicitacaoCurso" aria-label="Default select example" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="solicitacaoArquivo" class="form-label">Arquivos Comprovantes:</label>
                                <div class="anexos-container"></div> <!-- Este é o contêiner onde os anexos serão exibidos -->
                            </div>
                               
                        </div>                 
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="script/fontawesome.js"></script>
    <script type="text/javascript" src="script/script.js"></script>
    <script>

var modalAtualizar = document.getElementById('modal-atualizar');
modalAtualizar.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // Botão que acionou o modal
    var anexos = button.getAttribute('data-bs-anexo'); // Pega os anexos

    // Campo para exibir os anexos no modal
    var anexosContainer = modalAtualizar.querySelector('.anexos-container');
    anexosContainer.innerHTML = ''; // Limpa os anexos anteriores

    // Separa os anexos (caso haja mais de um, separados por vírgula)
    var anexosArray = anexos.split(',');

    // Itera sobre os anexos e cria links ou imagens para cada anexo
    anexosArray.forEach(function(anexo) {
        anexo = anexo.trim(); // Remove espaços em branco
        var ext = anexo.split('.').pop().toLowerCase(); // Obtém a extensão do arquivo

        // Cria um link ou uma imagem dependendo do tipo de arquivo
        if (ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif') {
            anexosContainer.innerHTML += '<img src="uploads/' + anexo + '" style="max-width: 100%; height: auto;" alt="Anexo">';
        } else {
            anexosContainer.innerHTML += '<a href="uploads/' + anexo + '" target="_blank">' + 'Visualizar PDF' + '</a><br>';
        }
    });

    var idsol = button.getAttribute('data-bs-id');
    var tipo = button.getAttribute('data-bs-tipo');
    var solicitacao = button.getAttribute('data-bs-sol');
    var justificativa = button.getAttribute('data-bs-just');
    var curso = button.getAttribute('data-bs-curso');
    var anexo = button.getAttribute('data-bs-anexo');
    var status = button.getAttribute('data-bs-status');
    var justificativa_coordenador = button.getAttribute('data-bs-justificativa-coordenador'); 
    var respostaAnalise = button.getAttribute('data-bs-resposta-coordenador');


    var modalBodyInputId = modalAtualizar.querySelector('#idsol');
    var modalBodyInputTipo = modalAtualizar.querySelector('#solicitacaoTipo');
    var modalBodyInputSolicitacao = modalAtualizar.querySelector('#solicitacaoSolicitacao');
    var modalBodyInputJustificativa = modalAtualizar.querySelector('#solicitacaoInputAdicionar');
    var modalBodyInputCurso = modalAtualizar.querySelector('#solicitacaoCurso');

    modalBodyInputId.value = idsol;
    modalBodyInputTipo.value = tipo;
    modalBodyInputSolicitacao.value = solicitacao;
    modalBodyInputJustificativa.value = justificativa;
    modalBodyInputCurso.value = curso;

    var modalBodyJustificativaCoordenador = modalAtualizar.querySelector('#justificativa-coordenador');
    if (status === 'Concluído' && justificativa_coordenador) {
        modalBodyJustificativaCoordenador.innerHTML = '<strong>Justificativa do Colegiado:</strong> ' + justificativa_coordenador;
    } else {
        modalBodyJustificativaCoordenador.innerHTML = '';
    }

    var modalBodyRespostaAnalise = modalAtualizar.querySelector('#resposta-analise');
    if (status === 'Concluído' && respostaAnalise) {
        modalBodyRespostaAnalise.innerHTML = '<strong>Resposta do Colegiado:</strong> ' + respostaAnalise;
    } else {
        modalBodyRespostaAnalise.innerHTML = '';
    }
});
    </script>

</body>
</html>