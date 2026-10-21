<?php
// Muat definisi class SEBELUM session_start agar objek di session dikenali saat dibaca.
require_once __DIR__ . "/Mahasiswa.php";
require_once __DIR__ . "/DataMahasiswa.php";

session_start();

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

if (!isset($_SESSION["mahasiswa"]) || !is_array($_SESSION["mahasiswa"])) {
    $_SESSION["mahasiswa"] = [];
}

$repo = new DataMahasiswa($_SESSION["mahasiswa"]);
$pesan = "";
$jenisPesan = "success";
$modeEdit = false;
$nimAsliEdit = "";
$form = [
    "nim" => "",
    "nama" => "",
    "fakultas" => "",
    "prodi" => "Teknik Informatika",
    "asalSekolah" => "",
    "semester" => "3",
    "hobi" => "",
    "noHp" => "",
    "alamat" => ""
];

$nimEdit = trim($_GET["edit"] ?? "");

if ($nimEdit !== "") {
    $m = $repo->cari($nimEdit);
    if ($m) {
        $modeEdit = true;
        $nimAsliEdit = $m->nim;
        $form = [
            "nim" => $m->nim,
            "nama" => $m->nama,
            "fakultas" => $m->fakultas,
            "prodi" => $m->prodi,
            "asalSekolah" => $m->asalSekolah,
            "semester" => (string) $m->semester,
            "hobi" => $m->hobi,
            "noHp" => $m->noHp,
            "alamat" => $m->alamat ?? ""
        ];
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aksi = $_POST["aksi"] ?? "";

    if ($aksi === "hapus") {
        $nimHapus = trim($_POST["nim_hapus"] ?? "");
        if ($repo->hapus($nimHapus)) {
            $pesan = "Data mahasiswa berhasil dihapus.";
        } else {
            $pesan = "Data tidak ditemukan.";
            $jenisPesan = "error";
        }
    } elseif ($aksi === "simpan") {
        foreach ($form as $key => $value) {
            $form[$key] = trim((string) ($_POST[$key] ?? ""));
        }

        $semesterValid = filter_var($form["semester"], FILTER_VALIDATE_INT);
        $semesterValid = $semesterValid !== false &&
            in_array($semesterValid, [1,2,3,4,5,6,7,8], true);

        $wajib = ["nim", "nama", "fakultas", "prodi", "asalSekolah", "hobi", "noHp", "alamat"];
        $lengkap = true;
        foreach ($wajib as $field) {
            if ($form[$field] === "") {
                $lengkap = false;
                break;
            }
        }

        if (!$lengkap || !$semesterValid) {
            $pesan = "Semua kolom wajib diisi dan semester harus dipilih dengan benar.";
            $jenisPesan = "error";
            $modeEdit = isset($_POST["nim_lama"]) && $_POST["nim_lama"] !== "";
            $nimAsliEdit = trim($_POST["nim_lama"] ?? "");
        } elseif (!preg_match('/^[0-9+\-\s()]{8,20}$/', $form["noHp"])) {
            $pesan = "Nomor HP/WhatsApp belum sesuai. Periksa kembali nomor yang dimasukkan.";
            $jenisPesan = "error";
            $modeEdit = isset($_POST["nim_lama"]) && $_POST["nim_lama"] !== "";
            $nimAsliEdit = trim($_POST["nim_lama"] ?? "");
        } else {
            $mhsBaru = new Mahasiswa(
                $form["nim"],
                $form["nama"],
                $form["fakultas"],
                $form["prodi"],
                $form["asalSekolah"],
                (int) $form["semester"],
                $form["hobi"],
                $form["noHp"],
                $form["alamat"]
            );

            $nimLama = trim($_POST["nim_lama"] ?? "");

            if ($nimLama !== "") {
                $ok = $repo->ubah($nimLama, $mhsBaru);
                $pesan = $ok ? "Data mahasiswa berhasil diperbarui." :
                    "Data gagal diperbarui. NIM mungkin sudah digunakan.";
            } else {
                $ok = $repo->tambah($mhsBaru);
                $pesan = $ok ? "Mahasiswa berhasil ditambahkan." :
                    "NIM tersebut sudah terdaftar.";
            }

            if (!$ok) {
                $jenisPesan = "error";
                $modeEdit = $nimLama !== "";
                $nimAsliEdit = $nimLama;
            } else {
                $form = [
                    "nim" => "", "nama" => "", "fakultas" => "",
                    "prodi" => "Teknik Informatika", "asalSekolah" => "",
                    "semester" => "3", "hobi" => "", "noHp" => "", "alamat" => ""
                ];
                $modeEdit = false;
            }
        }
    }

    $_SESSION["mahasiswa"] = $repo->semua();
}

if (isset($_GET["cari"]) && trim($_GET["cari"]) !== "") {
    $kataCari = trim($_GET["cari"]);
    $semuaData = $repo->semua();
    $dataTampil = [];
    foreach ($semuaData as $m) {
        if (
            stripos($m->nim, $kataCari) !== false ||
            stripos($m->nama, $kataCari) !== false ||
            stripos($m->prodi, $kataCari) !== false
        ) {
            $dataTampil[$m->nim] = $m;
        }
    }
} else {
    $kataCari = "";
    $dataTampil = $repo->semua();
}

$_SESSION["mahasiswa"] = $repo->semua();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentDesk | Data Mahasiswa</title>
<style>
*{box-sizing:border-box}
:root{--green:#176b59;--green-dark:#105342;--text:#1d2939;--muted:#758195;--border:#e4e9f0;--bg:#f4f7fb}
body{margin:0;padding:32px 18px;background:var(--bg);color:var(--text);font-family:"Segoe UI",Arial,sans-serif}
.container{width:100%;max-width:1180px;margin:0 auto}
.brand{display:flex;align-items:center;gap:10px;margin-bottom:25px}
.brand-icon{display:grid;place-items:center;width:42px;height:42px;border-radius:13px;background:#d9f5e9;color:var(--green);font-size:23px;font-weight:800}
.brand-name{font-size:15px;font-weight:800;letter-spacing:1.3px}
.brand-caption{margin-top:3px;color:var(--muted);font-size:11px}
.page-heading{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;margin-bottom:25px}
h1{margin:0 0 8px;font-size:clamp(27px,4vw,35px);letter-spacing:-1px}
.subtitle{margin:0;color:var(--muted);font-size:14px;line-height:1.7}
.tag{padding:9px 13px;border-radius:9px;background:#e4f4ed;color:var(--green);font-size:12px;font-weight:700;white-space:nowrap}
.stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:22px}
.stat,.card{background:white;border:1px solid var(--border);border-radius:17px;box-shadow:0 10px 28px rgba(25,45,70,.045)}
.stat{padding:20px}
.stat-label{color:var(--muted);font-size:12px;margin-bottom:10px}
.stat-value{font-size:27px;font-weight:800;letter-spacing:-.5px}
.stat-note{margin-top:5px;color:#8a95a5;font-size:11px}
.layout{display:grid;grid-template-columns:minmax(0,1fr);gap:20px;align-items:start}
.form-card form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));column-gap:16px;row-gap:0}
.form-card form .section-label,.form-card form .actions{grid-column:1/-1}
.form-card form .field{min-width:0}
.card{padding:22px;min-width:0}
.card-title{margin:0 0 7px;font-size:19px}
.description{margin:0 0 16px;color:var(--muted);font-size:13px;line-height:1.55}
.section-label{margin:14px 0 10px;padding-bottom:7px;border-bottom:1px solid var(--border);color:var(--green);font-size:11px;font-weight:800;letter-spacing:1px;text-transform:uppercase}
.field{margin-bottom:10px}
label{display:block;margin-bottom:7px;font-size:12px;font-weight:700;color:#344258}
input,select,textarea{width:100%;min-height:40px;padding:9px 11px;border:1px solid #dce3ec;border-radius:9px;background:#fff;color:var(--text);font:inherit;font-size:13px;outline:none}
input:focus,select:focus{border-color:#29977c;box-shadow:0 0 0 3px rgba(41,151,124,.11)}
.actions{display:flex;gap:9px;margin-top:12px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;min-height:42px;padding:10px 14px;border:0;border-radius:9px;background:var(--green);color:white;font:inherit;font-size:12px;font-weight:700;text-decoration:none;cursor:pointer}
.btn:hover{background:var(--green-dark)}
.btn.secondary{background:#eef2f6;color:#3b4758}
.btn.secondary:hover{background:#e0e7ef}
.btn.danger{background:#fff0f0;color:#b42318}
.btn.danger:hover{background:#ffe0e0}
.actions .btn{flex:1}
.notice{margin-bottom:20px;padding:13px 15px;border-radius:10px;background:#e7f7ed;color:#176b49;font-size:13px}
.notice.error{background:#fff0f0;color:#b42318}
.list-head{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;margin-bottom:20px}
.list-head .description{margin-bottom:0}
.search{display:flex;gap:8px;width:min(100%,340px)}
.search input{min-width:0}
.table-wrap{overflow-x:auto;border:1px solid var(--border);border-radius:11px}
table{width:100%;border-collapse:collapse;min-width:700px;font-size:12px}
th{padding:13px 12px;background:#f7f9fc;color:#68768a;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
td{padding:14px 12px;border-top:1px solid #edf0f4;vertical-align:middle}
tbody tr:hover{background:#fbfdfc}
.person{font-weight:700;color:#263449}
.sub{margin-top:4px;color:#8a95a5;font-size:11px}
.pill{display:inline-block;padding:5px 8px;border-radius:20px;background:#e4f4ed;color:var(--green);font-size:10px;font-weight:700}
.row-actions{display:flex;gap:6px;align-items:center}
.small-btn{padding:7px 9px;border:0;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;cursor:pointer}
.small-btn.edit{background:#eef3ff;color:#345bb5}
.small-btn.delete{background:#fff0f0;color:#b42318}
.inline-form{display:inline}
.empty{text-align:center;padding:35px 15px;color:var(--muted);font-size:13px}
.empty strong{display:block;margin-bottom:7px;color:var(--text);font-size:15px}
.footer{text-align:center;padding:24px 0 5px;color:#929dad;font-size:12px;line-height:1.8}
@media(max-width:950px){.layout{grid-template-columns:1fr}}
@media(max-width:620px){.form-card form{grid-template-columns:1fr}.form-card form .section-label,.form-card form .actions{grid-column:1/-1}}
@media(max-width:620px){body{padding:22px 12px}.page-heading{align-items:flex-start;flex-direction:column}.stats{gap:9px}.stat{padding:14px}.stat-value{font-size:22px}.stat-note{font-size:10px}.card{padding:19px 15px}.list-head{flex-direction:column}.search{width:100%}.actions .btn{font-size:11px;padding:10px 8px}}
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
            <div>
                <h1>Data Mahasiswa</h1>
                <p class="subtitle">Kelola data mahasiswa dalam satu halaman dengan mudah.</p>
            </div>
            <span class="tag">PRAKTIKUM PBO · PERTEMUAN 3</span>
        </div>
    </header>

    <?php if ($pesan !== ""): ?>
        <div class="notice <?php echo $jenisPesan === "error" ? "error" : ""; ?>">
            <?php echo e($pesan); ?>
        </div>
    <?php endif; ?>

    <section class="stats">
        <div class="stat">
            <div class="stat-label">Total Mahasiswa</div>
            <div class="stat-value"><?php echo $repo->jumlahMahasiswa(); ?></div>
            <div class="stat-note">Data yang tersimpan pada sesi ini</div>
        </div>
        <div class="stat">
            <div class="stat-label">Program Studi</div>
            <div class="stat-value" style="font-size:19px">Teknik Informatika</div>
            <div class="stat-note">Fakultas Sains dan Teknologi</div>
        </div>
        <div class="stat">
            <div class="stat-label">Penyimpanan</div>
            <div class="stat-value" style="font-size:19px">Session PHP</div>
            <div class="stat-note">Tanpa database MySQL</div>
        </div>
    </section>

    <div class="layout">
        <section class="card form-card">
            <h2 class="card-title"><?php echo $modeEdit ? "Edit Mahasiswa" : "Tambah Mahasiswa"; ?></h2>
            <p class="description">Isi data mahasiswa pada kolom berikut.</p>

            <form method="POST" action="index.php">
                <input type="hidden" name="aksi" value="simpan">
                <input type="hidden" name="nim_lama" value="<?php echo $modeEdit ? e($nimAsliEdit !== "" ? $nimAsliEdit : ($form["nim"] ?? "")) : ""; ?>">

                <div class="section-label">Identitas Mahasiswa</div>

                <div class="field">
                    <label for="nim">NIM</label>
                    <input id="nim" name="nim" type="text" maxlength="30" required value="<?php echo e($form["nim"]); ?>" placeholder="Contoh: 2530521052">
                </div>

                <div class="field">
                    <label for="nama">Nama Mahasiswa</label>
                    <input id="nama" name="nama" type="text" maxlength="100" required value="<?php echo e($form["nama"]); ?>" placeholder="Masukkan nama lengkap">
                </div>

                <div class="field">
                    <label for="fakultas">Fakultas</label>
                    <input id="fakultas" name="fakultas" type="text" maxlength="100" required value="<?php echo e($form["fakultas"]); ?>" placeholder="Nama fakultas">
                </div>

                <div class="field">
                    <label for="prodi">Program Studi</label>
                    <input id="prodi" name="prodi" type="text" maxlength="100" required value="<?php echo e($form["prodi"]); ?>" placeholder="Contoh: Teknik Informatika">
                </div>

                <div class="field">
                    <label for="semester">Semester</label>
                    <select id="semester" name="semester" required>
                        <option value="">Pilih semester</option>
                        <?php foreach ([1,2,3,4,5,6,7,8] as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo (string)$form["semester"] === (string)$s ? "selected" : ""; ?>>
                                Semester <?php echo $s; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="asalSekolah">Asal Sekolah</label>
                    <input id="asalSekolah" name="asalSekolah" type="text" maxlength="120" required value="<?php echo e($form["asalSekolah"]); ?>" placeholder="Nama sekolah sebelumnya">
                </div>

                <div class="section-label">Informasi Kontak</div>

                <div class="field">
                    <label for="noHp">Nomor HP / WhatsApp</label>
                    <input id="noHp" name="noHp" type="tel" maxlength="20" required value="<?php echo e($form["noHp"]); ?>" placeholder="Contoh: 081234567890">
                </div>

                <div class="field">
                    <label for="alamat">Alamat Mahasiswa</label>
                    <textarea id="alamat" name="alamat" rows="2" maxlength="300" required placeholder="Contoh: Desa, Kecamatan, Kabupaten/Kota"><?php echo e($form["alamat"]); ?></textarea>
                </div>

                <div class="section-label">Informasi Tambahan</div>

                <div class="field">
                    <label for="hobi">Hobi</label>
                    <input id="hobi" name="hobi" type="text" maxlength="120" required value="<?php echo e($form["hobi"]); ?>" placeholder="Contoh: futsal, membaca">
                </div>

                <div class="actions">
                    <button class="btn" type="submit"><?php echo $modeEdit ? "Simpan Perubahan" : "＋ Tambah Mahasiswa"; ?></button>
                    <?php if ($modeEdit): ?>
                        <a class="btn secondary" href="index.php">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="card list-card">
            <div class="list-head">
                <div>
                    <h2 class="card-title">Daftar Mahasiswa</h2>
                    <p class="description">Data mahasiswa yang sudah dimasukkan.</p>
                </div>
                <form class="search" method="GET" action="index.php">
                    <input type="search" name="cari" value="<?php echo e($kataCari); ?>" placeholder="Cari NIM, nama, prodi...">
                    <button class="btn" type="submit">Cari</button>
                </form>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Program Studi</th>
                            <th>Semester</th>
                            <th>HP / WhatsApp</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($dataTampil) === 0): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty">
                                    <strong>Belum ada data mahasiswa</strong>
                                    Isi formulir di sebelah kiri untuk menambahkan data pertama.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dataTampil as $m): ?>
                            <tr>
                                <td>
                                    <div class="person"><?php echo e($m->nama); ?></div>
                                    <div class="sub"><?php echo e($m->nim); ?></div>
                                </td>
                                <td>
                                    <?php echo e($m->prodi); ?>
                                    <div class="sub"><?php echo e($m->fakultas); ?></div>
                                </td>
                                <td><span class="pill">Semester <?php echo e($m->semester); ?></span></td>
                                <td><?php echo e($m->noHp); ?></td>
                                <td><?php echo e($m->alamat ?? ""); ?></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="small-btn edit" href="index.php?edit=<?php echo rawurlencode($m->nim); ?>">Edit</a>
                                        <form class="inline-form" method="POST" action="index.php" onsubmit="return confirm('Yakin ingin menghapus data mahasiswa ini?')">
                                            <input type="hidden" name="aksi" value="hapus">
                                            <input type="hidden" name="nim_hapus" value="<?php echo e($m->nim); ?>">
                                            <button class="small-btn delete" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <p class="description" style="margin:14px 0 0">
                Menampilkan <?php echo count($dataTampil); ?> data dari <?php echo $repo->jumlahMahasiswa(); ?> mahasiswa.
            </p>
        </section>
    </div>

    <footer class="footer">
        StudentDesk · Praktikum PHP<br>
        Pemrograman Berorientasi Objek · Kelas 3B
    </footer>
</div>
</body>
</html>
