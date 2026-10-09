<?php
require_once __DIR__ . '/includes/booking.php';

$tujuanTravel = getDaftarTujuan();
$errors = [];
$booking = null;
$namaInput = '';
$tujuanInput = '';
$tanggalInput = '';
$tanggalHariIni = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaInput = isset($_POST['nama_penumpang']) && is_string($_POST['nama_penumpang'])
        ? $_POST['nama_penumpang']
        : '';
    $tujuanInput = isset($_POST['tujuan']) && is_string($_POST['tujuan'])
        ? $_POST['tujuan']
        : '';
    $tanggalInput = isset($_POST['tanggal_booking']) && is_string($_POST['tanggal_booking'])
        ? $_POST['tanggal_booking']
        : '';

    $namaPenumpang = rapikanNama($namaInput);

    if ($namaPenumpang === '') {
        $errors[] = 'Nama penumpang wajib diisi.';
    } elseif (strlen($namaPenumpang) > 100) {
        $errors[] = 'Nama penumpang maksimal 100 karakter.';
    }

    if (!array_key_exists($tujuanInput, $tujuanTravel)) {
        $errors[] = 'Silakan pilih tujuan travel yang tersedia.';
    }

    if (!tanggalValid($tanggalInput, $tanggalHariIni)) {
        $errors[] = 'Tanggal booking harus berupa tanggal yang valid dan tidak boleh sebelum hari ini.';
    }

    if (!$errors) {
        $waktuProses = getdate();
        $booking = [
            'nama' => $namaPenumpang,
            'tujuan' => $tujuanInput,
            'tanggal' => $tanggalInput,
            'waktu_proses' => date('d F Y, H:i:s', $waktuProses[0]),
            'keputusan_waktu' => keputusanWaktu($waktuProses),
        ];
    }
}

require __DIR__ . '/includes/header.php';
?>

<main class="page-shell">
    <section class="intro">
        <div class="intro-copy">
            <span class="eyebrow"><span class="eyebrow-dot"></span> MINI BOOKING TRAVEL</span>
            <h1>Mulai perjalanan<br><span>yang berarti.</span></h1>
            <p>Isi detail perjalananmu. Kami akan mencatat permintaan booking dan menampilkan ringkasannya di sini.</p>
        </div>
        <div class="intro-art" aria-hidden="true">
            <div class="sun"></div>
            <div class="plane">✈</div>
            <div class="art-caption">your next<br>adventure</div>
            <div class="art-route"></div>
        </div>
    </section>

    <section class="booking-layout">
        <div class="form-card">
            <div class="section-heading">
                <div>
                    <span class="step-label">01 — DETAIL PERJALANAN</span>
                    <h2>Pesan perjalanan</h2>
                </div>
                <span class="heading-icon" aria-hidden="true">↗</span>
            </div>

            <?php if ($errors): ?>
                <div class="alert alert-error" role="alert">
                    <strong>Periksa kembali data booking:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= escapeHtml($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="field">
                    <label for="nama_penumpang">Nama penumpang</label>
                    <input
                        type="text"
                        id="nama_penumpang"
                        name="nama_penumpang"
                        value="<?= escapeHtml($namaInput) ?>"
                        placeholder="Contoh: Alya Putri"
                        maxlength="100"
                        autocomplete="name"
                        required
                    >
                    <small>Nama akan dirapikan otomatis saat booking diproses.</small>
                </div>

                <div class="field">
                    <label for="tujuan">Tujuan perjalanan</label>
                    <select id="tujuan" name="tujuan" required>
                        <option value="">Pilih destinasi</option>
                        <?php foreach ($tujuanTravel as $namaTujuan => $paket): ?>
                            <option value="<?= escapeHtml($namaTujuan) ?>" <?= $tujuanInput === $namaTujuan ? 'selected' : '' ?>>
                                <?= escapeHtml($namaTujuan) ?> — <?= escapeHtml($paket['lokasi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="tanggal_booking">Tanggal booking</label>
                    <input
                        type="date"
                        id="tanggal_booking"
                        name="tanggal_booking"
                        value="<?= escapeHtml($tanggalInput) ?>"
                        min="<?= escapeHtml($tanggalHariIni) ?>"
                        required
                    >
                </div>

                <button class="submit-button" type="submit">
                    Proses booking <span aria-hidden="true">→</span>
                </button>
                <p class="form-note">Waktu pemrosesan dicatat otomatis oleh sistem.</p>
            </form>
        </div>

        <aside class="side-column">
            <?php if ($booking !== null): ?>
                <section class="result-card" aria-live="polite">
                    <div class="result-top">
                        <span class="result-check" aria-hidden="true">✓</span>
                        <span class="step-label">02 — PERMINTAAN DICATAT</span>
                    </div>
                    <h2>Booking diterima,<br><?= escapeHtml($booking['nama']) ?>.</h2>
                    <p class="result-description">Berikut ringkasan permintaan perjalananmu.</p>
                    <dl class="summary-list">
                        <div><dt>Nama penumpang</dt><dd><?= escapeHtml($booking['nama']) ?></dd></div>
                        <div><dt>Tujuan</dt><dd><?= escapeHtml($booking['tujuan']) ?></dd></div>
                        <div><dt>Tanggal booking</dt><dd><?= escapeHtml(formatTanggal($booking['tanggal'])) ?></dd></div>
                        <div><dt>Diproses pada</dt><dd><?= escapeHtml($booking['waktu_proses']) ?> WIB</dd></div>
                    </dl>
                    <div class="time-decision">
                        <span class="decision-icon" aria-hidden="true">◷</span>
                        <p><?= escapeHtml($booking['keputusan_waktu']) ?></p>
                    </div>
                    <p class="disclaimer">Ini adalah simulasi pencatatan booking; belum ada pembayaran atau pemesanan tiket.</p>
                </section>
            <?php else: ?>
                <section class="destination-card">
                    <span class="step-label">PILIH DESTINASIMU</span>
                    <h2>Tempat baru,<br>cerita baru.</h2>
                    <div class="destination-list">
                        <?php foreach ($tujuanTravel as $namaTujuan => $paket): ?>
                            <div class="destination-row">
                                <span class="destination-mark"><?= escapeHtml(substr($namaTujuan, 0, 1)) ?></span>
                                <span class="destination-name">
                                    <strong><?= escapeHtml($namaTujuan) ?></strong>
                                    <small><?= escapeHtml($paket['lokasi']) ?> · <?= escapeHtml($paket['durasi']) ?></small>
                                </span>
                                <span class="destination-price">Rp <?= number_format($paket['harga'], 0, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <div class="process-note">
                <span class="process-note-icon" aria-hidden="true">✦</span>
                <p><strong>Informasi sistem</strong><br>Tanggal dan jam proses serta pesan layanan menyesuaikan waktu saat formulir dikirim.</p>
            </div>
        </aside>
    </section>

    <footer class="page-footer">
        <span>TRAVEL BOOKING <span class="footer-separator">/</span> MINI WEB APPLICATION</span>
        <span>PHP · Form processing · <?= date('Y') ?></span>
    </footer>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
