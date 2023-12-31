<?php
include 'dbconfig.php';
include 'karyawan.php';

// mengambil data dari database
$query = "SELECT * FROM karyawan";
$result = $connectionStr->query($query);

// menyimpan data dalam array
$karyawanData = array();
while ($row = $result->fetch_assoc()) {
    $karyawan = new Karyawan($row['nik'], $row['nama'], $row['upah_per_jam'], $row['jam_kerja'], $row['jam_lembur']);
    $karyawanData[] = $karyawan;
}

// memperbarui jam lembur
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($karyawanData as $karyawan) {
        $newJamLembur = $_POST["jamLembur_" . $karyawan->nik];

        // Update data di database
        $updateQuery = "UPDATE karyawan SET jam_lembur = '$newJamLembur' WHERE nik = '{$karyawan->nik}'";
        $connectionStr->query($updateQuery);

        // Update data jam lembur di objek Karyawan
        $karyawan->jam_lembur = $newJamLembur;
    }
}

// menghitung rekap total gaji tiap karyawan 
$rekapMingguan = array();

foreach ($karyawanData as $karyawan) {
    $rekapMingguan[$karyawan->nik] = array();

    // menghitung total gaji per minggu
    for ($mingguKe = 1; $mingguKe <= 5; $mingguKe++) {
        $totalGajiMingguan = $karyawan->hitungGaji();
        $rekapMingguan[$karyawan->nik][$mingguKe] = $totalGajiMingguan;

    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Gaji Karyawan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Sistem Gaji Karyawan</h1>

    <h4>Masukkan Jam Lembur</h4>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <table>
        <thead>
            <tr>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Upah Per Jam</th>
                <th>Jam Lembur</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($karyawanData as $karyawan): ?>
                <tr>
                    <td><?php echo $karyawan->nik; ?></td>
                    <td><?php echo $karyawan->nama; ?></td>
                    <td><?php echo "Rp." . number_format($karyawan->upah_per_jam, 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td><input type="number" name="jamLembur_<?php echo $karyawan->nik; ?>" value="<?php echo $karyawan->jam_lembur; ?>"></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>

        <button type="submit">Lihat Gaji</button>

    <h4>Rekap Gaji Mingguan</h4>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <table>
        <thead>
            <tr>
                <th>Minggu Ke-1</th>
                <th>Minggu Ke-2</th>
                <th>Minggu Ke-3</th>
                <th>Minggu Ke-4</th>
                <th>Minggu Ke-5</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($karyawanData as $karyawan): ?>
                <tr>
                    <?php for ($mingguKe = 1; $mingguKe <= 5; $mingguKe++): ?>
                        <td><?php echo "Rp." . number_format($rekapMingguan[$karyawan->nik][$mingguKe], 0, ',', '.'); ?></td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>



    </form>
</body>
</html>

<?php
// Menutup connectionStr ke database
$connectionStr->close();
?>
