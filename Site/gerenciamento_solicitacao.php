<?php
session_start();

include('dao/conexao.php');
require_once("dao/verificacao_login.php");
require_once("dao/verifica_adm.php"); 

//Verifica se sessões foram setadas antes de entrar nesta página (quando envia a atualização de curso, por exemplo, é setado sessões e é redirecionado para esta página)
if (isset($_SESSION['success'])) {
    echo "<script>alert('".$_SESSION['success']."');</script>";
    unset($_SESSION['success']);
}else if (isset($_SESSION['error'])) {
    echo "<script>alert('".$_SESSION['error']."');</script>";
    unset($_SESSION['error']);

}else if (isset($_SESSION["duplicated"])) {
   echo "<script>alert('".$_SESSION['duplicated']."');</script>"; 
   unset($_SESSION['duplicated']);
}

// limpa o POST (quando usuario pressionar "Filtrar")
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
$where = isset($filtro) ? "where lower(aluno.nome_alu) like '%" . mb_strtolower($filtro) . "%'" : "";
$total_sql = $pdo->prepare("SELECT s.*, aluno.nome_alu FROM solicitacao s INNER JOIN aluno ON s.aluno_idalu = aluno.idalu {$where}");
$total_sql->execute();
$total_records = $total_sql->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);



 $sql = $pdo->prepare("SELECT s.*, aluno.nome_alu FROM solicitacao s INNER JOIN aluno ON s.aluno_idalu = aluno.idalu {$where} ORDER BY idsol LIMIT :limit OFFSET :offset"); // em ordem UD
//$sql = $pdo->prepare("SELECT * FROM curso {$where} ORDER BY idcur ASC LIMIT :limit OFFSET :offset"); // em ordem crescente do idcur



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
    <title>Gerenciamento de Solicitações</title>
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
                <h1 id="h1-header">Gerenciamento de Solicitações</h1>
            </header>
            <main class="container">
                <!-- Formulário de Filtro -->
                <form class="form-horizontal" action="gerenciamento_solicitacao.php" method="get">
                    <div class="row">
                        <div class="col">
                            <div class="controls">
                                <input size="20" class="form-control" name="filtro" type="text" placeholder="Nome (Ex: Fabiano)" value="<?= $filtro ?? "" ?>">
                            </div>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary" title="Filtrar" style="background-color: #46697F;"><i class="fa-solid fa-magnifying-glass" style="color: #FFF; width: 20px; height: 20px;"></i></button>
                        </div>
                    </div>
                    <br/>
                </form>
    
                <div class="table-wrapper">
                    <div class="row">
                        <table class="table table-striped " >
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 20%;">Id Solicitação</th>
                                    <th scope="col" style="width: 20%;">Nome do Solicitante</th>
                                    <th scope="col" style="width: 50%;">Solicitação</th>
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
                                        echo '<td width=250>';
                                        echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modal-atualizar' title='Atualizar " . $value['idsol'] . "' data-bs-id='" . $value['idsol'] . "' data-bs-curso='" . $value['nome_curso_sol'] . "' data-bs-sol='" . $value['solicitacao'] . "' data-bs-just='" . $value['justificativa_sol'] . "' data-bs-anexo='" . $value['anexo_sol'] . "' data-bs-tipo='" . $value['tipo_sol'] . "' style='background-color: #46697F; width: 42px; height: 38px;'><i class='fa-solid fa-eye'></i> </button>";
                                        echo '</td>';
                                        echo '</tr>';
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
                                    echo '<li class="page-item"><a class="page-link" href="?page='.($page - 1).'&filtro='.$filtro.'" aria-label="Anterior"><span aria-hidden="true">&laquo;</span></a></li>';
                                }

                                for ($i = 1; $i <= $total_pages; $i++) {
                                    $offset = ($i - 1) * $records_per_page;
                                    $sql = $pdo->prepare("SELECT s.*, aluno.nome_alu FROM solicitacao s INNER JOIN aluno ON s.aluno_idalu = aluno.idalu {$where} ORDER BY idsol LIMIT :limit OFFSET :offset");
                                    $sql->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
                                    $sql->bindValue(':offset', $offset, PDO::PARAM_INT);
                                    $sql->execute();
                                    $res = $sql->fetchAll(PDO::FETCH_ASSOC);
                                    if (count($res) > 0) {
                                        // Exibir a página
                                        echo '<li class="page-item"><a class="page-link" href="?page='.$i.'&filtro='.$filtro.'">'.$i.'</a></li>';
                                    }
                                }

                                // Ícone de próximo
                                if ($page < $total_pages) {
                                    echo '<li class="page-item"><a class="page-link" href="?page='.($page + 1).'&filtro='.$filtro.'" aria-label="Próximo"><span aria-hidden="true">&raquo;</span></a></li>';
                                }
                            } else {
                                echo '<li class="page-item"><a class="page-link" href="?page=1&filtro='.$filtro.'">1</a></li>';
                            }
                        ?>
                    </ul>
                </nav>
            </main>
        </div>

    <!-- MODAL ATUALIZAR CURSO -->
    <div class="modal fade" id="modal-atualizar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Atualizar Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/solicitacao.php" method="POST">
                        <input type="hidden" id="idsol" name="idsol"> <!-- !Importante!  input invisível apenas para enviar o Id do curso no formulario -->
                        <div class="campos-modal-consulta-solicitacao">
                            <div class="mb-3">
                                <label for="solicitacaoTipo" class="form-label">Tipo de Solicitação:</label>
                                <input class = "form-control" type="text" name="solicitacaoTipo" id="solicitacaoTipo" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="solicitacaoSolicitacao" class="form-label">Solicitação:</label>
                                <input type="text" class="form-control" id="solicitacaoSolicitacao" name="solicitacaoSolicitacao" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label for="solicitacaoJustificativa" class="form-label">Justificativa:</label>
                                <input type="text" class="form-control" id="solicitacaoInputAdicionar" placeholder="Insira aqui sua justificativa" maxlength="255" name="solicitacaoJustificativa" disabled>
                            </div>

                            <!-- <div class="mb-3">
                                <label for="solicitacaoArquivo" class="form-label">Arquivos Comprovantes:</label>
                                <input type="text" class="form-control" id="solicitacaoArquivo" name="solicitacaoArquivo[]" multiple="multiple" disabled>
                            </div> -->
                            
                            <div class="mb-3">
                                <label for="solicitacaoCurso" class="form-label">Curso:</label>
                                    <select class="form-select" name="solicitacaoCurso" id="solicitacaoCurso" aria-label="Default select example" disabled>
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
    document.addEventListener('DOMContentLoaded', function () {
            const atualizarButtonModal = document.getElementById('modal-atualizar'); 

            // FUNÇÃO PARA PEGAR BOTÃO DE ACIONAMENTO DO MODAL COM OS ATRIBUTOS PASSADOS A ELE 
            atualizarButtonModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const solicitacaoId = button.getAttribute('data-bs-id'); 
                const solicitacaoCurso = button.getAttribute('data-bs-curso'); 
                const solicitacaoSolicitacao = button.getAttribute('data-bs-sol'); 
                const solicitacaoJustificativa = button.getAttribute('data-bs-just'); 
                // const solicitacaoAnexo = button.getAttribute('data-bs-anexo'); 
                const solicitacaoTipo = button.getAttribute('data-bs-tipo'); 

                const idsolInput = document.getElementById('idsol'); 
                const solicitacaoCursoSelect = document.getElementById('solicitacaoCurso'); 
                const solicitacaoSolicitacaoInput = document.getElementById('solicitacaoSolicitacao');
                // const solicitacaoAnexoInput = document.getElementById('solicitacaoArquivo'); 
                const solicitacaoJustificativaInput = document.getElementById('solicitacaoInputAdicionar'); 
                const solicitacaoTipoSelect = document.getElementById('solicitacaoTipo'); 

                idsolInput.value = solicitacaoId; 
                solicitacaoCursoSelect.value = solicitacaoCurso;
                solicitacaoSolicitacaoInput.value = solicitacaoSolicitacao; 
                // solicitacaoAnexoInput.value = solicitacaoAnexo;
                solicitacaoJustificativaInput.value = solicitacaoJustificativa;
                solicitacaoTipoSelect.value = solicitacaoTipo;
            });
        });

    </script>

    </script>
</body>
</html>