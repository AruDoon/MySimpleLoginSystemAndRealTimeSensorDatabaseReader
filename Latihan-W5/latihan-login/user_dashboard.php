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

// Helper functions
function getBatteryClass($perc) {
    if ($perc > 70) return '';
    if ($perc > 30) return 'medium';
    return 'low';
}

function getStatusIndicator($status) {
    if ($status == 'online') {
        return '<span class="status-online"></span>Online';
    } elseif ($status == 'warning') {
        return '<span class="status-warning"></span>Warning';
    }
    return '<span class="status-warning"></span>Offline';
}

// Fetch user biodata
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Hardcoded battery for demo
$battery_percentage = 78;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - <?php echo $user['nama']; ?></title>
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
        .content-card p { color: #fff; }
        .battery { display: flex; align-items: center; justify-content: center; margin: 10px 0; }
        .battery-icon { width: 50px; height: 30px; border: 2px solid #FFD700; border-radius: 5px; position: relative; margin-right: 10px; }
        .battery-fill { height: 100%; background: #4CAF50; width: <?php echo ($battery_percentage / 100 * 98); ?>%; border-radius: 3px; }
        .battery-fill.medium { background: #FF9800; }
        .battery-fill.low { background: #F44336; }
        .battery span { color: #FFD700; }
        .status-online { background: #4CAF50; width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .status-warning { background: #FF9800; width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        @media (max-width: 768px) { .dashboard-wrapper { flex-direction: column; } .sidebar { width: 100%; } }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <nav class="sidebar">
            <h3>Selamat Datang, <?php echo $_SESSION['username']; ?>!</h3>
            <ul>
                <li><a href="profile.php">Profil</a></li>
                <li><a href="sensors.php">Data Sensor</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <header class="header">
                <h1>Dashboard PBL Robotika</h1>
                <p>NIM: <?php echo $user['nim']; ?> | <?php echo $user['nama']; ?></p>
                <div class="clock-date">
                    <div id="clock"></div>
                    <div id="date"></div>
                </div>
                <div class="battery">
                    <div class="battery-icon">
                        <div class="battery-fill <?php echo getBatteryClass($battery_percentage); ?>"></div>
                    </div>
                    <span><?php echo $battery_percentage; ?>% (<?php echo $battery_percentage > 50 ? 'Baik' : 'Perlu Charge'; ?>)</span>
                </div>
            </header>

            <section class="content-card">
                <h2>Biodata Diri</h2>
                <p><strong>Jurusan:</strong> <?php echo $user['jurusan']; ?></p>
                <p><strong>Prodi:</strong> <?php echo $user['prodi']; ?></p>
                <p><strong>Alamat:</strong> <?php echo $user['address']; ?></p>
                <p><strong>Telepon:</strong> <?php echo $user['phone']; ?></p>
            </section>
        </main>
    </div>

    <script>
        // Clock/Date update (updated for 2025 context if needed, but dynamic)
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
           
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            document.getElementById('date').textContent = `${dayName}, ${date} ${monthName} ${year}`;
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>