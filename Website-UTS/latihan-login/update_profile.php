<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header('Location: login.html'); 
    exit(); 
}

$pdo = new PDO("mysql:host=localhost;dbname=web;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$success = [];
$error = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $updates = [];
    $params = [];

    if (!empty($_POST['nama'])) {
        $updates[] = "nama = ?";
        $params[] = trim($_POST['nama']);
    }
    if (!empty($_POST['jurusan'])) {
        $updates[] = "jurusan = ?";
        $params[] = trim($_POST['jurusan']);
    }
    if (!empty($_POST['prodi'])) {
        $updates[] = "prodi = ?";
        $params[] = trim($_POST['prodi']);
    }
    if (!empty($_POST['address'])) {
        $updates[] = "address = ?";
        $params[] = trim($_POST['address']);
    }
    if (!empty($_POST['phone']) && preg_match('/^[0-9+]+$/', $_POST['phone'])) {
        $updates[] = "phone = ?";
        $params[] = trim($_POST['phone']);
    } else if (!empty($_POST['phone'])) {
        $error[] = "No. Telepon tidak valid!";
    }
    if (!empty($_POST['password']) && strlen($_POST['password']) >= 5) {
        $updates[] = "password = ?";
        $params[] = trim($_POST['password']);
    } else if (!empty($_POST['password'])) {
        $error[] = "Password minimal 5 karakter!";
    }

    if (!empty($updates)) {
        $params[] = $_SESSION['user_id'];
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $success[] = "Profil berhasil diupdate !";
        } else {
            $error[] = "Gagal update profil. Coba lagi.";
        }
    } else {
        $error[] = "Tidak ada perubahan yang valid.";
    }
}

if (!empty($success)) {
    header('Location: profile.php?success=' . urlencode(implode(' ', $success)));
} elseif (!empty($error)) {
    header('Location: profile.php?error=' . urlencode(implode(' ', $error)));
} else {
    header('Location: profile.php');
}
exit();
?>