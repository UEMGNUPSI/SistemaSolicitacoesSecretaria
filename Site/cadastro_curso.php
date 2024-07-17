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
$where = isset($filtro) ? "where lower(nome) like '%" . mb_strtolower($filtro) . "%'" : "";
$total_sql = $pdo->prepare("SELECT COUNT(*) FROM curso {$where}");
$total_sql->execute();
$total_records = $total_sql->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);



// $sql = $pdo->prepare("SELECT * FROM curso {$where} ORDER BY nome LIMIT :limit OFFSET :offset"); // em ordem alfabética
$sql = $pdo->prepare("SELECT * FROM curso {$where} ORDER BY idcur ASC LIMIT :limit OFFSET :offset"); // em ordem crescente do idcur



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
    <title>Gerenciamento de cursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="Estilos/estilo_cadastro_curso.css">
</head>
<body>
    <div id="sidebar">
        <aside>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            <h3> lorem</h3>
            
        </aside>
    </div>
    <div id="righ-content">
        <header>
            <h1>Gerenciamento de cursos</h1>
        </header>
        <main class="container">
        
            <form class="form-horizontal" action="cadastro_curso.php" method="get">
                <div class="row">
                    <div class="col-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adicionarCurso" title="Adicionar curso" >Adicionar <i class="fa-solid fa-graduation-cap" style="margin-left: 5px;"></i></button>
                    </div>
                    <div class="col-4">
                        <div class="controls">
                            <input size="20" class="form-control" name="filtro" type="text" placeholder="Filtro (nome)" value="<?= $filtro ?? "" ?>">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-primary" title="Filtrar"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </div>
                <br/>
            </form>
             <!-- TABELA DOS CURSOS -->
            <div class="row">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Nome</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($res as $key => $value) {
                            echo '<tr>';
                            echo '<th scope="row">' . $value['idcur'] . '</th>';
                            echo '<td>' . $value['nome'] . '</td>';
                            echo '<td width=250>';
                            echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" title="Atualizar '. $value['nome'] .'" data-id="' . $value['idcur'] . '" data-nome="' . $value['nome'] . '"><i class="fa-solid fa-rotate"></i> </button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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

    <!-- MODAL ATUALIZAR CURSO -->

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/curso_atualizar.php" method="POST">
                        <input type="hidden" id="cursoId" name="cursoId">
                        <div class="mb-3">
                            <label for="cursoNome" class="form-label">Novo nome</label>
                            <input type="text" class="form-control" id="cursoNome" name="cursoNome">
                        </div>
                                
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Salvar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                </form>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL ADICIONAR CURSO -->

    <div class="modal fade" id="adicionarCurso" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Adicionar Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="dao/curso_adicionar.php" method="POST">
                        <div class="mb-3">
                            <label for="cursoNome" class="form-label">Nome do curso:</label>
                            <input type="text" class="form-control" id="cursoNome" placeholder="Insira aqui" minlength="4" maxlength="25" name="cursoNome">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Adicionar</button>
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
    
    <script type="text/javascript">


        
        document.addEventListener('DOMContentLoaded', function () {

            const staticBackdrop = document.getElementById('staticBackdrop'); 

            // FUNÇÃO PARA PEGAR BOTÃO DE ACIONAMENTO DO MODAL COM OS ATRIBUTOS PASSADOS A ELE 
            staticBackdrop.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const cursoId = button.getAttribute('data-id');
                const cursoNome = button.getAttribute('data-nome');
                const modalTitle = staticBackdrop.querySelector('.modal-title');
                const modalBody = staticBackdrop.querySelector('.modal-body');
                const Id = document.getElementById('cursoId');
                Id.value = cursoId;
                modalTitle.textContent = 'Atualizar Curso - ' + cursoNome;
            });
        });
    </script>
</body>
</html>
