<?php
class Mahasiswa
{
    public function __construct(
        public string $nim,
        public string $nama,
        public string $fakultas,
        public string $prodi,
        public string $asalSekolah,
        public int $semester,
        public string $hobi,
        public string $noHp,
        public string $alamat = ""
    ) {}

    public function ringkasan(): string
    {
        return $this->nim . " - " . $this->nama;
    }
}
