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

// Fetch current user data
$stmt = $pdo->prepare("SELECT username, email, nim, nama, jurusan, prodi, address, phone FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - <?php echo $user['nama']; ?></title>
    <style>
        /* Dark theme: Based on original login/register styles, adapted for black cards, yellow headers, white text */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-image: url('../Banner.png'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; color: #fff; }
        .login-container { background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5); width: 100%; max-width: 500px; border: 2px solid rgba(255, 215, 0, 0.3); animation: fadeIn 0.5s ease-in; color: #fff; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        h2 { text-align: center; color: #FFD700; margin-bottom: 30px; font-size: 32px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8); font-weight: 700; letter-spacing: 1px; }
        p { margin: 20px 0 8px 0; }
        label, p { color: #FFD700; font-weight: 500; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8); }
        input[type="text"], input[type="tel"], input[type="password"], input[type="email"] { width: 100%; padding: 12px 15px; border: 2px solid rgba(255, 215, 0, 0.3); border-radius: 10px; background: rgba(255, 255, 255, 0.1); color: #fff; font-size: 15px; transition: all 0.3s ease; margin-top: 5px; }
        input[type="text"]:focus, input[type="tel"]:focus, input[type="password"]:focus, input[type="email"]:focus { outline: none; border-color: #FFD700; background: rgba(255, 255, 255, 0.2); box-shadow: 0 0 15px rgba(255, 215, 0, 0.5); transform: translateY(-2px); }
        input::placeholder { color: rgba(255,255,255,0.7); }
        .readonly { background: rgba(255, 215, 0, 0.1); color: #FFD700; border-color: rgba(255,215,0,0.5); }
        button { width: 100%; padding: 14px; background: linear-gradient(135deg, #ff6b9d 0%, #e2cd11 100%); color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-top: 10px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(255, 107, 157, 0.4); }
        button:hover { background: linear-gradient(135deg, #ff5a8d 0%, #b85dc7 100%); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255, 107, 157, 0.6); }
        button:active { transform: translateY(0); }
        .error { color: #ff4444; background: rgba(0,0,0,0.8); padding: 5px 10px; border-radius: 5px; font-size: 12px; display: none; margin-top: 5px; font-weight: 600; }
        #errorPopup, #successPopup { position: fixed; top: 0; left: 0; width: 50%; height: 20%; background-color: rgba(0, 0, 0, 0.7); display: none; z-index: 999; backdrop-filter: blur(5px); }
        #overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); display: none; z-index: 999; backdrop-filter: blur(5px); }
        #errorPopup, #successPopup { top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; padding: 20px; border-radius: 15px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5); z-index: 1000; width: 80%; max-width: 350px; min-width: 250px; text-align: center; animation: popupSlide 0.3s ease; border: 2px solid rgba(255,215,0,0.3); }
        @keyframes popupSlide { from { opacity: 0; transform: translate(-50%, -60%); } to { opacity: 1; transform: translate(-50%, -50%); } }
        #errorPopup { background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%); }
        #successPopup { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); }
        #errorPopup p, #successPopup p { margin-bottom: 20px; font-size: 16px; color: #fff; }
        #errorPopup button, #successPopup button { width: auto; padding: 10px 30px; background: rgba(255,215,0,0.2); color: #fff; font-weight: 700; border: 1px solid #FFD700; border-radius: 5px; cursor: pointer; transition: all 0.3s; }
        #errorPopup button:hover, #successPopup button:hover { background: rgba(255,215,0,0.4); transform: translateY(-1px); }
        .link { text-align: center; margin-top: 20px; }
        .link a { color: #FFD700; text-decoration: none; font-weight: bold; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); }
        .link a:hover { text-decoration: underline; }
        @media (max-width: 480px) { .login-container { padding: 30px 20px; } h2 { font-size: 26px; } #errorPopup, #successPopup { width: 90%; padding: 15px; } }
    </style>
</head>
<body>
    <div id="overlay"></div>
    <div id="errorPopup">
        <p id="errorMessage"></p>
        <button onclick="closePopup()">OK</button>
    </div>
    <div id="successPopup">
        <p id="successMessage"></p>
        <button onclick="closePopup()">OK</button>
    </div>

    <div class="login-container">
        <h2>Edit Profil</h2>
        <form id="profileForm" action="update_profile.php" method="POST">
            <p>Username:</p>
            <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" class="readonly" readonly>

            <p>Email:</p>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="readonly" readonly>

            <p>NIM:</p>
            <input type="text" value="<?php echo htmlspecialchars($user['nim']); ?>" class="readonly" readonly>

            <p>Nama:</p>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
            <span class="error" id="namaError"></span>

            <p>Jurusan:</p>
            <input type="text" name="jurusan" value="<?php echo htmlspecialchars($user['jurusan']); ?>" required>
            <span class="error" id="jurusanError"></span>

            <p>Prodi:</p>
            <input type="text" name="prodi" value="<?php echo htmlspecialchars($user['prodi']); ?>" required>
            <span class="error" id="prodiError"></span>

            <p>Alamat:</p>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required maxlength="255">
            <span class="error" id="addressError"></span>

            <p>No. Telepon:</p>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required pattern="[0-9+]+" title="Hanya angka dan +">
            <span class="error" id="phoneError"></span>

            <p>Password Baru (kosongkan jika tidak ingin ubah):</p>
            <input type="password" name="password" minlength="5">
            <span class="error" id="passwordError"></span>

            <p><button type="submit">Update Profil</button></p>
        </form>
        <div class="link"><a href="user_dashboard.php">Kembali ke Dashboard</a></div>
    </div>

    <script>
        // Client-side validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            let valid = true;
            const nama = document.querySelector('input[name="nama"]').value.trim();
            const jurusan = document.querySelector('input[name="jurusan"]').value.trim();
            const prodi = document.querySelector('input[name="prodi"]').value.trim();
            const address = document.querySelector('input[name="address"]').value.trim();
            const phone = document.querySelector('input[name="phone"]').value.trim();
            const password = document.querySelector('input[name="password"]').value;

            if (!nama) { showError('Nama tidak boleh kosong!'); valid = false; }
            if (!jurusan) { showError('Jurusan tidak boleh kosong!'); valid = false; }
            if (!prodi) { showError('Prodi tidak boleh kosong!'); valid = false; }
            if (!address) { showError('Alamat tidak boleh kosong!'); valid = false; }
            if (!/^[0-9+]+$/.test(phone)) { showError('No. Telepon hanya angka dan +!'); valid = false; }
            if (password && password.length < 5) { showError('Password minimal 5 karakter!'); valid = false; }

            if (!valid) e.preventDefault();
        });

        // Check URL params for messages
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        const error = urlParams.get('error');
        if (error) {
            showError(decodeURIComponent(error));
            window.history.replaceState({}, document.title, window.location.pathname);
        } else if (success) {
            showSuccess(decodeURIComponent(success));
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorPopup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function showSuccess(message) {
            document.getElementById('successMessage').textContent = message;
            document.getElementById('successPopup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('errorPopup').style.display = 'none';
            document.getElementById('successPopup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    </script>
</body>
</html>