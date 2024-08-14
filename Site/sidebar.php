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
        <img src="assets/Banner uemg.png" id="banner-uemg" alt="banner uemg">
        <HR></HR>
        <h4>Solicitações ADM</h4>
        <HR></HR>
        <button class="btn-sidebar" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-gerenciamento" aria-expanded="false" aria-controls="collapseExample">
        Gerenciamento
        </button>
        <div class="collapse show" id="collapse-gerenciamento">

        <?php if ($perfil == "aluno"): ?>
            <div class="card-body">
                <a href="solicitacao_aluno.php"><p>Solicitações</p></a>
            </div>
        <?php elseif ($perfil == "administrador"): ?>
            <div class="card-body">
                <a href="gerenciamento_administrador.php"><p>Administrador</p></a>
                <a href="gerenciamento_aluno.php"><p>Aluno</p></a>
                <a href="gerenciamento_coordenador.php"><p>Coordenador</p></a>
                <a href="curso.php"><p>Curso</p></a>
                <a href="gerenciamento_tpu.php"><p>Tipo Usuário</p></a>
                <a href="gerenciamento_encaminhamento.php"><p>Encaminhamento</p></a>
            </div>
        <?php else:?> <!-- coordenador -->
            <div class="card-body">

            </div>
        <?php endif; ?>
        </div>
        <a href="dao/logout.php">
            <div class="card-body my-2 bg-danger" id="logout">
                Logout
            </div>
        </a>
    </aside>        