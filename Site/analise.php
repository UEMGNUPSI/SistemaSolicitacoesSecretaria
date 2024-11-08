<?php

session_start();

require_once("dao/verificacao_login.php");
require_once("dao/conexao.php");

if ($_SESSION['tipo-usuario'] != "coordenador"){
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['success'])) {
    echo "<script>alert('" . $_SESSION['success'] . "');</script>";
    unset($_SESSION['success']);
} else if (isset($_SESSION['error'])) {
    echo "<script>alert('" . $_SESSION['error'] . "');</script>";
    unset($_SESSION['error']);
}

$id_crd = $_SESSION['id-usuario'];

$stmt = $pdo->prepare("
    SELECT e.*, s.*, a.nome_alu 
    FROM encaminhamento AS e 
    INNER JOIN solicitacao AS s ON e.solicitacao_idsol = s.idsol 
    INNER JOIN aluno AS a ON s.aluno_idalu = a.idalu  -- Usando 'idalu' da tabela 'solicitacao' para fazer o JOIN
    WHERE e.coordenador_idcrd = :id_crd AND s.status_sol = 'em análise'
");
$stmt->bindParam(':id_crd', $id_crd);
$stmt->execute();
$numero_resultados = $stmt->rowCount();
$encaminhamento = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise</title>
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
            <h1 id="h1-header">Análise</h1>
        </header>

        <div class="container mt-5">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header" style="background-color: #46697F; color: white">
                            <h4>Solicitações</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-group" id="solicitacoesList">
                                <?php
                                if ($numero_resultados == 0){
                                    echo '<p>Não há nenhuma solicitação pendente.</p>';
                                }
                                foreach ($encaminhamento as $row => $value) {
                                    // Formata a data da solicitação
                                    $dataSolicitacao = date('d/m/Y', strtotime($value['data_sol'])); // Ajuste o nome do campo conforme necessário
                                    echo '<div class="d-flex justify-content-between align-items-center" style="margin: 5px;">';
                                    echo '<span>' . htmlspecialchars($value['nome_alu']) . ' - ' . htmlspecialchars($value['tipo_sol']) . ' - ' . $dataSolicitacao . '</span>';
                                    echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modal-atualizar' title='Atualizar " . $value['idsol'] . "' data-bs-id='" . $value['idsol'] . "' data-bs-curso='" . $value['nome_curso_sol'] . "' data-bs-sol='" . $value['solicitacao'] . "' data-bs-just='" . $value['justificativa_sol'] . "' data-bs-anexo='" . $value['anexo_sol'] . "' data-bs-tipo='" . $value['tipo_sol'] . "' style='background-color: #46697F;width: 30%;'>Analisar</button>";
                                    echo '</div><hr>';
                                }

                                ?>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-atualizar" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Atualizar Aluno</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="updateForm" action="dao/analise.php" method="POST">
                                <input type="hidden" id="idsol" name="idsol">
                                <!-- !Importante!  input invisível apenas para enviar o Id do curso no formulario -->
                                <div class="campos-modal-consulta-solicitacao">
                                    <div class="mb-3">
                                        <label for="solicitacaoTipo" class="form-label">Tipo de Solicitação:</label>
                                        <input class="form-control" type="text" name="solicitacaoTipo"
                                            id="solicitacaoTipo" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="solicitacaoSolicitacao" class="form-label">Solicitação do
                                            aluno:</label>
                                        <textarea class="form-control" id="solicitacaoSolicitacao"
                                            placeholder="Informe a justificativa" maxlength="255"
                                            name="solicitacaoSolicitacao" required disabled></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="solicitacaoJustificativa" class="form-label">Justificativa do
                                            aluno:</label>
                                        <textarea class="form-control" id="solicitacaoInputAdicionar"
                                            placeholder="Informe a justificativa" maxlength="255"
                                            name="solicitacaoJustificativa" required disabled></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="solicitacaoArquivo" class="form-label">Arquivos Comprovantes:</label>
                                        <div id="arquivosComprovantes">
                                            <!-- Aqui vão os links gerados dinamicamente para os arquivos -->
                                        </div>
                                    </div>


                                    <div class="mb-3">
                                        <label for="solicitacaoCurso" class="form-label">Curso:</label>
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
                                        <label for="AnaliseResposta" class="form-label">Resposta da Solicitação:</label>
                                        <select class="form-select" name="AnaliseResposta" id="AnaliseResposta"
                                            aria-label="Default select example" required>
                                            <option value="" selected disabled>Selecione a Resposta: </option>
                                            <option value="Deferido">Deferido</option>
                                            <option value="Indeferido">Indeferido</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="AnaliseJustificativa" class="form-label">Justificativa:</label>
                                        <input type="text" class="form-control" id="AnaliseJustificativa"
                                            name="AnaliseJustificativa" multiple="multiple" required>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Você realmente deseja enviar a resposta?');">Enviar</button>
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
                const solicitacaoCurso = button.getAttribute('data-bs-curso');
                const solicitacaoSolicitacao = button.getAttribute('data-bs-sol');
                const solicitacaoJustificativa = button.getAttribute('data-bs-just');
                const solicitacaoAnexo = button.getAttribute('data-bs-anexo');
                const solicitacaoTipo = button.getAttribute('data-bs-tipo');

                const idsolInput = document.getElementById('idsol');
                const solicitacaoCursoSelect = document.getElementById('solicitacaoCurso');
                const solicitacaoSolicitacaoInput = document.getElementById('solicitacaoSolicitacao');
                const solicitacaoJustificativaInput = document.getElementById('solicitacaoInputAdicionar');
                const solicitacaoTipoSelect = document.getElementById('solicitacaoTipo');
                const arquivosComprovantesDiv = document.getElementById('arquivosComprovantes');

                idsolInput.value = solicitacaoId;
                solicitacaoCursoSelect.value = solicitacaoCurso;
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