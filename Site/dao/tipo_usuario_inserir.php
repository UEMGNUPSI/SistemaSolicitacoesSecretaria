<?php
    require_once("conexao.php");
    $tipo = trim($_POST['tipo']);

    if (empty($tipo)) {
        echo "<script>
                        alert('Valor inválido');
                        window.location.href = '../cadastro_tipo_usuario.php';
                    </script>";
        exit();

    }else {

        if (isset($_POST['inserir'])){
            $consultaTipos = $pdo->prepare("SELECT * FROM tp_u WHERE descricao = ?");
            $consultaTipos->execute(array($tipo));
            
            if($consultaTipos->rowCount() > 0){
                echo "<script>
                    window.alert('O tipo de usuário já está cadastrado no sistema.');
                    window.location.href = '../cadastro_tipo_usuario.php'
                    </script>
                ";
                exit();

            }else{
                
                $sql = $pdo->prepare("INSERT INTO tp_u VALUES (null, ?)");

                $sql->execute(array($tipo));
                echo "<script>
                            alert('Usuário $tipo inserido com sucesso!');
                            window.location.href = '../cadastro_tipo_usuario.php';
                        </script>";
                exit();
            }


        } else if (isset($_POST["deletar"])){ 
            $consultaTipos = $pdo->prepare("SELECT * FROM tp_u WHERE descricao = ?");
            $consultaTipos->execute(array($tipo));

            if($consultaTipos->rowCount() > 0){
                $sql = $pdo->prepare("DELETE FROM tp_u WHERE descricao = ?");
                $sql->execute(array($tipo));
                echo "<script>
                            alert('O tipo de usuário $tipo foi removido com sucesso!');
                            window.location.href = '../cadastro_tipo_usuario.php';
                        </script>";
                exit();

            }else{
                echo "<script>
                            alert('O tipo de usuário inserido não existe!');
                            window.location.href = '../cadastro_tipo_usuario.php';
                        </script>";
                exit();

            }
        }else{
            return;
        }

    }

?>