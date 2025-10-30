<?php
    $suhu = 29;
?>
<!DOCTYPE html>
<html>
<head><title>Percabangan PHP</title></head>
    <body>
        <h2>Monitoring Suhu</h2>
        <p>Suhu Ruangan: <?= $suhu ?>°C</p>
        <?php
        if ($suhu > 30) {
        echo "<p style='color:red'>Peringatan! Suhu terlalu panas.</p>";
        } else {
        echo "<p style='color:green'>Suhu aman.</p>";
        }
        ?>
    </body>
</html>