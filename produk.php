<?php 
include 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Market - Data Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Smart Market</a>
            <div class="navbar-nav">
                <a class="nav-link" href="kasir.php">Halaman Kasir</a>
                <a class="nav-link active" href="produk.php">Stok Barang</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Master Produk</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProduk" onclick="bukaModalTambah()">
                + Tambah Produk Baru
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id DESC");
                        if(mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='5' class='text-center py-3 text-muted'>Belum ada data barang.</td></tr>";
                        }
                        while ($row = mysqli_fetch_array($query)) {
                        ?>
                        <tr>
                            <td><?= $row['kode_produk']; ?></td>
                            <td><?= $row['nama_produk']; ?></td>
                            <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                            <td><?= $row['stok']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="bukaModalEdit('<?= $row['kode_produk']; ?>', '<?= $row['nama_produk']; ?>', <?= $row['harga_jual']; ?>, <?= $row['stok']; ?>)">Edit</button>
                                <a href="proses_produk.php?hapus=<?= $row['kode_produk']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalProduk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProdukTitle">Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form_produk" method="POST" action="proses_produk.php">
                    <div class="modal-body">
                        <input type="hidden" id="aksi" name="aksi" value="tambah">
                        <div class="mb-3">
                            <label class="form-label">Kode Produk</label>
                            <input type="text" class="form-control" id="kode_produk" name="kode_produk" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Jual (Rp)</label>
                            <input type="number" class="form-control" id="harga_jual" name="harga_jual" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modalTitle = document.getElementById('modalProdukTitle');
        const inputAksi = document.getElementById('aksi');
        const inputKode = document.getElementById('kode_produk');
        const inputNama = document.getElementById('nama_produk');
        const inputHarga = document.getElementById('harga_jual');
        const inputStok = document.getElementById('stok');

        function bukaModalTambah() {
            modalTitle.innerText = "Tambah Produk Baru";
            inputAksi.value = "tambah";
            inputKode.value = ""; inputKode.removeAttribute("readonly");
            inputNama.value = ""; inputHarga.value = ""; inputStok.value = "";
        }

        function bukaModalEdit(kode, nama, harga, stok) {
            const modalInstance = new bootstrap.Modal(document.getElementById('modalProduk'));
            modalInstance.show();
            modalTitle.innerText = "Edit Data Produk";
            inputAksi.value = "edit";
            inputKode.value = kode; inputKode.setAttribute("readonly", true);
            inputNama.value = nama; inputHarga.value = harga; inputStok.value = stok;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>