<?php


session_start();

require_once('verificacao_login.php');
require_once('conexao.php');

if (isset($_POST['confirmar-atualizacoes'])){
    $nome = $_POST['Nome'];
    $cpf = trim($_POST['Cpf']);
    $ra = trim($_POST['Ra']);
    $email = trim($_POST['Email']);
    $celular = trim($_POST['Celular']);
    $turno = trim($_POST['Turno']);
    $curso = trim($_POST['Curso']);
    $alunoId =  $_SESSION['id-usuario'];

    $sql = $pdo->prepare("UPDATE aluno SET nome_alu = ?, cpf_alu = ?, ra_alu = ?, email_alu = ?, celular_alu = ?, turno_alu = ?, curso_idcur = ? WHERE idalu = ?");
    $sql->execute(array($nome,$cpf,$ra,$email,$celular,$turno,$curso,$alunoId));
    $_SESSION['success'] = 'Aluno atualizado com sucesso!';
    header("Location: ../dados_aluno.php");
    exit();

}elseif (isset($_POST['Alteracao-senha'])) {
    $alunoId =  $_SESSION['id-usuario'];

    // Get the input values
    $senhaAtual = $_POST["senhaAtual"];
    $novaSenha = $_POST["novaSenha"];
    $confirmarNovaSenha = $_POST["confirmarNovaSenha"];

    // Query to retrieve the current password from the database
    $query = "SELECT senha_alu FROM aluno WHERE idalu = :idalu";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idalu', $alunoId);
    $stmt->execute();

    // Retrieve the current password from the database
    $row = $stmt->fetch();
    $senhaAtualBD = $row['senha_alu'];

    // Check if the hashed input password matches the hashed password stored in the database
    if (!password_verify($senhaAtual, $senhaAtualBD)) {
        $_SESSION['erro'] = "Senha atual incorreta.";
        header("Location: ../dados_aluno.php");
        exit;
    } 
    // Check if the new password is empty
    elseif (empty($novaSenha)) {
        $_SESSION['erro'] = "Por favor, insira a nova senha.";
        header("Location: ../dados_aluno.php");
        exit;
    } 
    // Check if the confirm new password is empty
    elseif (empty($confirmarNovaSenha)) {
        $_SESSION['erro'] = "Por favor, confirme a nova senha.";
        header("Location: ../dados_aluno.php");
        exit;
    } 
    // Check if the new passwords match
    elseif ($novaSenha != $confirmarNovaSenha) {
        $_SESSION['erro'] = "As senhas novas não coincidem.";
        header("Location: ../dados_aluno.php");
        exit;
    } 
    else {
        // Hash the new password
        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Update the password in the database
        $query = "UPDATE aluno SET senha_alu = :senha_alu WHERE idalu = :idalu";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':senha_alu', $novaSenhaHash);
        $stmt->bindParam(':idalu', $alunoId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['erro'] = "Senha alterada com sucesso!";
            header("Location: ../dados_aluno.php");
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar a senha.";
            header("Location: ../dados_aluno.php");
            exit;
        }
    }
} else {
    Header("Location: ../dados_aluno.php");
    exit();
}

?>
