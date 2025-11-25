<?php
require_once 'config/database.php';
require_once 'includes/ahp.php';
require_once 'includes/topsis.php';

$ahp = new AHP($conn);
$bobot = $ahp->hitungBobot();

// Ambil filter dari user
$filterHargaMax = isset($_GET['harga_max']) ? (int)$_GET['harga_max'] : 999999999;
$filterJarakMax = isset($_GET['jarak_max']) ? (float)$_GET['jarak_max'] : 999;
$filterFasilitasMin = isset($_GET['fasilitas_min']) ? (int)$_GET['fasilitas_min'] : 0;
$filterKeamananMin = isset($_GET['keamanan_min']) ? (int)$_GET['keamanan_min'] : 0;
$filterKebersihanMin = isset($_GET['kebersihan_min']) ? (int)$_GET['kebersihan_min'] : 0;
$filterNama = isset($_GET['nama']) ? trim($_GET['nama']) : '';

// Build SQL query dengan filter
$conditions = [];
$params = [];

if ($filterHargaMax < 999999999) {
    $conditions[] = "harga <= ?";
    $params[] = $filterHargaMax;
}
if ($filterJarakMax < 999) {
    $conditions[] = "jarak <= ?";
    $params[] = $filterJarakMax;
}
if ($filterFasilitasMin > 0) {
    $conditions[] = "fasilitas >= ?";
    $params[] = $filterFasilitasMin;
}
if ($filterKeamananMin > 0) {
    $conditions[] = "keamanan >= ?";
    $params[] = $filterKeamananMin;
}
if ($filterKebersihanMin > 0) {
    $conditions[] = "kebersihan >= ?";
    $params[] = $filterKebersihanMin;
}
if ($filterNama != '') {
    $conditions[] = "nama_kost LIKE ?";
    $params[] = "%$filterNama%";
}

$whereClause = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : "";

// Get filtered data
$stmt = $conn->prepare("SELECT * FROM alternatif $whereClause");
$stmt->execute($params);
$dataFiltered = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Jika ada hasil, hitung TOPSIS
$hasil = [];
if (count($dataFiltered) > 0) {
    // Hitung TOPSIS untuk data yang sudah difilter
    $matriks = [];
    foreach ($dataFiltered as $alt) {
        $matriks[] = [
            $alt['harga'],
            $alt['jarak'],
            $alt['fasilitas'],
            $alt['keamanan'],
            $alt['kebersihan']
        ];
    }
    
    // Normalisasi
    $jumlahKuadrat = array_fill(0, 5, 0);
    foreach ($matriks as $row) {
        for ($j = 0; $j < 5; $j++) {
            $jumlahKuadrat[$j] += pow($row[$j], 2);
        }
    }
    $pembagi = array_map('sqrt', $jumlahKuadrat);
    
    $matriksNormalisasi = [];
    foreach ($matriks as $i => $row) {
        for ($j = 0; $j < 5; $j++) {
            $matriksNormalisasi[$i][$j] = $pembagi[$j] > 0 ? $row[$j] / $pembagi[$j] : 0;
        }
    }
    
    // Terbobot
    $matriksTerbobot = [];
    foreach ($matriksNormalisasi as $i => $row) {
        for ($j = 0; $j < 5; $j++) {
            $matriksTerbobot[$i][$j] = $row[$j] * $bobot[$j];
        }
    }
    
    // Ideal
    $kriteria = $conn->query("SELECT * FROM kriteria ORDER BY id_kriteria")->fetchAll(PDO::FETCH_ASSOC);
    $idealPositif = [];
    $idealNegatif = [];
    for ($j = 0; $j < 5; $j++) {
        $kolom = array_column($matriksTerbobot, $j);
        if (count($kolom) > 0) {
            $idealPositif[$j] = ($kriteria[$j]['jenis'] == 'benefit') ? max($kolom) : min($kolom);
            $idealNegatif[$j] = ($kriteria[$j]['jenis'] == 'benefit') ? min($kolom) : max($kolom);
        }
    }
    
    // Hitung preferensi
    foreach ($dataFiltered as $i => $alt) {
        $dPlus = 0;
        $dMinus = 0;
        for ($j = 0; $j < 5; $j++) {
            $dPlus += pow($matriksTerbobot[$i][$j] - $idealPositif[$j], 2);
            $dMinus += pow($matriksTerbobot[$i][$j] - $idealNegatif[$j], 2);
        }
        $dPlus = sqrt($dPlus);
        $dMinus = sqrt($dMinus);
        $preferensi = ($dPlus + $dMinus) > 0 ? $dMinus / ($dPlus + $dMinus) : 0;
        
        $hasil[] = [
            'alternatif' => $alt,
            'preferensi' => $preferensi
        ];
    }
    
    // Urutkan
    usort($hasil, function($a, $b) {
        return $b['preferensi'] <=> $a['preferensi'];
    });
}

$totalHasil = count($hasil);
$isFiltered = ($filterHargaMax < 999999999 || $filterJarakMax < 999 || $filterFasilitasMin > 0 || 
               $filterKeamananMin > 0 || $filterKebersihanMin > 0 || $filterNama != '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Kost - SPK</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .filter-box {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
            font-size: 0.9rem;
        }
        .filter-group input, .filter-group select {
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
        }
        .filter-group input:focus, .filter-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 1rem;
        }
        .range-display {
            font-size: 0.85rem;
            color: #667eea;
            margin-top: 0.25rem;
            font-weight: 600;
        }
        .no-result {
            text-align: center;
            padding: 3rem;
            background: #f8f9ff;
            border-radius: 15px;
            margin: 2rem 0;
        }
        .no-result h3 {
            color: #667eea;
            margin-bottom: 1rem;
        }
        .filter-info {
            background: #f0f4ff;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }
        .quick-filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        .quick-filter-btn {
            padding: 8px 16px;
            background: #f0f4ff;
            border: 2px solid #667eea;
            border-radius: 20px;
            color: #667eea;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
            text-decoration: none;
        }
        .quick-filter-btn:hover {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1>🏠 SPK Pemilihan Kost Mahasiswa</h1>
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="hasil.php" class="active">Cari Kost</a></li>
                <li><a href="testing.php">Testing</a></li>
                <li><a href="admin/">Admin</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="hero">
            <h2>🔍 Cari Kost Sesuai Keinginan Anda</h2>
            <p>Gunakan filter di bawah untuk menemukan kost yang sesuai dengan kebutuhan Anda</p>
        </div>

        <!-- Filter Box -->
        <div class="filter-box">
            <h3 style="color: #667eea; margin-bottom: 1.5rem;">🎯 Filter Pencarian</h3>
            
            <form method="GET" action="hasil.php" id="filterForm">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label>💰 Harga Maksimal (Rp)</label>
                        <input type="number" name="harga_max" id="harga_max" 
                               value="<?= $filterHargaMax < 999999999 ? $filterHargaMax : '' ?>" 
                               placeholder="Contoh: 1000000" step="50000" min="0">
                        <div class="range-display" id="harga_display"></div>
                    </div>
                    
                    <div class="filter-group">
                        <label>📍 Jarak Maksimal (km)</label>
                        <input type="number" name="jarak_max" id="jarak_max" 
                               value="<?= $filterJarakMax < 999 ? $filterJarakMax : '' ?>" 
                               placeholder="Contoh: 2.5" step="0.5" min="0">
                        <div class="range-display" id="jarak_display"></div>
                    </div>
                    
                    <div class="filter-group">
                        <label>⭐ Fasilitas Minimal</label>
                        <select name="fasilitas_min" id="fasilitas_min">
                            <option value="0">Semua</option>
                            <?php for ($i = 5; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>" <?= $filterFasilitasMin == $i ? 'selected' : '' ?>><?= $i ?>/10</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>🔒 Keamanan Minimal</label>
                        <select name="keamanan_min" id="keamanan_min">
                            <option value="0">Semua</option>
                            <?php for ($i = 5; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>" <?= $filterKeamananMin == $i ? 'selected' : '' ?>><?= $i ?>/10</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>🧹 Kebersihan Minimal</label>
                        <select name="kebersihan_min" id="kebersihan_min">
                            <option value="0">Semua</option>
                            <?php for ($i = 5; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>" <?= $filterKebersihanMin == $i ? 'selected' : '' ?>><?= $i ?>/10</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>🏷️ Nama Kost</label>
                        <input type="text" name="nama" id="nama" 
                               value="<?= htmlspecialchars($filterNama) ?>" 
                               placeholder="Cari nama kost...">
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">🔍 Cari Kost</button>
                    <a href="hasil.php" class="btn btn-warning">🔄 Reset Filter</a>
                </div>
            </form>

            <!-- Quick Filters -->
            <div style="margin-top: 1.5rem;">
                <p style="font-weight: 600; color: #666; margin-bottom: 0.5rem;">⚡ Filter Cepat:</p>
                <div class="quick-filters">
                    <a href="?harga_max=700000" class="quick-filter-btn">💵 Budget Hemat (< 700k)</a>
                    <a href="?jarak_max=1.5" class="quick-filter-btn">🚶 Dekat Kampus (< 1.5km)</a>
                    <a href="?fasilitas_min=8&keamanan_min=8" class="quick-filter-btn">⭐ Premium (Rating 8+)</a>
                    <a href="?harga_max=1000000&jarak_max=2&fasilitas_min=7" class="quick-filter-btn">🎯 Recommended</a>
                </div>
            </div>
        </div>

        <!-- Filter Info -->
        <?php if ($isFiltered): ?>
        <div class="filter-info">
            <strong>📌 Filter Aktif:</strong>
            <?php if ($filterHargaMax < 999999999): ?>
                Harga ≤ Rp <?= number_format($filterHargaMax, 0, ',', '.') ?> |
            <?php endif; ?>
            <?php if ($filterJarakMax < 999): ?>
                Jarak ≤ <?= $filterJarakMax ?> km |
            <?php endif; ?>
            <?php if ($filterFasilitasMin > 0): ?>
                Fasilitas ≥ <?= $filterFasilitasMin ?>/10 |
            <?php endif; ?>
            <?php if ($filterKeamananMin > 0): ?>
                Keamanan ≥ <?= $filterKeamananMin ?>/10 |
            <?php endif; ?>
            <?php if ($filterKebersihanMin > 0): ?>
                Kebersihan ≥ <?= $filterKebersihanMin ?>/10 |
            <?php endif; ?>
            <?php if ($filterNama != ''): ?>
                Nama: "<?= htmlspecialchars($filterNama) ?>"
            <?php endif; ?>
            <strong style="color: #667eea; margin-left: 1rem;">Ditemukan: <?= $totalHasil ?> kost</strong>
        </div>
        <?php endif; ?>

        <!-- Hasil -->
        <?php if ($totalHasil > 0): ?>
            <!-- Top 3 Rekomendasi -->
            <?php if ($totalHasil >= 3): ?>
            <div class="top-recommendations">
                <h3>🏆 Top 3 Rekomendasi Terbaik</h3>
                <div class="top-cards">
                    <?php for ($i = 0; $i < min(3, $totalHasil); $i++): 
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
            <?php endif; ?>

            <!-- Tabel Lengkap -->
            <div class="card">
                <h3>📋 Semua Hasil Pencarian (<?= $totalHasil ?> kost)</h3>
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
        <?php else: ?>
            <div class="no-result">
                <h3>😔 Tidak Ada Kost yang Sesuai</h3>
                <p>Maaf, tidak ditemukan kost yang sesuai dengan kriteria pencarian Anda.</p>
                <p><strong>Saran:</strong></p>
                <ul style="list-style: none; padding: 0;">
                    <li>• Coba naikkan budget maksimal</li>
                    <li>• Perluas jarak pencarian</li>
                    <li>• Turunkan rating minimal fasilitas</li>
                    <li>• Atau gunakan filter cepat di atas</li>
                </ul>
                <a href="hasil.php" class="btn btn-primary" style="margin-top: 1rem;">🔄 Reset & Lihat Semua Kost</a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 SPK Pemilihan Kost | Paralel C | Metode AHP-TOPSIS</p>
    </footer>

    <script>
        // Real-time display untuk harga
        const hargaInput = document.getElementById('harga_max');
        const hargaDisplay = document.getElementById('harga_display');
        
        hargaInput.addEventListener('input', function() {
            if (this.value) {
                const formatted = new Intl.NumberFormat('id-ID').format(this.value);
                hargaDisplay.textContent = `≤ Rp ${formatted}`;
            } else {
                hargaDisplay.textContent = '';
            }
        });
        
        // Real-time display untuk jarak
        const jarakInput = document.getElementById('jarak_max');
        const jarakDisplay = document.getElementById('jarak_display');
        
        jarakInput.addEventListener('input', function() {
            if (this.value) {
                jarakDisplay.textContent = `≤ ${this.value} km dari kampus`;
            } else {
                jarakDisplay.textContent = '';
            }
        });
        
        // Trigger on load
        if (hargaInput.value) {
            hargaInput.dispatchEvent(new Event('input'));
        }
        if (jarakInput.value) {
            jarakInput.dispatchEvent(new Event('input'));
        }
        
        // Enter key untuk submit
        document.getElementById('filterForm').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.submit();
            }
        });
    </script>
</body>
</html>
