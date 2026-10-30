<?php
// Uso (desde la raíz del proyecto):
//   php database/crear_admin.php "Nombre" correo@ejemplo.com "contraseña"
// Crea el primer usuario administrador y la categoría "General" si no existe ninguna.
if (PHP_SAPI !== 'cli') {
    exit('Solo desde la línea de comandos.');
}
require __DIR__ . '/../src/config/env.php';
cargarEnv(__DIR__ . '/../.env');
require __DIR__ . '/../src/config/database.php';
require __DIR__ . '/../src/models/Usuario.php';

[$_, $nombre, $email, $password] = $argv + [null, null, null, null];
if (!$nombre || !$email || !$password) {
    exit("Uso: php database/crear_admin.php \"Nombre\" correo@ejemplo.com \"contraseña\"\n");
}
if (strlen($password) < 8) {
    exit("La contraseña debe tener al menos 8 caracteres.\n");
}

Usuario::crear($nombre, $email, $password, 'admin');
if ((int)db()->query('SELECT COUNT(*) FROM categorias')->fetchColumn() === 0) {
    db()->exec("INSERT INTO categorias (nombre) VALUES ('General')");
}
echo "Administrador creado: $email\n";
