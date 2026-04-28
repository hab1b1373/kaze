<?php
declare(strict_types=1);

$dbHost = "127.0.0.1";
$dbUser = "root";
$dbPass = "";
$dbName = "kaze_db";

// Koneksi awal tanpa memilih database agar bisa auto-create.
$conn = mysqli_connect($dbHost, $dbUser, $dbPass);
if (!$conn) {
    die("Koneksi MySQL gagal. Pastikan MySQL XAMPP sudah berjalan.");
}

$safeDbName = preg_replace("/[^a-zA-Z0-9_]/", "", $dbName);
if ($safeDbName === "") {
    die("Nama database tidak valid.");
}

$createDbSql = "CREATE DATABASE IF NOT EXISTS `{$safeDbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!mysqli_query($conn, $createDbSql)) {
    die("Gagal membuat database `{$safeDbName}`: " . mysqli_error($conn));
}

if (!mysqli_select_db($conn, $safeDbName)) {
    die("Gagal memilih database `{$safeDbName}`: " . mysqli_error($conn));
}

mysqli_set_charset($conn, "utf8mb4");

// Auto-init tabel inti jika belum ada.
$schemaSql = [
    "CREATE TABLE IF NOT EXISTS admins (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS berita (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(180) NOT NULL,
        kategori ENUM('Klub', 'Latihan', 'Turnamen') NOT NULL DEFAULT 'Klub',
        isi TEXT NOT NULL,
        tanggal DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS pemain (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(120) NOT NULL,
        posisi ENUM('Tunggal', 'Ganda') NOT NULL DEFAULT 'Tunggal',
        nickname VARCHAR(80) NOT NULL,
        foto VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS turnamen (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(160) NOT NULL,
        tanggal DATE NOT NULL,
        lokasi VARCHAR(160) NOT NULL,
        status ENUM('Akan Datang', 'Berlangsung', 'Selesai') NOT NULL DEFAULT 'Akan Datang',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS score (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        pemain_id INT UNSIGNED NOT NULL,
        turnamen_id INT UNSIGNED NOT NULL,
        point SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        tanggal DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_score_pemain FOREIGN KEY (pemain_id) REFERENCES pemain(id) ON DELETE CASCADE,
        CONSTRAINT fk_score_turnamen FOREIGN KEY (turnamen_id) REFERENCES turnamen(id) ON DELETE CASCADE
    )"
];

foreach ($schemaSql as $sql) {
    if (!mysqli_query($conn, $sql)) {
        die("Gagal inisialisasi tabel: " . mysqli_error($conn));
    }
}

// Migrasi ringan: jika masih ada kolom lama `umur`, ubah ke `nickname`.
$hasUmurResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'pemain'
       AND COLUMN_NAME = 'umur'"
);
$hasNicknameResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'pemain'
       AND COLUMN_NAME = 'nickname'"
);

$hasUmur = $hasUmurResult ? (int) (mysqli_fetch_assoc($hasUmurResult)["total"] ?? 0) : 0;
$hasNickname = $hasNicknameResult ? (int) (mysqli_fetch_assoc($hasNicknameResult)["total"] ?? 0) : 0;

if ($hasUmur > 0 && $hasNickname === 0) {
    if (!mysqli_query($conn, "ALTER TABLE pemain CHANGE COLUMN umur nickname VARCHAR(80) NOT NULL")) {
        die("Gagal migrasi kolom pemain.umur ke pemain.nickname: " . mysqli_error($conn));
    }
}

// Tambah kolom foto jika belum ada.
$hasFotoResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'pemain'
       AND COLUMN_NAME = 'foto'"
);
$hasFoto = $hasFotoResult ? (int) (mysqli_fetch_assoc($hasFotoResult)["total"] ?? 0) : 0;
if ($hasFoto === 0) {
    if (!mysqli_query($conn, "ALTER TABLE pemain ADD COLUMN foto VARCHAR(255) NULL AFTER nickname")) {
        die("Gagal menambah kolom pemain.foto: " . mysqli_error($conn));
    }
}

