<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.html');
    exit();
}
 
// DB connection (same as above)
$pdo = new PDO("mysql:host=localhost;dbname=web;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
// Fetch all users
$stmt = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
// Handle add/edit/delete (simple POST handling)
if ($_POST) {
    if (isset($_POST['add_user'])) {
        // Add logic: similar to register, but for admin panel
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, nim, nama, jurusan, prodi, address, phone, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'user')");
        $stmt->execute([$_POST['username'], $_POST['email'], $_POST['password'], $_POST['nim'], $_POST['nama'], $_POST['jurusan'], $_POST['prodi'], $_POST['address'], $_POST['phone']]);
    } elseif (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$_POST['delete_id']]);
    } elseif (isset($_POST['edit_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET nama = ?, jurusan = ?, prodi = ?, address = ?, phone = ? WHERE id = ?");
        $stmt->execute([$_POST['nama'], $_POST['jurusan'], $_POST['prodi'], $_POST['address'], $_POST['phone'], $_POST['edit_id']]);
    }
    header('Location: admin_dashboard.php'); // Refresh
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <style>
        /* Dark theme: Black cards, yellow headers, white text */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-image: url('../Banner.png'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed; color: #fff; }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(10px); padding: 20px; border-right: 2px solid rgba(255, 215, 0, 0.3); }
        .sidebar h3 { color: #FFD700; margin-bottom: 20px; text-align: center; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); }
        .sidebar ul { list-style: none; }
        .sidebar li { margin: 10px 0; }
        .sidebar a { color: #fff; text-decoration: none; padding: 10px; display: block; border-radius: 10px; background: rgba(255,215,0,0.1); transition: all 0.3s; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); }
        .sidebar a:hover { background: rgba(255,215,0,0.3); transform: translateX(5px); }
        .main-content { flex: 1; padding: 20px; display: flex; flex-direction: column; }
        .header { background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(10px); padding: 20px; border-radius: 20px; margin-bottom: 20px; color: #fff; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.5); border: 2px solid rgba(255,215,0,0.3); }
        .header h1 { color: #FFD700; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); }
        .clock-date { display: flex; justify-content: space-between; margin-top: 10px; font-size: 18px; color: #fff; }
        .content-card { background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); padding: 20px; border-radius: 20px; margin-bottom: 20px; color: #fff; box-shadow: 0 8px 32px rgba(0,0,0,0.5); border: 2px solid rgba(255,215,0,0.2); }
        .content-card h2 { margin-bottom: 15px; color: #FFD700; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; color: #fff; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid rgba(255,215,0,0.3); }
        th { background: rgba(255,215,0,0.1); color: #FFD700; }
        .status-online { background: #4CAF50; width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .status-warning { background: #FF9800; width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .battery { display: flex; align-items: center; justify-content: center; margin: 10px 0; }
        .battery-icon { width: 50px; height: 30px; border: 2px solid #FFD700; border-radius: 5px; position: relative; margin-right: 10px; }
        .battery-fill { height: 100%; background: #4CAF50; width: 98%; border-radius: 3px; } /* Hardcoded for now */
        .battery-fill.medium { background: #FF9800; }
        .battery-fill.low { background: #F44336; }
        .battery span { color: #FFD700; }
        /* Form inputs: Dark theme for all types */
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"] { 
            background: rgba(255,255,255,0.1); 
            color: #fff; 
            border: 1px solid rgba(255,215,0,0.3); 
            padding: 8px 12px; 
            border-radius: 5px; 
            width: 100%; 
            margin-bottom: 10px; 
            transition: all 0.3s; 
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, input[type="tel"]:focus { 
            border-color: #FFD700; 
            background: rgba(255,255,255,0.2); 
            box-shadow: 0 0 10px rgba(255,215,0,0.3); 
            outline: none; 
        }
        input::placeholder { color: rgba(255,255,255,0.7); }
        button { background: #ff6b9d; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; transition: all 0.3s; }
        button:hover { background: #ff5a8d; transform: translateY(-1px); }
        button[type="submit"] { width: 100%; padding: 12px; font-weight: bold; text-transform: uppercase; }
        form { display: flex; flex-direction: column; }
        @media (max-width: 768px) { .dashboard-wrapper { flex-direction: column; } .sidebar { width: 100%; } }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="dashboard-wrapper">
        <nav class="sidebar">
            <h3>Admin Panel</h3>
            <ul>
                <li><a href="user_dashboard.php">User Dashboard</a></li> <!-- Switch to user view if needed -->
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <header class="header">
                <h1>Manajemen Pengguna</h1>
                <p>Kelola akun user</p>
            </header>
 
            <section class="content-card">
                <h2>Tabel Data User</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Prodi</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo $u['nim']; ?></td>
                            <td><?php echo $u['nama']; ?></td>
                            <td><?php echo $u['jurusan']; ?></td>
                            <td><?php echo $u['prodi']; ?></td>
                            <td><?php echo $u['address']; ?></td>
                            <td><?php echo $u['phone']; ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="edit_id" value="<?php echo $u['id']; ?>">
                                    <input type="text" name="nama" value="<?php echo $u['nama']; ?>" placeholder="Nama" required>
                                    <button type="submit">Edit</button>
                                </form>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus user?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" style="background:#f44336;">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
 
            <section class="content-card">
                <h2>Tambah User Baru</h2>
                <form method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required minlength="5">
                    <input type="text" name="nim" placeholder="NIM" required>
                    <input type="text" name="nama" placeholder="Nama" required>
                    <input type="text" name="jurusan" placeholder="Jurusan" required>
                    <input type="text" name="prodi" placeholder="Prodi" required>
                    <input type="text" name="address" placeholder="Alamat" required>
                    <input type="tel" name="phone" placeholder="Telepon" required>
                    <button type="submit" name="add_user">Tambah</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>