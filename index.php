<?php
include 'koneksi.php';

// Hapus satu data
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id=$id");
    header("Location: index.php"); exit;
}

// Hapus semua data
if (isset($_GET['hapus_semua'])) {
    mysqli_query($koneksi, "DELETE FROM pelanggan");
    header("Location: index.php"); exit;
}

// Insert data
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jumlah = (int) $_POST['jumlah'];
    $status = $_POST['status'] === 'Lunas' ? 'Lunas' : 'Belum Lunas';
    mysqli_query($koneksi, "INSERT INTO pelanggan (nama, jumlah, status) VALUES ('$nama', $jumlah, '$status')");
    header("Location: index.php"); exit;
}

// Update data
if (isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jumlah = (int) $_POST['jumlah'];
    $status = $_POST['status'] === 'Lunas' ? 'Lunas' : 'Belum Lunas';
    mysqli_query($koneksi, "UPDATE pelanggan SET nama='$nama', jumlah=$jumlah, status='$status' WHERE id=$id");
    header("Location: index.php"); exit;
}

// Ambil data untuk edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id=$id");
    if (mysqli_num_rows($res)) {
        $editData = mysqli_fetch_assoc($res);
    }
}

// Filter status
$filter = $_GET['filter'] ?? 'Semua';
$where = '';
if ($filter === 'Lunas') $where = "WHERE status='Lunas'";
elseif ($filter === 'Belum Lunas') $where = "WHERE status='Belum Lunas'";

// Ambil semua data
$query = "SELECT * FROM pelanggan $where ORDER BY 
    CASE WHEN status='Belum Lunas' THEN 0 ELSE 1 END,
    waktu DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pelanggan</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f8f9fa; padding: 40px; color: #333; }
        h2 { color: #343a40; }

        form, .controls {
            background: #fff; padding: 24px; border-radius: 10px;
            max-width: 500px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }
        label { display: block; margin-top: 15px; font-size: 14px; }
        input[type="text"], input[type="number"], select {
            width: 100%; padding: 10px; border: 1px solid #ccc;
            border-radius: 6px; margin-top: 4px;
        }
        input[type="submit"], .btn {
            margin-top: 20px; padding: 10px 16px;
            background: #0d6efd; color: white;
            border: none; border-radius: 6px;
            font-size: 14px; cursor: pointer;
            text-decoration: none; display: inline-block;
        }
        input[type="submit"]:hover, .btn:hover { background: #0b5ed7; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-gray { background: #6c757d; color: white; }

        table {
            width: 100%; border-collapse: collapse;
            background: white; border-radius: 10px;
            overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px 16px; border-bottom: 1px solid #eee;
            text-align: center; font-size: 14px;
        }
        th { background: #f1f3f5; color: #495057; }
        tr:hover { background: #f8f9fa; }
    </style>
</head>
<body>

<h2><?= $editData ? 'Edit Pelanggan' : 'Tambah Pelanggan' ?></h2>

<form method="post">
    <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">
    <label>Nama
        <input type="text" name="nama" value="<?= $editData['nama'] ?? '' ?>" required>
    </label>
    <label>Jumlah (Rp)
        <input type="number" name="jumlah" value="<?= $editData['jumlah'] ?? '' ?>" required>
    </label>
    <label>Status
        <select name="status">
            <option value="Belum Lunas" <?= ($editData['status'] ?? '') == 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
            <option value="Lunas" <?= ($editData['status'] ?? '') == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
        </select>
    </label>
    <input type="submit" name="<?= $editData ? 'update' : 'submit' ?>" value="<?= $editData ? 'Update' : 'Simpan' ?>">
</form>

<div class="controls">
    <h2>Filter</h2>
    <div>
        <a href="?filter=Semua" class="btn <?= $filter === 'Semua' ? 'btn-gray' : '' ?>">Semua</a>
        <a href="?filter=Belum Lunas" class="btn <?= $filter === 'Belum Lunas' ? 'btn-gray' : '' ?>">Belum Lunas</a>
        <a href="?filter=Lunas" class="btn <?= $filter === 'Lunas' ? 'btn-gray' : '' ?>">Lunas</a>
        <a href="?hapus_semua=1" class="btn btn-danger" onclick="return confirm('Hapus semua data?')">Hapus Semua</a>
    </div>
</div>

<h2>Daftar Pelanggan</h2>
<table>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Jumlah (Rp)</th>
        <th>Status</th>
        <th>Waktu</th>
        <th>Aksi</th>
    </tr>
    <?php $no=1; while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= number_format($row['jumlah'],0,',','.') ?></td>
        <td><?= $row['status'] ?></td>
        <td><?= date('d-m-Y H:i:s', strtotime($row['waktu'])) ?></td>
        <td>
            <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
            <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>