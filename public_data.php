<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/db.php";

$berita = [];
$pemain = [];
$turnamen = [];
$score = [];

$beritaRows = mysqli_query($conn, "SELECT id, judul, kategori, isi, tanggal FROM berita ORDER BY tanggal DESC, id DESC");
if ($beritaRows) {
    while ($row = mysqli_fetch_assoc($beritaRows)) {
        $berita[] = [
            "id" => (int) $row["id"],
            "title" => $row["judul"],
            "category" => $row["kategori"],
            "content" => $row["isi"],
            "date" => $row["tanggal"]
        ];
    }
}

$pemainRows = mysqli_query($conn, "SELECT id, nama, posisi, nickname FROM pemain ORDER BY id DESC");
if ($pemainRows) {
    while ($row = mysqli_fetch_assoc($pemainRows)) {
        $pemain[] = [
            "id" => (int) $row["id"],
            "name" => $row["nama"],
            "role" => $row["posisi"],
            "nickname" => $row["nickname"]
        ];
    }
}

$turnamenRows = mysqli_query($conn, "SELECT id, nama, tanggal, lokasi, status FROM turnamen ORDER BY tanggal DESC, id DESC");
if ($turnamenRows) {
    while ($row = mysqli_fetch_assoc($turnamenRows)) {
        $turnamen[] = [
            "id" => (int) $row["id"],
            "name" => $row["nama"],
            "date" => $row["tanggal"],
            "location" => $row["lokasi"],
            "status" => $row["status"]
        ];
    }
}

$scoreRows = mysqli_query($conn, "SELECT id, pemain_id, turnamen_id, point, tanggal FROM score ORDER BY tanggal DESC, id DESC");
if ($scoreRows) {
    while ($row = mysqli_fetch_assoc($scoreRows)) {
        $score[] = [
            "id" => (int) $row["id"],
            "playerId" => (int) $row["pemain_id"],
            "tournamentId" => (int) $row["turnamen_id"],
            "point" => (int) $row["point"],
            "date" => $row["tanggal"]
        ];
    }
}

echo json_encode(
    [
        "berita" => $berita,
        "pemain" => $pemain,
        "turnamen" => $turnamen,
        "score" => $score
    ],
    JSON_UNESCAPED_UNICODE
);

