<?php
session_start();

require_once("dao/verificacao_login.php");

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

$pdo = new PDO('mysql:host=localhost;dbname=sistema_solicitacoes_uemg', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

// Obter o total de registros
$where = isset($filtro) ? "where lower(nome_alu) like '%" . mb_strtolower($filtro) . "%'" : "";
$total_sql = $pdo->prepare("SELECT COUNT(*) FROM aluno {$where}");
$total_sql->execute();
$total_records = $total_sql->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);



 $sql = $pdo->prepare("SELECT * FROM aluno {$where} ORDER BY nome_alu LIMIT :limit OFFSET :offset"); // em ordem alfabética
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
    <title>Gerenciamento de alunos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
</head>
<body>
    
    <?php include_once("sidebar.php");?>      

<!-- Cabeçalho -->
        <div class="right-content">
            <header>
                <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
                <h1 id="h1-header">Gerenciamento de Alunos</h1>
            </header>
            <main class="container">
                <!-- Formulário de Filtro -->
                <form class="form-horizontal" action="gerenciamento_aluno.php" method="get">
                    <div class="row">
                        <div class="col">
                            <!-- Botão de adicionar curso -->
                            <button type="button"  class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-adicionar" title="Adicionar aluno" >Adicionar Aluno<i class="fa-solid fa-graduation-cap" style="margin-left: 5px;"></i></button>
                        </div>
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
                                    <th scope="col" style="width: 7%;">Id</th>
                                    <th scope="col" style="width: 80%;">Nome</th>
                                   <!-- <th scope="col" style="width: 6%;"></th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($res as $key => $value) {
                                    echo '<tr>';
                                    echo '<th scope="row">' . $value['idalu'] . '</th>';
                                    echo '<td>' . $value['nome_alu'] . '</td>';
                                    echo '<td width=250>';
                                    echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-atualizar" title="Atualizar '. $value['nome_alu'] .'" data-id="' . $value['idalu'] . '" data-nome="' . $value['nome_alu'] . '" data-cpf="'. $value['cpf_alu'] .'"data-ra="'.$value['ra_alu'].'"data-email="'.$value['email_alu'].'"data-celular="'.$value['celular_alu'].'"data-turno="'.$value['turno_alu'].'"data-status="'.$value['status_alu'].'"data-senha="'.$value['senha_alu'].'"data-curso="'.$value['curso_idcur'].'"data-tipo="'.$value['tp_u_idtpu'].'"  style="background-color: #46697F; width: 42px; height: 38px;"><i class="fa-solid fa-pen" style="color: #FFF;"></i> </button>';
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
                        if ($page > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page='.($page - 1).'&filtro='.$filtro.'">Anterior</a></li>';
                        }
                        for ($i = 1; $i <= $total_pages; $i++) {
                            $active = ($i == $page) ? 'active' : '';
                            echo '<li class="page-item '.$active.'"><a class="page-link" href="?page='.$i.'&filtro='.$filtro.'">'.$i.'</a></li>';
                        }
                        if ($page < $total_pages) {
                            echo '<li class="   "><a class="page-link" href="?page='.($page + 1).'&filtro='.$filtro.'">Próxima</a></li>';
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
                    <form id="updateForm" action="dao/aluno.php" method="POST">
                        <input type="hidden" id="alunoId" name="alunoId"> <!-- !Importante!  input invisível apenas para enviar o Id do curso no formulario -->
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="alunoNome" class="form-label">Nome:</label>
                                <input type="text" class="form-control" id="alunoNome" name="alunoNome">
                            </div>
                            <div class="mb-3">
                                <label for="alunoCpf" class="form-label">CPF:</label>
                                <input type="text" class="form-control" id="alunoCpf" name="alunoCpf">
                            </div>
                        </div>

                        <div class="form-input">
                            
                            <div class="mb-3">
                                <label for="alunoCurso" class="form-label">Curso:</label>
                                <select class="form-select" name="alunoCurso" id="alunoCurso" aria-label="Default select example">
                                    <option selected disabled>Selecione o curso:</option>
                                     <?php
                                        $sql = $pdo->prepare("SELECT * FROM curso");
                                        $sql->execute();
                                        $info = $sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($info as $key => $value){
                                            echo '<option value="'.$value['idcur'].'" >'.$value['nome_cur'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>


                            <div class="mb-3">
                                <label for="alunoRa" class="form-label">RA:</label>
                                <input type="text" class="form-control" id="alunoRa" name="alunoRa">
                            </div>
                        </div>

                        <div class="form-input">
                            
                            <div class="mb-3">
                                <label for="alunoEmail" class="form-label">Email:</label>
                                <input type="text" class="form-control" id="alunoEmail" name="alunoEmail">
                            </div>
                            <div class="mb-3">
                                <label for="alunoCelular" class="form-label">Celular:</label>
                                <input type="text" class="form-control" id="alunoCelular" name="alunoCelular">
                            </div>
                    

                        </div>

                        <div class="form-input">
                            <div class="mb-3">
                                <label class="form-label" for="alunoTurno">Turno:</label>
                                <select class="form-select" name="alunoTurno" id="alunoTurno" aria-label="Default select example">
                                    <option selected disabled>Selecione o Turno: </option>
                                    <option value="diurno">Diurno</option>
                                    <option value="integral">Integral</option>
                                    <option value="noturno">Noturno</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-input">
                        <div class="mb-3">
                                <label for="alunoSenha" class="form-label">Senha:</label>
                                <input type="password" class="form-control" id="alunoSenha" name="alunoSenha">
                            </div>
                            
                            <div class="form-input">
                                <div class="mb-3">
                                    <label for="Status:">Status Aluno: </label>
                                    <div>
                                        <div>
                                            <input type="radio" id="alunoStatus" name="status" checked value="1">
                                            <label for="alunoStatus">Ativo</label>
                                        </div>
                                        <div>
                                            <input type="radio" id="alunoStatus" name="status" value="0">
                                            <label for="">Inativo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>             
                                           
                        <div class="modal-footer">
                            <button type="submit" name="atualizar-aluno" class="btn btn-primary" data-bs-dismiss="modal">Salvar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ADICIONAR CURSO -->
    <div class="modal fade" id="modal-adicionar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Adicionar Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/aluno.php" method="POST">
                        
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="alunoNome" class="form-label">Nome:</label>
                                <input type="text" class="form-control" id="alunoInputAdicionar" placeholder="Insira aqui" maxlength="30" name="alunoNome">
                            </div>
                            <div class="mb-3">
                                <label for="alunoCpf" class="form-label">CPF:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" name="alunoCpf">
                            </div>
                        </div>

                        <div class="form-input">
                            <div class="mb-3">
                                <label for="alunoCurso" class="form-label">Curso:</label>
                                <select class="form-select" name="alunoCurso" id="alunoCurso" aria-label="Default select example">
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
                                <label for="alunoRa" class="form-label">RA:</label>
                                <input type="text" class="form-control" placeholder="Insira aqui" maxlength="30" name="alunoRa">
                            </div>
                        </div>

                        <div class="form-input">
                            <div class="mb-3">
                                <label for="alunoEmail" class="form-label">Email:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" name="alunoEmail">
                            </div>
                            <div class="mb-3">
                                <label for="alunoCelular" class="form-label">Celular:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" name="alunoCelular">
                            </div>
                    
                        </div>

                        <div class="form-input">
                            <div class="mb-3">
                                <label class="form-label" for="alunoTurno">Turno:</label>
                                <select class="form-select" name="alunoTurno" id="alunoTurno" aria-label="Default select example">
                                    <option selected disabled>Selecione o Turno: </option>
                                    <option value="diurno">Diurno</option>
                                    <option value="integral">Integral</option>
                                    <option value="noturno">Noturno</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-input">
                            <div class="mb-3">
                                <label for="alunoSenha" class="form-label">Senha:</label>
                                <input type="password" class="form-control"  placeholder="Insira aqui" maxlength="30" name="alunoSenha">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="adicionar-aluno" class="btn btn-primary" data-bs-dismiss="modal">Adicionar</button>
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
            const adicionarButtonModal = document.getElementById('modal-adicionar');

            // FUNÇÃO PARA PEGAR BOTÃO DE ACIONAMENTO DO MODAL COM OS ATRIBUTOS PASSADOS A ELE 
            atualizarButtonModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const alunoId = button.getAttribute('data-id'); //
                const alunoNome = button.getAttribute('data-nome');
                const alunoCPF = button.getAttribute('data-cpf');
                const alunoRA = button.getAttribute('data-ra');
                const alunoEmail = button.getAttribute('data-email');
                const alunoCelular = button.getAttribute('data-celular');
                const alunoTurno= button.getAttribute('data-turno');
                const alunoStatus= button.getAttribute('data-status');
                const alunoSenha = button.getAttribute('data-senha');
                const alunoCurso= button.getAttribute('data-curso');
                const alunoTipo= button.getAttribute('data-tipo');
                const inputId = document.getElementById('alunoId');

                inputId.value = alunoId;
                document.getElementById('alunoNome').value = alunoNome;
                document.getElementById('alunoCpf').value = alunoCPF;
                document.getElementById('alunoRa').value = alunoRA;
                document.getElementById('alunoEmail').value = alunoEmail;
                document.getElementById('alunoCelular').value = alunoCelular;
                document.getElementById('alunoTurno').value = alunoTurno;
                document.getElementById('alunoStatus').value = alunoStatus;
                document.getElementById('alunoSenha').value = alunoSenha;
                document.getElementById('alunoCurso').value = alunoCurso;
                document.getElementById('alunoTipo').value = alunoTipo;

                // input invisivel do formulario
                Id.value = alunoId; // definir o valor do input invisível para o id do curso
                inputModal.value = alunoNome; // para o input do modal já ficar com o nome do curso digitado.
                inputModal.focus();
            });

        

            adicionarButtonModal.addEventListener('shown.bs.modal', function (event) {
                const Input = document.getElementById('alunoInputAdicionar');
                Input.focus();
            });

            /*
            const adicionarCursoModal = document.getElementById('adicionarCurso');
            adicionarCursoModal.addEventListener('shown.bs.modal', function () {
                const cursoNomeInput = document.getElementById('cursoNomeAdicionar');
                cursoNomeInput.focus();
            });
            */
        });

    </script>
</body>
</html>