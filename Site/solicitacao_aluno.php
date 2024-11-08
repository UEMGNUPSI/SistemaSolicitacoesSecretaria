<?php
session_start();

require_once("dao/verificacao_login.php");
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
 $nomealu = $_SESSION['nome-usuario'];
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
        textarea {
            resize: none;
            height: 180px;
        }

        .form-bottom{
            padding-bottom: 40px;
        }

    </style>
</head>
<body>

    <?php include_once('sidebar.php'); ?>

    <div class="right-content">
        <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button>
            <h1 id="h1-header">Envio de Solicitações</h1>      
        </header>
        

        <form id="updateForm" class="container mb-3" action="dao/solicitacao.php" method="POST" enctype="multipart/form-data">   
            <input type="hidden" id="solId" name="solId">
                    <div class="mb-3">
                        <label for="nome-usuario" class="form-label">Nome do Solicitante:</label>
                        <input type="text" class="form-control" id="nome-usuario" value="<?php echo $nomealu; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="solicitacaoCurso" class="form-label">Curso que deseja enviar a solictação (Se a disciplina for eletiva, marque o curso correspondente):</label>
                            <select class="form-select" name="solicitacaoCurso" id="solicitacaoCurso" aria-label="Default select example" required>
                                <option value="" style="color: #919191;" >Selecione o curso</option>
                                    <?php
                                        $sql = $pdo->prepare("SELECT * FROM curso ORDER BY nome_cur");
                                        $sql->execute();
                                        $info = $sql->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($info as $key => $value){
                                            echo '<option value='.$value['idcur'].'>'.$value['nome_cur'].'</option>';
                                        }
                                    ?>
                            </select>
                    </div>                        
                    <div class="mb-3">
                        <label for="solicitacaoPeriodo" class="form-label">Período do Solicitante:</label>
                            <select class="form-select" name="solicitacaoPeriodo" id="solicitacaoPeriodo" aria-label="Default select example" required>
                                <option value="" style="color: #919191;" >Selecione o seu Período</option>
                                    <option value = "1"> 1º Período </option>
                                    <option value = "2"> 2º Período </option>
                                    <option value = "3"> 3º Período </option>
                                    <option value = "4"> 4º Período </option>
                                    <option value = "5"> 5º Período </option>
                                    <option value = "6"> 6º Período </option>
                                    <option value = "7"> 7º Período </option>
                                    <option value = "8"> 8º Período </option>
                                    <option value = "9"> 9º Período </option>
                                    <option value = "10"> 10º Período </option>
                            </select>
                    </div>
                    <div class="mb-3">
                        <label for="solicitacaoTipo" class="form-label">Tipo de Solicitação:</label>
                        <select class="form-select" name="solicitacaoTipo" aria-placeholder="askdjk" id="solicitacaoTipo" aria-label="Default select example" required onchange="changeText()">
                            <option value = "" style="color: #919191;" >Selecione o Tipo de Solicitação</option>
                                <option value = "Prova de 2 Oportunidade"> Prova de 2ª Oportunidade </option>
                                <option value = "Revisão de notas"> Revisão de notas </option>
                                <option value = "Ajuste de Matrícula"> Ajuste de Matrícula </option>
                                <option value = "Rematricula"> Rematrícula </option>
                                <option value = "Dispensa de Disciplina"> Dispensa de Disciplina </option>
                                <option value = "Regime Especial"> Regime Especial </option>
                                <option value = "Trancamento Total"> Trancamento Total </option>
                                <option value = "Trancamento Parcial"> Trancamento Parcial </option>
                                <option value = "Troca de Turno"> Troca de Turno </option>
                                <option value = "Defesa de TCC"> Defesa de TCC </option>
                                <option value = "Troca de Orientador de TCC"> Troca de Orientador de TCC </option>
                                <option value = "Cancelamento de Matrícula"> Cancelamento de Matrícula </option>    
                        </select>
                    </div>
                    

                    <div class="mb-3">
                        <label for="solicitacaoSolicitacao" class="form-label">Solicitação :</label><b><p id="textParagraph"></p></b>
                        <textarea class="form-control" id="solicitacaoSolicitacao" placeholder="Insira aqui o que deseja solicitar" maxlength="255" name="solicitacaoSolicitacao" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="solicitacaoJustificativa" class="form-label">Justificativa:</label>
                        <textarea class="form-control" id="solicitacaoJustificativa" placeholder="Informe a justificativa" maxlength="255" name="solicitacaoJustificativa" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="solicitacaoArquivo" class="form-label">Arquivos de Comprovantes:</label>
                        <input type="file" class="form-control" id="solicitacaoArquivo" name="solicitacaoArquivo[]" multiple="multiple" required>
                        <p style="font-size: 0.92rem;"><b>Apenas arquivos do tipo pdf, png e jpeg são permitidos *</b></p>
                    

                    
        
                    <div class="form-bottom">
                        <button type="submit" name="adicionar-solicitacao" class="btn btn-primary" id="button-adicionar">Adicionar</button>
                        <button type="reset" class="btn btn-secondary">Limpar Formulario</button>
                    </div>              
        </form>
    </div>                     

                <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
                <script src="script/fontawesome.js"></script>
                <script type="text/javascript" src="script/script.js"></script>
                <script>
                 function changeText() {
                    var selectElement = document.getElementById("solicitacaoTipo");
                    var texto = document.getElementById("textParagraph");
                    
                    if (selectElement.value == "Prova de 2 Oportunidade") {
                        texto.innerText = "Atestado Inferior a 7 dias, enviar atestado no prazo máximo de 48 horas após sua emissão, necessário enviar comprovante de atestado e justificativa para realizar a prova.";
                    } else if (selectElement.value == "Revisão de notas") {
                        texto.innerText = "O discente poderá solicitar a revisão de notas no prazo máximo de 5 (cinco) dias  úteis, contados da divulgação do resultado, necessário inserir a justificativa para a revisão.";
                    } else if (selectElement.value == "Ajuste de Matrícula") {
                        texto.innerText = "Escreva o nome da Disciplina que deseja retirar/acrescentar e o Turno em que você esta matriculado.";
                    } else if (selectElement.value == "Rematricula") {
                        texto.innerText = "Inserir o Motivo por ter parado o curso e Justificativa para a retomada.";
                    } else if (selectElement.value == "Dispensa de Disciplina") {
                        texto.innerText = "Indique o nome da(s) disciplina(s) que você deseja solicitar a dispensa e entre parênteses, a(s) disciplina(s) correspondente(s) que você ja cursou.";
                        texto.innerText = "Necessário Anexar Formulário de Dispensa, Anexo com Histórico Escolar Anterior e Anexo do Plano de Ensino ou Ementa."; 
                    } else if (selectElement.value == "Regime Especial") {
                        texto.innerText = "Necessário o Atestado Médico ser de 7 dias ou superior, Necessário cadastrar a justificativa e o anexo com o atestado.";
                    } else if (selectElement.value == "Trancamento Total") {
                        texto.innerText = "Necessário Inserir Justificativa e Comprovante com a Justificativa, Além disso, insira seu R.A, vale lembrar que 1º Periodo não esta habilitado a realizar Trancamento.";
                    } else if (selectElement.value == "Trancamento Parcial") {
                        texto.innerText = "O trancamento parcial se da quando o discente deseja trancar uma ou mais disciplinas no semestre. O aluno deve permanecer em no Minimo 8 Créditos por semestre. (1 Aula equivale a 1 Crédito)";
                    } else if (selectElement.value == "Troca de Turno") {
                        texto.innerText = "Justificativa para a Troca de Turno e Comprovante com a Justificativa.";
                    } else if (selectElement.value == "Defesa de TCC") {
                        texto.innerText = "Para a defesa, se faz necessário estar com o Plano Pedagógico Completo, Declaração de cumprimento de Estágios e Horas Complementares. \nÉ necessário um Prazo de 30 (Trinta) dias para previsão de data da defesa, sugestão de defesa será analisada pelo coordenador de Trabalho de Conclusão de Curso, (Inserir a Data em Justificativa).";
                    }  else if (selectElement.value == "Troca de Orientador de TCC") {
                        texto.innerText = "Necessário inserir Justificativa para Trocar Orientador e Anexo com a Assinatura de Ambos.";
                    } else if (selectElement.value == "Cancelamento de Matrícula") {
                        texto.innerText = "Insira seu R.A e Turno que esta Matriculado.";
                }
            }
                </script>
</body>
</html>