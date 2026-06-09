<?php
include 'koneksi.php';

// Fitur Aksi Hapus lewat URL Method GET
if (isset($_GET['hapus'])) {
    $kode = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $query = "DELETE FROM produk WHERE kode_produk = '$kode'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Produk berhasil dihapus!'); window.location='produk.php';</script>";
    }
}

// Fitur Tambah & Edit lewat Method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'];
    $kode_produk = mysqli_real_escape_string($koneksi, $_POST['kode_produk']);
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga_jual  = intval($_POST['harga_jual']);
    $stok        = intval($_POST['stok']);

    if ($aksi === 'tambah') {
        $query = "INSERT INTO produk (kode_produk, nama_produk, harga_jual, stok) VALUES ('$kode_produk', '$nama_produk', $harga_jual, $stok)";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Produk berhasil ditambahkan!'); window.location='produk.php';</script>";
        }
    } else if ($aksi === 'edit') {
        $query = "UPDATE produk SET nama_produk = '$nama_produk', harga_jual = $harga_jual, stok = $stok WHERE kode_produk = '$kode_produk'";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Produk berhasil diperbarui!'); window.location='produk.php';</script>";
        }
    }
}
?>