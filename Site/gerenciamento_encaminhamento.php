<?php  

session_start(); 

require_once("dao/verificacao_login.php"); 

require_once("dao/verifica_adm.php"); 

require_once("dao/conexao.php");

$id_adm = $_SESSION['id-usuario'];

if (isset($_SESSION['success'])) {
    echo "<script>alert('".$_SESSION['success']."');</script>";
    unset($_SESSION['success']);
}else if (isset($_SESSION['error'])) {
    echo "<script>alert('".$_SESSION['error']."');</script>";
    unset($_SESSION['error']);
}


$sql = $pdo->prepare("SELECT * FROM curso ORDER BY nome_cur");
$sql->execute();
$cursos = $sql->fetchAll(PDO::FETCH_ASSOC);

$sql = $pdo->prepare("SELECT solicitacao.*, aluno.idalu, aluno.nome_alu FROM solicitacao INNER JOIN aluno on solicitacao.aluno_idalu = aluno.idalu WHERE solicitacao.status_sol = 'em aberto'");

$sql->execute();
$solicitacoes = $sql->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT * FROM solicitacao WHERE status_sol = 'em aberto'";
$stmt = $pdo->prepare($query);
if ($stmt->execute()) {
    $open_requests = $stmt->fetchAll();
    if (!empty($open_requests)) {
        foreach ($open_requests as $request) {
            $curso_query = "SELECT nome_cur FROM curso WHERE idcur = ?";
            $curso_stmt = $pdo->prepare($curso_query);
            $curso_stmt->execute([$request['curso_idcur']]);
            $curso_nome = $curso_stmt->fetchColumn();
        }
    }                                
}

$sql = $pdo->prepare("SELECT * FROM coordenador ORDER BY nome_crd");
$sql->execute();
$coordenador = $sql->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encaminhamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

<?php include_once("sidebar.php");?>
    <div class="right-content">
            <header>
                <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
                <h1 id="h1-header">Encaminhamento para Coordenação</h1>
            </header>

            <div class="container mt-5">
    
            <form action="dao/encaminhamento.php" method="post">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header " style = "background-color: #46697F; color: white">
                                <h4>Solicitações</h4>
                            </div>
                            <div class="card-body">
                            <div class="form-group mb-2">
                                <label for="cursoFilter">Filtrar por Curso:</label>
                                <select class="form-select" id="cursoFilter">
                                    <option value="">Todos</option> <!-- Opção para exibir todas as solicitações -->
                                    <?php
                                        foreach ($cursos as $key => $value){
                                            echo '<option value="'.$value['idcur'].'" >'.$value['nome_cur'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                                <div class="form-check mb-2" >
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Selecionar Todos</label>
                                </div>

                                <ul class="list-group" id="solicitacoesList">
                                    <?php
                                    foreach ($solicitacoes as $solicitacao) {
                                        $curso_nome = '';
                                        foreach ($cursos as $curso) {
                                            if ($curso['idcur'] == $solicitacao['nome_curso_sol']) {
                                                $curso_nome = $curso['nome_cur'];
                                                break;
                                            }
                                        }
                                        echo '<li class="list-group-item" data-curso-id="' . $solicitacao['nome_curso_sol'] . '">';
                                        echo '<input type="checkbox"  class="form-check-input" name="solicitacao[]" id="solicitacao-'  . $solicitacao['idsol'] . '" value="' . $solicitacao['idsol'] . '">';
                                        echo '<label style ="margin-left: 0.75rem" for="solicitacao-' .$solicitacao['idsol'] . '">' . $solicitacao['nome_alu'] . ' - ' . $solicitacao['tipo_sol'] .  '</label>';
                                        echo '</li>';
                                    }
                                    ?>
                                </ul>

                            </div>
                        </div>
                    </div>

                    <div style="width: auto; margin: auto">
                        <i class="fa-solid fa-arrow-right-long" style="font-size: 50px; color: var(--azul-principal)"></i>
                    </div>

                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-header" style = "background-color: #46697F; color: white">
                                <h4>Coordenadores</h4>
                            </div>
                            <div class="card-body">
                                <select id="coordenador" name="coordenador" class="form-select" required>
                                    <option value="" disabled selected >Selecione um Coordenador</option>
                                    <?php
                                    foreach ($coordenador as $coordenador) {
                                        $curso_nome = '';
                                        foreach ($cursos as $curso) {
                                            if ($curso['idcur'] == $coordenador['curso_idcur']) {
                                                $curso_nome = $curso['nome_cur'];
                                                break;
                                            }
                                        }
                                        echo '<option value="' . $coordenador['idcrd'] . '">' . $coordenador['nome_crd'] . ' - ' . $curso_nome . '</option>';
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="administrador_idadm" value="<?php echo $id_adm; ?>">

                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmar">
                Enviar
                </button>

                <!-- Modal confirmar -->
                <div class="modal fade" id="confirmar" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmação de Encaminhamento</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <span id="modalBodyText">Deseja enviar <span id="solicitacoesCount"></span> solicitações para o coordenador <span id="coordenadorNome"></span>?</span>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary" id="enviarSolicitacoes">Enviar Solicitações</button>
                            </div>
                        </div>
                    </div>
                </div>

                
        
                

            </form>
        
            </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="script/fontawesome.js"></script>
    <script type="text/javascript" src="script/script.js"></script>

    <script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
    const solicitacoesList = document.getElementById("solicitacoesList");
    const selectAllCheckbox = document.getElementById("selectAll");
    const checkboxes = solicitacoesList.querySelectorAll("input[type='checkbox']");
    const cursoFilter = document.getElementById("cursoFilter");
    const modalBody = document.querySelector(".modal-body span");
    const coordenadorSelect = document.getElementById("coordenador");
    const enviarSolicitacoesBtn = document.getElementById("enviarSolicitacoes");

// Função para selecionar/desselecionar todas as solicitações visíveis
selectAllCheckbox.addEventListener('change', function() {
    const isChecked = selectAllCheckbox.checked;
    const solicitacaoItems = solicitacoesList.querySelectorAll(".list-group-item");

    solicitacaoItems.forEach(function(item) {
        // Verifica se o item está visível
        if (item.style.display !== "none") {
            const checkbox = item.querySelector("input[type='checkbox']");
            checkbox.checked = isChecked; // Seleciona ou desmarca o checkbox
        }
    });
});

// Função para filtrar solicitações por curso
cursoFilter.addEventListener("change", function() {
    const selectedCurso = cursoFilter.value;
    const solicitacaoItems = solicitacoesList.querySelectorAll(".list-group-item");

    solicitacaoItems.forEach(function(item) {
        const cursoId = item.getAttribute("data-curso-id");
        if (selectedCurso === "" || cursoId === selectedCurso) {
            item.style.display = ""; // Mostrar item
        } else {
            item.style.display = "none"; // Ocultar item
        }
    });

    // Desmarcar todas as caixas de seleção ao mudar o filtro
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = false; // Desmarcar checkbox
    });
    selectAllCheckbox.checked = false; // Desmarcar "Selecionar Todos"
});


    // Função para filtrar solicitações por curso
    cursoFilter.addEventListener("change", function() {
        const selectedCurso = cursoFilter.value;
        const solicitacaoItems = solicitacoesList.querySelectorAll(".list-group-item");

        solicitacaoItems.forEach(function(item) {
            const cursoId = item.getAttribute("data-curso-id");
            if (selectedCurso === "" || cursoId === selectedCurso) {
                item.style.display = ""; // Mostrar item
            } else {
                item.style.display = "none"; // Ocultar item
            }
        });
    });

    // Função para mostrar no modal o número de solicitações selecionadas e o nome do coordenador
    document.querySelector("button[data-bs-target='#confirmar']").addEventListener("click", function() {
        const selectedSolicitacoes = Array.from(checkboxes).filter(function(checkbox) {
            return checkbox.checked;
        });

        const selectedCoordenador = coordenadorSelect.options[coordenadorSelect.selectedIndex].text;
        const numSolicitacoes = selectedSolicitacoes.length;

        if (numSolicitacoes > 0 && coordenadorSelect.value) {
            modalBody.textContent = `Deseja enviar ${numSolicitacoes} solicitação(ões) para o coordenador ${selectedCoordenador}?`;
        } else {
            modalBody.textContent = "Selecione ao menos uma solicitação e um coordenador.";
        }
    });

    // Função para garantir que ao menos uma solicitação está marcada e coordenador selecionado ao enviar o formulário
    enviarSolicitacoesBtn.addEventListener("click", function(event) {
        let atLeastOneChecked = false;
        let coordenadorSelected = !!coordenadorSelect.value;

        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                atLeastOneChecked = true;
            }
        });

        if (!atLeastOneChecked || !coordenadorSelected) {
            event.preventDefault();
            alert("Selecione ao menos uma solicitação e um coordenador.");
        }
    });
});


</script>
</body>
</html>