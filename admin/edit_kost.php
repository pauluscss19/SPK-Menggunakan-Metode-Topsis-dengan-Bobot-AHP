<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

if ($id == 0) {
    $_SESSION['error_message'] = "ID tidak valid!";
    header('Location: index.php');
    exit;
}

// Get data kost
try {
    $stmt = $conn->prepare("SELECT * FROM alternatif WHERE id_alternatif = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        $_SESSION['error_message'] = "Data tidak ditemukan!";
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die("Error mengambil data: " . $e->getMessage());
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $nama_kost = trim($_POST['nama_kost']);
        $harga = floatval($_POST['harga']);
        $jarak = floatval($_POST['jarak']);
        $fasilitas = intval($_POST['fasilitas']);
        $keamanan = intval($_POST['keamanan']);
        $kebersihan = intval($_POST['kebersihan']);
        $alamat = trim($_POST['alamat']);
        
        // Validasi
        if (empty($nama_kost) || empty($alamat)) {
            throw new Exception("Nama kost dan alamat tidak boleh kosong!");
        }
        
        if ($fasilitas < 1 || $fasilitas > 10 || $keamanan < 1 || $keamanan > 10 || $kebersihan < 1 || $kebersihan > 10) {
            throw new Exception("Nilai rating harus antara 1-10!");
        }
        
        $stmt = $conn->prepare("UPDATE alternatif SET nama_kost=?, harga=?, jarak=?, fasilitas=?, keamanan=?, kebersihan=?, alamat=? WHERE id_alternatif=?");
        $result = $stmt->execute([$nama_kost, $harga, $jarak, $fasilitas, $keamanan, $kebersihan, $alamat, $id]);
        
        if ($result) {
            $_SESSION['success_message'] = "✅ Data kost berhasil diupdate!";
            header('Location: index.php');
            exit;
        } else {
            throw new Exception("Gagal mengupdate data!");
        }
        
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kost - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-danger {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        .form-hint {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }
        .info-box {
            background: #f0f4ff;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="card">
                <h3>✏️ Edit Data Kost</h3>
                
                <div class="info-box">
                    <strong>📌 ID Kost:</strong> <?= $id ?> | 
                    <strong>Nama:</strong> <?= htmlspecialchars($data['nama_kost']) ?>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <strong>❌ Error:</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" onsubmit="return validateForm(this)">
                    <div class="form-group">
                        <label>Nama Kost *</label>
                        <input type="text" name="nama_kost" id="nama_kost" value="<?= htmlspecialchars($data['nama_kost']) ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Harga per Bulan (Rp) *</label>
                            <input type="number" name="harga" id="harga" value="<?= $data['harga'] ?>" min="0" step="1000" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Jarak ke Kampus (km) *</label>
                            <input type="number" name="jarak" id="jarak" value="<?= $data['jarak'] ?>" step="0.1" min="0" max="50" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Fasilitas (1-10) *</label>
                            <input type="number" name="fasilitas" id="fasilitas" value="<?= $data['fasilitas'] ?>" min="1" max="10" required>
                            <div class="form-hint">Rating 1-10</div>
                        </div>
                        
                        <div class="form-group">
                            <label>Keamanan (1-10) *</label>
                            <input type="number" name="keamanan" id="keamanan" value="<?= $data['keamanan'] ?>" min="1" max="10" required>
                            <div class="form-hint">Rating 1-10</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Kebersihan (1-10) *</label>
                        <input type="number" name="kebersihan" id="kebersihan" value="<?= $data['kebersihan'] ?>" min="1" max="10" required>
                        <div class="form-hint">Rating 1-10</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Alamat Lengkap *</label>
                        <textarea name="alamat" id="alamat" rows="3" required><?= htmlspecialchars($data['alamat']) ?></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" id="btnSubmit" class="btn btn-success">💾 Update Data</button>
                        <a href="index.php" class="btn btn-warning">← Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function validateForm(form) {
            const btnSubmit = document.getElementById('btnSubmit');
            
            // Disable button dan tampilkan loading
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '⏳ Mengupdate...';
            
            // Validasi manual
            const fasilitas = parseInt(form.fasilitas.value);
            const keamanan = parseInt(form.keamanan.value);
            const kebersihan = parseInt(form.kebersihan.value);
            
            if (fasilitas < 1 || fasilitas > 10) {
                alert('Fasilitas harus antara 1-10');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '💾 Update Data';
                return false;
            }
            
            if (keamanan < 1 || keamanan > 10) {
                alert('Keamanan harus antara 1-10');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '💾 Update Data';
                return false;
            }
            
            if (kebersihan < 1 || kebersihan > 10) {
                alert('Kebersihan harus antara 1-10');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '💾 Update Data';
                return false;
            }
            
            console.log('Form valid, mengirim data...');
            
            // Auto-enable kembali setelah 5 detik (jaga-jaga)
            setTimeout(function() {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '💾 Update Data';
            }, 5000);
            
            return true;
        }
        
        // Log untuk debugging
        console.log('Edit form loaded for ID: <?= $id ?>');
    </script>
</body>
</html>
