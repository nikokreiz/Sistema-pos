<?php
require __DIR__ . '/../../src/bootstrap.php';
requiere_login();

$q = trim($_GET['q'] ?? '');
$productos = Producto::listar($q);

$titulo = 'Productos';
require ROOT . '/src/views/header.php';
?>
<div class="encabezado">
    <h1>Productos</h1>
    <?php if (es_admin()): ?>
        <a href="/productos/crear.php" class="btn">+ Nuevo producto</a>
    <?php endif; ?>
</div>

<form method="get" class="busqueda">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre, SKU o código de barras">
    <button type="submit" class="btn btn-secundario">Buscar</button>
</form>

<div class="tabla-wrap">
<table>
    <thead>
        <tr>
            <th>SKU</th><th>Nombre</th><th>Categoría</th>
            <th class="num">Precio</th><th class="num">Stock</th>
            <?php if (es_admin()): ?><th></th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($productos as $p): ?>
        <?php $critico = $p['stock'] <= $p['stock_minimo']; ?>
        <tr class="<?= $critico ? 'stock-critico' : '' ?>">
            <td><?= e($p['sku']) ?></td>
            <td><?= e($p['nombre']) ?></td>
            <td><?= e($p['categoria'] ?? '—') ?></td>
            <td class="num"><?= clp((int)$p['precio_venta']) ?></td>
            <td class="num"><?= (int)$p['stock'] ?></td>
            <?php if (es_admin()): ?>
            <td class="acciones-fila">
                <a href="/productos/editar.php?id=<?= (int)$p['id'] ?>">Editar</a>
                <form method="post" action="/productos/desactivar.php" onsubmit="return confirm('¿Desactivar este producto?');">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                    <button type="submit" class="link-peligro">Desactivar</button>
                </form>
            </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    <?php if (!$productos): ?>
        <tr><td colspan="6" class="vacio">No hay productos para mostrar.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
<p class="nota">Las filas resaltadas tienen stock igual o menor al mínimo.</p>
<?php require ROOT . '/src/views/footer.php'; ?>
