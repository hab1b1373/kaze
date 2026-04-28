<?php
declare(strict_types=1);

require_once __DIR__ . "/db.php";

$members = [];
$query = "SELECT id, nama, nickname, posisi, foto FROM pemain WHERE foto IS NOT NULL AND foto <> '' ORDER BY nama ASC";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $members[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Member - Kaze Club</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0b1120, #0f172a);
            color: #e2e8f0;
            min-height: 100vh;
        }
        .container { max-width: 1150px; margin: 0 auto; padding: 0 1rem; }
        header {
            position: sticky; top: 0; z-index: 20;
            background: rgba(2, 6, 23, 0.92);
            border-bottom: 1px solid rgba(34, 197, 94, 0.25);
            backdrop-filter: blur(8px);
        }
        .topbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.9rem 0;
        }
        .logo { color: #22c55e; font-weight: 800; }
        .back-link {
            color: #cbd5e1; text-decoration: none; border: 1px solid #334155;
            border-radius: 8px; padding: 0.45rem 0.7rem;
        }
        .hero { padding: 2.3rem 0 1.6rem; text-align: center; }
        .hero h1 { color: #86efac; margin-bottom: 0.4rem; }
        .hero p { color: #94a3b8; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
            padding-bottom: 2rem;
        }
        .card {
            background: #111827;
            border: 1px solid #334155;
            border-radius: 14px;
            overflow: hidden;
        }
        .thumb {
            width: 100%;
            height: 240px;
            object-fit: cover;
            display: block;
            background: #1e293b;
        }
        .card-body { padding: 0.75rem; }
        .name { color: #dcfce7; font-weight: 700; margin-bottom: 0.25rem; }
        .meta { color: #94a3b8; font-size: 0.9rem; }
        .empty {
            background: #111827;
            border: 1px dashed #334155;
            border-radius: 12px;
            padding: 1rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <header>
        <div class="container topbar">
            <div class="logo"><i class="fas fa-images"></i> Galeri Member Kaze</div>
            <a class="back-link" href="index.html">Kembali ke Home</a>
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <h1>Galeri Member</h1>
            <p>Menampilkan member yang sudah memiliki foto profil.</p>
        </section>

        <?php if (count($members) === 0): ?>
            <div class="empty">
                Belum ada foto member. Tambahkan URL/path foto pada data pemain di halaman admin.
            </div>
        <?php else: ?>
            <section class="grid">
                <?php foreach ($members as $member): ?>
                    <article class="card">
                        <img
                            class="thumb"
                            src="<?= htmlspecialchars($member["foto"]) ?>"
                            alt="<?= htmlspecialchars($member["nama"]) ?>"
                            loading="lazy"
                        >
                        <div class="card-body">
                            <div class="name"><?= htmlspecialchars($member["nama"]) ?></div>
                            <div class="meta">@<?= htmlspecialchars($member["nickname"]) ?> | <?= htmlspecialchars($member["posisi"]) ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>

