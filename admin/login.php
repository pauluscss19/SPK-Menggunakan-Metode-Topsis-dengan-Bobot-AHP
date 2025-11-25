<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Cek apakah admin ditemukan
    if ($admin) {
        // Jika password di database kosong atau belum ter-hash, set default
        if (empty($admin['password']) || strlen($admin['password']) < 20) {
            // Update password langsung
            $newHash = password_hash('admin123', PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE admin SET password = ? WHERE id_admin = ?");
            $updateStmt->execute([$newHash, $admin['id_admin']]);
            $admin['password'] = $newHash;
        }
        
        // Verifikasi password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_nama'] = $admin['nama'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Password salah!';
        }
    } else {
        // Jika admin belum ada, buat otomatis
        $newHash = password_hash('admin123', PASSWORD_DEFAULT);
        $createStmt = $conn->prepare("INSERT INTO admin (username, password, nama) VALUES (?, ?, ?)");
        $createStmt->execute(['admin', $newHash, 'Administrator']);
        
        $error = 'Admin baru dibuat! Silakan login dengan username: admin, password: admin123';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SPK Kost</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .login-container h2 {
            text-align: center;
            color: #667eea;
            margin-bottom: 2rem;
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
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .error-message {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .info-message {
            background: #e0f2fe;
            color: #0369a1;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 Login Admin</h2>
        
        <?php if ($error): ?>
            <?php if (strpos($error, 'dibuat') !== false): ?>
                <div class="info-message">ℹ️ <?= $error ?></div>
            <?php else: ?>
                <div class="error-message">❌ <?= $error ?></div>
            <?php endif; ?>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="admin" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" value="admin123" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem; color: #999;">
            Default: <strong>admin</strong> / <strong>admin123</strong>
        </p>
        
        <p style="text-align: center; margin-top: 1rem;">
            <a href="../index.php" style="color: #667eea;">← Kembali ke Beranda</a>
        </p>
    </div>
</body>
</html>
