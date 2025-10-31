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

// Fetch user biodata
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Hardcoded battery for demo (from example)
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
        .main-content { flex: 1; padding: 20px; display: flex; flex-direction: column; align-items: center; }
        .cards-row { display: flex; justify-content: space-around; width: 100%; max-width: 1200px; margin-bottom: 30px; gap: 20px; }
        .card { background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); padding: 20px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.5); border: 2px solid rgba(255,215,0,0.2); width: 30%; text-align: center; transition: all 0.3s ease; }
        .card:hover { transform: scale(1.05); box-shadow: 0 12px 40px rgba(255,215,0,0.3); border-color: rgba(255,215,0,0.5); }
        .card h3 { color: #FFD700; margin-bottom: 10px; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); font-size: 18px; }
        .biodata-separator { border-bottom: 1px solid rgba(255,215,0,0.3); margin: 10px 0; }
        .biodata-content { display: flex; justify-content: space-between; align-items: flex-start; margin-top: 10px; }
        .biodata-label { font-size: 15px; color: #FFD700; text-align: left; min-width: 50%; padding-right: 10px; }
        .biodata-value { font-size: 15px; color: #fff; text-align: right; min-width: 50%; font-weight: bold; }
        .card p { color: #fff; margin: 5px 0; font-size: 14px; }
        .card .clock { font-size: 24px; font-weight: bold; color: #FFD700; }
        .card .date { font-size: 14px; color: #fff; }
        .battery { display: flex; flex-direction: column; align-items: center; }
        .battery-icon { width: 60px; height: 35px; border: 2px solid #FFD700; border-radius: 8px; position: relative; margin-bottom: 10px; }
        .battery-fill { height: 100%; background: #4CAF50; width: <?php echo ($battery_percentage / 100 * 95); ?>%; border-radius: 6px; transition: width 0.3s; }
        .battery-fill.medium { background: #FF9800; }
        .battery-fill.low { background: #F44336; }
        .battery-perc { font-size: 20px; font-weight: bold; color: #FFD700; margin-bottom: 5px; }
        .battery-details { font-size: 12px; color: #fff; }
        /* Side-by-side info section */
        .info-section { display: flex; width: 100%; max-width: 1200px; gap: 20px; margin: 30px auto; }
        .robot-image { flex: 1; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); border-radius: 15px; padding: 20px; text-align: center; box-shadow: 0 8px 32px rgba(0,0,0,0.5); border: 2px solid rgba(255,215,0,0.2); transition: all 0.3s ease; }
        .robot-image:hover { transform: scale(1.02); box-shadow: 0 12px 40px rgba(255,215,0,0.3); border-color: rgba(255,215,0,0.5); }
        .robot-image img { width: 100%; height: auto; border-radius: 10px; }
        .explanation-card { flex: 1; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); padding: 30px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.5); border: 2px solid rgba(255,215,0,0.2); transition: all 0.3s ease; }
        .explanation-card:hover { transform: scale(1.02); box-shadow: 0 12px 40px rgba(255,215,0,0.3); border-color: rgba(255,215,0,0.5); }
        .explanation-card h2 { color: #FFD700; text-align: center; margin-bottom: 20px; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); }
        .explanation-card p, .explanation-card ul { color: #fff; line-height: 1.6; margin-bottom: 15px; }
        .explanation-card ul { list-style-type: disc; padding-left: 20px; }
        .explanation-card li { margin-bottom: 10px; }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }
        @media (max-width: 768px) { 
            .cards-row { flex-direction: column; align-items: center; } 
            .card { width: 80%; } 
            .sidebar { width: 100%; } 
            .dashboard-wrapper { flex-direction: column; } 
            .info-section { flex-direction: column; } 
            .biodata-content { flex-direction: column; align-items: flex-start; }
            .biodata-label, .biodata-value { text-align: left; min-width: auto; }
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
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
            <div class="cards-row">
                <!-- Biodata Card -->
                <div class="card">
                    <h3>Biodata</h3>
                    <div class="biodata-separator"></div>
                    <div class="biodata-content">
                        <div class="biodata-label">
                            <div>Nama:</div>
                            <div>NIM:</div>
                            <div>Jurusan:</div>
                            <div>Program Studi:</div>
                            <div>Nomor Telpon:</div>
                            <div>Alamat:</div>
                        </div>
                        <div class="biodata-value">
                            <div><?php echo $user['nama']; ?></div>
                            <div><?php echo $user['nim']; ?></div>
                            <div><?php echo $user['jurusan']; ?></div>
                            <div><?php echo $user['prodi']; ?></div>
                            <div><?php echo $user['phone']; ?></div>
                            <div><?php echo $user['address']; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Waktu Realtime Card -->
                <div class="card">
                    <h3>Waktu Realtime</h3>
                    <div class="biodata-separator"></div>
                    <div class="clock" id="clock"></div>
                    <div class="date" id="date"></div>
                </div>

                <!-- Status Baterai Card -->
                <div class="card">
                    <h3>Status Baterai</h3>
                    <div class="biodata-separator"></div>
                    <div class="battery">
                        <div class="battery-icon">
                            <div class="battery-fill <?php echo getBatteryClass($battery_percentage); ?>"></div>
                        </div>
                        <div class="battery-perc"><?php echo $battery_percentage; ?>%</div>
                    </div>
                </div>
            </div>

            <!-- Side-by-side Robot Image and Explanation -->
            <div class="info-section">
                <div class="robot-image">
                    <img src="../NewHexapod.png" alt="Robot Hexapod BARELANG F1"> <!-- Use your hexapod image file -->
                </div>
                <div class="explanation-card">
                    <h2>Tentang Robot Hexapod BARELANG F1</h2>
                    <div class="biodata-separator"></div>
                    <p>Halo, nama saya <?php echo $user['nama']; ?> dan ini adalah robot PBL saya di Barelang F1 yang berletak di BRAIL. Ini adalah hasil dari PBL Barelang F1 yang dimana robot ini terdiri dari 6 kaki daya mimana da hasi dari 3 servo yang merepresentasikan Coxa, Femur, dan Tibia yang digunakan untuk pergerakan robot di medan tidak rata.</p>
                    <p>Robot hexapod ini dilengkapi dengan berbagai sensor dan juga sistem kontrol gerak untuk pergerakan yang stabil.</p>
                    <ul>
                        <li>18 Servo motor untuk 6 kaki (3 set per kaki)</li>
                        <li>IMU BNO055 untuk stabilisasi dan orientasi arah</li>
                        <li>Sistem kontrol berbasis mini computer (Single Board Computer)</li>
                        <li>Navigasi autonomous dan juga kontrol manual menggunakan joystick</li>
                        <li>Navigasi autonomous dan juga kontrol manual menggunakan joystick untuk percobaan</li>
                    </ul>
                     <p>Berikut dibawah ini adalah contoh data-data dummy atau data palsu untuk percobaan saya dalam pembuatan website ini.</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Clock/Date update
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