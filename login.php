<?php
declare(strict_types=1);

require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/db.php";

if (is_admin_logged_in()) {
    header("Location: admin.php");
    exit;
}

// Buat akun admin default jika tabel admin masih kosong.
// Default login: admin / admin123
$countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM admins");
if ($countResult) {
    $countRow = mysqli_fetch_assoc($countResult);
    if ((int) ($countRow["total"] ?? 0) === 0) {
        $defaultUser = "admin";
        $defaultHash = password_hash("admin123", PASSWORD_DEFAULT);
        $insertSql = "INSERT INTO admins (username, password_hash) VALUES (?, ?)";
        $insertStmt = mysqli_prepare($conn, $insertSql);
        if ($insertStmt) {
            mysqli_stmt_bind_param($insertStmt, "ss", $defaultUser, $defaultHash);
            mysqli_stmt_execute($insertStmt);
            mysqli_stmt_close($insertStmt);
        }
    }
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } else {
        $sql = "SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $admin = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($admin && password_verify($password, $admin["password_hash"])) {
                $_SESSION["admin_id"] = (int) $admin["id"];
                $_SESSION["admin_username"] = $admin["username"];
                header("Location: admin.php");
                exit;
            }
        }

        $error = "Login gagal. Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kaze Club</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #0f172a, #020617);
            color: #e2e8f0;
            padding: 1rem;
        }
        .card {
            width: 100%;
            max-width: 420px;
            background: #111827;
            border: 1px solid #334155;
            border-radius: 14px;
            padding: 1.25rem;
        }
        h1 { margin-bottom: 0.75rem; color: #86efac; }
        p { color: #94a3b8; margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.35rem; }
        input {
            width: 100%;
            border: 1px solid #475569;
            background: #1e293b;
            color: #f8fafc;
            padding: 0.65rem 0.7rem;
            border-radius: 8px;
            margin-bottom: 0.9rem;
        }
        .btn {
            width: 100%;
            border: none;
            padding: 0.7rem 0.8rem;
            background: #16a34a;
            color: white;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }
        .error {
            margin-bottom: 0.9rem;
            padding: 0.55rem 0.7rem;
            border-radius: 8px;
            background: #7f1d1d;
            border: 1px solid #ef4444;
            color: #fee2e2;
        }
        .back {
            display: inline-block;
            margin-top: 0.9rem;
            color: #93c5fd;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Login Admin</h1>
        <p>Halaman ini hanya untuk admin Kaze Club.</p>

        <?php if ($error !== ""): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" required>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>

            <button class="btn" type="submit">Masuk</button>
        </form>

        <a class="back" href="index.html">Kembali ke Home</a>
    </div>
</body>
</html>

