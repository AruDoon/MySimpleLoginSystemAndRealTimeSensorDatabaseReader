<?php
// proses_forgot.php

session_start();
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: forgot.html?error=" . urlencode("Error: Gunakan method POST!"));
    exit();
}

// DB connection (same as register)
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "web";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    header("Location: forgot.html?error=" . urlencode("Koneksi DB gagal: " . $e->getMessage()));
    exit();
}

$step = $_POST["step"] ?? "1";
$identifier = trim($_POST["identifier"] ?? "");

if ($step == "1") {
    if (empty($identifier)) {
        header("Location: forgot.html?error=" . urlencode("NIM atau Email tidak boleh kosong!"));
        exit();
    }
    $stmt = $pdo->prepare("SELECT id FROM users WHERE nim = ? OR email = ?");
    $stmt->execute([$identifier, $identifier]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['forgot_user_id'] = $stmt->fetchColumn();
        header("Location: forgot.html?success=" . urlencode("User ditemukan. Masukkan password baru."));
        exit();
    } else {
        header("Location: forgot.html?error=" . urlencode("NIM atau Email tidak ditemukan!"));
        exit();
    }
} elseif ($step == "2") {
    $new_password = trim($_POST["new_password"] ?? "");
    if (empty($_SESSION['forgot_user_id']) || strlen($new_password) < 5) {
        unset($_SESSION['forgot_user_id']);
        header("Location: forgot.html?error=" . urlencode("Password baru tidak valid atau sesi hilang!"));
        exit();
    }
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    if ($stmt->execute([$new_password, $_SESSION['forgot_user_id']])) {
        unset($_SESSION['forgot_user_id']);
        header("Location: login.html?success=" . urlencode("Password berhasil diubah! Silakan login dengan yang baru."));
    } else {
        header("Location: forgot.html?error=" . urlencode("Gagal mengubah password. Coba lagi."));
    }
    exit();
}
?>