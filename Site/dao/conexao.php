<?php
    // Conexão ao banco de dados; UTF8
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_solicitacoes_uemg', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
?>
