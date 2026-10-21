<?php
require_once __DIR__ . "/Mahasiswa.php";

class DataMahasiswa
{
    private array $data = [];

    public function __construct(array $dataAwal = [])
    {
        foreach ($dataAwal as $item) {
            if ($item instanceof Mahasiswa) {
                $this->data[$item->nim] = $item;
            }
        }
    }

    public function tambah(Mahasiswa $mahasiswa): bool
    {
        if (isset($this->data[$mahasiswa->nim])) {
            return false;
        }

        $this->data[$mahasiswa->nim] = $mahasiswa;
        return true;
    }

    public function semua(): array
    {
        return $this->data;
    }

    public function cari(string $nim): ?Mahasiswa
    {
        return $this->data[$nim] ?? null;
    }

    public function ubah(string $nimLama, Mahasiswa $baru): bool
    {
        if (!isset($this->data[$nimLama])) {
            return false;
        }

        if ($baru->nim !== $nimLama && isset($this->data[$baru->nim])) {
            return false;
        }

        unset($this->data[$nimLama]);
        $this->data[$baru->nim] = $baru;
        return true;
    }

    public function hapus(string $nim): bool
    {
        if (!isset($this->data[$nim])) {
            return false;
        }

        unset($this->data[$nim]);
        return true;
    }

    public function jumlahMahasiswa(): int
    {
        return count($this->data);
    }

    public function cariBerdasarkanProdi(string $prodi): array
    {
        $hasil = [];

        foreach ($this->data as $mahasiswa) {
            if (stripos($mahasiswa->prodi, $prodi) !== false) {
                $hasil[$mahasiswa->nim] = $mahasiswa;
            }
        }

        return $hasil;
    }
}
