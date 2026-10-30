<?php
require __DIR__ . '/../src/bootstrap.php';

if (usuario_actual()) {
    redirigir('/productos/');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $usuario = Usuario::buscarPorEmail($email);
    // Hash falso para que la respuesta tarde lo mismo si el usuario no existe
    $hash = $usuario['password_hash'] ?? '$2y$10$usesomesillystringforsalt.invalidinvalidinvalidinvali';

    if ($usuario && password_verify($password, $hash)) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'     => (int)$usuario['id'],
            'nombre' => $usuario['nombre'],
            'rol'    => $usuario['rol'],
        ];
        redirigir('/productos/');
    }
    $error = 'Correo o contraseña incorrectos.';
}

$titulo = 'Iniciar sesión';
require ROOT . '/src/views/header.php';
?>
<section class="login">
    <h1>Iniciar sesión</h1>
    <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="formulario">
        <?= csrf_campo() ?>
        <label>Correo
            <input type="email" name="email" required autofocus>
        </label>
        <label>Contraseña
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="btn">Entrar</button>
    </form>
</section>
<?php require ROOT . '/src/views/footer.php'; ?>
