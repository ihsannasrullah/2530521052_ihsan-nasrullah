<?php
$nama = "Ihsan Nasrullah";
$nim = "2530521052";
$kelas = "3B";
$prodi = "Teknik Informatika";
$matkul = "Pemrograman Berorientasi Objek";
$tahunAkademik = "2026/2027";

$semesterList = [1, 2, 3, 4, 5, 6, 7, 8];
$jenisList = ["Tugas", "UTS", "UAS"];
$batasLulus = 70.0;

$semester = "3";
$jenis = "Tugas";
$nilaiInput = "85";
$hasil = null;
$pesan = "";
$berhasil = false;

function e($data) {
    return htmlspecialchars(
        (string) $data,
        ENT_QUOTES,
        "UTF-8"
    );
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
    $nim = trim($_POST["nim"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $kelas = trim($_POST["kelas"] ?? "");
    $semester = $_POST["semester"] ?? "";
    $jenis = $_POST["jenis"] ?? "";
    $nilaiInput = $_POST["nilai"] ?? "";

    $nilai = filter_var(
        $nilaiInput,
        FILTER_VALIDATE_INT
    );

    if (
        $nim === "" ||
        $nama === "" ||
        $kelas === "" ||
        !in_array($semester, array_map("strval", $semesterList), true) ||
        !in_array($jenis, $jenisList, true) ||
        $nilai === false ||
        $nilai === null ||
        $nilai < 0 ||
        $nilai > 100
    ) {
        $pesan = "Periksa kembali data yang kamu masukkan.";
    } else {
        $lulus = $nilai >= $batasLulus;
        $grade = tentukanGrade($nilai);

        $hasil = [
            "NIM" => $nim,
            "Nama Mahasiswa" => $nama,
            "Kelas" => $kelas,
            "Program Studi" => $prodi,
            "Mata Kuliah" => $matkul,
            "Tahun Akademik" => $tahunAkademik,
            "Semester" => $semester,
            "Jenis Penilaian" => $jenis,
            "Nilai Akhir" => $nilai,
            "Grade" => $grade,
            "Status" => $lulus ? "Lulus" : "Belum Lulus"
        ];

        $pesan = "Data berhasil diproses.";
        $berhasil = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentDesk - Data Mahasiswa</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --primary: #176b59;
            --primary-dark: #105342;
            --text: #1d2939;
            --muted: #758195;
            --border: #e4e9f0;
            --background: #f4f7fb;
        }

        body {
            margin: 0;
            padding: 38px 18px;
            background: var(--background);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 27px;
        }

        .brand-icon {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            border-radius: 13px;
            background: #d9f5e9;
            color: var(--primary);
            font-size: 23px;
            font-weight: bold;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 1.3px;
        }

        .brand-caption {
            margin-top: 3px;
            color: var(--muted);
            font-size: 11px;
        }

        .page-heading {
            margin-bottom: 25px;
        }

        h1 {
            margin: 0 0 9px;
            font-size: clamp(27px, 4vw, 35px);
            letter-spacing: -1px;
        }

        .subtitle {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .card {
            padding: 30px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: 0 12px 35px rgba(25, 45, 70, 0.055);
        }

        .card-heading {
            margin-bottom: 25px;
        }

        h2 {
            margin: 0 0 7px;
            font-size: 20px;
        }

        .description {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
        }

        .section-label {
            margin: 27px 0 17px;
            padding-bottom: 11px;
            border-bottom: 1px solid var(--border);
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
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
            font-size: 13px;
            font-weight: 650;
        }

        input, select {
            width: 100%;
            min-height: 47px;
            padding: 12px 13px;
            border: 1px solid #dce3ec;
            border-radius: 10px;
            background: #fff;
            color: var(--text);
            font: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus, select:focus {
            border-color: #29977c;
            box-shadow: 0 0 0 3px rgba(41, 151, 124, .12);
        }

        input[readonly] {
            background: #f5f7fa;
            color: #69778c;
        }

        .hint {
            display: block;
            margin-top: 7px;
            color: #8994a5;
            font-size: 11px;
        }

        .button {
            display: block;
            width: 100%;
            min-height: 49px;
            margin-top: 28px;
            border: 0;
            border-radius: 11px;
            background: var(--primary);
            color: white;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        .button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .notice {
            margin-top: 22px;
            padding: 14px 16px;
            border-radius: 10px;
            background: #e7f7ed;
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
            border: 1px solid #dcece4;
            border-radius: 15px;
            background: #f8fbf9;
        }

        .result-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 17px;
        }

        .result-header h2 {
            margin: 0;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 30px;
            background: #d9f5e5;
            color: #176b49;
            font-size: 11px;
            font-weight: 700;
        }

        .result-row {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            padding: 12px 0;
            border-bottom: 1px solid #e5eee8;
            font-size: 13px;
        }

        .result-row:last-child {
            border-bottom: 0;
        }

        .result-row span {
            color: var(--muted);
        }

        .result-row strong {
            text-align: right;
            overflow-wrap: anywhere;
        }

        .grade-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 18px;
            padding: 19px;
            border-radius: 13px;
            background: #e5f5ed;
        }

        .grade-number {
            color: var(--primary);
            font-size: 31px;
            font-weight: 800;
        }

        .grade-label {
            margin-bottom: 5px;
            color: #61786d;
            font-size: 12px;
        }

        .status {
            padding: 8px 12px;
            border-radius: 8px;
            background: white;
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
        }

        .footer {
            padding: 23px 0 4px;
            color: #929dad;
            text-align: center;
            font-size: 12px;
            line-height: 1.8;
        }

        @media (max-width: 600px) {
            body {
                padding: 24px 12px;
            }

            .card {
                padding: 22px 17px;
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

    <header>
        <div class="brand">
            <div class="brand-icon">✦</div>
            <div>
                <div class="brand-name">STUDENTDESK</div>
                <div class="brand-caption">Student Academic Portal</div>
            </div>
        </div>

        <div class="page-heading">
            <h1>Data Mahasiswa</h1>
            <p class="subtitle">
                Kelola informasi akademik dan proses penilaian
                mahasiswa dengan mudah.
            </p>
        </div>
    </header>

    <main class="card">
        <div class="card-heading">
            <h2>Informasi Akademik</h2>
            <p class="description">
                Lengkapi data mahasiswa dan informasi penilaian di bawah ini.
            </p>
        </div>

        <form method="POST">
            <div class="section-label">Identitas Mahasiswa</div>

            <div class="form-grid">
                <div class="field">
                    <label for="nim">NIM</label>
                    <input
                        type="text"
                        id="nim"
                        name="nim"
                        value="<?php echo e($nim); ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="nama">Nama Mahasiswa</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="<?php echo e($nama); ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="kelas">Kelas</label>
                    <input
                        type="text"
                        id="kelas"
                        name="kelas"
                        value="<?php echo e($kelas); ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="prodi">Program Studi</label>
                    <input
                        type="text"
                        id="prodi"
                        value="<?php echo e($prodi); ?>"
                        readonly
                    >
                </div>
            </div>

            <div class="section-label">Informasi Perkuliahan</div>

            <div class="form-grid">
                <div class="field full">
                    <label for="matkul">Mata Kuliah</label>
                    <input
                        type="text"
                        id="matkul"
                        value="<?php echo e($matkul); ?>"
                        readonly
                    >
                </div>

                <div class="field">
                    <label for="tahun">Tahun Akademik</label>
                    <input
                        type="text"
                        id="tahun"
                        value="<?php echo e($tahunAkademik); ?>"
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
                                echo (string) $semester === (string) $s
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Semester <?php echo $s; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="jenis">Jenis Penilaian</label>
                    <select id="jenis" name="jenis" required>
                        <?php foreach ($jenisList as $j): ?>
                            <option
                                value="<?php echo e($j); ?>"
                                <?php echo $jenis === $j ? "selected" : ""; ?>
                            >
                                <?php echo e($j); ?>
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
                        step="1"
                        value="<?php echo e($nilaiInput); ?>"
                        required
                    >
                    <small class="hint">
                        Masukkan nilai antara 0 sampai 100.
                    </small>
                </div>
            </div>

            <button class="button" type="submit">
                Simpan dan Proses Data &rarr;
            </button>
        </form>

        <?php if ($pesan !== ""): ?>
            <div class="notice <?php echo $berhasil ? "" : "error"; ?>">
                <?php echo e($pesan); ?>
            </div>
        <?php endif; ?>

        <?php if ($hasil !== null): ?>
            <section class="result">
                <div class="result-header">
                    <h2>Hasil Penilaian</h2>
                    <span class="badge">Berhasil diproses</span>
                </div>

                <?php foreach ($hasil as $label => $isi): ?>
                    <?php if ($label !== "Grade" && $label !== "Status"): ?>
                        <div class="result-row">
                            <span><?php echo e($label); ?></span>
                            <strong><?php echo e($isi); ?></strong>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="grade-panel">
                    <div>
                        <div class="grade-label">Nilai / Grade</div>
                        <div class="grade-number">
                            <?php echo e($hasil["Nilai Akhir"]); ?>
                            <span style="font-size:15px">
                                / 100 · <?php echo e($hasil["Grade"]); ?>
                            </span>
                        </div>
                    </div>

                    <span class="status">
                        <?php echo e($hasil["Status"]); ?>
                    </span>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <footer class="footer">
        StudentDesk · Praktikum PHP<br>
        Pemrograman Berorientasi Objek · Kelas 3B
    </footer>

</div>
</body>
</html>