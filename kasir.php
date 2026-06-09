<?php 
include 'koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Market - Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-responsive { max-height: 350px; overflow-y: auto; }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Smart Market</a>
            <div class="navbar-nav">
                <a class="nav-link active" href="kasir.php">Halaman Kasir</a>
                <a class="nav-link" href="produk.php">Stok Barang</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="row">
            
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white fw-bold">Input Barang</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="pilih_produk" class="form-label">Cari/Pilih Produk</label>
                            <select class="form-select" id="pilih_produk">
                                <option value="" data-harga="0">-- Pilih Produk --</option>
                                <?php
                                $tampil = mysqli_query($koneksi, "SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk ASC");
                                while ($data = mysqli_fetch_array($tampil)) {
                                    echo "<option value='{$data['kode_produk']}' data-nama='{$data['nama_produk']}' data-harga='{$data['harga_jual']}'>
                                            {$data['kode_produk']} - {$data['nama_produk']} (Rp " . number_format($data['harga_jual'], 0, ',', '.') . ")
                                          </option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah (Qty)</label>
                            <input type="number" class="form-control" id="jumlah" value="1" min="1">
                        </div>
                        <button type="button" class="btn btn-success w-100" id="btn_tambah">Tambah ke Keranjang</button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span>Keranjang Belanja</span>
                        <span class="badge bg-warning text-dark fs-6" id="display_total">Total: Rp0</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 align-middle">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th style="width: 100px;">Qty</th>
                                        <th>Subtotal</th>
                                        <th style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel_keranjang">
                                    <tr id="row_kosong">
                                        <td colspan="6" class="text-center text-muted py-4">Keranjang masih kosong</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 align-items-center justify-content-end">
                            <div class="col-md-3 text-end">
                                <label for="bayar" class="col-form-label fw-bold">Nominal Bayar (Rp):</label>
                            </div>
                            <div class="col-md-3">
                                <input type="number" id="bayar" class="form-control form-control-lg text-end" placeholder="0">
                            </div>
                            <div class="col-md-3 text-end">
                                <span class="fw-bold text-danger fs-5" id="kembalian">Kembalian: Rp0</span>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary btn-lg w-100" id="btn_selesai" disabled>Selesai Transaksi</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        let keranjang = [];
        let grandTotal = 0;

        const selectProduk = document.getElementById('pilih_produk');
        const inputJumlah = document.getElementById('jumlah');
        const btnTambah = document.getElementById('btn_tambah');
        const tabelKeranjang = document.getElementById('tabel_keranjang');
        const displayTotal = document.getElementById('display_total');
        const inputBayar = document.getElementById('bayar');
        const displayKembalian = document.getElementById('kembalian');
        const btnSelesai = document.getElementById('btn_selesai');

        btnTambah.addEventListener('click', function() {
            const code = selectProduk.value;
            if (!code) return alert('Silakan pilih produk terlebih dahulu!');

            const selectedOption = selectProduk.options[selectProduk.selectedIndex];
            const nama = selectedOption.getAttribute('data-nama');
            const harga = parseInt(selectedOption.getAttribute('data-harga'));
            const qty = parseInt(inputJumlah.value);

            const existingIndex = keranjang.findIndex(item => item.code === code);

            if (existingIndex > -1) {
                keranjang[existingIndex].qty += qty;
                keranjang[existingIndex].subtotal = keranjang[existingIndex].qty * harga;
            } else {
                keranjang.push({ code, nama, harga, qty, subtotal: harga * qty });
            }

            selectProduk.value = '';
            inputJumlah.value = '1';
            renderKeranjang();
        });

        function renderKeranjang() {
            tabelKeranjang.innerHTML = '';
            grandTotal = 0;

            if (keranjang.length === 0) {
                tabelKeranjang.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Keranjang masih kosong</td></tr>`;
                displayTotal.innerText = 'Total: Rp0';
                inputBayar.value = '';
                hitungKembalian();
                btnSelesai.disabled = true;
                return;
            }

            keranjang.forEach((item, index) => {
                grandTotal += item.subtotal;
                tabelKeranjang.innerHTML += `
                    <tr>
                        <td>${item.code}</td>
                        <td>${item.nama}</td>
                        <td>Rp${item.harga.toLocaleString('id-ID')}</td>
                        <td>${item.qty}</td>
                        <td>Rp${item.subtotal.toLocaleString('id-ID')}</td>
                        <td><button class="btn btn-sm btn-danger" onclick="hapusItem(${index})">Hapus</button></td>
                    </tr>`;
            });

            displayTotal.innerText = `Total: Rp${grandTotal.toLocaleString('id-ID')}`;
            hitungKembalian();
        }

        function hapusItem(index) {
            keranjang.splice(index, 1);
            renderKeranjang();
        }

        inputBayar.addEventListener('input', hitungKembalian);

        function hitungKembalian() {
            const bayar = parseInt(inputBayar.value) || 0;
            if (grandTotal > 0 && bayar >= grandTotal) {
                const sisa = bayar - grandTotal;
                displayKembalian.innerText = `Kembalian: Rp${sisa.toLocaleString('id-ID')}`;
                displayKembalian.classList.replace('text-danger', 'text-success');
                btnSelesai.disabled = false;
            } else {
                displayKembalian.innerText = `Kembalian: Rp0`;
                displayKembalian.classList.replace('text-success', 'text-danger');
                btnSelesai.disabled = true;
            }
        }

        btnSelesai.addEventListener('click', function() {
            const nominalBayar = parseInt(inputBayar.value) || 0;

            const dataTransaksi = {
                total: grandTotal,
                bayar: nominalBayar,
                items: keranjang
            };

            fetch('proses_transaksi.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataTransaksi)
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    alert('Transaksi Sukses! ' + res.message);
                    keranjang = [];
                    renderKeranjang();
                    location.reload(); // Refresh halaman untuk memperbarui opsi stok dropdown
                } else {
                    alert('Gagal: ' + res.message);
                }
            })
            .catch(err => alert('Terjadi kesalahan sistem.'));
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>