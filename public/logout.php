<?php
require __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $_SESSION = [];
    session_destroy();
}
redirigir('/login.php');
