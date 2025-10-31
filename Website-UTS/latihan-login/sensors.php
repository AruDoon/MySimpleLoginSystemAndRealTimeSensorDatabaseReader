<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: login.html');
    exit();
}

// DB connection
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "web";
$pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username_db, $password_db);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Helper function for status
function getStatusIndicator($status) {
    if ($status == 'online') {
        return '<span class="status-online"></span>Online';
    } elseif ($status == 'warning') {
        return '<span class="status-warning"></span>Warning';
    } elseif ($status == 'offline') {
        return '<span class="status-offline"></span>Offline';
    }
    return '<span class="status-warning"></span>Offline';
}

// Fetch all sensor logs (static template data, not user-specific)
$sensors = $pdo->query("SELECT * FROM sensors_logs ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Sensor - <?php echo $_SESSION['username']; ?></title>
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
        .content-card { background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); padding: 20px; border-radius: 20px; margin-bottom: 20px; color: #fff; box-shadow: 0 8px 32px rgba(0,0,0,0.5); border: 2px solid rgba(255,215,0,0.2); }
        .content-card h2 { margin-bottom: 15px; color: #FFD700; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; color: #fff; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid rgba(255,215,0,0.3); }
        th { background: rgba(255,215,0,0.1); color: #FFD700; }
        .status-online { background: #4CAF50; width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; box-shadow: 0 0 10px #00ff88;}
        .status-offline { background: #ff2626ff; width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; box-shadow: 0 0 10px #ff2626ff;}
        .status-warning { background: #FF9800; width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; box-shadow: 0 0 10px #ffaa00;}
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }
        @media (max-width: 768px) { .dashboard-wrapper { flex-direction: column; } .sidebar { width: 100%; } }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="dashboard-wrapper">
        <nav class="sidebar">
            <h3>Data Sensor PBL</h3>
            <ul>
                <li><a href="user_dashboard.php">Kembali ke Dashboard</a></li>
                <li><a href="profile.php">Profil</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <header class="header">
                <h1>Log Data Sensor</h1>
                <p>Seluruh riwayat sensor (data template standar)</p>
            </header>

            <section class="content-card">
                <h2>Tabel Log Sensor</h2>
                <?php if (empty($sensors)): ?>
                    <p style="text-align: center; color: #fff;">Belum ada data sensor. Tambahkan melalui admin atau simulasi.</p>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID Log</th>
                            <th>Nama Sensor</th>
                            <th>Nilai</th>
                            <th>Satuan</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sensors as $sensor): ?>
                        <tr>
                            <td><?php echo $sensor['id']; ?></td>
                            <td><?php echo $sensor['sensor_name']; ?></td>
                            <td><?php echo $sensor['value']; ?></td>
                            <td><?php echo $sensor['unit']; ?></td>
                            <td><?php echo getStatusIndicator($sensor['status']); ?></td>
                            <td><?php echo date('d/m/Y H:i:s', strtotime($sensor['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>