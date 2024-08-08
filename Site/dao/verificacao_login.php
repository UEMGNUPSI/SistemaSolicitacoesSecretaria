<?php

    if (isset($_SESSION['usuario'])){
        if ($_SESSION['tipo-usuario'] != 'Administrador'){
            $_SESSION['acesso-negado'] = "Acesso Negado.";
            header('Location: index.php');
            exit();
        }
    } else{
        $_SESSION['acesso-negado'] = "Acesso Negado.";
        header('Location: index.php');
        exit();
    }

?>