CREATE DATABASE IF NOT EXISTS minimarket;
USE minimarket;

CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(50) NOT NULL UNIQUE,
    nama_produk VARCHAR(100) NOT NULL,
    harga_jual INT NOT NULL,
    stok INT NOT NULL
);

CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATETIME NOT NULL,
    total_bayar INT NOT NULL
);

CREATE TABLE detail_penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penjualan_id INT NOT NULL,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (penjualan_id) REFERENCES penjualan(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
);

-- Data Awal untuk Uji Coba
INSERT INTO produk (kode_produk, nama_produk, harga_jual, stok) VALUES
('BRG001', 'Indomie Goreng', 3500, 50),
('BRG002', 'Aqua Botol 600ml', 4000, 100),
('BRG003', 'Susu Kotak UHT', 6500, 30);