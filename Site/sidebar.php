    <?php
    if ($_SESSION['tipo-usuario'] == "aluno"){
        $perfil = "aluno";
    }elseif ($_SESSION['tipo-usuario'] == "administrador"){
        $perfil = "administrador";
    }else{
        $perfil = "coordenador";
    }


?>
<style>

    a{
        text-decoration: none;
    }

    #logout{
        color: white;
        font-weight: bolder;
        text-decoration: none;
        text-align: center;
    
    }
</style>

<aside class="sidebar" id="sidebar">
    <a href="home.php"><img src="assets/Banner uemg.png" id="banner-uemg" alt="banner uemg"></a>
    <HR></HR>
    <?php if ($perfil == "aluno"): ?>
        <h4 class="mb-3"><i class="fa-solid fa-user" style="margin-right: 0.3rem;" aria-label="Aluno" title="Aluno"></i> <?php echo $_SESSION['nome-usuario']; ?></h4>
        <a href="dados_aluno.php" class=" link-light link-opacity-50-hover">
            <h4><i class="fa-solid fa-gear"></i> Informações da Conta</h4>
        </a>
       
    <?php elseif ($perfil == "administrador"): ?>
        <h4><b>Adm</b>.: <?php echo $_SESSION['nome-usuario']; ?> </h4>
        <a href="dados_admin.php" class=" link-light link-opacity-50-hover">
            <h4><i class="fa-solid fa-gear"></i> Alterar Senha</h4>
        </a>
    <?php else: ?>
        <h4><b>Coordenador</b>: <?php echo $_SESSION['nome-usuario']; ?> </h4>
    <a href="dados_coordenador.php" class=" link-light link-opacity-50-hover">
        <h4><i class="fa-solid fa-gear"></i> Informações da Conta</h4>
    </a>

    <?php endif; ?>
    <a href="home.php" class="">
        <h4  class="link-light link-opacity-50-hover"><i class="fa-solid fa-house-user"></i> Home</h4>
    </a>
    <HR></HR>
    

    <?php if ($perfil == "aluno"): ?>
        <button class="btn-sidebar" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-gerenciamento" aria-expanded="false" aria-controls="collapseExample">
            Menu
        </button>
        <div class="collapse show" id="collapse-gerenciamento">
            <div class="card-body">
                <a href="solicitacao_aluno.php"><p>Enviar Solicitações</p></a>
                <a href="visualizar_solicitacao.php"><p>Visualizar Solicitações</p></a>
            </div>
        </div>
    <?php elseif ($perfil == "administrador"): ?>
        <button class="btn-sidebar" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-gerenciamento" aria-expanded="false" aria-controls="collapseExample">
            Gerenciamento
        </button>
        <div class="collapse show" id="collapse-gerenciamento">
            <div class="card-body">
                
                <a href="gerenciamento_administrador.php"><p>Administrador</p></a>
                <a href="gerenciamento_aluno.php"><p>Aluno</p></a>
                <a href="gerenciamento_coordenador.php"><p>Coordenador</p></a>
                <a href="curso.php"><p>Curso</p></a>
                <a href="gerenciamento_encaminhamento.php"><p>Encaminhamento</p></a>
                <a href="gerenciamento_solicitacao.php"><p>Solicitações</p></a>
            </div>
        </div>
    <?php else:?> <!-- coordenador -->
        <button class="btn-sidebar" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-gerenciamento" aria-expanded="false" aria-controls="collapseExample">
            Requerimentos
        </button>
        <div class="collapse show" id="collapse-gerenciamento">
            <div class="card-body">
            <a href="analise.php"><p>Abertos</p></a>
            <a href="finalizados.php"><p>Finalizados</p></a>
            </div>
        </div>
    <?php endif; ?>
    <a href="dao/logout.php">
        <div class="my-2 p-1 rounded-1 btn btn-danger w-100" id="logout">
            Logout
        </div>
    </a>
</aside>        