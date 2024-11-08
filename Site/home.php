<?php
    session_start();
    include("dao/conexao.php");
    include('dao/verificacao_login.php');
    date_default_timezone_set('America/Sao_Paulo');

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Administradores</title>
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
    <link rel="stylesheet" href="Estilos/estilo_home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php
        include ("sidebar.php");
    ?>
    <div class="right-content">
        <?php if ($_SESSION['tipo-usuario'] == "aluno"): ?>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f0f0f0;
            }

            .container {
                width: 80%;
                margin: 40px auto;
                text-align: center;
            }

            .caixa-bem-vindo {
                background-color: #fff;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }

            .caixa-links {
                background-color: #fff;
                padding: 20px;
                gap: 10px;
                border: 1px solid #ddd;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .caixa-links ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .caixa-links li {
                margin-bottom: 10px;
            }

            .caixa-links a {
                text-decoration: none;
                color: #337ab7;
            }

            .caixa-links a:hover {
                color: #23527c;
            }
        </style>
        <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
            <h1 id="h1-header">Página inicial</h1>
        </header>

        <div class="container">
            <div class="caixa-bem-vindo">
                <h2>Bem-vindo, <?php echo $_SESSION['nome-usuario']; ?>!</h2>
            </div>
            <div class="caixa-links">
                <h2>Links úteis</h2>
                <ul>
                    <li><a href="https://www.uemg.br/" target="_blank">Site da UEMG</a></li>
                    <li><a href="https://lyceum.uemg.br/" target="_blank">Lyceum</a></li>
                    <li><a href="https://www.uemg.br/central-de-informacoes/" target="_blank">Central de Informações</a></li>
                </ul>
            </div>
            <div class="caixa-links">
                <h2>Solicitações Respondidas</h2>
                <ul>
                    <?php

                        $id_aluno = $_SESSION['id-usuario'];
                        $sql = $pdo->prepare("SELECT tipo_sol FROM solicitacao WHERE aluno_idalu = ? AND status_sol = 'concluido'");
                        $sql->execute(array($id_aluno));
                        $numero_linhas = $sql->rowCount();
                        $tipo_sol = $sql->fetchAll();
                        
                        if ($numero_linhas == 0){
                            echo "Nenhuma solicitação";
                        }else{
                            foreach ($tipo_sol as $sol) {
                                echo $sol['tipo_sol'];
                            }
                        }
                    ?>
                </ul>
            </div>
        </div>

        <script src="script.js"></script>

        <?php elseif( $_SESSION['tipo-usuario'] == "coordenador"): ?>
            <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f0f0f0;
            }

            .container {
                width: 80%;
                margin: 40px auto;
                text-align: center;
            }

            .caixa-bem-vindo {
                background-color: #fff;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }

            .caixa-links {
                background-color: #fff;
                padding: 20px;
                gap: 10px;
                border: 1px solid #ddd;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .caixa-links ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .caixa-links li {
                margin-bottom: 10px;
            }

            .caixa-links a {
                text-decoration: none;
                color: #337ab7;
            }

            .caixa-links a:hover {
                color: #23527c;
            }
        </style>
        <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
            <h1 id="h1-header">Página inicial</h1>
        </header>
            
        <div class="container">
            <div class="caixa-bem-vindo">
                <h2>Bem-vindo, <?php echo $_SESSION['nome-usuario']; ?>!</h2>
            </div>
            <div class="caixa-links">
                <h2>Links úteis</h2>
                <ul>
                    <li><a href="https://www.uemg.br/" target="_blank">Site da UEMG</a></li>
                    <li><a href="https://lyceum.uemg.br/" target="_blank">Lyceum</a></li>
                    <li><a href="https://www.uemg.br/central-de-informacoes/" target="_blank">Central de Informações</a></li>
                </ul>
            </div>
            <div class="caixa-links">
                <h2>Solicitações</h2>
                <ul>
                    <?php

                        $id_cor = $_SESSION['id-usuario']; 
                        $sql = $pdo->prepare("SELECT solicitacao.tipo_sol, solicitacao.status_sol FROM solicitacao INNER JOIN encaminhamento on solicitacao.idsol = encaminhamento.solicitacao_idsol  INNER JOIN coordenador on coordenador.idcrd  = encaminhamento.coordenador_idcrd  WHERE coordenador.idcrd = ? AND solicitacao.status_sol = 'em análise' ");
                        $sql->execute(array($id_cor));
                        $numero_linhas = $sql->rowCount();
                        $tipo_sol = $sql->fetchAll();
                        
                        if ($numero_linhas == 0){
                            echo "Nenhuma solicitação";
                        }else{
                            foreach ($tipo_sol as $sol) {
                                echo $sol['tipo_sol'].'</br>';
                            }
                        }
                    ?>
                </ul>
            </div> 
         </div>
        
        
        <?php elseif ($_SESSION['tipo-usuario'] == "administrador"): ?>
            <header>
            <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
            <h1 id="h1-header">Gerenciamento de Administradores</h1>
        </header>

        <!-- Parte de boas-vindas ao usuário -->
        <div class="welcome-message">
            <h2>Bem-vindo, <?php echo $_SESSION['nome-usuario']; ?>!</h2>
        </div>

        <!-- Parte que exibe o dia e hora de Brasília -->
        <div class="datetime">
            <h2>Horário Atual:</h2>
            <p id="clock"></p>
            <script>
                function updateClock() {
                    var currentTime = new Date();
                    var hours = currentTime.getHours();
                    var minutes = currentTime.getMinutes();
                    var seconds = currentTime.getSeconds();
                    var day = currentTime.getDate();
                    var month = currentTime.getMonth() + 1;
                    var year = currentTime.getFullYear();

                    var clockHtml = day + "/" + month + "/" + year + " " + 
                    (hours > 12 ? hours - 12 : hours) + ":" + 
                    (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                    (seconds < 10 ? "0" + seconds : seconds);
                    if (hours > 12) {
                        clockHtml += " PM";
                    } else {
                        clockHtml += " AM";
                    }
                    document.getElementById("clock").innerHTML = clockHtml;
                }
                setInterval(updateClock, 1000); // Atualizar a cada 1 segundo
            </script>
            <div class="avisos">
                    <h2>Novas Solicitações</h2>

                <div class="row">
                        <table class="table table-striped " >
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 5%;">Id</th>
                                    <th scope="col" style="width: 60%;">Tipo de Solicitação</th>
                                    <th scope="col" style="width: 30%;">Curso</th>
                                   <!-- <th scope="col" style="width: 6%;"></th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $query = "SELECT * FROM solicitacao WHERE status_sol = 'em aberto' ORDER BY idsol DESC";
                                    $stmt = $pdo->prepare($query);
                                    if ($stmt->execute()) {
                                        $open_requests = $stmt->fetchAll();
                                        if (!empty($open_requests)) {
                                            foreach ($open_requests as $request) {
                                                $curso_query = "SELECT nome_cur FROM curso WHERE idcur = ?";
                                                $curso_stmt = $pdo->prepare($curso_query);
                                                $curso_stmt->execute([$request['curso_idcur']]);
                                                $curso_nome = $curso_stmt->fetchColumn();
                                    
                                                echo '<tr>';
                                                echo '<th scope="row">' . $request['idsol'] . '</th>';
                                                echo '<td>' . $request['tipo_sol'] . '</td>';
                                                echo '<td>' . $curso_nome . '</td>';
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="3">Nenhuma nova solicitação!</td></tr>';
                                        }
                                    } 
                                ?>
                            </tbody>
                        </table>
                    </div>
        </div>
        
        
        <?php endif; ?>

    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script type="text/javascript" src="script/script.js"></script>
    <script type="text/javascript" src="script/fontawesome.js"></script>
</body>
</html>