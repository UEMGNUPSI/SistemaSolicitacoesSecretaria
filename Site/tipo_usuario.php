<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de tipos de usuários</title>
    <link rel="stylesheet" href="Estilos/estilo_cadastro_tipo_usuario.css">
</head>
<body>
    <header>
        <h1>Gerenciamento de tipos de usuário</h1>
    </header>
    <main>
        
        <form method="POST" action="dao/tipo_usuario_inserir.php">
            <label class="label" for="tipo">Nome do tipo de usuário: </label>
            <input type="text" name="tipo" id="tipo">
            <div id="button">
                <button class="button" id="inserir" type="submit" name="inserir">Inserir</button>
                <button class="button" id="deletar" type="submit" onclick="return confirmarExclusao()" name="deletar">Deletar</button>
            </div>
        </form>


        <h2>Tipos de usuários registrados: </h2>
        <div class="tipos_usuario">
            <?php
            $pdo = new PDO('mysql:host=localhost;dbname=sistema_secretaria_uemg', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = $pdo->prepare("SELECT * FROM tp_u ORDER BY descricao ASC");
            $sql->execute();
            $res = $sql->fetchAll(PDO::FETCH_ASSOC);

            if ($sql->rowCount() == 0) {
                echo "<h3>Nenhum tipo registrado</h3>";
            }else{
                foreach ($res as $key => $value) {
                    echo "
                        <h3>",$value['descricao'],"</h3>
                    ";
                }
            }


           

            ?>
        </div>
    </main>
            
    <script type="text/javascript">
        function confirmarExclusao() {
            let tipo = document.getElementById("curso").value;

            if (tipo.trim().length === 0){
                return;
            }else{
                return confirm("Você tem certeza que deseja excluir " + tipo + "?");
            }
        }
    </script>
</body>
</html>