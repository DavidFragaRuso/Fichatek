<?php 
define('BASE_PATH', __DIR__);

/**
 * DB Conection
 */
$host = 'localhost';
$dbname = 'fichatek_bbdd';
$username = 'root';
$password = ''; // Cambia según tu configuración

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

class Db {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addUser( $username, $password ) {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, 'worker']);
    }

    public function getRecordFromUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM work_records WHERE user_id = ? ORDER BY date DESC, time DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

class Auth {
    private $pdo;

    // Constructor que recibe la conexión a la base de datos
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para validar las credenciales
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['username'];
            return true;  // Login exitoso
        } else {
            return false;  // Login fallido
        }
    }

    // Método para verificar si el usuario está logueado
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Método para obtener el rol del usuario
    public function getRole() {
        return $_SESSION['role'] ?? null;
    }

    // Método para cerrar sesión
    public function logout() {
        session_destroy();
        header('Location: login');
        exit();
    }
}

?>