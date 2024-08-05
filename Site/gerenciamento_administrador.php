<?php
session_start();

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
$where = isset($filtro) ? "where lower(nome_adm) like '%" . mb_strtolower($filtro) . "%'" : "";
$total_sql = $pdo->prepare("SELECT COUNT(*) FROM administrador {$where}");
$total_sql->execute();
$total_records = $total_sql->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);



 $sql = $pdo->prepare("SELECT * FROM administrador {$where} ORDER BY nome_adm LIMIT :limit OFFSET :offset"); // em ordem alfabética
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
    <title>Gerenciamento de administradores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <img src="assets/Banner uemg.png" id="banner-uemg" alt="banner uemg">
        <HR></HR>
        <h4>Solicitações ADM</h4>
        <HR></HR>
        <button class="btn-sidebar" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-gerenciamento" aria-expanded="false" aria-controls="collapseExample">
        Gerenciamento
        </button>
        <div class="collapse" id="collapse-gerenciamento">
            <div class="card-body">
            <a href="gerenciamento_administrador.php"><p>Administrador</p></a>
                <a href="gerenciamento_aluno.php"><p>Aluno</p></a>
                <a href="gerenciamento_coordenador.php"><p>Coordenador</p></a>
                <a href="curso.php"><p>Curso</p></a>
                <a href="pagina_tpu.php"><p>Tipo Usuário</p></a>
            </div>
        </div>
    </aside>        

<!-- Cabeçalho -->
        <div class="right-content">
            <header>
                <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
                <h1 id="h1-header">Gerenciamento de Administradores</h1>
            </header>
            <main class="container">
                <!-- Formulário de Filtro -->
                <form class="form-horizontal" action="gerenciamento_aluno.php" method="get">
                    <div class="row">
                        <div class="col">
                            <!-- Botão de adicionar curso -->
                            <button type="button"  class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-adicionar" title="Adicionar aluno" >Adicionar Administrador<i class="fa-solid fa-graduation-cap" style="margin-left: 5px;"></i></button>
                        </div>
                        <div class="col">
                            <div class="controls">
                                <input size="20" class="form-control" name="filtro" type="text" placeholder="Filtro (nome)" value="<?= $filtro ?? "" ?>">
                            </div>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary" title="Filtrar" style="background-color: #46697F;"><i class="fa-solid fa-magnifying-glass" style="color: #FFF; width: 20px; height: 20px;"></i></button>
                        </div>
                    </div>
                    <br/>
                </form>
            
                <!-- TABELA DOS CURSOS -->
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
                                    echo '<th scope="row">' . $value['idadm'] . '</th>';
                                    echo '<td>' . $value['nome_adm'] . '</td>';
                                    echo '<td width=250>';
                                    echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-atualizar" title="Atualizar '. $value['nome_adm'] .'" data-id="' . $value['idadm'] . '" data-nome="' . $value['nome_adm'] . '" data-cpf="'. $value['cpf_adm'] .'" data-endereco="'.$value['endereco_adm'].'" data-cidade="'.$value['cidade_adm'].'"data-estado="'. $value['estado_adm'].'"data-telefone="'.$value['telefone_adm'].'"data-senha="'.$value['senha_adm'].'"data-status="'.$value['status_adm'].'"data-tipo="'.$value['tp_u_idtpu'].'" style="background-color: #46697F; width: 42px; height: 38px;"><i class="fa-solid fa-pen" style="color: #FFF;"></i> </button>';
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

    <!-- MODAL ATUALIZAR ADM -->
    <div class="modal fade" id="modal-atualizar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Atualizar Administrador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/administrador.php" method="POST">
                        <input type="hidden" id="admId" name="admId"> <!-- !Importante!  input invisível apenas para enviar o Id do ADM no formulario -->
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="admInputAdicionar" class="form-label">Nome:</label>
                                <input type="text" class="form-control" id="admNome" placeholder="Insira aqui" maxlength="30" name="admNome">
                            </div>
                            <div class="mb-3">
                                <label for="admCpf" class="form-label">CPF:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" id="admCpf" name="admCpf">
                            </div>
                        </div>
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="admSenha" class="form-label">Senha:</label>
                                <input type="password" class="form-control"  placeholder="Insira aqui" maxlength="30" id="admSenha" name="admSenha">
                            </div>
                            <div class="mb-3">
                            <label for="admCelular" class="form-label">Celular:</label>
                            <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" id="admCelular" name="admCelular">
                            </div>
                        </div>
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="admEndereco" class="form-label">Endereço:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" id="admEndereco" name="admEndereco">
                            </div>
                            <div class="mb-3">
                                <label for="admCidade" class="form-label">Cidade:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" id="admCidade" name="admCidade">
                            </div>
                        </div>
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="admEstado" class="form-label">Estado:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" id="admEstado" name="admEstado">
                            </div>
                            <div class="mb-3">
                                <label for="Status:">Status: </label>
                                <label for="status1">Ativo</label>
                                <input type="radio" id="status1" checked name="admStatus" value="1">
                                <label for="status2">Inativo</label>
                                <input type="radio" id="status2" name="admStatus" value="0">
                            </div>
                        </div>
                                               
                        <div class="modal-footer">
                            <button type="submit" name="atualizar-administrador" class="btn btn-primary" data-bs-dismiss="modal">Salvar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ADICIONAR ADMINISTRADOR -->
    <div class="modal fade" id="modal-adicionar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Adicionar Administrador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/administrador.php" method="POST">
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="admInputAdicionar" class="form-label">Nome:</label>
                                <input type="text" class="form-control" id="admInputAdicionar" placeholder="Insira aqui" maxlength="30" name="admNome">
                            </div>
                            <div class="mb-3">
                                <label for="admCpf" class="form-label">CPF:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" name="admCpf">
                            </div>
                        </div>
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="admSenha" class="form-label">Senha:</label>
                                <input type="password" class="form-control"  placeholder="Insira aqui" maxlength="30" name="admSenha">
                            </div>
                            <div class="mb-3">
                                <label for="admCelular" class="form-label">Celular:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" name="admCelular">
                            </div>
                        </div>
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="admEndereco" class="form-label">Endereço:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" name="admEndereco">
                            </div>
                            <div class="mb-3">
                                <label for="admCidade" class="form-label">Cidade:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" name="admCidade">
                            </div>
                        </div>
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="admEstado" class="form-label">Estado:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" name="admEstado">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="adicionar-administrador" class="btn btn-primary" data-bs-dismiss="modal">Adicionar</button>
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
                const admId = button.getAttribute('data-id'); //
                const admNome = button.getAttribute('data-nome');
                const admCPF = button.getAttribute('data-cpf');
                const admSenha = button.getAttribute('data-senha');
                const admEndereco = button.getAttribute('data-endereco');
                const admCidade = button.getAttribute('data-cidade');
                const admEstado = button.getAttribute('data-estado');
                const admTelefone = button.getAttribute('data-telefone');
                const admStatus= button.getAttribute('data-status');
                const inputId = document.getElementById('admId');

                inputId.value = admId;
                document.getElementById('admNome').value = admNome;
                document.getElementById('admCpf').value = admCPF;
                document.getElementById('admSenha').value = admSenha;
                document.getElementById('admEndereco').value = admEndereco;
                document.getElementById('admCidade').value = admCidade;
                document.getElementById('admEstado').value = admEstado;
                document.getElementById('admCelular').value = admTelefone;
                document.getElementById('admStatus').value = admStatus;

                // input invisivel do formulario
                Id.value = alunoId; // definir o valor do input invisível para o id do curso
                inputModal.value = alunoNome; // para o input do modal já ficar com o nome do curso digitado.
                inputModal.focus();
            });

        

            adicionarButtonModal.addEventListener('shown.bs.modal', function (event) {
                const Input = document.getElementById('admInputAdicionar');
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