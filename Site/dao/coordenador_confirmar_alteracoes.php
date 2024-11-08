<?php


session_start();

require_once('verificacao_login.php');
require_once('conexao.php');

if (isset($_POST['confirmar-atualizacoes'])){
    $nome = $_POST['Nome'];
    $cpf = trim($_POST['Cpf']);
    $masp = trim($_POST['Masp']);
    $celular = trim($_POST['Telefone']);
    
    $coordenadorId =  $_SESSION['id-usuario'];

    $sql = $pdo->prepare("UPDATE coordenador SET nome_crd = ?, cpf_crd = ?, masp_crd = ?, telefone_crd = ? WHERE idcrd = ?");
    $sql->execute(array($nome,$cpf,$masp,$celular,$coordenadorId));
    $_SESSION['success'] = 'coordenador atualizado com sucesso!';
    header("Location: ../dados_coordenador.php");
    exit();

}elseif (isset($_POST['Alteracao-senha'])) {
    $coordenadorId =  $_SESSION['id-usuario'];

    // Get the input values
    $senhaAtual = $_POST["senhaAtual"];
    $novaSenha = $_POST["novaSenha"];
    $confirmarNovaSenha = $_POST["confirmarNovaSenha"];

    // Query to retrieve the current password from the database
    $query = "SELECT senha_crd FROM coordenador WHERE idcrd = :idcrd";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idcrd', $coordenadorId);
    $stmt->execute();

    // Retrieve the current password from the database
    $row = $stmt->fetch();
    $senhaAtualBD = $row['senha_crd'];

    // Check if the hashed input password matches the hashed password stored in the database
    if (!password_verify($senhaAtual, $senhaAtualBD)) {
        $_SESSION['erro'] = "Senha atual incorreta.";
        header("Location: ../dados_coordenador.php");
        exit;
    } 
    // Check if the new password is empty
    elseif (empty($novaSenha)) {
        $_SESSION['erro'] = "Por favor, insira a nova senha.";
        header("Location: ../dados_coordenador.php");
        exit;
    } 
    // Check if the confirm new password is empty
    elseif (empty($confirmarNovaSenha)) {
        $_SESSION['erro'] = "Por favor, confirme a nova senha.";
        header("Location: ../dados_coordenador.php");
        exit;
    } 
    // Check if the new passwords match
    elseif ($novaSenha != $confirmarNovaSenha) {
        $_SESSION['erro'] = "As senhas novas não coincidem.";
        header("Location: ../dados_coordenador.php");
        exit;
    } 
    else {
        // Hash the new password
        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Update the password in the database
        $query = "UPDATE coordenador SET senha_crd = :senha_crd WHERE idcrd = :idcrd";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':senha_crd', $novaSenhaHash);
        $stmt->bindParam(':idcrd', $coordenadorId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['erro'] = "Senha alterada com sucesso!";
            header("Location: ../dados_coordenador.php");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar a senha.";
            header("Location: ../dados_coordenador.php");
            exit;
        }
    }
} else {
    Header("Location: ../dados_coordenador.php");
    exit();
}

?>
