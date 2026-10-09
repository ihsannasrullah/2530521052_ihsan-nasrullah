<?php
$nama = "Ihsan Nasrullah";
$nim = "2530521052";
$kelas = "3B";
$matkul = "Pemrograman Berorientasi Objek";

$semesterList = [1, 2, 3, 4, 5, 6, 7, 8];
$hasil = null;
$pesan = "";

function bersihkanData($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, "UTF-8");
}

function tentukanGrade($nilai) {
    if ($nilai >= 80) {
        return "A";
    } elseif ($nilai >= 70) {
        return "B";
    } elseif ($nilai >= 60) {
        return "C";
    } else {
        return "D";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nim = bersihkanData($_POST["nim"] ?? "");
    $nama = bersihkanData($_POST["nama"] ?? "");
    $kelas = bersihkanData($_POST["kelas"] ?? "");
    $semester = (int) ($_POST["semester"] ?? 0);
    $nilai = filter_var(
        $_POST["nilai"] ?? null,
        FILTER_VALIDATE_INT
    );

    if (
        $nim === "" || $nama === "" || $kelas === "" ||
        !in_array($semester, $semesterList, true) ||
        $nilai === false || $nilai === null ||
        $nilai < 0 || $nilai > 100
    ) {
        $pesan = "Periksa kembali data yang kamu masukkan.";
    } else {
        $grade = tentukanGrade($nilai);
        $keterangan = $nilai >= 70 ? "Lulus" : "Belum lulus";

        $hasil = [
            "NIM" => $nim,
            "Nama" => $nama,
            "Kelas" => $kelas,
            "Mata Kuliah" => $matkul,
            "Semester" => $semester,
            "Nilai Akhir" => $nilai,
            "Grade" => $grade,
            "Keterangan" => $keterangan
        ];

        $pesan = "Data berhasil diproses.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentDesk | Data Mahasiswa</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px 18px;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f3f6fa;
            color: #182338;
        }

        .container {
            max-width: 850px;
            margin: auto;
        }

        .header {
            margin-bottom: 28px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: #197b68;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 18px;
        }

        .brand-icon {
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            border-radius: 11px;
            background: #d9f5e9;
            font-size: 19px;
        }

        h1 {
            margin: 0 0 10px;
            font-size: clamp(27px, 5vw, 36px);
            letter-spacing: -1px;
        }

        .subtitle {
            margin: 0;
            color: #718096;
            line-height: 1.7;
            font-size: 14px;
        }

        .card {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e7ecf2;
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(29, 48, 75, 0.06);
        }

        .section-title {
            margin: 0 0 6px;
            font-size: 19px;
        }

        .section-description {
            margin: 0 0 25px;
            color: #8490a2;
            font-size: 13px;
            line-height: 1.6;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .field {
            min-width: 0;
        }

        .full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #344258;
            font-size: 13px;
            font-weight: 600;
        }

        input, select {
            width: 100%;
            min-height: 46px;
            padding: 12px 14px;
            border: 1px solid #dfe5ed;
            border-radius: 10px;
            background: #ffffff;
            color: #253248;
            font: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus, select:focus {
            border-color: #249b80;
            box-shadow: 0 0 0 3px rgba(36, 155, 128, .12);
        }

        input[readonly] {
            background: #f5f7fa;
            color: #65738a;
        }

        .hint {
            display: block;
            margin-top: 7px;
            color: #929daf;
            font-size: 11px;
        }

        .button {
            width: 100%;
            min-height: 49px;
            margin-top: 25px;
            border: none;
            border-radius: 11px;
            background: #176b59;
            color: #ffffff;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        .button:hover {
            background: #105545;
            transform: translateY(-1px);
        }

        .notice {
            margin-top: 22px;
            padding: 14px 16px;
            border-radius: 10px;
            background: #e9f8f0;
            color: #176b49;
            font-size: 13px;
        }

        .notice.error {
            background: #fff0f0;
            color: #b42318;
        }

        .result {
            margin-top: 25px;
            padding: 24px;
            background: #f8fbfa;
            border: 1px solid #e0eee8;
            border-radius: 15px;
        }

        .result h2 {
            margin: 0 0 18px;
            font-size: 19px;
        }

        .result-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #e7eeeb;
            font-size: 13px;
        }

        .result-row:last-child {
            border-bottom: none;
        }

        .result-row span:first-child {
            color: #738174;
        }

        .result-row strong {
            text-align: right;
            overflow-wrap: anywhere;
        }

        .footer {
            padding: 22px 0 5px;
            text-align: center;
            color: #98a2b3;
            font-size: 12px;
        }

        @media (max-width: 560px) {
            body {
                padding: 25px 13px;
            }

            .card {
                padding: 22px 18px;
                border-radius: 16px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 17px;
            }

            .full {
                grid-column: auto;
            }

            .result {
                padding: 18px 14px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <header class="header">
        <div class="brand">
            <span class="brand-icon">✦</span>
            STUDENTDESK
        </div>

        <h1>Data Mahasiswa</h1>

        <p class="subtitle">
            Lengkapi data akademik dan nilai mahasiswa
            melalui formulir berikut.
        </p>
    </header>

    <main class="card">
        <h2 class="section-title">Informasi Akademik</h2>

        <p class="section-description">
            Masukkan identitas dan data perkuliahan dengan benar.
        </p>

        <form method="POST">
            <div class="form-grid">

                <div class="field">
                    <label for="nim">NIM</label>
                    <input
                        type="text"
                        id="nim"
                        name="nim"
                        value="<?php echo $nim; ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="nama">Nama Mahasiswa</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="<?php echo $nama; ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="kelas">Kelas</label>
                    <input
                        type="text"
                        id="kelas"
                        name="kelas"
                        value="<?php echo $kelas; ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="matkul">Mata Kuliah</label>
                    <input
                        type="text"
                        id="matkul"
                        value="<?php echo $matkul; ?>"
                        readonly
                    >
                </div>

                <div class="field">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" required>
                        <option value="">Pilih semester</option>

                        <?php foreach ($semesterList as $s): ?>
                            <option
                                value="<?php echo $s; ?>"
                                <?php
                                if (
                                    isset($_POST["semester"]) &&
                                    (int) $_POST["semester"] === $s
                                ) {
                                    echo "selected";
                                }
                                ?>
                            >
                                Semester <?php echo $s; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="nilai">Nilai Mahasiswa</label>
                    <input
                        type="number"
                        id="nilai"
                        name="nilai"
                        min="0"
                        max="100"
                        value="<?php
                            echo isset($_POST["nilai"])
                                ? bersihkanData($_POST["nilai"])
                                : "85";
                        ?>"
                        required
                    >
                    <small class="hint">Masukkan nilai dari 0 sampai 100.</small>
                </div>

            </div>

            <button class="button" type="submit">
                Simpan dan Proses Data
            </button>
        </form>

        <?php if ($pesan !== ""): ?>
            <div class="notice <?php echo $hasil === null ? 'error' : ''; ?>">
                <?php echo $pesan; ?>
            </div>
        <?php endif; ?>

        <?php if ($hasil !== null): ?>
            <section class="result">
                <h2>Hasil Pengolahan Data</h2>

                <?php foreach ($hasil as $label => $isi): ?>
                    <div class="result-row">
                        <span><?php echo $label; ?></span>
                        <strong><?php echo $isi; ?></strong>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer class="footer">
        Praktikum PHP · Pemrograman Berorientasi Objek · Kelas 3B
    </footer>

</div>
</body>
</html>