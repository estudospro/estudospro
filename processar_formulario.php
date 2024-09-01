<?php
// Ativar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão com o banco de dados MySQL
$servername = "localhost";
$username = "root"; // Altere conforme necessário
$password = ""; // Altere conforme necessário
$dbname = "usuario_db";

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    if (isset($_POST['registrar'])) {
        $email = $_POST['email'] ?? ''; // O campo de email só é enviado no registro

        // Verificar se o usuário já existe
        $sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Usuário já existe
            echo '<div style="background-color: #001E33; color: #FFFFFF; font-family: Arial, sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; text-align: center;">
                    <div>
                        <h2>Usuário já cadastrado!</h2>
                        <p>Tente outro nome de usuário.</p>
                        <a href="index.html" style="color: #FFFFFF; text-decoration: underline;">Voltar para o Registro</a>
                    </div>
                  </div>';
        } else {
            // Inserir novo usuário no banco de dados
            $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
            $sql = "INSERT INTO usuarios (usuario, email, senha) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $usuario, $email, $senhaHash);

            if ($stmt->execute()) {
                // Redirecionar após registro bem-sucedido
                header("Location: https://www.estudospro.site/área-do-aluno"); // Substitua com a URL do seu site principal
                exit(); // Sempre use exit após o header para parar a execução do script
            } else {
                echo "Erro: " . $stmt->error;
            }
            $stmt->close();
        }
    } elseif (isset($_POST['login'])) {
        // Verificar login
        $sql = "SELECT senha FROM usuarios WHERE usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($senha, $row['senha'])) {
                // Login bem-sucedido, redirecionar para o site principal
                header("Location: https://www.estudospro.site/%C3%A1rea-do-aluno"); // Substitua com a URL do seu site principal
                exit(); // Sempre use exit após o header para parar a execução do script
            } else {
                // Senha incorreta
                echo '<div style="background-color: #001E33; color: #FFFFFF; font-family: Arial, sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; text-align: center;">
                        <div>
                            <h2>Senha incorreta!</h2>
                            <p>Tente novamente.</p>
                            <a href="index.html" style="color: #FFFFFF; text-decoration: underline;">Voltar para o Login</a>
                        </div>
                      </div>';
            }
        } else {
            // Usuário não encontrado
            echo '<div style="background-color: #001E33; color: #FFFFFF; font-family: Arial, sans-serif; height: 100vh; display: flex; justify-content: center; align-items: center; text-align: center;">
                    <div>
                        <h2>Usuário não encontrado!</h2>
                        <p>Tente novamente.</p>
                        <a href="index.html" style="color: #FFFFFF; text-decoration: underline;">Voltar para o Login</a>
                    </div>
                  </div>';
        }
        $stmt->close();
    }
}

$conn->close();
?>
