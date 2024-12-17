<?php
session_start();
require_once("dao/verificacao_login.php");
require_once("dao/verifica_adm.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adm Pessoas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="Estilos/estilo_gerenciamento.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .card-header{
            background-color: #46697F;
            color: white;
        }
    </style>
</head>
<body>

  <?php include_once("sidebar.php"); ?>

  <div class="right-content">
    <header>
        <button id="botao-menu"><i class="fa-solid fa-bars"></i></button> <!-- botao de acionamento do menu -->
        <h1 id="h1-header">Alterar Senha</h1>
    </header>       
    
    <main class="container">
    <div class="card mb-3" style="max-width: 75%; margin: auto;">
        <div class="card-header"><h4>Nova Senha</h4></div>
        <div class="card-body">
           <form action="dao/admin_confirmar_alteracoes.php" method="post" style="margin-bottom: 0px;">
               <div class="mb-3">
                   <label for="SenhaAtual">Senha Atual:</label>
                   <div class="input-group">
                       <input type="password" class="form-control" name="senhaAtual" id="SenhaAtual" value="<?php echo isset($_SESSION['senhaAtual']) ? $_SESSION['senhaAtual'] : ""; unset($_SESSION['senhaAtual']) ?>" required>
                       <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('SenhaAtual', 'toggleIconSenhaAtual')">
                           <i id="toggleIconSenhaAtual" class="fa-regular fa-eye"></i>
                       </button>
                   </div>
               </div>
               <div class="mb-3">
                   <label for="NovaSenha">Nova Senha:</label>
                   <div class="input-group">
                       <input type="password" class="form-control" name="NovaSenha" id="NovaSenha" value="<?php echo isset($_SESSION['novaSenha']) ? $_SESSION['novaSenha'] : ""; unset($_SESSION['novaSenha']) ?>" required>
                       <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('NovaSenha', 'toggleIconNovaSenha')">
                           <i id="toggleIconNovaSenha" class="fa-regular fa-eye"></i>
                       </button>
                   </div>
               </div>
               <div class="mb-3">
                   <label for="ConfirmarNovaSenha">Confirmar Senha:</label>
                   <div class="input-group">
                       <input type="password" class="form-control" name="ConfirmarNovaSenha" id="ConfirmarNovaSenha" value="<?php echo isset($_SESSION['confirmarNovaSenha']) ? $_SESSION['confirmarNovaSenha'] : ""; unset($_SESSION['confirmarNovaSenha']) ?>" required>
                       <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('ConfirmarNovaSenha', 'toggleIconConfirmarNovaSenha')">
                           <i id="toggleIconConfirmarNovaSenha" class="fa-regular fa-eye"></i>
                       </button>
                   </div>
               </div>
               <button type="submit" name="enviar_senha" class="btn btn-primary">Salvar</button>
           </form>
        </div>
        <?php if (isset($_SESSION['error'])): ?>  
            <div id="errorAlert" class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>  
            <div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>
    </main>
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
    function togglePasswordVisibility(inputId, iconId) {
      const inputField = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      
      if (inputField.type === "password") {
        inputField.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        inputField.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
  </script>

</body>
</html>