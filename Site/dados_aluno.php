<?php
session_start();
require_once("dao/conexao.php");

if (isset($_SESSION['success'])) {
    echo "<script>alert('".$_SESSION['success']."');</script>";
    unset($_SESSION['success']);
}


$id_aluno = $_SESSION['id-usuario'];

$sql = $pdo->prepare("SELECT nome_alu, cpf_alu, ra_alu, email_alu, celular_alu, turno_alu, curso_idcur FROM aluno WHERE idalu = $id_aluno;");
$sql->execute();

$info = $sql->fetchAll();


$alunoNome     = $info[0]['nome_alu'];
$alunoCpf      = $info[0]['cpf_alu'];
$alunoRa       = $info[0]['ra_alu'];
$alunoEmail    = $info[0]['email_alu'];
$alunoCelular  = $info[0]['celular_alu'];
$alunoTurno    = $info[0]['turno_alu'];
$alunoCurso    = $info[0]['curso_idcur'];






?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserção de Solicitação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
    <style>
        #button-adicionar{
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

    <?php include_once('sidebar.php'); ?>

    <div class="right-content">
        <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button>
            <h1 id="h1-header">Informações da Conta</h1>      
        </header>
        

        <form id="updateForm" class="container mb-3" action="dao/aluno_confirmar_alteracoes.php" method="POST" enctype="multipart/form-data">   
            <p>* É possível alterar as informações. Para fazer isso, altere o que for necessário, clique em salvar e confirme.</p>
            <p>* Caso deseje cancelar as alterações, apenas atualize a página.</p>
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome:</label>
                        <input type="text" class="form-control" id="nome" name="Nome" value="<?php echo $alunoNome ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF:</label>
                        <input type="text" class="form-control" id="cpf" name="Cpf" value="<?php echo $alunoCpf; ?>"  required>
                    </div>
                    <div class="mb-3">
                        <label for="ra" class="form-label">RA:</label>
                        <input type="text" class="form-control" id="ra" name="Ra" value="<?php echo $alunoRa; ?>"  required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="text" class="form-control" id="email" name="Email" value="<?php echo $alunoEmail; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="celular" class="form-label">Celular:</label>
                        <input type="text" class="form-control" id="celular" name="Celular" value="<?php echo $alunoCelular; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="turno" class="form-label">Turno:</label>
                        <select class="form-select" name="Turno" id="turno" aria-label="Default select example">
                            <option selected value="<?php echo $alunoTurno ?>" style="color: darkgray;" ><?php echo $alunoTurno; ?> // atual</option>
                            <option value="diurno">Diurno</option>
                            <option value="integral">Integral</option>
                            <option value="noturno">Noturno</option>
                        </select>
                    </div>
                        <div class="mb-3">
                        <label for="curso" class="form-label">Curso:</label>
                            <select class="form-select" name="Curso" id="curso" value="<?php echo $alunoCurso ?>" aria-label="Default select example">
                                <option selected disabled>Selecione o curso</option>
                                    <?php
                                        $sql = $pdo->prepare("SELECT * FROM curso");
                                        $sql->execute();
                                        $info = $sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($info as $key => $value){
                                            $selected = ($alunoCurso == $value['idcur']) ? 'selected' : '';
                                            echo '<option value='.$value['idcur'].' '. $selected. ' >'.$value['nome_cur'].'</option>';
                                        }
                                    ?>
                            </select>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" id="button-adicionar"  data-bs-toggle="modal" data-bs-target="#modal-confirmar">Salvar</button>
                        <button type="button" class="btn btn-secondary" id="button-adicionar"  data-bs-toggle="modal" data-bs-target="#modal-alterar-senha">Alterar Senha</button>

                        <!-- modal de confimar alterações -->
                        <div class="modal fade" id="modal-confirmar" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tem certeza de que deseja salvar as alterações?</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                           
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" name="confirmar-atualizacoes" class="btn btn-primary">Confirmar</button>
                            </div>
                            </div>
                        </div>
                        </div>   
                    </div>       
        </form>
                
        <!-- modal de alterar a senha -->
        <form action="dao/aluno_confirmar_alteracoes.php" method="POST">
            <div class="modal fade" id="modal-alterar-senha" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Alterar a senha</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="SenhaAtual">Senha Atual:</label>
                        <input type="text" class="form-control" name="senhaAtual" id="SenhaAtual" required>
                    </div>
                    <div class="mb-3">
                        <label for="SenhaAtual">Nova Senha:</label>
                        <input type="text" class="form-control" name="senhaAtual" id="SenhaAtual" required>
                    </div>
                    <div class="mb-3">
                        <label for="SenhaAtual">Confirmar Nova Senha:</label>
                        <input type="text" class="form-control" name="senhaAtual" id="SenhaAtual" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="Alteracao-senha" class="btn btn-primary">Confirmar</button>
                </div>
                </div>
            </div>
            </div>
        </form>

    </div>                     
                <!-- Modal -->

                <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
                <script src="script/fontawesome.js"></script>
                <script type="text/javascript" src="script/script.js"></script>
</body>
</html>