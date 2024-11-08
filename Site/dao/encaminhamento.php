<?php 
session_start();

require_once("verificacao_login.php");
require_once("verifica_adm.php");
require_once("conexao.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se existem solicitações selecionadas
    if (isset($_POST['solicitacao']) && !empty($_POST['solicitacao'])) {
        $solicitacoes_idsol = $_POST['solicitacao']; // Array de IDs das solicitações
        $coordenador_idcrd = $_POST['coordenador']; // ID do coordenador selecionado
        $id_adm = $_SESSION['id-usuario'];
    
        // Datas de encaminhamento e retorno
        $data_enc = date('Y-m-d');
        $data_retorno_enc = date('Y-m-d', strtotime('+7 days'));
    
        // Prepara a query de inserção no encaminhamento
        $sql = $pdo->prepare("INSERT INTO encaminhamento (data_enc, data_retorno_enc, solicitacao_idsol, administrador_idadm, coordenador_idcrd) 
                              VALUES (?, ?, ?, ?, ?)");

        // Prepara a query de atualização do status da solicitação
        $update_status_sql = $pdo->prepare("UPDATE solicitacao SET status_sol = 'em análise' WHERE idsol = ?");

        // Percorre cada solicitação e realiza a inserção e atualização individualmente
        foreach ($solicitacoes_idsol as $solicitacao_idsol) {
            // Executa a inserção para cada solicitação selecionada
            $sql->execute(array($data_enc, $data_retorno_enc, $solicitacao_idsol, $id_adm, $coordenador_idcrd));

            // Atualiza o status da solicitação para "em análise"
            $update_status_sql->execute(array($solicitacao_idsol));
        }

        // Verifica se a inserção foi bem-sucedida
        if ($sql->rowCount() > 0) {
            // Redireciona ou exibe mensagem de sucesso
            $_SESSION['success'] = "Solicitacões enviadas com sucesso!";
            Header("Location: ../gerenciamento_encaminhamento.php");
            exit();
        } else {
            $_SESSION['error'] = "Erro ao enviar as solicitações.";
            Header("Location: ../gerenciamento_encaminhamento.php");
            exit();
        }
    } else {
        // Se nenhuma solicitação foi selecionada, exibe uma mensagem
        $_SESSION['error'] = "Erro ao enviar as solicitações.";
        Header("Location: ../gerenciamento_encaminhamento.php");
        exit();
    }
}
