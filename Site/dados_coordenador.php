<?php
session_start();

require_once("dao/conexao.php");
require_once("dao/verificacao_login.php");
require_once("dao/verifica_adm.php");

//Verifica se sessões foram setadas antes de entrar nesta página (quando envia a atualização de coordenador, por exemplo, é setado sessões e é redirecionado para esta página)
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



// limpa o POST (quando usuario pressionar "Filtrar")
$post_array = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

// Verifica se filtro foi definido
$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : "";
if ($filtro === "") {
    $filtro = null;
}

$sql = $pdo->prepare("SELECT * FROM curso ORDER BY nome_cur");
$sql->execute();
$cursos = $sql->fetchAll(PDO::FETCH_ASSOC);
$numeroCur = count($cursos);

/* Paginação da tabela: */
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 10; // DEFINE A QUANTIDADE DE REGISTROS POR PÁGINA DE TABELA.
$offset = ($page - 1) * $records_per_page;

// Obter o total de registros
$where = isset($filtro) ? "WHERE LOWER(c.nome_crd) LIKE '%" . mb_strtolower($filtro) . "%'" : "";
$total_sql = $pdo->prepare("SELECT COUNT(*) FROM coordenador c LEFT JOIN curso cr ON c.curso_idcur = cr.idcur {$where}");
$total_sql->execute();
$total_records = $total_sql->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

$sql = $pdo->prepare("SELECT c.*, cr.nome_cur FROM coordenador c
LEFT JOIN curso cr ON c.curso_idcur = cr.idcur
{$where}
ORDER BY c.nome_crd
LIMIT :limit OFFSET :offset"); // em ordem alfabética
//$sql = $pdo->prepare("SELECT * FROM coordenador {$where} ORDER BY idcrd ASC LIMIT :limit OFFSET :offset"); // em ordem crescente do idcrd

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
    <title>Gerenciamento de coordenador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
</head>

<body>

    <?php include_once("sidebar.php"); ?>
    <!-- Cabeçalho -->
    <div class="right-content">
        <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
            <h1 id="h1-header">Gerenciamento de coordenadores</h1>
        </header>
        <main class="container">
            <!-- Formulário de Filtro -->
            <form class="form-horizontal" action="gerenciamento_coordenador.php" method="get">
                <div class="row">
                    <div class="col">

                        <?php if ($numeroCur == 0): ?>
                            <b>
                                <p>Para adicionar coordenador, é necessário inserir, pelo menos, um <a
                                        href="curso.php">curso</a></p>
                            </b>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#modal-adicionar"
                                title="Para adicionar coordenador, é necessário inserir, pelo menos, um curso"
                                disabled>Adicionar coordenador<i class="fa-solid fa-graduation-cap"
                                    style="margin-left: 5px;"></i></button>
                        <?php else: ?>
                            <!-- Botão de adicionar coordenador -->
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#modal-adicionar" title="Adicionar coordenador">Adicionar coordenador<i
                                    class="fa-solid fa-graduation-cap" style="margin-left: 5px;"></i></button>
                        <?php endif; ?>
                    </div>
                    <div class="col">
                        <div class="controls">
                            <input size="20" class="form-control" name="filtro" type="text" placeholder="Filtro (nome)"
                                value="<?= $filtro ?? "" ?>">
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary" title="Filtrar"
                            style="background-color: #46697F;"><i class="fa-solid fa-magnifying-glass"
                                style="color: #FFF; width: 20px; height: 20px;"></i></button>
                    </div>
                </div>
                <br />
            </form>

            <!-- TABELA DOS Coordenadores -->
            <div class="table-wrapper">
                <div class="row table-responsive">
                    <table class="table table-striped ">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 7%;">Id</th>
                                <th scope="col" style="width: 15%;">Nome</th>
                                <th scope="col" style="width: 15%;">CPF</th>
                                <th scope="col" style="width: 40%;">Curso</th>
                                <th scope="col" style="width: 6%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($res as $key => $value) {
                                echo '<tr>';
                                echo '<th scope="row">' . $value['idcrd'] . '</th>';
                                echo '<td>' . $value['nome_crd'] . '</td>';
                                echo '<td>' . $value['cpf_crd'] . '</td>';
                                echo '<td>' . $value['nome_cur'] . '</td>';
                                echo '<td width=250>';
                                echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-atualizar" title="Atualizar ' . $value['nome_crd'] . '" data-id="' . $value['idcrd'] . '" data-nome="' . $value['nome_crd'] . '" data-cpf="' . $value['cpf_crd'] . '" data-status="' . $value['status_crd'] . '" data-masp="' . $value['masp_crd'] . '" data-curso="' . $value['curso_idcur'] . '" data-celular="' . $value['telefone_crd'] . '"  style="background-color: #46697F; width: 42px; height: 38px;"><i class="fa-solid fa-pen" style="color: #FFF;"></i> </button>';
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
                        echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '&filtro=' . $filtro . '">Anterior</a></li>';
                    }
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $active = ($i == $page) ? 'active' : '';
                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '&filtro=' . $filtro . '">' . $i . '</a></li>';
                    }
                    if ($page < $total_pages) {
                        echo '<li class="   "><a class="page-link" href="?page=' . ($page + 1) . '&filtro=' . $filtro . '">Próxima</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </main>
    </div>

    <!-- MODAL ATUALIZAR Coordenador -->
    <div class="modal fade" id="modal-atualizar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Atualizar Coordenador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/coordenador.php" method="POST">
                        <input type="hidden" id="coordenadorId" name="coordenadorId">
                        <!-- !Importante!  input invisível apenas para enviar o Id do coordenador no formulario -->
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="coordenadorNome" class="form-label">Nome:</label>
                                <input type="text" class="form-control" id="InputCoordenador" placeholder="Insira aqui"
                                    maxlength="30" name="coordenadorNome" required>
                            </div>
                            <div class="mb-3">
                                <label for="coordenadorCpf" class="form-label">CPF:</label>
                                <input type="number" class="form-control" id="InputCpf" placeholder="Insira aqui"
                                    maxnumber="11" name="coordenadorCpf" required>
                            </div>
                        </div>
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="alunoCurso" class="form-label">Curso:</label>
                                <select class="form-select" name="coordenadorCurso" id="InputCurso"
                                    aria-label="Default select example">
                                    <option selected disabled>Selecione o curso:</option>
                                    <?php
                                    foreach ($cursos as $key => $value) {
                                        echo '<option value="' . $value['idcur'] . '" >' . $value['nome_cur'] . '</option>';
                                    }


                                    ?>
                                </select>


                            </div>
                            <div class="mb-3">
                                <label for="coordenadorMasp" class="form-label">MASP:</label>
                                <input type="number" class="form-control" id="InputMasp" placeholder="Insira aqui"
                                    name="coordenadorMasp" required>
                            </div>
                        </div>
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="Celular" class="form-label">Celular:</label>
                                <input type="number" class="form-control" id="InputCelular" placeholder="Insira aqui"
                                    name="coordenadorCelular" required>
                            </div>
                            <div class="mb-3">
                                <label for="Status:">Status: </label>
                                <input type="radio" id="status1" name="status" checked value="1">
                                <label for="status1">Ativo</label>
                                <input type="radio" id="status2" name="status" value="0">
                                <label for="status2">Inativo</label>
                            </div>
                        </div>

                        <div class="form-input">
                            <div class="mb-3">
                                <input type="hidden" id="coordenadorSenha" name="coordenadorSenha" value="">
                                <button type="submit" id="redefinir-senha" name="redefinirSenha"
                                    class="btn btn-primary">Redefinir Senha</button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="atualizar-coordenador" class="btn btn-primary">Salvar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ADICIONAR COORDENADOR -->
    <div class="modal fade" id="modal-adicionar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Adicionar Coordenador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/coordenador.php" method="POST">
                        <div class="form-input">
                            <div class="mb-3">

                                <label for="coordenadorNome" class="form-label">Nome:</label>
                                <input type="text" class="form-control" id="coordenadorInputAdicionar"
                                    placeholder="Insira aqui" maxlength="30" name="coordenadorNome" required>
                            </div>
                            <div class="mb-3">
                                <label for="coordenadorCpf" class="form-label">CPF:</label>
                                <input type="number" class="form-control" placeholder="Insira aqui" maxnumber="11"
                                    name="coordenadorCpf" maxlength="2" required>
                            </div>
                        </div>
                        <div class="form-input">
                            <div class="mb-3">
                                <label for="alunoCurso" class="form-label">Curso:</label>
                                <select class="form-select" name="coordenadorCurso" id="coordenadorCurso"
                                    aria-label="Default select example">
                                    <option selected disabled>Selecione o curso:</option>
                                    <?php
                                    foreach ($cursos as $key => $value) {
                                        echo '<option value="' . $value['idcur'] . '" >' . $value['nome_cur'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="coordenadorMasp" class="form-label">MASP:</label>
                                <input type="number" class="form-control" placeholder="Insira aqui" maxlength="30"
                                    name="coordenadorMasp" required>
                            </div>
                        </div>
                        <div class="form-input">

                            <div class="mb-3">
                                <label for="Celular" class="form-label">Celular:</label>
                                <input type="text" class="form-control" placeholder="(xx) xxxxx-xxxx" maxlength="15"
                                    name="coordenadorCelular" required>
                            </div>
                            <div class="mb-3">
                                <label for="coordenadorCpf" class="form-label">Senha:</label>
                                <input type="password" class="form-control" placeholder="Insira aqui" maxlength="30"
                                    name="coordenadorSenha" required>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" name="adicionar-coordenador" class="btn btn-primary">Adicionar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
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

        // FUNÇÃO PARA REDEFINIR A SENHA DO ALUNO
        document.getElementById('redefinir-senha').addEventListener('click', function () {
            const alunoCpf = document.getElementById('alunoCpf').value;
            const alunoId = document.getElementById('alunoId').value;


        });

        document.addEventListener('DOMContentLoaded', function () {
            const atualizarButtonModal = document.getElementById('modal-atualizar');
            const adicionarButtonModal = document.getElementById('modal-adicionar');

            // FUNÇÃO PARA PEGAR BOTÃO DE ACIONAMENTO DO MODAL COM OS ATRIBUTOS PASSADOS A ELE 
            atualizarButtonModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const coordenadorId = button.getAttribute('data-id'); // id do curso
                const coordenadorNome = button.getAttribute('data-nome');
                const coordenadorCpf = button.getAttribute('data-cpf');
                // const coordenadorStatus = button.getAttribute('data-status');
                const coordenadorMasp = button.getAttribute('data-masp');
                const coordenadorCurso = button.getAttribute('data-curso');
                const coordenadorCelular = button.getAttribute('data-celular');
                const inputNome = document.getElementById('InputCoordenador');
                const inputCpf = document.getElementById('InputCpf');
                const inputSenha = document.getElementById('InputSenha');
                // const inputStatus = document.getElementById('InputStatus');
                const inputMasp = document.getElementById('InputMasp');
                const inputCurso = document.getElementById('InputCurso')
                const inputCelular = document.getElementById('InputCelular');
                const Id = document.getElementById('coordenadorId');

                console.log(coordenadorMasp + ", " + coordenadorCurso)
                // input invisivel do formulario
                Id.value = coordenadorId; // definir o valor do input invisível para o id do curso
                inputNome.value = coordenadorNome;
                inputCpf.value = coordenadorCpf;
                // inputStatus.value = coordenadorStatus;
                inputMasp.value = coordenadorMasp;
                inputCurso.value = coordenadorCurso;
                inputCelular.value = coordenadorCelular;
                inputNome.focus();
            });

            adicionarButtonModal.addEventListener('shown.bs.modal', function (event) {
                const Input = document.getElementById('coordenadorInputAdicionar');
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