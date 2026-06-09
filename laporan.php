<?php 
include 'koneksi.php'; 

// 1. Hitung Total Pendapatan (Omzet) Keseluruhan
$queryOmzet = mysqli_query($koneksi, "SELECT SUM(total_bayar) as total_omzet FROM penjualan");
$dataOmzet  = mysqli_fetch_assoc($queryOmzet);
$totalOmzet = $dataOmzet['total_omzet'] ?? 0;

// 2. Hitung Jumlah Transaksi yang Terjadi
$queryTransaksi = mysqli_query($koneksi, "SELECT COUNT(id) as total_transaksi FROM penjualan");
$dataTransaksi  = mysqli_fetch_assoc($queryTransaksi);
$totalTransaksi = $dataTransaksi['total_transaksi'] ?? 0;

// 3. Hitung Total Produk yang Terdaftar
$queryProduk = mysqli_query($koneksi, "SELECT COUNT(id) as total_produk FROM produk");
$dataProduk  = mysqli_fetch_assoc($queryProduk);
$totalProduk = $dataProduk['total_produk'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Market - Laporan Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Smart Market</a>
            <div class="navbar-nav">
                <a class="nav-link" href="kasir.php">Halaman Kasir</a>
                <a class="nav-link" href="produk.php">Stok Barang</a>
                <a class="nav-link active" href="laporan.php">Laporan Penjualan</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <h2 class="mb-4">Dashboard & Laporan Toko</h2>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white border-0 shadow-sm">
                    <div class="card-body py-4">
                        <h6 class="card-title text-white-50 uppercase text-xs fw-bold">Total Pendapatan</h6>
                        <h2 class="fw-bold mb-0">Rp <?= number_format($totalOmzet, 0, ',', '.'); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white border-0 shadow-sm">
                    <div class="card-body py-4">
                        <h6 class="card-title text-white-50 uppercase text-xs fw-bold">Jumlah Transaksi</h6>
                        <h2 class="fw-bold mb-0"><?= $totalTransaksi; ?> Struk</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark border-0 shadow-sm">
                    <div class="card-body py-4">
                        <h6 class="card-title text-dark-50 uppercase text-xs fw-bold">Variasi Produk Terdaftar</h6>
                        <h2 class="fw-bold mb-0"><?= $totalProduk; ?> Jenis Item</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white font-weight-bold">
                Riwayat Transaksi Masuk
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width: 80px;" class="text-center">No Struk</th>
                                <th>Tanggal & Waktu</th>
                                <th>Total Belanja</th>
                                <th style="width: 200px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ambil data transaksi terbaru dari database
                            $queryList = mysqli_query($koneksi, "SELECT * FROM penjualan ORDER BY tanggal DESC");
                            
                            if (mysqli_num_rows($queryList) == 0) {
                                echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Belum ada transaksi penjualan dicatat.</td></tr>";
                            }
                            
                            while ($row = mysqli_fetch_array($queryList)) {
                            ?>
                            <tr>
                                <td class="text-center fw-bold">#<?= $row['id']; ?></td>
                                <td><?= date('d M Y - H:i', strtotime($row['tanggal'])); ?> WIB</td>
                                <td class="fw-bold text-success">Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="alert('Fitur cetak nota ID: <?= $row['id']; ?> bisa diintegrasikan di sini!')">
                                        Lihat Detail / Cetak
                                    </button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>