<?php
session_start();

include('dao/conexao.php');
require_once("dao/verificacao_login.php");

// Verifica se sessões foram setadas antes de entrar nesta página
if (isset($_SESSION['success'])) {
    echo "<script>alert('" . $_SESSION['success'] . "');</script>";
    unset($_SESSION['success']);
} else if (isset($_SESSION['error'])) {
    echo "<script>alert('" . $_SESSION['error'] . "');</script>";
    unset($_SESSION['error']);
} else if (isset($_SESSION["duplicated"])) {
    echo "<script>alert('" . $_SESSION['duplicated'] . "');</script>";
    unset($_SESSION['duplicated']);
}

// Captura o filtro e limpa a entrada
$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : "";

// Paginação
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 10; // Define a quantidade de registros por página
$offset = ($page - 1) * $records_per_page;

// Recuperar o ID do coordenador logado
$coordenador_id = $_SESSION['id-usuario'];
if (!$coordenador_id) {
    die("Erro: Coordenador não autenticado.");
}

// Construir a cláusula WHERE com prioridades corretas
$where = "WHERE e.coordenador_idcrd = :coordenador_id AND (s.status_sol = 'Analisado' OR s.status_sol = 'Concluído')";

if (!empty($filtro)) {
    $where .= " AND aluno.nome_alu LIKE :aluno_nome";
}

// Obter o total de registros
$total_sql = $pdo->prepare("
    SELECT COUNT(*)
    FROM solicitacao s
    INNER JOIN aluno ON s.aluno_idalu = aluno.idalu
    LEFT JOIN encaminhamento e ON s.idsol = e.solicitacao_idsol
    {$where}
");

$total_sql->bindValue(':coordenador_id', $coordenador_id, PDO::PARAM_INT);

if (!empty($filtro)) {
    $total_sql->bindValue(':aluno_nome', "%{$filtro}%", PDO::PARAM_STR);
}

$total_sql->execute();
$total_records = $total_sql->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

// Consulta para obter as solicitações com os filtros
$sql = $pdo->prepare("
    SELECT
        s.*,
        aluno.nome_alu,
        aluno.ra_alu,
        curso.nome_cur,
        a.justificativa_ana AS justificativa_coordenador,
        a.resultado_ana AS resultado_analise
    FROM solicitacao s
    INNER JOIN aluno ON s.aluno_idalu = aluno.idalu
    LEFT JOIN curso ON s.nome_curso_sol = curso.idcur
    LEFT JOIN encaminhamento e ON s.idsol = e.solicitacao_idsol
    LEFT JOIN analise a ON e.idenc = a.encaminhamento_idenc
    {$where}
    ORDER BY
        CASE
            WHEN s.status_sol = 'Analisado' THEN 1
            WHEN s.status_sol = 'Concluído' THEN 2
            ELSE 3
        END,
        s.idsol DESC
    LIMIT :limit OFFSET :offset
");

$sql->bindValue(':coordenador_id', $coordenador_id, PDO::PARAM_INT);

if (!empty($filtro)) {
    $sql->bindValue(':aluno_nome', "%{$filtro}%", PDO::PARAM_STR);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .campos-modal-consulta-solicitacao {
            display: flex;
            flex-direction: column;
        }

        .status-concluido {
            color: green !important;
            font-weight: bold;
        }

        .status-em-aberto {
            color: red !important;
            font-weight: bold;
        }

        .status-em-analise {
            color: orange !important;
            font-weight: bold;
        }

        .status-Analisado {
            color: blue !important;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <?php include_once("sidebar.php"); ?>

    <!-- Cabeçalho -->
    <div class="right-content">
        <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
            <h1 id="h1-header">Visualização de Solicitações</h1>
        </header>
        <main class="container">
            <!-- Formulário de Filtro -->
            <form class="form-horizontal" action="finalizados.php" method="get">
                <div class="row">
                    <!-- Filtro por Curso -->
                    <div class="col">
                        <div class="controls">
                            <input size="20" class="form-control" name="filtro" type="text"
                                placeholder="Nome (Ex: Fabiano)" value="<?= $filtro ?? "" ?>">
                        </div>
                    </div>
                    <div class="col">                               
                        <button type="submit" class="btn btn-primary" title="Filtrar" style="background-color: #46697F;">
                            <i class="fa-solid fa-magnifying-glass" style="color: #FFF; width: 20px; height: 20px;"></i>
                        </button>
                        <a href="finalizados.php" class="btn btn-secondary">Limpar filtro</a>
                    </div>
                 
            
                </div>
            </form>

            <div class="table-wrapper">
                <div class="row table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 15%;">ID</th>
                                <th scope="col" style="width: 20%;">Nome Solicitante</th>
                                <th scope="col" style="width: 20%;">Solicitação</th>
                                <th scope="col" style="width: 20%;">Data Solicitação</th>
                                <th scope="col" style="width: 50%;">Status Solicitação</th>
                                <th scope="col" style="width: 50%;">Visualizar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (count($res) == 0) {
                                echo "
                                    <tr>
                                        <td><b>Nenhuma solicitação</b></td>
                                    </tr>";
                            } else {
                                foreach ($res as $value) {

                                    $status_class = '';
                                    if ($value['status_sol'] === 'Concluído') {
                                        $status_class = 'status-concluido';
                                    } elseif ($value['status_sol'] === 'em aberto') {
                                        $status_class = 'status-em-aberto';
                                    } elseif ($value['status_sol'] === 'em análise') {
                                        $status_class = 'status-em-analise';
                                    } elseif ($value['status_sol'] === 'Analisado') {
                                        $status_class = 'status-Analisado';
                                    }

                                    echo '<tr>';
                                    echo '<th scope="row">' . htmlspecialchars($value['idsol']) . '</th>';
                                    echo '<td>' . htmlspecialchars($value['nome_alu']) . '</td>';
                                    echo '<td>' . htmlspecialchars($value['tipo_sol']) . '</td>';
                                    echo '<td>' . htmlspecialchars(date('d/m/Y', strtotime($value['data_sol']))) . '</td>';
                                    echo '<td class="' . $status_class . '">' . htmlspecialchars($value['status_sol']) . '</td>';
                                    echo '<td width=250>';
                                    echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modal-atualizar' 
                                                    title='Visualizar solicitação " . $value['idsol'] . "' 
                                                    data-bs-id='" . $value['idsol'] . "' 
                                                    data-bs-solicitante='" . $value['nome_alu'] . "'
                                                    data-bs-raAluno='" . $value['ra_alu'] . "'
                                                    data-bs-datasol='" . $value['data_sol'] . "' 
                                                    data-bs-curso='" . $value['nome_curso_sol'] . "' 
                                                    data-bs-alunoId='" . $value['aluno_idalu'] . "' 
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
                            echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '&filtro='.$filtro.'" aria-label="Anterior"><span aria-hidden="true">&laquo;</span></a></li>';
                        }

                        for ($i = 1; $i <= $total_pages; $i++) {
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '&filtro='.$filtro.'">' . $i . '</a></li>';
                        }

                        // Ícone de próximo
                        if ($page < $total_pages) {
                            echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '&filtro='.$filtro.'" aria-label="Próximo"><span aria-hidden="true">&raquo;</span></a></li>';
                        }
                    } else {
                        echo '<li class="page-item"><a class="page-link" href="?page=1 &filtro='.$filtro.'">1</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </main>
    </div>

    <div class="modal fade" id="modal-atualizar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" method="POST">
                        <input type="hidden" id="idsol" name="idsol">
                        <input type="hidden" id="nomesolicitante" name="solicitante">
                        <input type="hidden" id="datasol" name="datasol">
                        <input type="hidden" id="cursoSolicitado" name="cursoSolicitado">
                        <input type="hidden" id="alunoId" name="alunoId">
                        <div class="campos-modal-consulta-solicitacao">
                            <div class="mb-3">
                                <b></b>
                                <div id="resposta-analise" class="mb-3"></div></b>
                                <b></b>
                                <div id="justificativa-coordenador"></div><b></b>
                            </div>
                            <div class="mb-3">
                                <label for="solicitacaoTipo" class="form-label">Tipo de Solicitação:</label>
                                <input class="form-control" type="text" name="solicitacaoTipo" id="solicitacaoTipo"
                                    readonly>
                            </div>
                            <div class="mb-3">
                                <label for="solicitacaoSolicitacao" class="form-label">Solicitação do aluno:</label>
                                <textarea class="form-control" id="solicitacaoSolicitacao"
                                    placeholder="Informe a justificativa" maxlength="255" name="solicitacaoSolicitacao"
                                    required readonly></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="solicitacaoJustificativa" class="form-label">Justificativa do aluno:</label>
                                <textarea class="form-control" id="solicitacaoInputAdicionar"
                                    placeholder="Informe a justificativa" maxlength="255"
                                    name="solicitacaoJustificativa" required readonly></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="solicitacaoCurso" class="form-label">Curso a qual se destina:</label>
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
                                <label for="solicitacaoArquivo" class="form-label">Arquivos Comprovantes:</label>
                                <div id="arquivosComprovantes">
                                    <!-- Aqui vão os links gerados dinamicamente para os arquivos -->
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer" style="display: flex; justify-content: space-between;">
                    <div style="display: flex; gap: 10px">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
                </form>
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
    <script>
        function marcarconcluido() {
            const form = document.getElementById('updateForm');
            form.action = "requerimento.php";
            form.target = "_blank";
            form.submit();
        }

        var modalAtualizar = document.getElementById('modal-atualizar');

        document.addEventListener('DOMContentLoaded', function () {
            var modalAtualizar = document.getElementById('modal-atualizar');
            modalAtualizar.addEventListener('show.bs.modal', function (event) {
                // Botão que acionou o modal
                var button = event.relatedTarget;

                // Extrai os dados do atributo data-* dos botões
                var idsol = button.getAttribute('data-bs-id');
                var solicitante = button.getAttribute('data-bs-solicitante');
                var raAluno = button.getAttribute('data-bs-raAluno');
                var datasol = button.getAttribute('data-bs-datasol');
                var alunoId = button.getAttribute('data-bs-alunoId');
                var tipo = button.getAttribute('data-bs-tipo');
                var solicitacao = button.getAttribute('data-bs-sol');
                var justificativa = button.getAttribute('data-bs-just');
                var curso = button.getAttribute('data-bs-curso');
                var anexo = button.getAttribute('data-bs-anexo');
                var status = button.getAttribute('data-bs-status');
                var justificativa_coordenador = button.getAttribute('data-bs-justificativa-coordenador');
                var respostaAnalise = button.getAttribute('data-bs-resposta-coordenador');

                // Atualiza os campos do modal com os dados recebidos
                var modalTitle = document.getElementById("staticBackdropLabel");
                var modalBodyInputId = modalAtualizar.querySelector('#idsol');
                var modalBodyInputsolicitante = modalAtualizar.querySelector('#nomesolicitante');
                var modalBodyInputdatasol = modalAtualizar.querySelector('#datasol');
                var modalBodyInputHiddenCursoSolicitado = modalAtualizar.querySelector('#cursoSolicitado');
                var modalBodyInputAlunoId = modalAtualizar.querySelector('#alunoId');
                var modalBodyInputTipo = modalAtualizar.querySelector('#solicitacaoTipo');
                var modalBodyInputSolicitacao = modalAtualizar.querySelector('#solicitacaoSolicitacao');
                var modalBodyInputJustificativa = modalAtualizar.querySelector('#solicitacaoInputAdicionar');
                var modalBodyInputCurso = modalAtualizar.querySelector('#solicitacaoCurso');
                var arquivosComprovantesDiv = document.getElementById('arquivosComprovantes');


                modalTitle.innerHTML = "<b>Solicitante: " + solicitante + " - RA: " + raAluno + "</b>";
                modalBodyInputId.value = idsol;
                modalBodyInputsolicitante.value = solicitante;
                modalBodyInputdatasol.value = datasol;
                modalBodyInputHiddenCursoSolicitado.value = curso;
                modalBodyInputAlunoId.value = alunoId;
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
                if (status === 'Analisado' || status == "Concluído" && justificativa_coordenador) {
                    modalBodyJustificativaCoordenador.innerHTML = '<strong>Justificativa do Colegiado:</strong> ' + justificativa_coordenador;
                } else {
                    modalBodyJustificativaCoordenador.innerHTML = '';
                }

                var modalBodyRespostaAnalise = modalAtualizar.querySelector('#resposta-analise');
                if (status === 'Analisado' || status == "Concluído" && respostaAnalise) {
                    modalBodyRespostaAnalise.innerHTML = '<strong>Resposta do Colegiado:</strong> ' + respostaAnalise;
                } else {
                    modalBodyRespostaAnalise.innerHTML = '';
                }

                // Seleciona o curso correto no dropdown
                if (curso) {
                    modalBodyInputCurso.value = curso;
                }
            });
        });
    </script>
</body>
</html>