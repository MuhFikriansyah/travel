<?php
date_default_timezone_set('Asia/Jakarta');

function getDaftarTujuan()
{
    return [
        'Bali' => ['lokasi' => 'Indonesia', 'durasi' => '4 Hari 3 Malam', 'harga' => 3500000],
        'Kyoto' => ['lokasi' => 'Jepang', 'durasi' => '7 Hari 6 Malam', 'harga' => 12500000],
        'Paris' => ['lokasi' => 'Prancis', 'durasi' => '10 Hari 9 Malam', 'harga' => 25000000],
        'Seoul' => ['lokasi' => 'Korea Selatan', 'durasi' => '6 Hari 5 Malam', 'harga' => 11000000],
        'Phuket' => ['lokasi' => 'Thailand', 'durasi' => '5 Hari 4 Malam', 'harga' => 4800000],
        'Lombok' => ['lokasi' => 'Indonesia', 'durasi' => '3 Hari 2 Malam', 'harga' => 2800000],
    ];
}

function rapikanNama($nama)
{
    $nama = trim($nama);
    $nama = preg_replace('/\s+/', ' ', $nama);

    if (function_exists('mb_convert_case')) {
        return mb_convert_case($nama, MB_CASE_TITLE, 'UTF-8');
    }

    return ucwords(strtolower($nama));
}

function tanggalValid($tanggal, $tanggalMinimum)
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        return false;
    }

    $bagianTanggal = explode('-', $tanggal);

    return checkdate((int) $bagianTanggal[1], (int) $bagianTanggal[2], (int) $bagianTanggal[0])
        && $tanggal >= $tanggalMinimum;
}

function keputusanWaktu($waktu)
{
    $jam = (int) $waktu['hours'];

    if ($jam >= 8 && $jam < 17) {
        return 'Permintaan diterima pada jam layanan. Tim travel dapat menindaklanjutinya pada hari ini.';
    }

    return 'Permintaan diterima di luar jam layanan (08.00–17.00 WIB). Tim travel akan menindaklanjutinya pada jam layanan berikutnya.';
}

function formatTanggal($tanggal)
{
    $waktu = strtotime($tanggal);

    if ($waktu === false) {
        return $tanggal;
    }

    $namaBulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    return date('j', $waktu) . ' ' . $namaBulan[(int) date('n', $waktu)] . ' ' . date('Y', $waktu);
}

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
