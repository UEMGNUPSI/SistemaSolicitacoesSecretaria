<?php
    // Código em php para verificar o tipo de usuário cadastrado para determinar o que será exibido no sidebar. 
    // Ex: Tipo administrador poderá ver apenas a aba de gerenciamento e encaminhamento; Aluno terá acesso somente a aba de solicitações.


?>

<aside class="sidebar" id="sidebar">
        <img src="assets/Banner uemg.png" id="banner-uemg" alt="banner uemg">
        <HR></HR>
        <h4>Solicitações ADM</h4>
        <HR></HR>
        <button class="btn-sidebar" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-gerenciamento" aria-expanded="false" aria-controls="collapseExample">
        Gerenciamento
        </button>
        <div class="collapse" id="collapse-gerenciamento">
            <div class="card-body">
                <a href="gerenciamento_administrador.php"><p>Administrador</p></a>
                <a href="gerenciamento_aluno.php"><p>Aluno</p></a>
                <a href="gerenciamento_coordenador.php"><p>Coordenador</p></a>
                <a href="curso.php"><p>Curso</p></a>
                <a href="pagina_tpu.php"><p>Tipo Usuário</p></a>
            </div>
        </div>
    </aside>        