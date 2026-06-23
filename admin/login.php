<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("
        SELECT *
        FROM users
        WHERE email = :email
        LIMIT 1
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && (int)$user['activo'] !== 1) {
        $error = 'Tu usuario se encuentra desactivado. Contactá al administrador.';
    } elseif ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_role'] = $user['rol'];

        if ((int)$user['debe_cambiar_password'] === 1) {
            header('Location: change-password.php');
            exit;
        }

        header('Location: index.php');
        exit;
    } else {
        $error = 'Email o contraseña incorrectos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | FORMA CMS</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body class="admin-login">

    <div class="login-box">

        <h1>FORMA CMS</h1>

        <?php if ($error): ?>
            <p class="admin-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" autocomplete="on">

            <label>Email</label>
            <input
                type="email"
                name="email"
                placeholder="Email"
                autocomplete="email"
                required
            >

            <label>Contraseña</label>

            <div class="password-field">
                <input
                    type="password"
                    name="password"
                    id="passwordInput"
                    placeholder="Contraseña"
                    autocomplete="current-password"
                    required
                >

                <button type="button" id="togglePassword" aria-label="Mostrar contraseña">
                    👁
                </button>
            </div>

            <button type="submit">Ingresar</button>

            <p style="margin-top:20px;">
                <a href="forgot-password.php">
                    ¿Olvidaste tu contraseña?
                </a>
            </p>

        </form>

    </div>

<script>
const passwordInput = document.getElementById('passwordInput');
const togglePassword = document.getElementById('togglePassword');

if (passwordInput && togglePassword) {
    togglePassword.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';

        passwordInput.type = isPassword ? 'text' : 'password';
        togglePassword.textContent = isPassword ? '🙈' : '👁';
    });
}
</script>

</body>
</html>