<?php
require_once 'config/database.php';
require_once 'includes/ahp.php';
require_once 'includes/topsis.php';

$ahp = new AHP($conn);
$bobot = $ahp->hitungBobot();
$cr = $ahp->hitungConsistencyRatio();

$kriteria = $conn->query("SELECT * FROM kriteria ORDER BY id_kriteria")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Pemilihan Kost Mahasiswa - AHP & TOPSIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1>🏠 SPK Pemilihan Kost Mahasiswa</h1>
            <ul>
                <li><a href="index.php" class="active">Beranda</a></li>
                <li><a href="hasil.php">Cari Kost</a></li>
                <li><a href="admin/">Admin</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="hero">
            <h2>Sistem Pendukung Keputusan Pemilihan Kost</h2>
            <p>Menggunakan Metode AHP untuk Pembobotan & TOPSIS untuk Perangkingan</p>
            <a href="hasil.php" class="btn btn-primary">Mulai Cari Kost →</a>
        </div>

        <div class="card">
            <h3>📊 Kriteria Penilaian</h3>
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Kriteria</th>
                        <th>Bobot (AHP)</th>
                        <th>Jenis</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kriteria as $k): ?>
                    <tr>
                        <td><?= $k['kode_kriteria'] ?></td>
                        <td><?= $k['nama_kriteria'] ?></td>
                        <td><?= number_format($k['bobot'], 4) ?></td>
                        <td><span class="badge <?= $k['jenis'] == 'benefit' ? 'badge-success' : 'badge-warning' ?>">
                            <?= ucfirst($k['jenis']) ?>
                        </span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>✅ Consistency Ratio (CR) - AHP</h3>
            <div class="cr-info">
                <p><strong>λmax:</strong> <?= number_format($cr['lambda_max'], 4) ?></p>
                <p><strong>CI:</strong> <?= number_format($cr['CI'], 4) ?></p>
                <p><strong>CR:</strong> <?= number_format($cr['CR'], 4) ?> 
                    <?php if ($cr['konsisten']): ?>
                        <span class="badge badge-success">✓ Konsisten (CR < 0.1)</span>
                    <?php else: ?>
                        <span class="badge badge-danger">✗ Tidak Konsisten</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="card">
            <h3>ℹ️ Tentang Sistem</h3>
            <ul>
                <li><strong>AHP (Analytical Hierarchy Process):</strong> Digunakan untuk menentukan bobot kepentingan setiap kriteria berdasarkan perbandingan berpasangan</li>
                <li><strong>TOPSIS (Technique for Order Preference by Similarity to Ideal Solution):</strong> Digunakan untuk merangking alternatif berdasarkan jarak terhadap solusi ideal</li>
                <li><strong>Data:</strong> Sistem memiliki 45 data kost dengan 5 kriteria penilaian</li>
            </ul>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 SPK Pemilihan Kost | Paralel C | Metode AHP-TOPSIS</p>
    </footer>
</body>
</html>
