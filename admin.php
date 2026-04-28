<?php
declare(strict_types=1);

require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/db.php";
require_admin_login();

function redirect_to_admin(): void
{
    header("Location: admin.php");
    exit;
}

function get_int_post(string $key): int
{
    return (int) ($_POST[$key] ?? 0);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "reset_konten") {
        mysqli_query($conn, "DELETE FROM score");
        mysqli_query($conn, "DELETE FROM berita");
        mysqli_query($conn, "DELETE FROM pemain");
        mysqli_query($conn, "DELETE FROM turnamen");
        redirect_to_admin();
    }

    if ($action === "create_berita") {
        $judul = trim($_POST["judul"] ?? "");
        $kategori = $_POST["kategori"] ?? "Klub";
        $isi = trim($_POST["isi"] ?? "");
        $tanggal = $_POST["tanggal"] ?? date("Y-m-d");
        if ($judul !== "" && $isi !== "") {
            $sql = "INSERT INTO berita (judul, kategori, isi, tanggal) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssss", $judul, $kategori, $isi, $tanggal);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "update_berita") {
        $id = get_int_post("id");
        $judul = trim($_POST["judul"] ?? "");
        $kategori = $_POST["kategori"] ?? "Klub";
        $isi = trim($_POST["isi"] ?? "");
        $tanggal = $_POST["tanggal"] ?? date("Y-m-d");
        if ($id > 0 && $judul !== "" && $isi !== "") {
            $sql = "UPDATE berita SET judul = ?, kategori = ?, isi = ?, tanggal = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssi", $judul, $kategori, $isi, $tanggal, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "delete_berita") {
        $id = get_int_post("id");
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "DELETE FROM berita WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "create_pemain") {
        $nama = trim($_POST["nama"] ?? "");
        $posisi = $_POST["posisi"] ?? "Tunggal";
        $nickname = trim($_POST["nickname"] ?? "");
        $foto = trim($_POST["foto"] ?? "");
        if ($nama !== "" && $nickname !== "") {
            $stmt = mysqli_prepare($conn, "INSERT INTO pemain (nama, posisi, nickname, foto) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssss", $nama, $posisi, $nickname, $foto);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "update_pemain") {
        $id = get_int_post("id");
        $nama = trim($_POST["nama"] ?? "");
        $posisi = $_POST["posisi"] ?? "Tunggal";
        $nickname = trim($_POST["nickname"] ?? "");
        $foto = trim($_POST["foto"] ?? "");
        if ($id > 0 && $nama !== "" && $nickname !== "") {
            $stmt = mysqli_prepare($conn, "UPDATE pemain SET nama = ?, posisi = ?, nickname = ?, foto = ? WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssi", $nama, $posisi, $nickname, $foto, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "delete_pemain") {
        $id = get_int_post("id");
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "DELETE FROM pemain WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "create_turnamen") {
        $nama = trim($_POST["nama"] ?? "");
        $tanggal = $_POST["tanggal"] ?? date("Y-m-d");
        $lokasi = trim($_POST["lokasi"] ?? "");
        $status = $_POST["status"] ?? "Akan Datang";
        if ($nama !== "" && $lokasi !== "") {
            $stmt = mysqli_prepare($conn, "INSERT INTO turnamen (nama, tanggal, lokasi, status) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssss", $nama, $tanggal, $lokasi, $status);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "update_turnamen") {
        $id = get_int_post("id");
        $nama = trim($_POST["nama"] ?? "");
        $tanggal = $_POST["tanggal"] ?? date("Y-m-d");
        $lokasi = trim($_POST["lokasi"] ?? "");
        $status = $_POST["status"] ?? "Akan Datang";
        if ($id > 0 && $nama !== "" && $lokasi !== "") {
            $stmt = mysqli_prepare($conn, "UPDATE turnamen SET nama = ?, tanggal = ?, lokasi = ?, status = ? WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssi", $nama, $tanggal, $lokasi, $status, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "delete_turnamen") {
        $id = get_int_post("id");
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "DELETE FROM turnamen WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "create_score") {
        $pemainId = get_int_post("pemain_id");
        $turnamenId = get_int_post("turnamen_id");
        $point = get_int_post("point");
        $tanggal = $_POST["tanggal"] ?? date("Y-m-d");
        if ($pemainId > 0 && $turnamenId > 0 && $point >= 0) {
            $stmt = mysqli_prepare($conn, "INSERT INTO score (pemain_id, turnamen_id, point, tanggal) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iiis", $pemainId, $turnamenId, $point, $tanggal);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "update_score") {
        $id = get_int_post("id");
        $pemainId = get_int_post("pemain_id");
        $turnamenId = get_int_post("turnamen_id");
        $point = get_int_post("point");
        $tanggal = $_POST["tanggal"] ?? date("Y-m-d");
        if ($id > 0 && $pemainId > 0 && $turnamenId > 0 && $point >= 0) {
            $stmt = mysqli_prepare($conn, "UPDATE score SET pemain_id = ?, turnamen_id = ?, point = ?, tanggal = ? WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iiisi", $pemainId, $turnamenId, $point, $tanggal, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "delete_score") {
        $id = get_int_post("id");
        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "DELETE FROM score WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }
}

$editBerita = null;
$editPemain = null;
$editTurnamen = null;
$editScore = null;
$editAdmin = null;

$editType = $_GET["edit"] ?? "";
$editId = (int) ($_GET["id"] ?? 0);
if ($editId > 0 && $editType !== "") {
    if ($editType === "berita") {
        $stmt = mysqli_prepare($conn, "SELECT * FROM berita WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $editId);
            mysqli_stmt_execute($stmt);
            $editBerita = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
            mysqli_stmt_close($stmt);
        }
    } elseif ($editType === "pemain") {
        $stmt = mysqli_prepare($conn, "SELECT * FROM pemain WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $editId);
            mysqli_stmt_execute($stmt);
            $editPemain = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
            mysqli_stmt_close($stmt);
        }
    } elseif ($editType === "turnamen") {
        $stmt = mysqli_prepare($conn, "SELECT * FROM turnamen WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $editId);
            mysqli_stmt_execute($stmt);
            $editTurnamen = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
            mysqli_stmt_close($stmt);
        }
    } elseif ($editType === "score") {
        $stmt = mysqli_prepare($conn, "SELECT * FROM score WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $editId);
            mysqli_stmt_execute($stmt);
            $editScore = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
            mysqli_stmt_close($stmt);
        }
    } elseif ($editType === "admin") {
        $stmt = mysqli_prepare($conn, "SELECT id, username FROM admins WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $editId);
            mysqli_stmt_execute($stmt);
            $editAdmin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
            mysqli_stmt_close($stmt);
        }
    }
}

$beritaRows = mysqli_query($conn, "SELECT * FROM berita ORDER BY tanggal DESC, id DESC");
$pemainRows = mysqli_query($conn, "SELECT * FROM pemain ORDER BY id DESC");
$turnamenRows = mysqli_query($conn, "SELECT * FROM turnamen ORDER BY tanggal DESC, id DESC");
$scoreRows = mysqli_query(
    $conn,
    "SELECT s.id, s.point, s.tanggal, p.nama AS pemain_nama, p.id AS pemain_id, t.nama AS turnamen_nama, t.id AS turnamen_id
     FROM score s
     INNER JOIN pemain p ON p.id = s.pemain_id
     INNER JOIN turnamen t ON t.id = s.turnamen_id
     ORDER BY s.tanggal DESC, s.id DESC"
);

$allPemain = mysqli_query($conn, "SELECT id, nama FROM pemain ORDER BY nama ASC");
$allTurnamen = mysqli_query($conn, "SELECT id, nama FROM turnamen ORDER BY tanggal DESC");
$adminRows = mysqli_query($conn, "SELECT id, username, created_at FROM admins ORDER BY id DESC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "create_admin") {
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        if ($username !== "" && $password !== "") {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO admins (username, password_hash) VALUES (?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $username, $passwordHash);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }

    if ($action === "update_admin") {
        $id = get_int_post("id");
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        if ($id > 0 && $username !== "") {
            if ($password !== "") {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "UPDATE admins SET username = ?, password_hash = ? WHERE id = ?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssi", $username, $passwordHash, $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE admins SET username = ? WHERE id = ?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "si", $username, $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
        redirect_to_admin();
    }

    if ($action === "delete_admin") {
        $id = get_int_post("id");
        $currentAdminId = (int) ($_SESSION["admin_id"] ?? 0);
        if ($id > 0 && $id !== $currentAdminId) {
            $stmt = mysqli_prepare($conn, "DELETE FROM admins WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        redirect_to_admin();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Kaze Club</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #0f172a; color: #e2e8f0; }
        .container { max-width: 1150px; margin: 0 auto; padding: 1rem; }
        .topbar {
            display: flex; justify-content: space-between; align-items: center; gap: 0.6rem; flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .title { color: #86efac; font-size: 1.5rem; font-weight: 700; }
        .actions a {
            display: inline-block; margin-right: 0.45rem; text-decoration: none; color: #e2e8f0;
            border: 1px solid #475569; padding: 0.45rem 0.7rem; border-radius: 8px;
        }
        .actions a.green { background: #14532d; border-color: #22c55e; color: #dcfce7; }
        .top-inline-form { display: inline-block; margin-right: 0.45rem; }
        .top-inline-form button {
            border: 1px solid #ef4444; background: #7f1d1d; color: #fee2e2;
            padding: 0.45rem 0.7rem; border-radius: 8px; cursor: pointer;
        }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .card {
            background: #111827; border: 1px solid #334155; border-radius: 12px; padding: 0.9rem;
        }
        h2 { font-size: 1.05rem; color: #bbf7d0; margin-bottom: 0.7rem; }
        label { display: block; color: #cbd5e1; margin-bottom: 0.2rem; font-size: 0.9rem; }
        input, textarea, select {
            width: 100%; border: 1px solid #475569; border-radius: 8px; padding: 0.6rem;
            background: #1e293b; color: #f8fafc; margin-bottom: 0.6rem;
        }
        textarea { min-height: 86px; resize: vertical; }
        .btn {
            border: none; border-radius: 8px; padding: 0.5rem 0.7rem; color: #fff; cursor: pointer;
            font-weight: 600; margin-right: 0.3rem;
        }
        .btn-primary { background: #16a34a; }
        .btn-danger { background: #dc2626; }
        .btn-muted { background: #475569; color: #e2e8f0; text-decoration: none; display: inline-block; }
        .list { margin-top: 0.6rem; }
        .item { border: 1px solid #334155; border-radius: 10px; padding: 0.7rem; margin-bottom: 0.6rem; }
        .item h3 { color: #d1fae5; font-size: 0.98rem; margin-bottom: 0.25rem; }
        .meta { color: #94a3b8; font-size: 0.84rem; margin-bottom: 0.4rem; }
        .empty { color: #94a3b8; font-style: italic; }
        .inline-form { display: inline-block; margin-right: 0.25rem; }
        @media (max-width: 900px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div>
                <div class="title">Admin Panel Kaze Club</div>
                <div class="meta">Login sebagai: <?= htmlspecialchars($_SESSION["admin_username"]) ?></div>
            </div>
            <div class="actions">
                <a class="green" href="news.html">Lihat Halaman Pengunjung</a>
                <form class="top-inline-form" method="post" onsubmit="return confirm('Yakin hapus semua histori berita, pemain, turnamen, dan score?')">
                    <input type="hidden" name="action" value="reset_konten">
                    <button type="submit">Hapus Histori Lama</button>
                </form>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="grid">
            <section class="card">
                <h2><?= $editBerita ? "Edit Berita" : "Tambah Berita" ?></h2>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editBerita ? "update_berita" : "create_berita" ?>">
                    <?php if ($editBerita): ?><input type="hidden" name="id" value="<?= (int) $editBerita["id"] ?>"><?php endif; ?>
                    <label>Judul</label>
                    <input name="judul" required value="<?= htmlspecialchars($editBerita["judul"] ?? "") ?>">
                    <label>Kategori</label>
                    <select name="kategori">
                        <?php $kategoriAktif = $editBerita["kategori"] ?? "Klub"; ?>
                        <option value="Klub" <?= $kategoriAktif === "Klub" ? "selected" : "" ?>>Klub</option>
                        <option value="Latihan" <?= $kategoriAktif === "Latihan" ? "selected" : "" ?>>Latihan</option>
                        <option value="Turnamen" <?= $kategoriAktif === "Turnamen" ? "selected" : "" ?>>Turnamen</option>
                    </select>
                    <label>Isi</label>
                    <textarea name="isi" required><?= htmlspecialchars($editBerita["isi"] ?? "") ?></textarea>
                    <label>Tanggal</label>
                    <input name="tanggal" type="date" required value="<?= htmlspecialchars($editBerita["tanggal"] ?? date("Y-m-d")) ?>">
                    <button class="btn btn-primary" type="submit"><?= $editBerita ? "Update Berita" : "Simpan Berita" ?></button>
                    <?php if ($editBerita): ?><a class="btn-muted btn" href="admin.php">Batal</a><?php endif; ?>
                </form>
                <div class="list">
                    <?php if ($beritaRows && mysqli_num_rows($beritaRows) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($beritaRows)): ?>
                            <article class="item">
                                <h3><?= htmlspecialchars($row["judul"]) ?></h3>
                                <p class="meta"><?= htmlspecialchars($row["tanggal"]) ?> | <?= htmlspecialchars($row["kategori"]) ?></p>
                                <p><?= nl2br(htmlspecialchars($row["isi"])) ?></p>
                                <div style="margin-top:0.45rem;">
                                    <a class="btn btn-muted" href="admin.php?edit=berita&id=<?= (int) $row["id"] ?>">Edit</a>
                                    <form class="inline-form" method="post" onsubmit="return confirm('Hapus berita ini?')">
                                        <input type="hidden" name="action" value="delete_berita">
                                        <input type="hidden" name="id" value="<?= (int) $row["id"] ?>">
                                        <button class="btn btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty">Belum ada data berita.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card">
                <h2><?= $editPemain ? "Edit Pemain" : "Tambah Pemain" ?></h2>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editPemain ? "update_pemain" : "create_pemain" ?>">
                    <?php if ($editPemain): ?><input type="hidden" name="id" value="<?= (int) $editPemain["id"] ?>"><?php endif; ?>
                    <label>Nama</label>
                    <input name="nama" required value="<?= htmlspecialchars($editPemain["nama"] ?? "") ?>">
                    <label>Posisi</label>
                    <select name="posisi">
                        <?php $posisiAktif = $editPemain["posisi"] ?? "Tunggal"; ?>
                        <option value="Tunggal" <?= $posisiAktif === "Tunggal" ? "selected" : "" ?>>Tunggal</option>
                        <option value="Ganda" <?= $posisiAktif === "Ganda" ? "selected" : "" ?>>Ganda</option>
                    </select>
                    <label>Nickname</label>
                    <input name="nickname" required value="<?= htmlspecialchars($editPemain["nickname"] ?? "") ?>">
                    <label>URL/Path Foto (opsional)</label>
                    <input name="foto" placeholder="contoh: img/member/ajiw.jpg" value="<?= htmlspecialchars($editPemain["foto"] ?? "") ?>">
                    <button class="btn btn-primary" type="submit"><?= $editPemain ? "Update Pemain" : "Simpan Pemain" ?></button>
                    <?php if ($editPemain): ?><a class="btn-muted btn" href="admin.php">Batal</a><?php endif; ?>
                </form>
                <div class="list">
                    <?php if ($pemainRows && mysqli_num_rows($pemainRows) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($pemainRows)): ?>
                            <article class="item">
                                <h3><?= htmlspecialchars($row["nama"]) ?></h3>
                                <p class="meta"><?= htmlspecialchars($row["posisi"]) ?> | Nickname: <?= htmlspecialchars($row["nickname"]) ?></p>
                                <?php if (!empty($row["foto"])): ?>
                                    <p class="meta">Foto: <?= htmlspecialchars($row["foto"]) ?></p>
                                <?php endif; ?>
                                <div>
                                    <a class="btn btn-muted" href="admin.php?edit=pemain&id=<?= (int) $row["id"] ?>">Edit</a>
                                    <form class="inline-form" method="post" onsubmit="return confirm('Hapus pemain ini?')">
                                        <input type="hidden" name="action" value="delete_pemain">
                                        <input type="hidden" name="id" value="<?= (int) $row["id"] ?>">
                                        <button class="btn btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty">Belum ada data pemain.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card">
                <h2><?= $editTurnamen ? "Edit Turnamen" : "Tambah Turnamen" ?></h2>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editTurnamen ? "update_turnamen" : "create_turnamen" ?>">
                    <?php if ($editTurnamen): ?><input type="hidden" name="id" value="<?= (int) $editTurnamen["id"] ?>"><?php endif; ?>
                    <label>Nama Turnamen</label>
                    <input name="nama" required value="<?= htmlspecialchars($editTurnamen["nama"] ?? "") ?>">
                    <label>Tanggal</label>
                    <input name="tanggal" type="date" required value="<?= htmlspecialchars($editTurnamen["tanggal"] ?? date("Y-m-d")) ?>">
                    <label>Lokasi</label>
                    <input name="lokasi" required value="<?= htmlspecialchars($editTurnamen["lokasi"] ?? "") ?>">
                    <label>Status</label>
                    <select name="status">
                        <?php $statusAktif = $editTurnamen["status"] ?? "Akan Datang"; ?>
                        <option value="Akan Datang" <?= $statusAktif === "Akan Datang" ? "selected" : "" ?>>Akan Datang</option>
                        <option value="Berlangsung" <?= $statusAktif === "Berlangsung" ? "selected" : "" ?>>Berlangsung</option>
                        <option value="Selesai" <?= $statusAktif === "Selesai" ? "selected" : "" ?>>Selesai</option>
                    </select>
                    <button class="btn btn-primary" type="submit"><?= $editTurnamen ? "Update Turnamen" : "Simpan Turnamen" ?></button>
                    <?php if ($editTurnamen): ?><a class="btn-muted btn" href="admin.php">Batal</a><?php endif; ?>
                </form>
                <div class="list">
                    <?php if ($turnamenRows && mysqli_num_rows($turnamenRows) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($turnamenRows)): ?>
                            <article class="item">
                                <h3><?= htmlspecialchars($row["nama"]) ?></h3>
                                <p class="meta"><?= htmlspecialchars($row["tanggal"]) ?> | <?= htmlspecialchars($row["lokasi"]) ?> | <?= htmlspecialchars($row["status"]) ?></p>
                                <div>
                                    <a class="btn btn-muted" href="admin.php?edit=turnamen&id=<?= (int) $row["id"] ?>">Edit</a>
                                    <form class="inline-form" method="post" onsubmit="return confirm('Hapus turnamen ini?')">
                                        <input type="hidden" name="action" value="delete_turnamen">
                                        <input type="hidden" name="id" value="<?= (int) $row["id"] ?>">
                                        <button class="btn btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty">Belum ada data turnamen.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card">
                <h2><?= $editScore ? "Edit Score" : "Tambah Score" ?></h2>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editScore ? "update_score" : "create_score" ?>">
                    <?php if ($editScore): ?><input type="hidden" name="id" value="<?= (int) $editScore["id"] ?>"><?php endif; ?>
                    <label>Pemain</label>
                    <select name="pemain_id" required>
                        <option value="">Pilih pemain</option>
                        <?php if ($allPemain && mysqli_num_rows($allPemain) > 0): ?>
                            <?php while ($p = mysqli_fetch_assoc($allPemain)): ?>
                                <option value="<?= (int) $p["id"] ?>" <?= ((int) ($editScore["pemain_id"] ?? 0) === (int) $p["id"]) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($p["nama"]) ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                    <label>Turnamen</label>
                    <select name="turnamen_id" required>
                        <option value="">Pilih turnamen</option>
                        <?php if ($allTurnamen && mysqli_num_rows($allTurnamen) > 0): ?>
                            <?php while ($t = mysqli_fetch_assoc($allTurnamen)): ?>
                                <option value="<?= (int) $t["id"] ?>" <?= ((int) ($editScore["turnamen_id"] ?? 0) === (int) $t["id"]) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($t["nama"]) ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                    <label>Point</label>
                    <input name="point" type="number" min="0" required value="<?= htmlspecialchars((string) ($editScore["point"] ?? "")) ?>">
                    <label>Tanggal</label>
                    <input name="tanggal" type="date" required value="<?= htmlspecialchars($editScore["tanggal"] ?? date("Y-m-d")) ?>">
                    <button class="btn btn-primary" type="submit"><?= $editScore ? "Update Score" : "Simpan Score" ?></button>
                    <?php if ($editScore): ?><a class="btn-muted btn" href="admin.php">Batal</a><?php endif; ?>
                </form>
                <div class="list">
                    <?php if ($scoreRows && mysqli_num_rows($scoreRows) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($scoreRows)): ?>
                            <article class="item">
                                <h3><?= htmlspecialchars($row["pemain_nama"]) ?> - <?= (int) $row["point"] ?> poin</h3>
                                <p class="meta"><?= htmlspecialchars($row["turnamen_nama"]) ?> | <?= htmlspecialchars($row["tanggal"]) ?></p>
                                <div>
                                    <a class="btn btn-muted" href="admin.php?edit=score&id=<?= (int) $row["id"] ?>">Edit</a>
                                    <form class="inline-form" method="post" onsubmit="return confirm('Hapus score ini?')">
                                        <input type="hidden" name="action" value="delete_score">
                                        <input type="hidden" name="id" value="<?= (int) $row["id"] ?>">
                                        <button class="btn btn-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty">Belum ada data score.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card">
                <h2><?= $editAdmin ? "Update Admin" : "Tambah Admin" ?></h2>
                <form method="post">
                    <input type="hidden" name="action" value="<?= $editAdmin ? "update_admin" : "create_admin" ?>">
                    <?php if ($editAdmin): ?><input type="hidden" name="id" value="<?= (int) $editAdmin["id"] ?>"><?php endif; ?>
                    <label>Username</label>
                    <input name="username" required value="<?= htmlspecialchars($editAdmin["username"] ?? "") ?>">
                    <label>Password <?= $editAdmin ? "(kosongkan jika tidak diubah)" : "" ?></label>
                    <input name="password" type="password" <?= $editAdmin ? "" : "required" ?>>
                    <button class="btn btn-primary" type="submit"><?= $editAdmin ? "Update Admin" : "Simpan Admin" ?></button>
                    <?php if ($editAdmin): ?><a class="btn-muted btn" href="admin.php">Batal</a><?php endif; ?>
                </form>
                <div class="list">
                    <?php if ($adminRows && mysqli_num_rows($adminRows) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($adminRows)): ?>
                            <article class="item">
                                <h3><?= htmlspecialchars($row["username"]) ?></h3>
                                <p class="meta">ID <?= (int) $row["id"] ?> | Dibuat <?= htmlspecialchars($row["created_at"]) ?></p>
                                <div>
                                    <a class="btn btn-muted" href="admin.php?edit=admin&id=<?= (int) $row["id"] ?>">Edit</a>
                                    <?php if ((int) $row["id"] !== (int) ($_SESSION["admin_id"] ?? 0)): ?>
                                        <form class="inline-form" method="post" onsubmit="return confirm('Hapus admin ini?')">
                                            <input type="hidden" name="action" value="delete_admin">
                                            <input type="hidden" name="id" value="<?= (int) $row["id"] ?>">
                                            <button class="btn btn-danger" type="submit">Hapus</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="meta">Akun aktif</span>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="empty">Belum ada data admin.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>

