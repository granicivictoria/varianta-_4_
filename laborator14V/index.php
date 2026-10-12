<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$user_dir = get_user_dir($user);

// Deconectare
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Încărcare fișier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validări: max 5MB și fără scripturi PHP
    if ($file['size'] > 5 * 1024 * 1024) {
        $msg = 'Fișierul este prea mare (max 5MB)!';
    } elseif (in_array($ext, ['php', 'exe', 'js', 'html'])) {
        $msg = 'Tip de fișier nepermis!';
    } else {
        move_uploaded_file($file['tmp_name'], $user_dir . basename($file['name']));
    }
}

// Ștergere
if (isset($_GET['delete'])) {
    $file_to_del = $user_dir . basename($_GET['delete']);
    if (file_exists($file_to_del)) unlink($file_to_del);
    header('Location: index.php');
    exit;
}

// Descărcare
if (isset($_GET['download'])) {
    $file_to_down = $user_dir . basename($_GET['download']);
    if (file_exists($file_to_down)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_to_down) . '"');
        readfile($file_to_down);
        exit;
    }
}

$files = array_diff(scandir($user_dir), ['.', '..']);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Panou Fișiere</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-lg font-bold">Salut, <?= htmlspecialchars($user) ?>!</h1>
            <a href="?logout=1" class="text-red-500 text-sm">Deconectare</a>
        </div>

        <?php if (isset($msg)): ?><p class="text-red-500 mb-3"><?= $msg ?></p><?php endif; ?>

        <!-- Formular Încarcare -->
        <form method="POST" enctype="multipart/form-data" class="flex gap-2 mb-6">
            <input type="file" name="file" required class="border p-1 rounded w-full">
            <button type="submit" class="bg-green-500 text-white px-4 py-1 rounded">Încarcă</button>
        </form>

        <!-- Lista Fișiere -->
        <h2 class="font-semibold mb-2">Fișierele tale:</h2>
        <ul class="divide-y">
            <?php foreach ($files as $f): ?>
                <li class="py-2 flex justify-between items-center">
                    <span><?= htmlspecialchars($f) ?></span>
                    <div class="space-x-2 text-sm">
                        <a href="?download=<?= urlencode($f) ?>" class="text-blue-500">Descarcă</a>
                        <a href="?delete=<?= urlencode($f) ?>" class="text-red-500" onclick="return confirm('Ștergi fișierul?')">Șterge</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>