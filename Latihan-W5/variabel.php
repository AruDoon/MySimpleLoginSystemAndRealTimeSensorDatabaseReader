<?php
    $nama = "Aldon Zufar Putra Twyn";
    $umur = 20;
    $ipk = 9.99;
    $lulus = true;
?>
<!DOCTYPE html>
<html>
<head><title>Variabel PHP</title></head>
    <body>
        <h2>Data Mahasiswa</h2>
        <p>Nama : <?= $nama ?></p>
        <p>Umur : <?= $umur ?> tahun</p>
        <p>IPK : <?= $ipk ?></p>
        <p>Status Lulus : <?= $lulus ? "Ya" : "Belum" ?></p>
    </body>
</html>