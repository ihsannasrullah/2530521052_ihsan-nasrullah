<?php
$nim = "2530521052";
$nama = "Ihsan Nasrullah";
$kelas = "3B";
$matkul = "Pemrograman Berorientasi Objek";

$pesan = "";
$data = [];

function bersihkanData($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim = bersihkanData($_POST["nim"] ?? "");
    $nama = bersihkanData($_POST["nama"] ?? "");
    $kelas = bersihkanData($_POST["kelas"] ?? "");
    $nilai = (int) ($_POST["nilai"] ?? 0);
    $semester = bersihkanData($_POST["semester"] ?? "");

    if ($nim != "" && $nama != "" && $kelas != "") {
        if ($nilai >= 70) {
            $keterangan = "Lulus";
        } else {
            $keterangan = "Tidak Lulus";
        }

        $data = [
            "nim" => $nim,
            "nama" => $nama,
            "kelas" => $kelas,
            "matkul" => $matkul,
            "semester" => $semester,
            "nilai" => $nilai,
            "keterangan" => $keterangan
        ];

        $pesan = "Data mahasiswa berhasil diproses.";
    } else {
        $pesan = "NIM, nama, dan kelas wajib diisi.";
    }
}

$daftarSemester = [
    1, 2, 3, 4, 5, 6, 7, 8
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Mahasiswa</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px 15px;
        }

        .container {
            max-width: 750px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 12px #ddd;
        }

        h1 {
            text-align: center;
            color: #39b817;
            font-size: 30px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 7px;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 13px;
            margin-top: 22px;
            border: none;
            border-radius: 5px;
            background: #168bd2;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #0874b5;
        }

        .hasil {
            margin-top: 25px;
            padding: 18px;
            background: #eef8ee;
            border-radius: 6px;
            overflow-wrap: anywhere;
        }

        .pesan {
            color: #187b25;
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="container">
    <h1>Input Data Mahasiswa</h1>

    <form method="POST">
        <label>NIM</label>
        <input type="text" name="nim"
            value="<?php echo $nim; ?>" required>

        <label>Nama Mahasiswa</label>
        <input type="text" name="nama"
            value="<?php echo $nama; ?>" required>

        <label>Kelas</label>
        <input type="text" name="kelas"
            value="<?php echo $kelas; ?>" required>

        <label>Mata Kuliah</label>
        <input type="text" value="<?php echo $matkul; ?>"
            readonly>

        <label>Semester</label>
        <select name="semester" required>
            <option value="">-- Pilih Semester --</option>
            <?php
            foreach ($daftarSemester as $s) {
                echo "<option value='$s'>Semester $s</option>";
            }
            ?>
        </select>

        <label>Nilai Mahasiswa</label>
        <input type="number" name="nilai"
            min="0" max="100" value="85" required>

        <button type="submit">Simpan Data</button>
    </form>

    <?php if ($pesan != ""): ?>
        <div class="hasil">
            <p class="pesan">
                <?php echo $pesan; ?>
            </p>

            <?php if (!empty($data)): ?>
                <h3>Hasil Data Mahasiswa</h3>
                <?php
                foreach ($data as $label => $isi) {
                    echo "<p><b>" .
                        ucfirst($label) .
                        ":</b> " . $isi . "</p>";
                }
                ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>