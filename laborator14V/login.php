<?php
require_once 'config.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    $users = get_users();

    if ($action === 'register') {
        if (isset($users[$user])) {
            $msg = 'Utilizatorul există deja!';
        } else {
            $users[$user] = password_hash($pass, PASSWORD_DEFAULT);
            file_put_contents(USERS_FILE, json_encode($users));
            get_user_dir($user);
            $msg = 'Cont creat cu succes! Conectează-te.';
        }
    } elseif ($action === 'login') {
        if (isset($users[$user]) && password_verify($pass, $users[$user])) {
            $_SESSION['user'] = $user;
            header('Location: index.php');
            exit;
        } else {
            $msg = 'Date de conectare incorecte!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded-lg shadow-md w-80 text-center">
        <h2 class="text-xl font-bold mb-4">FileShare</h2>
        <?php if ($msg): ?><p class="text-red-500 text-sm mb-3"><?= $msg ?></p><?php endif; ?>
        
        <form method="POST" class="space-y-3">
            <input type="text" name="username" placeholder="Nume utilizator" required class="w-full p-2 border rounded">
            <input type="password" name="password" placeholder="Parolă" required class="w-full p-2 border rounded">
            <button type="submit" name="action" value="login" class="w-full bg-blue-500 text-white p-2 rounded">Conectare</button>
            <button type="submit" name="action" value="register" class="w-full bg-gray-500 text-white p-2 rounded">Înregistrare</button>
        </form>
    </div>
</body>
</html>