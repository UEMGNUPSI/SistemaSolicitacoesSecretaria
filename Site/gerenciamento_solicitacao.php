<?php
session_start();

include('dao/conexao.php');
require_once("dao/verificacao_login.php");
require_once("dao/verifica_adm.php"); 

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

// Limpa o POST (quando usuário pressionar "Filtrar")
$post_array = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

// Verifica se filtro foi definido
$curso_id = isset($_GET['curso']) ? trim($_GET['curso']) : "";
if ($curso_id === "") {
    $curso_id = null;
}

/* Paginação da tabela: */

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10; // DEFINE A QUANTIDADE DE REGISTROS POR PÁGINA DE TABELA.
$offset = ($page - 1) * $records_per_page;

// Construir a cláusula WHERE
$where = "WHERE 1=1"; // Inicia com uma condição verdadeira
if (!empty($curso_id) && $curso_id !== "todas") {
    $where .= " AND s.nome_curso_sol = :curso_id";
}

// Obter o total de registros
$total_sql = $pdo->prepare("SELECT COUNT(*) FROM solicitacao s INNER JOIN aluno ON s.aluno_idalu = aluno.idalu {$where}");
if (!empty($curso_id) && $curso_id !== "todas") {
    $total_sql->bindValue(':curso_id', $curso_id);
}
$total_sql->execute();
$total_records = $total_sql->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

// Consulta para obter as solicitações com resposta e justificativa do coordenador
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
    {$where} 
    ORDER BY s.idsol DESC 
    LIMIT :limit OFFSET :offset
");

if (!empty($curso_id) && $curso_id !== "todas") {
    $sql->bindValue(':curso_id', $curso_id);
}
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
                <h1 id="h1-header">Visualização de Solicitações</h1>
            </header>
            <main class="container">
                <!-- Formulário de Filtro -->
                <form class="form-horizontal" action="gerenciamento_solicitacao.php" method="get">
                    <div class="row">
                        <div class=" col">
                            <div class="controls"  style="display: flex; align-items: center; gap: 10px;">
                            <label for="filtrocurso" class="form-label" style="margin-bottom: 0px;">Curso:</label>
                            <select class="form-select" name="curso" id="filtrocurso" required>
                                    <option value="" disabled <?= $curso_id === null ? 'selected' : '' ?>>Selecione o curso</option>
                                    <option value="todas" <?= $curso_id === "todas" ? 'selected' : '' ?>>Todos</option>
                                    <?php
                                        $curso_sql = $pdo->prepare("SELECT * FROM curso ORDER BY nome_cur");
                                        $curso_sql->execute();
                                        $cursos = $curso_sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($cursos as $curso) {
                                            $selected = ($curso['idcur'] == $curso_id) ? 'selected' : '';
                                            echo '<option value="'.$curso['idcur'].'" '.$selected.'>'.$curso['nome_cur'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary" title="Filtrar" style="background-color: #46697F;"><i class="fa-solid fa-magnifying-glass" style="color: #FFF; width: 20px; height: 20px;"></i></button>
                        </div>
                    </div>
                    <br/>
                </form>
    
                <div class="table-wrapper">
                    <div class="row table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 15%;">Id Solicitação</th>
                                    <th scope="col" style="width: 20%;">Nome do Solicitante</th>
                                    <th scope="col" style="width: 20%;">Solicitação</th>
                                    <th scope="col" style="width: 20%;">Data da Solicitação</th>
                                    <th scope="col" style="width: 50%;">Status da Solicitação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if (count($res) == 0){
                                        echo "<p>Nenhuma solicitação</p>";
                                    }else{
                                        foreach ($res as $value) {
                                            echo '<tr>';
                                            echo '<th scope="row">' . htmlspecialchars($value['idsol']) . '</th>';
                                            echo '<td>' . htmlspecialchars($value['nome_alu']) . '</td>';
                                            echo '<td>' . htmlspecialchars($value['tipo_sol']) . '</td>';
                                            echo '<td>' . htmlspecialchars(date('d/m/Y', strtotime($value['data_sol']))) . '</td>';
                                            echo '<td>' . htmlspecialchars($value['status_sol']) . '</td>';
                                            echo '<td width=250>';
                                            echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modal-atualizar' 
                                                    title='Visualizar solicitação " . $value['idsol'] . "' 
                                                    data-bs-id='" . $value['idsol'] . "' 
                                                    data-bs-curso='" . $value['nome_curso_sol'] . "' 
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
                                    } 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Paginação -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php
                            if ($total_pages > 1) {
                                // Ícone de anterior
                                if ($page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?page='.($page - 1).'&curso='.$curso_id.'" aria-label="Anterior"><span aria-hidden="true">&laquo;</span></a></li>';
                                }

                                for ($i = 1; $i <= $total_pages; $i++) {
                                    echo '<li class="page-item"><a class="page-link" href="?page='.$i.'&curso='.$curso_id.'">'.$i.'</a></li>';
                                }

                                // Ícone de próximo
                                if ($page < $total_pages) {
                                    echo '<li class="page-item"><a class="page-link" href="?page='.($page + 1).'&curso='.$curso_id.'" aria-label="Próximo"><span aria-hidden="true">&raquo;</span></a></li>';
                                }
                            } else {
                                echo '<li class="page-item"><a class="page-link" href="?page=1 &curso='.$curso_id.'">1</a></li>';
                            }
                        ?>
                    </ul>
                </nav>
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
                                <input class="form-control" type="text" name="solicitacaoTipo" id="solicitacaoTipo" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="solicitacaoSolicitacao" class="form-label">Solicitação do aluno:</label>
                                <textarea class="form-control" id="solicitacaoSolicitacao" placeholder="Informe a justificativa" maxlength="255" name="solicitacaoSolicitacao" required disabled></textarea>                            
                            </div>
                            
                            <div class="mb-3">
                                <label for="solicitacaoJustificativa" class="form-label">Justificativa do aluno:</label>
                                <textarea class="form-control" id="solicitacaoInputAdicionar" placeholder="Informe a justificativa" maxlength="255" name="solicitacaoJustificativa" required disabled></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="solicitacaoCurso" class="form-label">Curso:</label>
                                <select class="form-control" name="solicitacaoCurso" id="solicitacaoCurso" aria-label="Default select example" disabled>
                                    <option selected disabled>Selecione o curso</option>
                                    <?php
                                        $sql = $pdo->prepare("SELECT * FROM curso");
                                        $sql->execute();
                                        $info = $sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($info as $key => $value){
                                            echo '<option value='.$value['idcur'].'>'.$value['nome_cur'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="solicitacaoArquivo" class="form-label">Arquivos Comprovantes:</label>
                                <div id="arquivosComprovantes">
                                    <!-- Aqui vão os links gerados dinamicamente para os arquivos -->
                                </div>
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

    // modalAtualizar.addEventListener('show.bs.modal', function (event) {
    //     var button = event.relatedTarget; // Botão que acionou o modal
    //     var anexos = button.getAttribute('data-bs-anexo'); // Pega os anexos

    //     // Campo para exibir os anexos no modal
    //     var anexosContainer = modalAtualizar.querySelector('.anexos-container');
    //     anexosContainer.innerHTML = ''; // Limpa os anexos anteriores

    //     // Separa os anexos (caso haja mais de um, separados por vírgula)
    //     var anexosArray = anexos.split(',');

    //     // Itera sobre os anexos e cria links ou imagens para cada anexo
    //     anexosArray.forEach(function(anexo) {
    //         anexo = anexo.trim(); // Remove espaços em branco
    //         var ext = anexo.split('.').pop().toLowerCase(); // Obtém a extensão do arquivo

    //         // Cria um link ou uma imagem dependendo do tipo de arquivo
    //         if (ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif') {
    //             anexosContainer.innerHTML += '<img src="uploads/' + anexo + '" style="max-width: 100%; height: auto;" alt="Anexo">';
    //         } else {
    //             anexosContainer.innerHTML += '<a href="uploads/' + anexo + '" target="_blank">' + 'Visualizar PDF' + '</a><br>';
    //         }
    //     });
    // });

    document.addEventListener('DOMContentLoaded', function () {
        var modalAtualizar = document.getElementById('modal-atualizar');
        
        modalAtualizar.addEventListener('show.bs.modal', function (event) {
            // Botão que acionou o modal
            var button = event.relatedTarget;

            // Extrai os dados do atributo data-* dos botões
            var idsol = button.getAttribute('data-bs-id');
            var tipo = button.getAttribute('data-bs-tipo');
            var solicitacao = button.getAttribute('data-bs-sol');
            var justificativa = button.getAttribute('data-bs-just');
            var curso = button.getAttribute('data-bs-curso');
            var anexo = button.getAttribute('data-bs-anexo');
            var status = button.getAttribute('data-bs-status');
            var justificativa_coordenador = button.getAttribute('data-bs-justificativa-coordenador'); 
            var respostaAnalise = button.getAttribute('data-bs-resposta-coordenador');


            // Atualiza os campos do modal com os dados recebidos
            var modalBodyInputId = modalAtualizar.querySelector('#idsol');
            var modalBodyInputTipo = modalAtualizar.querySelector('#solicitacaoTipo');
            var modalBodyInputSolicitacao = modalAtualizar.querySelector('#solicitacaoSolicitacao');
            var modalBodyInputJustificativa = modalAtualizar.querySelector('#solicitacaoInputAdicionar');
            var modalBodyInputCurso = modalAtualizar.querySelector('#solicitacaoCurso');
            var arquivosComprovantesDiv = document.getElementById('arquivosComprovantes');
            
           

            modalBodyInputId.value = idsol;
            modalBodyInputTipo.value = tipo;
            modalBodyInputSolicitacao.value = solicitacao;
            modalBodyInputJustificativa.value = justificativa;
            

        
            // Seleciona o curso correto no dropdown
            if (curso) {
                modalBodyInputCurso.value = curso;
            }

            arquivosComprovantesDiv.innerHTML = '';


            const arquivos = anexo.split(',');
            arquivos.forEach(function (arquivo) {
                const link = document.createElement('a');
                link.href = 'uploads/' + arquivo.trim(); 
                link.target = '_blank'; 
                link.classList.add('btn', 'btn-secondary', 'me-2');
                link.textContent = 'Abrir ' + arquivo.trim(); 
                arquivosComprovantesDiv.appendChild(link); 
            });

            var modalBodyJustificativaCoordenador = modalAtualizar.querySelector('#justificativa-coordenador');
            console.log(modalBodyJustificativaCoordenador);
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
    });
</script>

</body>
</html>