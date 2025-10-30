<?php
// Biodata
$nama = "Aldon Zufar Putra Twyn";
$nim = "4222301042";
$jurusan = "Teknik Elektro";
$prodi = "Teknik Rekayasa Robotika";

// Battery Status
$battery_percentage = 78;
$voltage = 11.8;
$current = 2.4;

// IMU BNO055 Sensor Data
$imu_data = array(
    'roll' => 5.23,
    'pitch' => -3.15,
    'yaw' => 185.67,
    'accel_x' => 0.45,
    'accel_y' => -0.32,
    'accel_z' => 9.81,
    'gyro_x' => 1.25,
    'gyro_y' => -0.87,
    'gyro_z' => 0.54
);

// Daftar Sensor Project PBL
$sensors = array(
    array(
        'no' => 1,
        'nama' => 'IMU BNO055',
        'status' => 'online',
        'nilai' => 'Connected',
        'satuan' => '-'
    ),
    array(
        'no' => 2,
        'nama' => 'Ultrasonic HC-SR04',
        'status' => 'online',
        'nilai' => '25.3',
        'satuan' => 'cm'
    ),
    array(
        'no' => 3,
        'nama' => 'Servo SG90 (x12)',
        'status' => 'online',
        'nilai' => '12/12',
        'satuan' => 'Active'
    ),
    array(
        'no' => 4,
        'nama' => 'GPS NEO-6M',
        'status' => 'warning',
        'nilai' => 'Searching',
        'satuan' => '-'
    ),
    array(
        'no' => 5,
        'nama' => 'Temperature DHT22',
        'status' => 'online',
        'nilai' => '28.5',
        'satuan' => '°C'
    )
);

// Fungsi untuk mendapatkan class battery
function getBatteryClass($percentage) {
    if ($percentage < 20) {
        return 'battery-fill low';
    } elseif ($percentage < 50) {
        return 'battery-fill medium';
    } else {
        return 'battery-fill';
    }
}

// Fungsi untuk mendapatkan status indicator
function getStatusIndicator($status) {
    if ($status == 'online') {
        return '<span class="status-indicator status-online"></span>Online';
    } else {
        return '<span class="status-indicator status-warning"></span>Standby';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hexapod Robot Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('Banner.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            min-height: 100vh;
            padding: 20px;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: rgba(30, 30, 30, 0.75);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #FFD700;
            border-bottom: 2px solid rgba(255, 215, 0, 0.5);
            padding-bottom: 10px;
        }

        .info-item {
            margin: 12px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-label {
            font-weight: bold;
            color: #aaa;
        }

        .info-value {
            color: #fff;
            font-size: 1.1em;
        }

        .sensor-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .sensor-item {
            background: rgba(50, 50, 50, 0.6);
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }

        .sensor-label {
            font-size: 0.9em;
            color: #aaa;
            margin-bottom: 5px;
        }

        .sensor-value {
            font-size: 1.2em;
            font-weight: bold;
            color: #00ff88;
        }

        .battery {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .battery-bar {
            flex: 1;
            height: 30px;
            background: rgba(50, 50, 50, 0.8);
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }

        .battery-fill {
            height: 100%;
            background: linear-gradient(90deg, #00ff88, #00cc66);
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .battery-fill.low {
            background: linear-gradient(90deg, #ff4444, #cc0000);
        }

        .battery-fill.medium {
            background: linear-gradient(90deg, #ffaa00, #ff8800);
        }

        .clock {
            font-size: 2em;
            text-align: center;
            color: #FFD700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .date {
            font-size: 1.2em;
            text-align: center;
            color: #aaa;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(255, 215, 0, 0.2);
            color: #FFD700;
            font-weight: bold;
        }

        td {
            background: rgba(50, 50, 50, 0.4);
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-online {
            background: #00ff88;
            box-shadow: 0 0 10px #00ff88;
        }

        .status-warning {
            background: #ffaa00;
            box-shadow: 0 0 10px #ffaa00;
        }

        .robot-showcase {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .robot-image {
            background: rgba(30, 30, 30, 0.75);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .robot-image img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            display: block;
            margin: 0 auto;
        }

        .robot-description {
            display: flex;
            flex-direction: column;
        }

        .description-content {
            flex: 1;
        }

        .description-content p {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #ddd;
        }

        .description-content ul {
            list-style: none;
            padding: 0;
        }

        .description-content ul li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            color: #ddd;
        }

        .description-content ul li:before {
            content: "▸";
            position: absolute;
            left: 0;
            color: #FFD700;
            font-weight: bold;
        }

        @media (max-width: 968px) {
            .robot-showcase {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    
    <div class="container">
        <h1>ROBOT HEXAPOD BARELANG F.1</h1>
        
        <!-- Robot Image and Description Section -->
        <div class="robot-showcase">
            <div class="robot-image">
                <img src="NewHexapod.png" alt="Hexapod Robot">
            </div>
            <div class="card robot-description">
                <div class="card-title">Tentang Robot Hexapod BARELANG F.1</div>
                <div class="description-content">
                    <p>
                        Halo, saya Aldon dan ini adalah robot PBL saya di tim Barelang F.1 yang berletak di BRAIL. 
                        <br>
                        Robot ini adalah hasil dari tim PBL Barelang F.1 yang dimana robot ini terdiri dari 6 kaki dayng dimana di setiap kaki memiliki 3 servo yang merepresentasikan Coxa, Femur, dan Tibia yang digunakan untuk pergerakan robot di medan tidak rata.
                    </p>
                    <p>
                        Robot hexapod ini dilengkapi dengan berbagai sensor dan juga sistem kontrol gerak untuk pergerakan yang stabil.
                    </p>
                    <ul>
                        <li>18 Servo motor untuk 6 kaki (3 di setiap kaki)</li>
                        <li>IMU BNO055 untuk stabilisasi dan orientasi arah</li>
                        <li>Sistem kontrol berbasis mini computer (Single Board Computer)</li>
                        <li>Navigasi autonomous dan juga kendali manual menggunakan joystick</li>
                    </ul>
                    <p>
                        Berikut dibawah ini adalah contoh data-data dummy atau data palsu untuk percobaan saya dalam pembuatan website ini.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <!-- Biodata Card -->
            <div class="card">
                <div class="card-title">📋 Biodata</div>
                <div class="info-item">
                    <span class="info-label">Nama:</span>
                    <span class="info-value"><?php echo $nama; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">NIM:</span>
                    <span class="info-value"><?php echo $nim; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jurusan:</span>
                    <span class="info-value"><?php echo $jurusan; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Program Studi:</span>
                    <span class="info-value"><?php echo $prodi; ?></span>
                </div>
            </div>

            <!-- Waktu Realtime Card -->
            <div class="card">
                <div class="card-title">🕐 Waktu Realtime</div>
                <div class="clock" id="clock">00:00:00</div>
                <div class="date" id="date">Loading...</div>
            </div>

            <!-- Battery Status Card -->
            <div class="card">
                <div class="card-title">🔋 Status Baterai</div>
                <div class="battery">
                    <div class="battery-bar">
                        <div class="<?php echo getBatteryClass($battery_percentage); ?>" style="width: <?php echo $battery_percentage; ?>%">
                            <?php echo $battery_percentage; ?>%
                        </div>
                    </div>
                </div>
                <div class="info-item" style="margin-top: 15px;">
                    <span class="info-label">Voltage:</span>
                    <span class="info-value"><?php echo $voltage; ?>V</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Current:</span>
                    <span class="info-value"><?php echo $current; ?>A</span>
                </div>
            </div>
        </div>

        <!-- IMU Sensor BNO055 Card -->
        <div class="card">
            <div class="card-title">🎯 Sensor IMU BNO055</div>
            <div class="sensor-grid">
                <div class="sensor-item">
                    <div class="sensor-label">Roll (°)</div>
                    <div class="sensor-value"><?php echo $imu_data['roll']; ?></div>
                </div>
                <div class="sensor-item">
                    <div class="sensor-label">Pitch (°)</div>
                    <div class="sensor-value"><?php echo $imu_data['pitch']; ?></div>
                </div>
                <div class="sensor-item">
                    <div class="sensor-label">Yaw (°)</div>
                    <div class="sensor-value"><?php echo $imu_data['yaw']; ?></div>
                </div>
                <div class="sensor-item">
                    <div class="sensor-label">Accel X (m/s²)</div>
                    <div class="sensor-value"><?php echo $imu_data['accel_x']; ?></div>
                </div>
                <div class="sensor-item">
                    <div class="sensor-label">Accel Y (m/s²)</div>
                    <div class="sensor-value"><?php echo $imu_data['accel_y']; ?></div>
                </div>
                <div class="sensor-item">
                    <div class="sensor-label">Accel Z (m/s²)</div>
                    <div class="sensor-value"><?php echo $imu_data['accel_z']; ?></div>
                </div>
                <div class="sensor-item">
                    <div class="sensor-label">Gyro X (°/s)</div>
                    <div class="sensor-value"><?php echo $imu_data['gyro_x']; ?></div>
                </div>
                <div class="sensor-item">
                    <div class="sensor-label">Gyro Y (°/s)</div>
                    <div class="sensor-value"><?php echo $imu_data['gyro_y']; ?></div>
                </div>
                <div class="sensor-item">
                    <div class="sensor-label">Gyro Z (°/s)</div>
                    <div class="sensor-value"><?php echo $imu_data['gyro_z']; ?></div>
                </div>
            </div>
        </div>

        <br> 

        <!-- Sensor Project Table -->
        <div class="card">
            <div class="card-title">📊 Daftar Sensor Project PBL</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Sensor</th>
                        <th>Status</th>
                        <th>Nilai</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sensors as $sensor): ?>
                    <tr>
                        <td><?php echo $sensor['no']; ?></td>
                        <td><?php echo $sensor['nama']; ?></td>
                        <td><?php echo getStatusIndicator($sensor['status']); ?></td>
                        <td><?php echo $sensor['nilai']; ?></td>
                        <td><?php echo $sensor['satuan']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Update waktu realtime
        function updateTime() {
            const now = new Date();
            
            // Format waktu
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
            
            // Format tanggal
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            document.getElementById('date').textContent = 
                `${dayName}, ${date} ${monthName} ${year}`;
        }

        // Update setiap detik
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>