<?php
session_start();

require_once("dao/verificacao_login.php");
require_once("dao/conexao.php");

if ($_SESSION['tipo-usuario'] != "coordenador") {
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['success'])) {
    echo "<script>alert('" . $_SESSION['success'] . "');</script>";
    unset($_SESSION['success']);
} elseif (isset($_SESSION['error'])) {
    echo "<script>alert('" . $_SESSION['error'] . "');</script>";
    unset($_SESSION['error']);
}

$id_crd = $_SESSION['id-usuario'];

// Define o filtro
$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : null;
$nome_aluno = $filtro;

// Paginação da tabela
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10; 
$offset = ($page - 1) * $records_per_page;

// Define a cláusula WHERE para o filtro
$where = "";
if ($filtro) {
    $where = " AND lower(a.nome_alu) LIKE :nome_aluno";
    $filtro = '%' . strtolower($filtro) . '%'; // Adiciona os curingas para o LIKE
}

// Obter o total de registros
$total_sql = $pdo->prepare("
    SELECT COUNT(*) 
    FROM solicitacao s 
    INNER JOIN aluno a ON s.aluno_idalu = a.idalu 
    WHERE s.status_sol = 'em análise' {$where}
");
if ($filtro) {
    $total_sql->bindParam(':nome_aluno', $filtro);
}
$total_sql->execute();
$total_records = $total_sql->fetchColumn();

$total_pages = ($total_records > 0) ? ceil($total_records / $records_per_page) : 1;

// Limitar o número de páginas quando o número de registros for menor que a quantidade de registros por página
if ($total_records < $records_per_page) {
    $total_pages = 1;
}
// Consulta principal
$stmt = $pdo->prepare("
    SELECT e.*, s.*, a.nome_alu, a.ra_alu 
    FROM encaminhamento AS e 
    INNER JOIN solicitacao AS s ON e.solicitacao_idsol = s.idsol 
    INNER JOIN aluno AS a ON s.aluno_idalu = a.idalu
    WHERE e.coordenador_idcrd = :id_crd 
      AND s.status_sol = 'em análise' 
      {$where}
    ORDER BY a.nome_alu
    LIMIT :limit OFFSET :offset
");
$stmt->bindParam(':id_crd', $id_crd);
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

if ($filtro) {
    $stmt->bindParam(':nome_aluno', $filtro);
}
$stmt->execute();


$numero_resultados = $stmt->rowCount();
$encaminhamento = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requerimentos para analisar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        .card {
            margin-bottom: 20px;
        }

        .list-group-item {
            cursor: pointer;
        }

        .list-group-item.selected {
            background-color: lightgray;
        }
    </style>
</head>

<body>

    <?php include_once("sidebar.php"); ?>
    <div class="right-content">
        <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
            <h1 id="h1-header">Requerimentos</h1>
        </header>
        <div class="container mt-5" style="padding: 0px 30px;">
            <div class="card" style="border: none;">
                <div class="card-header" style="background-color: #46697F; color: white; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; padding: 20px">
                    <h4>Solicitações para analisar</h4>
                    <form class="form-horizontal" action="analise.php" method="get" style="margin-bottom: 0px;">
                        <div class="row">
                            <div class="col" style="padding: 0px">
                                <div class="controls">
                                    <input size="50" class="form-control" name="filtro" type="text"
                                        placeholder="Nome Solicitante" value="<?= $nome_aluno ?? "" ?>" style="background-clip: unset;">
                                </div>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary border-primary-subtle" title="Filtrar"
                                   ><i class="fa-solid fa-magnifying-glass"
                                        style="color: #FFF; width: 20px; height: 20px; font-size: 20px;"></i></button>
                                        <a href="analise.php" class="btn btn-secondary border-primary-subtle">Limpar filtro</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        
            <div class="table-wrapper">
                    <div class="row table-responsive">
                        <table class="table table-striped" style="border-collapse: inherit;">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 7%;">ID</th>
                                    <th scope="col" style="width: 40%;">Nome Solicitante</th>
                                    <th scope="col" style="width: 40%;">Tipo Solicitação</th>
                                    <th scope="col" style="width: 20%;">Data Solicitação</th>
                                    <th scope="col" style="width: 20%;">Analisar</th>
                                    <!-- <th scope="col" style="width: 6%;"></th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($numero_resultados == 0){
                                    echo "
                                    <tr>
                                        <td><b>Nenhuma solicitação</b></td>
                                    </tr>";
                                }else{
                                    foreach ($encaminhamento as $key => $value) {
                                        $dataSolicitacao = date('d/m/Y', strtotime($value['data_sol']));
                                        echo '<tr>';
                                        echo '<th scope="row">' . $value['idsol'] . '</th>';
                                        echo '<td>' . $value['nome_alu'] . '</td>';
                                        echo '<td>' . $value['tipo_sol'] . '</td>';
                                        echo '<td>' . $dataSolicitacao . '</td>';
                                        echo '<td width=250>';
                                        echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modal-atualizar' title='Analisar solicitação' data-bs-id='" . $value['idsol'] . "' data-bs-nomeAlu='" . $value['nome_alu'] . "' data-bs-raAlu='" . $value['ra_alu'] . "' data-bs-curso='" . $value['nome_curso_sol'] . "' data-bs-sol='" . $value['solicitacao'] . "' data-bs-just='" . $value['justificativa_sol'] . "' data-bs-anexo='" . $value['anexo_sol'] . "' data-bs-tipo='" . $value['tipo_sol'] . "' data-bs-dataSol='". date('d/m/Y', strtotime($value['data_sol'])) ."' style='background-color: #46697F;'><i class='fa-solid fa-pen' style='color: #FFF; alt='Analisar''></i></button>";
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <nav aria-label="Page navigation" class="pb-5">
                <ul class="pagination justify-content-center">
                    <?php
                    if ($page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '&filtro=' . str_replace('%', '', $filtro) . '">Anterior</a></li>';
                    }
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $active = ($i == $page) ? 'active' : '';
                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '&filtro=' . str_replace('%', '', $filtro) . '">' . $i . '</a></li>';
                    }
                    if ($page < $total_pages) {
                        echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '&filtro=' . str_replace('%', '', $filtro) . '">Próxima</a></li>';
                    }
                    ?>
                </ul>
            </nav>

            <div class="modal fade" id="modal-atualizar" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="updateForm" action="dao/analise.php" method="POST">
                                <input type="hidden" id="idsol" name="idsol">
                                <!-- !Importante!  input invisível apenas para enviar o Id do curso no formulario -->
                                <div class="campos-modal-consulta-solicitacao">
                                    <div class="mb-3">
                                        <label for="solicitacaoData" class="form-label">Data da Solicitação</label>
                                        <input class="form-control" type="text" name=""
                                            id="solicitacaoData" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="solicitacaoTipo" class="form-label">Tipo de Solicitação</label>
                                        <input class="form-control" type="text" name="solicitacaoTipo"
                                            id="solicitacaoTipo" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="solicitacaoSolicitacao" class="form-label">Solicitação do
                                            aluno</label>
                                        <textarea class="form-control" id="solicitacaoSolicitacao"
                                            placeholder="Informe a justificativa" maxlength="255"
                                            name="solicitacaoSolicitacao" required disabled></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="solicitacaoJustificativa" class="form-label">Justificativa do
                                            aluno</label>
                                        <textarea class="form-control" id="solicitacaoInputAdicionar"
                                            placeholder="Informe a justificativa" maxlength="255"
                                            name="solicitacaoJustificativa" required disabled></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="solicitacaoArquivo" class="form-label">Arquivos Comprovantes</label>
                                        <div id="arquivosComprovantes">
                                            <!-- Aqui vão os links gerados dinamicamente para os arquivos -->
                                        </div>
                                    </div>


                                    <div class="mb-3">
                                        <label for="solicitacaoCurso" class="form-label">Curso</label>
                                        <select class="form-control" name="solicitacaoCurso" id="solicitacaoCurso"
                                            aria-label="Default select example" disabled>
                                            <option selected disabled>Selecione o curso</option>
                                            <?php
                                            $sql = $pdo->prepare("SELECT * FROM curso");
                                            $sql->execute();
                                            $info = $sql->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($info as $key => $value) {
                                                echo '<option value=' . $value['idcur'] . '>' . $value['nome_cur'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="AnaliseResposta" class="form-label"><b>Resposta da Solicitação*</b></label>
                                        <select class="form-select" name="AnaliseResposta" id="AnaliseResposta"
                                            aria-label="Default select example" required>
                                            <option value="" selected disabled>Selecione a Resposta: </option>
                                            <option value="Deferido">Deferido</option>
                                            <option value="Indeferido">Indeferido</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="AnaliseJustificativa" class="form-label"><b>Justificativa*</b></label>
                                        <input type="text" class="form-control" id="AnaliseJustificativa"
                                            name="AnaliseJustificativa" multiple="multiple" required>
                                    </div>

                                </div>
                                <div class="modal-footer d-flex justify-content-between" >
                                    <b><p>* Campos obrigatórios</p></b>
                                    <div>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        <button type="submit" class="btn btn-primary" onclick="return confirm('Você realmente deseja enviar a resposta?');">Enviar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.js"
        integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="script/fontawesome.js"></script>
    <script type="text/javascript" src="script/script.js"></script>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            const atualizarButtonModal = document.getElementById('modal-atualizar');

            atualizarButtonModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const solicitacaoId = button.getAttribute('data-bs-id');
                const solicitante = button.getAttribute('data-bs-nomeAlu');
                const raAluno = button.getAttribute('data-bs-raAlu');
                const solicitacaoCurso = button.getAttribute('data-bs-curso');
                const solicitacaoSolicitacao = button.getAttribute('data-bs-sol');
                const solicitacaoJustificativa = button.getAttribute('data-bs-just');
                const solicitacaoAnexo = button.getAttribute('data-bs-anexo');
                const solicitacaoTipo = button.getAttribute('data-bs-tipo');
                const solicitacaoData = button.getAttribute('data-bs-dataSol');

                const modalTitle = document.getElementById('staticBackdropLabel');
                const idsolInput = document.getElementById('idsol');
                const solicitacaoCursoSelect = document.getElementById('solicitacaoCurso');
                const solicitacaoDataInput = document.getElementById('solicitacaoData');
                const solicitacaoSolicitacaoInput = document.getElementById('solicitacaoSolicitacao');
                const solicitacaoJustificativaInput = document.getElementById('solicitacaoInputAdicionar');
                const solicitacaoTipoSelect = document.getElementById('solicitacaoTipo');
                const arquivosComprovantesDiv = document.getElementById('arquivosComprovantes');

                modalTitle.innerHTML = "<b>Solicitante: " + solicitante + " - RA: " + raAluno + "</b>";
                idsolInput.value = solicitacaoId;
                solicitacaoCursoSelect.value = solicitacaoCurso;
                solicitacaoDataInput.value =solicitacaoData;
                solicitacaoSolicitacaoInput.value = solicitacaoSolicitacao;
                solicitacaoJustificativaInput.value = solicitacaoJustificativa;
                solicitacaoTipoSelect.value = solicitacaoTipo;

                arquivosComprovantesDiv.innerHTML = '';

                const arquivos = solicitacaoAnexo.split(',');

                arquivos.forEach(function (arquivo) {
                    const link = document.createElement('a');
                    link.href = 'uploads/' + arquivo.trim(); 
                    link.target = '_blank'; 
                    link.classList.add('btn', 'btn-secondary', 'me-2');
                    link.textContent = 'Abrir ' + arquivo.trim(); 
                    arquivosComprovantesDiv.appendChild(link); 
                });
            });
        });
    </script>
</body>
</html>