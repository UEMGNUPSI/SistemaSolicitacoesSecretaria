<?php
session_start();

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

$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : "";

if ($filtro === "") {
    $filtro = null;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10; // DEFINE A QUANTIDADE DE REGISTROS POR PÁGINA DE TABELA.
$offset = ($page - 1) * $records_per_page;

$pdo = new PDO('mysql:host=localhost;dbname=sistema_solicitacoes_uemg', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

// Get total records
$where = isset($filtro) ? "where lower(descricao_cur) like '%" . mb_strtolower($filtro) . "%'" : "";
$total_sql = $pdo->prepare("SELECT COUNT(*) FROM tp_u {$where}");
$total_sql->execute();
$total_records = $total_sql->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);



 $sql = $pdo->prepare("SELECT * FROM tp_u {$where} ORDER BY descricao_tpu LIMIT :limit OFFSET :offset"); // em ordem alfabética


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
    <title>Gerenciamento de Tipos de Usuários </title>
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
                <a href="curso.php"><p>Curso</p></a>
                <a href="pagina_tpu.php"><p>Tipo Usuário</p></a>
            </div>
        </div>
    </aside>     

<!-- Cabeçalho -->
    <div class="right-content">
        <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button>
            <h1 id="h1-header">Gerenciamento de Tipos de Usuários</h1>
        </header>
        <main class="container">
            <!-- Formulário de Filtro -->
            <form class="form-horizontal" action="tipo_usuario.php" method="get">
                <div class="row">
                    <div class="col">
                    <!-- Botão de adicionar tipo de usuario -->
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adicionar-tpu" title="adicionar-tpu" >Adicionar Tipo Usuario<i class="fa-solid fa-graduation-cap" style="margin-left: 5px;"></i></button>
                    </div>
                    <div class="col">
                        <div class="controls">
                            <input size="20" class="form-control" name="filtro" type="text" placeholder="Filtro (nome)" value="<?= $filtro ?? "" ?>">
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary" title="Filtrar"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>
                <br/>
            </form>
        
            <!-- TABELA DOS TIPOS DE USUARIO -->
            <div class="row">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 7%;">Id</th>
                            <th scope="col" style="width: 80%;">Nome</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($res as $key => $value) {
                            echo '<tr>';
                            echo '<th scope="row">' . $value['idtpu'] . '</th>';
                            echo '<td>' . $value['descricao_tpu'] . '</td>';
                            echo '<td width=250>';
                            echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" title="Atualizar '. $value['descricao_tpu'] .'" data-id="' . $value['idtpu'] . '" data-nome="' . $value['descricao_tpu'] . '"><i class="fa-solid fa-pen"></i> </button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
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
                        echo '<li class="page-item"><a class="page-link" href="?page='.($page + 1).'&filtro='.$filtro.'">Próxima</a></li>';
                    }
                    ?>
                </ul>
            </nav>
            
        </main>
    </div>

    <!-- MODAL ATUALIZAR TIPO DE USUARIO -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Atualizar Tipo de Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/tipo_usuario.php" method="POST">
                        <input type="hidden" id="tipoid" name="tipoid">
                        <div class="mb-3">
                            <label for="idtpu" class="form-label">Nome do Tipo de Usuário:</label>
                            <input type="text" class="form-control" id="input-atualizar" name="idtpu">
                        </div>
                                
                        <div class="modal-footer">
                            <button type="submit" name="atualizar-tpu" class="btn btn-primary" data-bs-dismiss="modal">Salvar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ADICIONAR TIPO DE USUARIO -->
    <div class="modal fade" id="adicionar-tpu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Adicionar Tipo de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/tipo_usuario.php" method="POST">
                        <div class="mb-3">
                            <label for="idtpu" class="form-label">Nome do Tipo de Usuário:</label>
                            <input type="text" class="form-control" id="input-adicionar" placeholder="Insira aqui" minlength="4" maxlength="13" name="idtpu">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="adicionar-tpu" class="btn btn-primary" data-bs-dismiss="modal">Adicionar</button>
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
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {

            const staticBackdrop = document.getElementById('staticBackdrop');
            const adicionarTipoModal = document.getElementById('adicionar-tpu');
 

            // FUNÇÃO PARA PEGAR BOTÃO DE ACIONAMENTO DO MODAL COM OS ATRIBUTOS PASSADOS A ELE 
            staticBackdrop.addEventListener('shown.bs.modal', function (event) {
                const button = event.relatedTarget;
                const tpuid = button.getAttribute('data-id');
                const idtpu = button.getAttribute('data-nome');
                const modalTitle = staticBackdrop.querySelector('.modal-title');
                const modalBody = staticBackdrop.querySelector('.modal-body');
                const inputModal = document.getElementById('input-atualizar');
                const Id = document.getElementById('tipoid');
                Id.value = tpuid;
                inputModal.value = idtpu;
                inputModal.focus();
            });

            adicionarTipoModal.addEventListener('shown.bs.modal', function (event) {
                const tipoUsuarioInput = document.getElementById('input-adicionar');
                tipoUsuarioInput.focus();
            });
        });
    </script>
</body>
</html>