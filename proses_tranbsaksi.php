<?php
header('Content-Type: application/json');
include 'koneksi.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
    exit;
}

$total_bayar = $input['total'];
$tanggal     = date('Y-m-d H:i:s');
$items       = $input['items'];

mysqli_begin_transaction($koneksi);

try {
    $queryPenjualan = "INSERT INTO penjualan (tanggal, total_bayar) VALUES ('$tanggal', $total_bayar)";
    mysqli_query($koneksi, $queryPenjualan);
    $penjualan_id = mysqli_insert_id($koneksi);

    foreach ($items as $item) {
        $kode_produk = mysqli_real_escape_string($koneksi, $item['code']);
        $qty         = intval($item['qty']);
        $subtotal    = intval($item['subtotal']);

        $resProduk = mysqli_query($koneksi, "SELECT id, stok FROM produk WHERE kode_produk = '$kode_produk'");
        $produk    = mysqli_fetch_assoc($resProduk);

        if (!$produk || $produk['stok'] < $qty) {
            throw new Exception("Stok produk {$item['nama']} tidak mencukupi atau tidak ditemukan!");
        }

        $produk_id = $produk['id'];
        
        mysqli_query($koneksi, "INSERT INTO detail_penjualan (penjualan_id, produk_id, jumlah, subtotal) VALUES ($penjualan_id, $produk_id, $qty, $subtotal)");
        mysqli_query($koneksi, "UPDATE produk SET stok = stok - $qty WHERE id = $produk_id");
    }

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil disimpan dan stok dipotong!']);
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>