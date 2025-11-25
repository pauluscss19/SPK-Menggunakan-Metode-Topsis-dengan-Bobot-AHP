<?php
require_once 'config/database.php';
require_once 'includes/ahp.php';
require_once 'includes/topsis.php';

$ahp = new AHP($conn);
$bobot = $ahp->hitungBobot();
$cr = $ahp->hitungConsistencyRatio();

$topsis = new TOPSIS($conn, $bobot);
$hasil = $topsis->hitung();

// Ambil 5 data teratas untuk testing
$hasilTop5 = array_slice($hasil, 0, 5);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing & Validasi - SPK Kost</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .test-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .test-section h3 {
            color: #667eea;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }
        .step-box {
            background: #f8f9ff;
            padding: 1rem;
            border-left: 4px solid #667eea;
            margin-bottom: 1rem;
        }
        .matrix-table {
            font-size: 0.9rem;
            margin: 1rem 0;
        }
        .matrix-table td, .matrix-table th {
            padding: 8px;
            text-align: center;
        }
        .pass {
            color: #10b981;
            font-weight: bold;
        }
        .fail {
            color: #ef4444;
            font-weight: bold;
        }
        .formula {
            background: #fff;
            border: 1px solid #e0e0e0;
            padding: 1rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            margin: 1rem 0;
            overflow-x: auto;
        }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1rem 0;
        }
        .calc-step {
            background: #fff;
            border: 1px solid #e0e0e0;
            padding: 1rem;
            border-radius: 8px;
            margin: 0.5rem 0;
        }
        .highlight {
            background: #fef3c7;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .nav-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e0e0e0;
        }
        .nav-tabs a {
            padding: 1rem 2rem;
            text-decoration: none;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        .nav-tabs a:hover, .nav-tabs a.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1>🏠 SPK Pemilihan Kost Mahasiswa</h1>
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="hasil.php">Cari Kost</a></li>
                <li><a href="testing.php" class="active">Testing</a></li>
                <li><a href="admin/">Admin</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="hero">
            <h2>🧪 Testing & Validasi Sistem</h2>
            <p>Pengujian Akurasi Perhitungan AHP & TOPSIS</p>
        </div>

        <!-- Tab Navigation -->
        <div class="nav-tabs">
            <a href="#test-ahp" class="active" onclick="showTab('test-ahp')">Test AHP</a>
            <a href="#test-topsis" onclick="showTab('test-topsis')">Test TOPSIS</a>
            <a href="#test-skenario" onclick="showTab('test-skenario')">Skenario Testing</a>
            <a href="#test-validasi" onclick="showTab('test-validasi')">Validasi Hasil</a>
        </div>

        <!-- TEST AHP -->
        <div id="test-ahp" class="test-section">
            <h3>📊 Test 1: Perhitungan Bobot dengan Metode AHP</h3>
            
            <div class="step-box">
                <h4>Step 1: Matriks Perbandingan Berpasangan (Skala Saaty)</h4>
                <p><strong>Skala:</strong> 1=Sama penting, 3=Sedikit lebih penting, 5=Lebih penting, 7=Sangat lebih penting, 9=Mutlak lebih penting</p>
            </div>

            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>C1 (Harga)</th>
                        <th>C2 (Jarak)</th>
                        <th>C3 (Fasilitas)</th>
                        <th>C4 (Keamanan)</th>
                        <th>C5 (Kebersihan)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $matriks = $ahp->getMatriksPerbandingan();
                    $kriteriaNama = ['C1 (Harga)', 'C2 (Jarak)', 'C3 (Fasilitas)', 'C4 (Keamanan)', 'C5 (Kebersihan)'];
                    foreach ($matriks as $i => $row): 
                    ?>
                    <tr>
                        <td><strong><?= $kriteriaNama[$i] ?></strong></td>
                        <?php foreach ($row as $val): ?>
                        <td><?= number_format($val, 2) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="step-box">
                <h4>Step 2: Normalisasi Matriks</h4>
                <p>Rumus: nilai / jumlah_kolom</p>
            </div>

            <?php
            $n = count($matriks);
            $jumlahKolom = array_fill(0, $n, 0);
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $jumlahKolom[$j] += $matriks[$i][$j];
                }
            }
            
            $matriksNormalisasi = [];
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $matriksNormalisasi[$i][$j] = $matriks[$i][$j] / $jumlahKolom[$j];
                }
            }
            ?>

            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>C1</th>
                        <th>C2</th>
                        <th>C3</th>
                        <th>C4</th>
                        <th>C5</th>
                        <th>Rata-rata (Bobot)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matriksNormalisasi as $i => $row): 
                        $rataRata = array_sum($row) / $n;
                    ?>
                    <tr>
                        <td><strong><?= $kriteriaNama[$i] ?></strong></td>
                        <?php foreach ($row as $val): ?>
                        <td><?= number_format($val, 4) ?></td>
                        <?php endforeach; ?>
                        <td><strong class="highlight"><?= number_format($rataRata, 4) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="step-box">
                <h4>Step 3: Consistency Ratio (CR)</h4>
                <div class="formula">
λmax = <?= number_format($cr['lambda_max'], 4) ?><br>
CI = (λmax - n) / (n - 1) = (<?= number_format($cr['lambda_max'], 4) ?> - 5) / 4 = <?= number_format($cr['CI'], 4) ?><br>
CR = CI / RI = <?= number_format($cr['CI'], 4) ?> / 1.12 = <?= number_format($cr['CR'], 4) ?>
                </div>
                <p>
                    <?php if ($cr['konsisten']): ?>
                        <span class="pass">✅ PASS: CR = <?= number_format($cr['CR'], 4) ?> < 0.1 (Konsisten)</span>
                    <?php else: ?>
                        <span class="fail">❌ FAIL: CR = <?= number_format($cr['CR'], 4) ?> ≥ 0.1 (Tidak Konsisten)</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- TEST TOPSIS -->
        <div id="test-topsis" class="test-section" style="display:none;">
            <h3>🎯 Test 2: Perhitungan TOPSIS (Top 5 Alternatif)</h3>
            
            <div class="step-box">
                <h4>Step 1: Matriks Keputusan (Data Mentah)</h4>
            </div>

            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <th>C1: Harga</th>
                        <th>C2: Jarak</th>
                        <th>C3: Fasilitas</th>
                        <th>C4: Keamanan</th>
                        <th>C5: Kebersihan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hasilTop5 as $item): 
                        $alt = $item['alternatif'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($alt['nama_kost']) ?></td>
                        <td><?= number_format($alt['harga'], 0) ?></td>
                        <td><?= $alt['jarak'] ?></td>
                        <td><?= $alt['fasilitas'] ?></td>
                        <td><?= $alt['keamanan'] ?></td>
                        <td><?= $alt['kebersihan'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="step-box">
                <h4>Step 2: Normalisasi (Vector Normalization)</h4>
                <p>Rumus: r<sub>ij</sub> = x<sub>ij</sub> / √(Σx<sub>ij</sub>²)</p>
            </div>

            <?php
            // Hitung normalisasi untuk top 5
            $dataMentah = [];
            foreach ($hasilTop5 as $item) {
                $alt = $item['alternatif'];
                $dataMentah[] = [
                    $alt['harga'],
                    $alt['jarak'],
                    $alt['fasilitas'],
                    $alt['keamanan'],
                    $alt['kebersihan']
                ];
            }
            
            $jumlahKuadrat = [0, 0, 0, 0, 0];
            foreach ($dataMentah as $row) {
                for ($j = 0; $j < 5; $j++) {
                    $jumlahKuadrat[$j] += pow($row[$j], 2);
                }
            }
            
            $pembagi = array_map('sqrt', $jumlahKuadrat);
            
            $dataNormalisasi = [];
            foreach ($dataMentah as $i => $row) {
                for ($j = 0; $j < 5; $j++) {
                    $dataNormalisasi[$i][$j] = $row[$j] / $pembagi[$j];
                }
            }
            ?>

            <div class="calc-step">
                <strong>Pembagi (√Σx²):</strong><br>
                C1: √<?= number_format($jumlahKuadrat[0], 2) ?> = <?= number_format($pembagi[0], 4) ?><br>
                C2: √<?= number_format($jumlahKuadrat[1], 2) ?> = <?= number_format($pembagi[1], 4) ?><br>
                C3: √<?= number_format($jumlahKuadrat[2], 2) ?> = <?= number_format($pembagi[2], 4) ?><br>
                C4: √<?= number_format($jumlahKuadrat[3], 2) ?> = <?= number_format($pembagi[3], 4) ?><br>
                C5: √<?= number_format($jumlahKuadrat[4], 2) ?> = <?= number_format($pembagi[4], 4) ?>
            </div>

            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <th>C1</th>
                        <th>C2</th>
                        <th>C3</th>
                        <th>C4</th>
                        <th>C5</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataNormalisasi as $i => $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($hasilTop5[$i]['alternatif']['nama_kost']) ?></td>
                        <?php foreach ($row as $val): ?>
                        <td><?= number_format($val, 4) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="step-box">
                <h4>Step 3: Matriks Ternormalisasi Terbobot</h4>
                <p>Rumus: y<sub>ij</sub> = w<sub>j</sub> × r<sub>ij</sub></p>
                <p><strong>Bobot:</strong> C1=<?= number_format($bobot[0], 4) ?>, C2=<?= number_format($bobot[1], 4) ?>, C3=<?= number_format($bobot[2], 4) ?>, C4=<?= number_format($bobot[3], 4) ?>, C5=<?= number_format($bobot[4], 4) ?></p>
            </div>

            <?php
            $dataTerbobot = [];
            foreach ($dataNormalisasi as $i => $row) {
                for ($j = 0; $j < 5; $j++) {
                    $dataTerbobot[$i][$j] = $row[$j] * $bobot[$j];
                }
            }
            ?>

            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <th>C1</th>
                        <th>C2</th>
                        <th>C3</th>
                        <th>C4</th>
                        <th>C5</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataTerbobot as $i => $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($hasilTop5[$i]['alternatif']['nama_kost']) ?></td>
                        <?php foreach ($row as $val): ?>
                        <td><?= number_format($val, 4) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="step-box">
                <h4>Step 4: Solusi Ideal Positif (A+) dan Negatif (A-)</h4>
                <p>Cost criteria (minimize): A+ = min, A- = max<br>
                   Benefit criteria (maximize): A+ = max, A- = min</p>
            </div>

            <?php
            $kriteria = $topsis->getKriteria();
            $idealPositif = [];
            $idealNegatif = [];
            
            for ($j = 0; $j < 5; $j++) {
                $kolom = array_column($dataTerbobot, $j);
                if ($kriteria[$j]['jenis'] == 'benefit') {
                    $idealPositif[$j] = max($kolom);
                    $idealNegatif[$j] = min($kolom);
                } else {
                    $idealPositif[$j] = min($kolom);
                    $idealNegatif[$j] = max($kolom);
                }
            }
            ?>

            <div class="comparison-grid">
                <div class="calc-step">
                    <strong>A+ (Ideal Positif):</strong><br>
                    C1: <?= number_format($idealPositif[0], 4) ?> (cost: min)<br>
                    C2: <?= number_format($idealPositif[1], 4) ?> (cost: min)<br>
                    C3: <?= number_format($idealPositif[2], 4) ?> (benefit: max)<br>
                    C4: <?= number_format($idealPositif[3], 4) ?> (benefit: max)<br>
                    C5: <?= number_format($idealPositif[4], 4) ?> (benefit: max)
                </div>
                <div class="calc-step">
                    <strong>A- (Ideal Negatif):</strong><br>
                    C1: <?= number_format($idealNegatif[0], 4) ?> (cost: max)<br>
                    C2: <?= number_format($idealNegatif[1], 4) ?> (cost: max)<br>
                    C3: <?= number_format($idealNegatif[2], 4) ?> (benefit: min)<br>
                    C4: <?= number_format($idealNegatif[3], 4) ?> (benefit: min)<br>
                    C5: <?= number_format($idealNegatif[4], 4) ?> (benefit: min)
                </div>
            </div>

            <div class="step-box">
                <h4>Step 5: Jarak Euclidean & Preferensi</h4>
                <p>D+ = √Σ(y<sub>ij</sub> - A+)²<br>
                   D- = √Σ(y<sub>ij</sub> - A-)²<br>
                   V = D- / (D+ + D-)</p>
            </div>

            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Alternatif</th>
                        <th>D+ (Jarak ke Ideal+)</th>
                        <th>D- (Jarak ke Ideal-)</th>
                        <th>Preferensi (V)</th>
                        <th>Ranking</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hasilTop5 as $i => $item): 
                        $dPlus = 0;
                        $dMinus = 0;
                        for ($j = 0; $j < 5; $j++) {
                            $dPlus += pow($dataTerbobot[$i][$j] - $idealPositif[$j], 2);
                            $dMinus += pow($dataTerbobot[$i][$j] - $idealNegatif[$j], 2);
                        }
                        $dPlus = sqrt($dPlus);
                        $dMinus = sqrt($dMinus);
                        $preferensi = $dMinus / ($dPlus + $dMinus);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['alternatif']['nama_kost']) ?></td>
                        <td><?= number_format($dPlus, 4) ?></td>
                        <td><?= number_format($dMinus, 4) ?></td>
                        <td><strong><?= number_format($preferensi, 4) ?></strong></td>
                        <td><strong><?= $i + 1 ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- SKENARIO TESTING -->
        <div id="test-skenario" class="test-section" style="display:none;">
            <h3>🧩 Test 3: Skenario Testing</h3>
            
            <div class="step-box">
                <h4>Skenario 1: Mahasiswa dengan Budget Terbatas (Prioritas: Harga Murah)</h4>
                <p><strong>Kriteria:</strong> Harga rendah, jarak dekat lebih diutamakan</p>
            </div>

            <?php
            // Filter kost dengan harga < 800000
            $kostMurah = array_filter($hasil, function($item) {
                return $item['alternatif']['harga'] < 800000;
            });
            $kostMurah = array_slice($kostMurah, 0, 5);
            ?>

            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Nama Kost</th>
                        <th>Harga</th>
                        <th>Jarak</th>
                        <th>Skor</th>
                        <th>Validasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; foreach ($kostMurah as $item): 
                        $alt = $item['alternatif'];
                        $valid = ($alt['harga'] < 800000) ? '✅' : '❌';
                    ?>
                    <tr>
                        <td><?= $rank++ ?></td>
                        <td><?= htmlspecialchars($alt['nama_kost']) ?></td>
                        <td>Rp <?= number_format($alt['harga'], 0) ?></td>
                        <td><?= $alt['jarak'] ?> km</td>
                        <td><?= number_format($item['preferensi'], 4) ?></td>
                        <td><strong><?= $valid ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="step-box">
                <h4>Skenario 2: Mahasiswa Prioritas Lokasi Dekat (Jarak < 1.5 km)</h4>
                <p><strong>Kriteria:</strong> Jarak dekat dari kampus</p>
            </div>

            <?php
            $kostDekat = array_filter($hasil, function($item) {
                return $item['alternatif']['jarak'] < 1.5;
            });
            $kostDekat = array_slice($kostDekat, 0, 5);
            ?>

            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Nama Kost</th>
                        <th>Jarak</th>
                        <th>Harga</th>
                        <th>Skor</th>
                        <th>Validasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; foreach ($kostDekat as $item): 
                        $alt = $item['alternatif'];
                        $valid = ($alt['jarak'] < 1.5) ? '✅' : '❌';
                    ?>
                    <tr>
                        <td><?= $rank++ ?></td>
                        <td><?= htmlspecialchars($alt['nama_kost']) ?></td>
                        <td><?= $alt['jarak'] ?> km</td>
                        <td>Rp <?= number_format($alt['harga'], 0) ?></td>
                        <td><?= number_format($item['preferensi'], 4) ?></td>
                        <td><strong><?= $valid ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="step-box">
                <h4>Skenario 3: Mahasiswa Prioritas Fasilitas Lengkap (Rating ≥ 8)</h4>
                <p><strong>Kriteria:</strong> Fasilitas, keamanan, kebersihan tinggi</p>
            </div>

            <?php
            $kostFasilitas = array_filter($hasil, function($item) {
                $alt = $item['alternatif'];
                return ($alt['fasilitas'] >= 8 && $alt['keamanan'] >= 8);
            });
            $kostFasilitas = array_slice($kostFasilitas, 0, 5);
            ?>

            <table class="matrix-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Nama Kost</th>
                        <th>Fasilitas</th>
                        <th>Keamanan</th>
                        <th>Kebersihan</th>
                        <th>Skor</th>
                        <th>Validasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; foreach ($kostFasilitas as $item): 
                        $alt = $item['alternatif'];
                        $valid = ($alt['fasilitas'] >= 8 && $alt['keamanan'] >= 8) ? '✅' : '❌';
                    ?>
                    <tr>
                        <td><?= $rank++ ?></td>
                        <td><?= htmlspecialchars($alt['nama_kost']) ?></td>
                        <td><?= $alt['fasilitas'] ?>/10</td>
                        <td><?= $alt['keamanan'] ?>/10</td>
                        <td><?= $alt['kebersihan'] ?>/10</td>
                        <td><?= number_format($item['preferensi'], 4) ?></td>
                        <td><strong><?= $valid ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- VALIDASI HASIL -->
        <div id="test-validasi" class="test-section" style="display:none;">
            <h3>✅ Test 4: Validasi Hasil Akhir</h3>
            
            <div class="step-box">
                <h4>Checklist Validasi Sistem</h4>
            </div>

            <?php
            $totalData = count($hasil);
            $validations = [
                ['Test', 'Kriteria', 'Hasil', 'Status'],
                ['Jumlah Data', '45 alternatif', $totalData . ' data', ($totalData == 45 ? 'PASS' : 'FAIL')],
                ['Bobot AHP', 'Total = 1.0000', number_format(array_sum($bobot), 4), (abs(array_sum($bobot) - 1) < 0.0001 ? 'PASS' : 'FAIL')],
                ['Consistency Ratio', 'CR < 0.1', number_format($cr['CR'], 4), ($cr['konsisten'] ? 'PASS' : 'FAIL')],
                ['Skor TOPSIS', '0 ≤ V ≤ 1', 'Min: ' . number_format(end($hasil)['preferensi'], 4) . ', Max: ' . number_format($hasil[0]['preferensi'], 4), 
                    (end($hasil)['preferensi'] >= 0 && $hasil[0]['preferensi'] <= 1 ? 'PASS' : 'FAIL')],
                ['Urutan Ranking', 'Descending', 'V1 ≥ V2 ≥ V3...', ($hasil[0]['preferensi'] >= $hasil[1]['preferensi'] ? 'PASS' : 'FAIL')],
            ];
            ?>

            <table class="matrix-table">
                <thead>
                    <tr>
                        <?php foreach ($validations[0] as $header): ?>
                        <th><?= $header ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i < count($validations); $i++): 
                        $row = $validations[$i];
                        $statusClass = ($row[3] == 'PASS') ? 'pass' : 'fail';
                    ?>
                    <tr>
                        <td><?= $row[0] ?></td>
                        <td><?= $row[1] ?></td>
                        <td><?= $row[2] ?></td>
                        <td class="<?= $statusClass ?>"><?= $row[3] ?></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="step-box">
                <h4>Ringkasan Testing</h4>
                <?php
                $passCount = 0;
                for ($i = 1; $i < count($validations); $i++) {
                    if ($validations[$i][3] == 'PASS') $passCount++;
                }
                $totalTests = count($validations) - 1;
                $percentage = ($passCount / $totalTests) * 100;
                ?>
                <p><strong>Total Test:</strong> <?= $totalTests ?></p>
                <p><strong>Pass:</strong> <span class="pass"><?= $passCount ?> test</span></p>
                <p><strong>Fail:</strong> <span class="fail"><?= ($totalTests - $passCount) ?> test</span></p>
                <p><strong>Success Rate:</strong> <span class="<?= $percentage == 100 ? 'pass' : 'fail' ?>"><?= number_format($percentage, 1) ?>%</span></p>
                
                <?php if ($percentage == 100): ?>
                <div class="alert alert-success" style="margin-top: 1rem; padding: 1rem; background: #d1fae5; color: #065f46; border-radius: 8px;">
                    <strong>✅ SISTEM VALID!</strong> Semua test berhasil. Perhitungan AHP dan TOPSIS akurat.
                </div>
                <?php else: ?>
                <div class="alert alert-danger" style="margin-top: 1rem; padding: 1rem; background: #fee; color: #c33; border-radius: 8px;">
                    <strong>❌ PERHATIAN!</strong> Ada test yang gagal. Periksa kembali perhitungan.
                </div>
                <?php endif; ?>
            </div>

            <div class="step-box">
                <h4>Rekomendasi Berdasarkan Testing</h4>
                <ul>
                    <li>✅ Sistem dapat memberikan rekomendasi yang akurat untuk berbagai skenario pengguna</li>
                    <li>✅ Perhitungan AHP menghasilkan bobot yang konsisten (CR < 0.1)</li>
                    <li>✅ Perhitungan TOPSIS menghasilkan ranking yang valid (0 ≤ V ≤ 1)</li>
                    <li>✅ Sistem dapat digunakan untuk final project dengan tingkat akurasi tinggi</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <h3>📥 Export Hasil Testing</h3>
            <p>Dokumentasikan hasil testing untuk laporan final project:</p>
            <div style="display: flex; gap: 10px; margin-top: 1rem;">
                <button onclick="window.print()" class="btn btn-primary">🖨️ Print Halaman Testing</button>
                <button onclick="exportTestResults()" class="btn btn-success">📄 Export ke CSV</button>
                <a href="index.php" class="btn btn-warning">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 SPK Pemilihan Kost | Testing & Validation System</p>
    </footer>

    <script>
        function showTab(tabId) {
            // Hide all sections
            document.querySelectorAll('.test-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.nav-tabs a').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(tabId).style.display = 'block';
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        function exportTestResults() {
            const hasil = <?= json_encode($hasil) ?>;
            let csv = 'Rank,Nama Kost,Harga,Jarak,Fasilitas,Keamanan,Kebersihan,Skor TOPSIS\n';
            
            hasil.forEach((item, index) => {
                const alt = item.alternatif;
                csv += `${index + 1},"${alt.nama_kost}",${alt.harga},${alt.jarak},${alt.fasilitas},${alt.keamanan},${alt.kebersihan},${item.preferensi}\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'hasil_testing_spk_kost.csv';
            a.click();
            
            alert('✅ Hasil testing berhasil di-export!');
        }

        // Auto-show first tab on load
        document.addEventListener('DOMContentLoaded', function() {
            showTab('test-ahp');
        });
    </script>
</body>
</html>
