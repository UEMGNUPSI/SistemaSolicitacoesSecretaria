<?php
session_start();

require_once("conexao.php");   

if (isset($_POST['acao'])){
    $cpf = $_POST['usuario'];
    $senha = $_POST['senha'];
    $tipo = $_POST['opcoes'];

 
    
    if ($tipo == 'Aluno'){
        $sql = $pdo->prepare("SELECT * FROM aluno where cpf_alu = ?");
        $sql->execute(array($cpf));
        
        $info = $sql->fetchAll(PDO::FETCH_ASSOC); 
        $nome = $info[0]['nome_alu'];
        $idUsuario = $info[0]['idalu'];
        $cursoUsuario = $info[0]['curso_idcur'];
        $senhaCript = $info[0]['senha_alu'];

        if ($sql->rowCount() > 0){
            if (password_verify($senha, $senhaCript)) { 
                // Sucesso no login
                $_SESSION['usuario']      = $cpf;
                $_SESSION['id-usuario']   = $idUsuario;
                $_SESSION['curso-idcur']  = $cursoUsuario;
                $_SESSION['nome-usuario'] = $nome;
                $_SESSION['tipo-usuario'] = strtolower($tipo);
                $_SESSION['success']      = "id: ".$idUsuario.", Nome: ".$nome." Id curso: ".$cursoUsuario."" ;

                header('Location: ../solicitacao_aluno.php');
                exit();
            } else {
                // Falha na senha
                $_SESSION['ERROR'] = "Senha incorreto(s)";
                header("Location: ../index.php");
                exit();
            }
        }else{
            $_SESSION['ERROR'] = "CPF";
            header("Location: ../index.php");
            exit();
        }
    }

    else if ($tipo == 'Administrador'){
        $sql = $pdo->prepare("SELECT * FROM administrador where cpf_adm = ? and senha_adm = ?");
        $sql->execute(array($cpf, $senha));
        
        $info = $sql->fetchAll(PDO::FETCH_ASSOC); 
        $nome = $info[0]['nome_adm'];
         
        if ($sql->rowCount() > 0) {
            $_SESSION['usuario'] = $cpf;
            $_SESSION['nome-usuario'] = $nome;
            $_SESSION['tipo-usuario'] = strtolower($tipo);
            header('Location: ../curso.php');
            exit();
        }else{
           $_SESSION['ERROR'] = 'Administrador não encontrado. CPF ou senha incorreto(s).';
           header('Location: ../index.php');
           exit();
        }

    }else{
        $sql = $pdo->prepare("SELECT * FROM coordenador where cpf_crd = ? and senha_crd = ?");
        $sql->execute(array($cpf, $senha));
        
        $info = $sql->fetchAll(PDO::FETCH_ASSOC); 
        $nome = $info[0]['nome_crd'];
         
        if ($sql->rowCount() > 0) {
            $_SESSION['usuario'] = $cpf;
            $_SESSION['nome-usuario'] = $nome;
            $_SESSION['tipo-usuario'] = strtolower($tipo);
            header('Location: ../curso.php');
            exit();
        }else{
           $_SESSION['ERROR'] = 'Coordenador não encontrado. CPF ou senha incorreto(s).';
           header('Location: ../index.php');
           exit();
        }
    }
}
?>