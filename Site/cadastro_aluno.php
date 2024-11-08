<?php
session_start();

require_once("dao/conexao.php");
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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de alunos</title>
    <link rel="stylesheet" type="text/css" href="bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
    <link rel="stylesheet" href="Estilos/estilo_cadastro_aluno.css">
</head>
<body>

    <div>
        <header>
            <img src="assets/Banner uemg.png" alt="" srcset="">
            <h1 id="h1-header">Cadastro de Alunos</h1>
            
        </header>
    </div>

    <form id="updateForm" action="dao/cadastro_aluno.php" method="POST">   
                        <div class="mb-3">
                                <label for="alunoNome" class="form-label">Nome:</label>
                                <input type="text" class="form-control" id="alunoInputAdicionar" placeholder="Insira aqui" maxlength="30" name="alunoNome" required>
                        </div>

                        <div class="mb-3">
                                <label for="alunoCpf" class="form-label">CPF:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="11" name="alunoCpf"required>
                            
                        </div>

                        <div class="mb-3">
                                <label for="alunoCpf" class="form-label">Data de Nascimento:</label>
                                <input type="date" class="form-control"  placeholder="Insira aqui" maxlength="11" name="alunoDt_nasc"required>
                            
                        </div>

                        <div class="mb-3">
                                <label for="alunoSenha" class="form-label">Senha:</label>
                                <input type="password" class="form-control"  placeholder="Insira aqui" maxlength="30" name="alunoSenha"required>
                        </div>

                        <div class="mb-3">
                                <label for="alunoRa" class="form-label">RA:</label>
                                <input type="text" class="form-control" placeholder="Insira aqui" maxlength="11" name="alunoRa"required>
                        </div>
                    
                        <div class="mb-3">
                                <label for="alunoEmail" class="form-label">Email:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="30" name="alunoEmail"required>
                        </div>
                        
                        <div class="mb-3">
                                <label for="alunoCelular" class="form-label">Celular:</label>
                                <input type="text" class="form-control"  placeholder="Insira aqui" maxlength="11" name="alunoCelular"required>
                        </div>                    
            
                        <div class="mb-3">
                            <label class="form-label" for="IalunoTurno">Turno:</label>
                            <select class="form-select" name="alunoTurno" id="IalunoTurno" aria-label="Default select example">
                                <option selected disabled>Selecione o Turno: </option>
                                <option value="diurno">Diurno</option>
                                <option value="integral">Integral</option>
                                <option value="noturno">Noturno</option>
                            </select>
                        </div>        
                        <div class="mb-3">
                                <label for="alunoCurso" class="form-label">Curso:</label>
                                <select class="form-select" name="alunoCurso" id="alunoCurso" required>
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
                        <div>
                            <button type="submit" name="adicionar-aluno" class="btn btn-primary" id="button-adicionar">Adicionar</button>
                            <button type="reset" class="btn btn-secondary">Limpar Formulario</button>
                        </div>
                    </div>
                </div>
            </div>              
    </form>
                                        
                <script>
                    document.getElementById('alunoCurso').addEventListener('invalid', function() {
                    document.getElementById('curso-error').style.display = 'block';
                });
                    document.getElementById('alunoCurso').addEventListener('input', function() {
                    document.getElementById('curso-error').style.display = 'none';
                });
                </script>

                <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
                <script src="script/fontawesome.js"></script>
                <script type="text/javascript" src="script/script.js"></script>
                <script>
        document.addEventListener('DOMContentLoaded', function () {
                inputId.value = alunoId;
                document.getElementById('alunoNome').value = alunoNome;
                document.getElementById('alunoCpf').value = alunoCPF;
                document.getElementById('alunoRa').value = alunoRA;
                document.getElementById('alunoEmail').value = alunoEmail;
                document.getElementById('alunoCelular').value = alunoCelular;
                document.getElementById('alunoPeriodo').value = alunoPeriodo;
                document.getElementById('alunoTurno').value = alunoTurno;
                document.getElementById('alunoStatus').value = alunoStatus;
                document.getElementById('alunoSenha').value = alunoSenha;
                document.getElementById('alunoCurso').value = alunoCurso;
                document.getElementById('alunoTipo').value = alunoTipo;

                inputId.value = alunoId;
                document.getElementById('alunoNome').value = alunoNome;
                document.getElementById('alunoCpf').value = alunoCPF;
                document.getElementById('alunoRa').value = alunoRA;
                document.getElementById('alunoEmail').value = alunoEmail;
                document.getElementById('alunoCelular').value = alunoCelular;
                document.getElementById('alunoPeriodo').value = alunoPeriodo;
                document.getElementById('alunoTurno').value = alunoTurno;
                document.getElementById('alunoStatus').value = alunoStatus;
                document.getElementById('alunoSenha').value = alunoSenha;
                document.getElementById('alunoCurso').value = alunoCurso;
                document.getElementById('alunoTipo').value = alunoTipo;
            });

    </script>
</body>
</html>
