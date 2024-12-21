<?php 
session_start();
require_once BASE_PATH . '/config.php';
require_once BASE_PATH . '/header.php';

// Crear la instancia de la clase Auth
$auth = new Auth($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($auth->login($username, $password)) {
        // Si el login fue exitoso, obtenemos el rol del usuario
        $stmt = $pdo->prepare("SELECT role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Redirige según el rol del usuario
        if ($user['role'] === 'worker') {
            header('Location: records'); // Redirige a records.php para trabajadores
        } else {
            header('Location: admin'); // Redirige a admin.php para administradores
        }
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}

$pageTitle = 'Panel de login';

?>
    <div class="login-container">
        <h1>Login</h1>
        <?php
        if ( !empty( $error ) ) echo "<p class='error'>$error</p>";
        ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar sesión</button>
        </form>
    </div>
</body>