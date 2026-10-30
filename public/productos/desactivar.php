<?php
require __DIR__ . '/../../src/bootstrap.php';
requiere_rol('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    Producto::desactivar((int)($_POST['id'] ?? 0));
    flash('ok', 'Producto desactivado.');
}
redirigir('/productos/');
