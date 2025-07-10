use  okediil_service;

create table customer (
    id_customer VARCHAR(6) NOT NULL, nama VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, no_hp VARCHAR(15) NOT NULL, alamat VARCHAR(20) NOT NULL, jenis_kelamin CHAR(1) NOT NULL, status_pekerjaan VARCHAR(10) NOT NULL, sumber VARCHAR(15) NOT NULL,media_sosial VARCHAR(20), diinput_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id_customer)
)

alter table customer
    add column berapa_kali_servis INT DEFAULT 0


select * from customer;
show TABLEs

create table karyawan (
    id_karyawan VARCHAR(6) NOT NULL, 
    nama VARCHAR(50) NOT NULL, 
    jenis_kelamin CHAR(1) NOT NULL, 
    tempat_tanggal_lahir VARCHAR(50) NOT NULL, 
    alamat VARCHAR(150) NOT NULL, 
    no_hp VARCHAR(15) NOT NULL, 
    tanggal_masuk DATE NOT NULL, 
    bidang VARCHAR(20), 
    status_karyawan VARCHAR(20), 
    cabang VARCHAR(20), 
    ukuran_baju VARCHAR(5), 
    tanggal_resign DATE, 
    PRIMARY KEY (id_karyawan)
);

create table transaksi(
    id_transaksi int NOT NULL AUTO_INCREMENT,
    id_customer VARCHAR(6) NOT NULL,
    id_karyawan VARCHAR(6) NOT NULL,
    servis_layanan VARCHAR(10) NOT NULL,
    merk VARCHAR(20) NOT NULL,
    tipe VARCHAR(20) NOT NULL,
    warna VARCHAR(20) NOT NULL,
    tanggal_masuk DATE NOT NULL,
    tanggal_keluar DATE,
    tambahan VARCHAR(100),
    catatan VARCHAR(1000),
    keluhan VARCHAR(1000),
    kelengkapan VARCHAR(100),
    pin VARCHAR(15),
    kerusakan VARCHAR(1000),
    id_pembelian INT,
    kuantitas INT NOT NULL,
    garansi INT,
    total_biaya DECIMAL(10, 2) NOT NULL,
    status_transaksi VARCHAR(20) NOT NULL,
    PRIMARY KEY (id_transaksi),
    FOREIGN KEY (id_customer) REFERENCES customer(id_customer),
    FOREIGN KEY (id_karyawan) REFERENCES karyawan(id_karyawan),
    FOREIGN KEY (id_pembelian) REFERENCES pembelian(id_pembelian)   
)

create table pembelian(
    id_pembelian INT AUTO_INCREMENT NOT NULL,
    nama_produk VARCHAR(50) NOT NULL,
    kategori_produk VARCHAR(20) NOT NULL,
    merk VARCHAR(20) NOT NULL,
    jenis_produk VARCHAR(30) NOT NULL,
    tanggal DATE NOT NULL,
    jumlah_produk INT NOT NULL,
    kualitas_produk VARCHAR(20) NOT NULL,
    garansi_produk DATE NOT NULL,
    nama_mitra VARCHAR(50) NOT NULL,
    harga_beli BIGINT NOT NULL,
    ongkir BIGINT NOT NULL,
    metode_pembayaran VARCHAR(20) NOT NULL,
    PRIMARY KEY (id_pembelian)
);

create Table pengeluaran(
    id_pengeluaran INT AUTO_INCREMENT NOT NULL,
    nama_pengeluaran VARCHAR(50) NOT NULL,
    jenis_pengeluaran VARCHAR(20) NOT NULL,
    harga BIGINT NOT NULL,
    kuantitas INT NOT NULL,
    tanggal DATE NOT NULL,
    lokasi VARCHAR(100),
    PRIMARY KEY (id_pengeluaran)
);

create table aset (
    id_aset INT AUTO_INCREMENT NOT NULL,
    nama_aset VARCHAR(50) NOT NULL,
    barcode BLOB,
    jenis_aset VARCHAR(20) NOT NULL,
    kondisi VARCHAR(20) NOT NULL,
    tanggal_pembelian DATE NOT NULL,
    harga BIGINT NOT NULL,
    lokasi VARCHAR(100),
    garansi DATE,
    jumlah INT NOT NULL,
    catatan VARCHAR(1000),
    PRIMARY KEY (id_aset)
);


CREATE Table omal(
    id_omal INT AUTO_INCREMENT NOT NULL,
    tanggal DATE NOT NULL,
    status_omal VARCHAR(20) NOT NULL,
    keterangan VARCHAR(1000) NOT NULL,
    PRIMARY KEY (id_omal)
)

create table teknisi (
    id_transaksi INT NOT NULL,
    nama_teknisi VARCHAR(50) NOT NULL,
    jenis_kelamin CHAR(1) NOT NULL,
    alamat VARCHAR(150) NOT NULL,
    no_hp VARCHAR(15) NOT NULL,
    tanggal_masuk DATE NOT NULL,
    bidang VARCHAR(20),
    status_teknisi VARCHAR(20),
    cabang VARCHAR(20),
    ukuran_baju VARCHAR(5),
    tanggal_resign DATE,
    PRIMARY KEY (id_teknisi)
);

ALTER TABLE karyawan 
MODIFY COLUMN password VARCHAR(255) NOT NULL;


