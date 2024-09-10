<?php
// Inclui o namespace
use Entities\Message;

try {
    // Inclui arquivo de conexão e autoload
    require_once "./connection.php";
    require_once "../vendor/autoload.php";
    
    // Verifica a conexão com o banco de dados
    if ($conexao->connect_error) {
        throw new Exception('Erro de conexão: ' . $conexao->connect_error);
    }

    // Verifica método
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        // Verifica a existência dos campos
        if (isset($_POST["name"], $_POST["email"], $_POST["message"], $_POST["phone"], $_POST["ddd"])) {
            // Filtra os dados do formulário
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
            $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
            $ddd = filter_input(INPUT_POST, 'ddd', FILTER_SANITIZE_SPECIAL_CHARS);

            // Valida o e-mail uma última vez
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('E-mail inválido.');
            }

            // Prepara a query SQL usando prepared statement
            $stmt = $conexao->prepare("INSERT INTO contacts (name, email, message, phone, ddd) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception('Erro ao preparar a query: ' . $conexao->error);
            }
            $stmt->bind_param("ssssi", $name, $email, $message, $phone, $ddd);
            
            // Criação de objeto com composer
            $user = new Message($name, $email, $message, $phone, $ddd);

            // Verifica se criou o objeto para mandar mensagem
            $classeCriada = ($user != "") ? "Classe criada com sucesso" : "Classe não foi criada";

            // Executa a query, Envia status e mensagem
            if ($stmt->execute()) {
                $_SESSION['msg'] = "<p style='color:green;'>Mensagem Enviada com Sucesso</p>";
                $classeCriadaEscapada = htmlspecialchars($classeCriada, ENT_QUOTES, 'UTF-8');
                header("Location: ../index.php?status=success&message=" . urlencode($classeCriadaEscapada));
                exit();
            } else {
                throw new Exception('Falha ao enviar mensagem.');
            }

        } else {
            throw new Exception('Campos não encontrados.');
        }

    } else {
        throw new Exception('Error 404.');
    }
} catch (Exception $e) {
    // Codifica a mensagem de erro para ser usada na URL
    $error_message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    header("Location: ../index.php?status=error&message=" . urlencode($error_message));
    exit();
}
?>
