<?php
require_once 'config/database.php';
require_once 'includes/ahp.php';
require_once 'includes/topsis.php';

$ahp = new AHP($conn);
$bobot = $ahp->hitungBobot();

$topsis = new TOPSIS($conn, $bobot);
$hasil = $topsis->hitung();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Rekomendasi Kost - SPK</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1>🏠 SPK Pemilihan Kost Mahasiswa</h1>
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="hasil.php" class="active">Cari Kost</a></li>
                <li><a href="admin/">Admin</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="hero">
            <h2>🎯 Hasil Rekomendasi Kost Terbaik</h2>
            <p>Berdasarkan perhitungan metode TOPSIS dengan bobot AHP</p>
        </div>

        <!-- Top 3 Rekomendasi -->
        <div class="top-recommendations">
            <h3>🏆 Top 3 Rekomendasi Terbaik</h3>
            <div class="top-cards">
                <?php for ($i = 0; $i < 3 && $i < count($hasil); $i++): 
                    $kost = $hasil[$i]['alternatif'];
                    $rank = $i + 1;
                    $medal = ['🥇', '🥈', '🥉'];
                ?>
                <div class="top-card rank-<?= $rank ?>">
                    <div class="medal"><?= $medal[$i] ?> Rank #<?= $rank ?></div>
                    <h4><?= htmlspecialchars($kost['nama_kost']) ?></h4>
                    <div class="score">Skor: <?= number_format($hasil[$i]['preferensi'], 4) ?></div>
                    <div class="details">
                        <p>💰 Rp <?= number_format($kost['harga'], 0, ',', '.') ?>/bulan</p>
                        <p>📍 <?= $kost['jarak'] ?> km dari kampus</p>
                        <p>⭐ Fasilitas: <?= $kost['fasilitas'] ?>/10</p>
                        <p>🔒 Keamanan: <?= $kost['keamanan'] ?>/10</p>
                        <p>🧹 Kebersihan: <?= $kost['kebersihan'] ?>/10</p>
                    </div>
                    <p class="address">📮 <?= htmlspecialchars($kost['alamat']) ?></p>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Tabel Lengkap -->
        <div class="card">
            <h3>📋 Daftar Lengkap Semua Kost (Terurut)</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Nama Kost</th>
                            <th>Harga</th>
                            <th>Jarak (km)</th>
                            <th>Fasilitas</th>
                            <th>Keamanan</th>
                            <th>Kebersihan</th>
                            <th>Skor TOPSIS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hasil as $index => $item): 
                            $kost = $item['alternatif'];
                        ?>
                        <tr>
                            <td><strong><?= $index + 1 ?></strong></td>
                            <td><?= htmlspecialchars($kost['nama_kost']) ?></td>
                            <td>Rp <?= number_format($kost['harga'], 0, ',', '.') ?></td>
                            <td><?= $kost['jarak'] ?></td>
                            <td><?= $kost['fasilitas'] ?>/10</td>
                            <td><?= $kost['keamanan'] ?>/10</td>
                            <td><?= $kost['kebersihan'] ?>/10</td>
                            <td><strong><?= number_format($item['preferensi'], 4) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 SPK Pemilihan Kost | Paralel C | Metode AHP-TOPSIS</p>
    </footer>
</body>
</html>
