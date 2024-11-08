<?php

session_start();

require_once("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idsol = $_POST["idsol"];
    $justificativa = $_POST["AnaliseJustificativa"];
    $resultado = $_POST["AnaliseResposta"];

    // Capturar o encaminhamento_idenc da solicitação selecionada
    $stmt = $pdo->prepare("SELECT idenc FROM encaminhamento WHERE solicitacao_idsol = :idsol");
    $stmt->bindParam(':idsol', $idsol);
    $stmt->execute();
    $encaminhamento_idenc = $stmt->fetchColumn();

    // Inserir a data automaticamente no campo cata_conc_ana
    $data = date("Y-m-d H:i:s");

    // Inserir os dados no banco de dados
    $stmt = $pdo->prepare("INSERT INTO analise (encaminhamento_idenc, data_conc_ana, justificativa_ana, resultado_ana) VALUES (:encaminhamento_idenc, :data, :justificativa, :resultado)");
    $stmt->bindParam(':encaminhamento_idenc', $encaminhamento_idenc);
    $stmt->bindParam(':data', $data);
    $stmt->bindParam(':justificativa', $justificativa);
    $stmt->bindParam(':resultado', $resultado);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Atualizar o status da solicitação para "Concluído"
        $stmt = $pdo->prepare("UPDATE solicitacao SET status_sol = 'Concluído' WHERE idsol = :idsol");
        $stmt->bindParam(':idsol', $idsol);
        $stmt->execute();
        $_SESSION['success'] = "Análise concluída com sucesso!";
        Header("Location: ../analise.php");
        exit() ;       
    } else {
        $_SESSION['error'] = "Erro";
        Header("Location: ../analise.php");
        exit() ; 
    }
} else {
    echo "Método de requisição inválido.";
}
?>