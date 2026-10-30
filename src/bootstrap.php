<?php
define('ROOT', dirname(__DIR__));

require ROOT . '/src/config/env.php';
cargarEnv(ROOT . '/.env');
require ROOT . '/src/config/database.php';
require ROOT . '/src/helpers/auth.php';
require ROOT . '/src/helpers/util.php';
require ROOT . '/src/models/Usuario.php';
require ROOT . '/src/models/Categoria.php';
require ROOT . '/src/models/Producto.php';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();
