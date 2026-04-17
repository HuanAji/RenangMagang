<?php
include 'db.php';
$result = $conn->query("SELECT * FROM hasil_lomba ORDER BY timestamp DESC");

$no = 1;
while($row = $result->fetch_assoc()) {
    // Ambil waktu dari format, misal "01:23:456"
    $format = $row['waktu_format']; // "01:23:456"
    $waktu_menit = $waktu_detik = $waktu_ms = '-';

   $parts = explode(':', $format); // pisah menit dan sisanya
    if (count($parts) === 2) {
        $waktu_menit = $parts[0];
        $subparts = explode('.', $parts[1]);
        if (count($subparts) === 2) {
            $waktu_detik = $subparts[0];
            $waktu_ms = $subparts[1];
        }
}


    echo "<tr>
            <td>".$no++."</td>
            <td>".$row['player']."</td>
            <td>".$waktu_menit."</td>
            <td>".$waktu_detik."</td>
            <td>".$waktu_ms."</td>
            <td>".$format."</td>
            <td>".$row['timestamp']."</td>
          </tr>";
}
?>
