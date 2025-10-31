<?php
// proses_register.php - Clean final version (no debug)

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: register.html?error=" . urlencode("Error: Gunakan method POST!"));
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
    header("Location: register.html?error=" . urlencode("Koneksi DB gagal: " . $e->getMessage()));
    exit();
}

// Get form data
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");
$nim = trim($_POST["nim"] ?? "");
$nama = trim($_POST["nama"] ?? "");
$jurusan = trim($_POST["jurusan"] ?? "");
$prodi = trim($_POST["prodi"] ?? "");
$address = trim($_POST["address"] ?? "");
$phone = trim($_POST["phone"] ?? "");

// Validation
$error = array();
if (empty($username) || strlen($username) < 3 || !ctype_alnum($username)) {
    $error[] = "Username tidak valid (minimal 3 karakter, huruf dan angka saja)!";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error[] = "Email tidak valid!";
}
if (empty($password) || strlen($password) < 5) {
    $error[] = "Password minimal 5 karakter!";
}
if (empty($nim) || !ctype_digit($nim)) {
    $error[] = "NIM harus angka saja!";
}
if (empty($nama)) {
    $error[] = "Nama tidak boleh kosong!";
}
if (empty($jurusan)) {
    $error[] = "Jurusan tidak boleh kosong!";
}
if (empty($prodi)) {
    $error[] = "Prodi tidak boleh kosong!";
}
if (empty($address)) {
    $error[] = "Alamat tidak boleh kosong!";
}
if (empty($phone) || !preg_match('/^[0-9+]+$/', $phone)) {
    $error[] = "No. Telepon tidak valid!";
}
if (!empty($error)) {
    $errorMsg = implode(" ", $error);
    header("Location: register.html?error=" . urlencode($errorMsg));
    exit();
}

// Check for duplicates
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? OR nim = ?");
$stmt->execute([$username, $email, $nim]);
if ($stmt->rowCount() > 0) {
    header("Location: register.html?error=" . urlencode("Username, email, atau NIM sudah terdaftar!"));
    exit();
}

// Insert user
$stmt = $pdo->prepare("INSERT INTO users (username, email, password, nim, nama, jurusan, prodi, address, phone, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'user')");
if ($stmt->execute([$username, $email, $password, $nim, $nama, $jurusan, $prodi, $address, $phone])) {
    header("Location: login.html?success=" . urlencode("Registrasi berhasil! Silakan login."));
} else {
    header("Location: register.html?error=" . urlencode("Gagal mendaftar. Coba lagi."));
}
exit();
?>