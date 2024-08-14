<?php  session_start(); require_once("dao/verificacao_login.php"); ?>
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
                <h1 id="h1-header">Encaminhamento</h1>
            </header>

            <div class="container mt-5">
    
        
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Solicitações</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="cursoFilter">Filtrar por Curso:</label>
                                    <select class="form-control" id="cursoFilter">
                                        <option value="">Todos</option>
                                        <option value="curso1">Curso 1</option>
                                        <option value="curso2">Curso 2</option>
                                        <option value="curso3">Curso 3</option>
                                    </select>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAll" checked>
                                    <label class="form-check-label" for="selectAll">Selecionar Todos</label>
                                </div>
                                <ul class="list-group" id="solicitacoesList">
                                    </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Coordenadores</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group" id="coordenadoresList">
                                    </ul>
                            </div>
                        </div>
                    </div>
                </div>
        
                <button class="btn btn-primary mt-3" id="enviarSolicitacoes">Enviar Solicitações</button>
            </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="script/fontawesome.js"></script>
    <script type="text/javascript" src="script/script.js"></script>
    <script src="script/encaminhamento.js"></script>
</body>
</html>