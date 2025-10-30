<?php
    $sensor = ["DHT11", "MQ-2", "Ultrasonik", "LDR"];
?>

<!DOCTYPE html>
<html>
<head><title>Loop PHP TEST</title></head>
    <body>
        <h2>Daftar Sensor Robot Hexapod Barelang F-1</h2>
        <ul>
        <?php foreach ($sensor as $item): ?>
        <li><?= $item ?></li>
        <?php endforeach; ?>
        </ul>
    </body>
</html>