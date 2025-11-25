<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = $search ? "WHERE nama_kost LIKE '%$search%' OR alamat LIKE '%$search%'" : '';

// Get total records
$totalStmt = $conn->query("SELECT COUNT(*) as total FROM alternatif $whereClause");
$totalRecords = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $limit);

// Get data
$stmt = $conn->query("SELECT * FROM alternatif $whereClause ORDER BY id_alternatif DESC LIMIT $limit OFFSET $offset");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SPK Kost</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-header {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .admin-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 1rem;
        }
        .search-box input {
            flex: 1;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-warning {
            background: #f59e0b;
            color: white;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-info {
            background: #3b82f6;
            color: white;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 2rem;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            background: white;
            border-radius: 5px;
            text-decoration: none;
            color: #667eea;
        }
        .pagination .active {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <h2>👨‍💼 Admin Panel - Kelola Data Kost</h2>
            <div class="action-buttons">
                <span>Halo, <?= $_SESSION['admin_nama'] ?></span>
                <a href="../index.php" class="btn btn-info btn-sm">Lihat Website</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>📋 Data Kost (<?= $totalRecords ?> data)</h3>
                <a href="tambah_kost.php" class="btn btn-success">+ Tambah Kost Baru</a>
            </div>

            <div class="search-box">
                <input type="text" id="searchInput" placeholder="🔍 Cari nama kost atau alamat..." value="<?= htmlspecialchars($search) ?>">
                <button onclick="searchData()" class="btn btn-primary">Cari</button>
                <?php if ($search): ?>
                    <a href="index.php" class="btn btn-warning">Reset</a>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Kost</th>
                            <th>Harga</th>
                            <th>Jarak (km)</th>
                            <th>Fasilitas</th>
                            <th>Keamanan</th>
                            <th>Kebersihan</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data) > 0): ?>
                            <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?= $row['id_alternatif'] ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_kost']) ?></strong></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td><?= $row['jarak'] ?></td>
                                <td><?= $row['fasilitas'] ?>/10</td>
                                <td><?= $row['keamanan'] ?>/10</td>
                                <td><?= $row['kebersihan'] ?>/10</td>
                                <td><?= htmlspecialchars(substr($row['alamat'], 0, 30)) ?>...</td>
                                <td>
                                    <a href="edit_kost.php?id=<?= $row['id_alternatif'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                                    <a href="#" onclick="confirmDelete(<?= $row['id_alternatif'] ?>, '<?= htmlspecialchars($row['nama_kost']) ?>')" class="btn btn-danger btn-sm">🗑️ Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 2rem;">
                                    Tidak ada data ditemukan
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">← Prev</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Next →</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
