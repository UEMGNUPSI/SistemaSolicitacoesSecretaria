<?php

if ($_SESSION['tipo-usuario'] != "administrador"){
    header("Location: index.php");
    exit();
}


?>