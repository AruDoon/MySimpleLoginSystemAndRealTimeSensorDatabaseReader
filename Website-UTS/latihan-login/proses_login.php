<?php
// proses_login.php - Fixed with debug & role redirect

// Temp: Enable errors (remove after working)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.html?error=" . urlencode("Error: Gunakan method POST!"));
    exit();
}

// Database connection
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "web";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    header("Location: login.html?error=" . urlencode("Koneksi DB gagal: " . $e->getMessage()));
    exit();
}

// Get form data
$username = trim($_POST["username"] ?? "");
$password = trim($_POST["password"] ?? "");
$email = trim($_POST["email"] ?? "");
$remember = $_POST["remember"] ?? "";

// Validation
$error = array();
if (empty($username)) $error[] = "Username tidak boleh kosong!";
if (empty($password)) $error[] = "Password tidak boleh kosong!";
if (empty($email)) $error[] = "Email tidak boleh kosong!";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error[] = "Format email tidak valid!";
if (strlen($username) < 3 && !empty($username)) $error[] = "Username minimal 3 karakter!";
if (strlen($password) < 5 && !empty($password)) $error[] = "Password minimal 5 karakter!";
if (!ctype_alnum($username)) $error[] = "Username hanya boleh huruf dan angka!";

if (!empty($error)) {
    $errorMsg = implode(" ", $error);
    header("Location: login.html?error=" . urlencode($errorMsg));
    exit();
}

// Check login - Fetch first, then validate
$stmt = $pdo->prepare("SELECT id, role FROM users WHERE username = ? AND password = ?");
$stmt->execute([$username, $password]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch row

if ($user) {  // If row exists (success)
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['username'] = $username;
    if ($remember == "yes") {
        setcookie('remember_user', $username, time() + (86400 * 30), "/");
    }
    // Role-based redirect (adjust paths as needed)
    if ($user['role'] == 'admin') {
        header("Location: admin_dashboard.php");  // Create this next
    } else {
        header("Location: user_dashboard.php");  // Your existing dashboard
    }
    exit();
} else {  // Failed login
    header("Location: login.html?error=" . urlencode("Username atau Password salah!"));
    exit();
}
?>

<!-- ../dashboard_Aldon_4222301042.php -->