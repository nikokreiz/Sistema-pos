<?php
require __DIR__ . '/../../src/bootstrap.php';
requiere_rol('admin');

$id = (int)($_GET['id'] ?? 0);
$producto = Producto::obtener($id);
if (!$producto) {
    flash('error', 'El producto no existe.');
    redirigir('/productos/');
}

$errores = [];
$valores = $producto;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    [$datos, $errores] = Producto::validar($_POST, false);
    $valores = $datos;
    if (!$errores) {
        try {
            Producto::actualizar($id, $datos);
            flash('ok', 'Producto actualizado correctamente.');
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
$esNuevo = false;
$titulo = 'Editar producto';
require ROOT . '/src/views/header.php';
?>
<h1>Editar producto</h1>
<p class="nota">Stock actual: <strong><?= (int)$producto['stock'] ?></strong>. El stock solo cambia mediante movimientos de inventario (próximo módulo).</p>
<?php require ROOT . '/src/views/producto_form.php'; ?>
<?php require ROOT . '/src/views/footer.php'; ?>
