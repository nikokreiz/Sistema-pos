<?php
require __DIR__ . '/../../src/bootstrap.php';
requiere_rol('admin');

$errores = [];
$valores = ['afecto_iva' => 1];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    [$datos, $errores] = Producto::validar($_POST, true);
    $valores = $datos;
    if (!$errores) {
        try {
            Producto::crear($datos, usuario_actual()['id']);
            flash('ok', 'Producto creado correctamente.');
            redirigir('/productos/');
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23505') {
                $errores[] = 'Ya existe un producto con ese SKU o código de barras.';
            } else {
                throw $ex;
            }
        }
    }
}

$categorias = Categoria::listar();
$esNuevo = true;
$titulo = 'Nuevo producto';
require ROOT . '/src/views/header.php';
?>
<h1>Nuevo producto</h1>
<?php require ROOT . '/src/views/producto_form.php'; ?>
<?php require ROOT . '/src/views/footer.php'; ?>
