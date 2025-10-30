<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: login.html');
    exit();
}

// Helper function for status
function getStatusIndicator($status) {
    if ($status == 'online') {
        return '<span class="status-online"></span>Online';
    } elseif ($status == 'warning') {
        return '<span class="status-warning"></span>Warning';
    }
    return '<span class="status-warning"></span>Offline';
}

// Hardcoded dummy sensor data (same for all users, from your DB)
$sensors = [
    [
        'id' => 1,
        'user_id' => 2,
        'sensor_name' => 'IMU BNO055',
        'value' => '5.23',
        'unit' => 'deg',
        'status' => 'online',
        'created_at' => '2025-10-30 19:22:24'
    ],
    [
        'id' => 2,
        'user_id' => 2,
        'sensor_name' => 'Ultrasonic HC-SR04',
        'value' => '25.30',
        'unit' => 'cm',
        'status' => 'online',
        'created_at' => '2025-10-30 19:22:24'
    ],
    [
        'id' => 3,
        'user_id' => 2,
        'sensor_name' => 'Servo SG90(x12)',
        'value' => '12.00',
        'unit' => '',
        'status' => 'online',
        'created_at' => '2025-10-30 19:22:24'
    ],
    [
        'id' => 4,
        'user_id' => 2,
        'sensor_name' => 'GPS NEO-6M',
        'value' => '0.00',
        'unit' => '',
        'status' => 'warning',
        'created_at' => '2025-10-30 19:22:24'
    ],
    [
        'id' => 5,
        'user_id' => 2,
        'sensor_name' => 'Temperature DHT22',
        'value' => '28.50',
        'unit' => '°C',
        'status' => 'online',
        'created_at' => '2025-10-30 19:22:24'
    ]
];
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
        .status-warning { background: #FF9800; width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; box-shadow: 0 0 10px #ffaa00;}
        @media (max-width: 768px) { .dashboard-wrapper { flex-direction: column; } .sidebar { width: 100%; } }
    </style>
</head>
<body>
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
                <p>Seluruh riwayat sensor (data dummy standar)</p>
            </header>

            <section class="content-card">
                <h2>Tabel Log Sensor</h2>
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
            </section>
        </main>
    </div>
</body>
</html>