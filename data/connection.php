<?php
//Dados do Servidor
$servidor = "localhost";
$usuario = "root";
$senha = "";
$dbname = "portifolio_db";

// Criar a conexão
$conexao = mysqli_connect($servidor, $usuario, $senha, $dbname);

// Verificar a conexão
if (!$conexao) {
    die("Conexão falhou: " . mysqli_connect_error());
}
?>
