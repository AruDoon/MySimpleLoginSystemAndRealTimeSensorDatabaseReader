<?php
    function luasLingkaran($r) {
    return 3.14 * $r * $r;
    }
?>

<!DOCTYPE html>
<html>
<head><title>Fungsi PHP</title></head>
    <body>
        <h2>Hitung Luas Langkah Robot Hexapod</h2>
        <?php
        $jari = 7;
        echo "Jari-jari: $jari cm<br>";
        echo "Luas: " . luasLingkaran($jari) . " cm²";
        ?>
    </body>
</html>