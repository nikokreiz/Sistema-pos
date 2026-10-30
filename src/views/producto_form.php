<?php
// Variables esperadas: $valores (array), $errores (array), $categorias (array), $esNuevo (bool)
?>
<?php if ($errores): ?>
    <div class="alerta alerta-error">
        <ul>
            <?php foreach ($errores as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="formulario">
    <?= csrf_campo() ?>

    <label>SKU *
        <input type="text" name="sku" value="<?= e($valores['sku'] ?? '') ?>" required maxlength="50">
    </label>
    <label>Código de barras
        <input type="text" name="codigo_barras" value="<?= e($valores['codigo_barras'] ?? '') ?>" maxlength="50">
    </label>
    <label>Nombre *
        <input type="text" name="nombre" value="<?= e($valores['nombre'] ?? '') ?>" required maxlength="150">
    </label>
    <label>Categoría
        <select name="categoria_id">
            <option value="">Sin categoría</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= (string)($valores['categoria_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>>
                    <?= e($c['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Costo (CLP)
        <input type="number" name="costo" min="0" step="1" value="<?= e((string)($valores['costo'] ?? 0)) ?>">
    </label>
    <label>Precio de venta con IVA incluido (CLP) *
        <input type="number" name="precio_venta" min="0" step="1" value="<?= e((string)($valores['precio_venta'] ?? '')) ?>" required>
    </label>
    <?php if ($esNuevo): ?>
    <label>Stock inicial
        <input type="number" name="stock" min="0" step="1" value="<?= e((string)($valores['stock'] ?? 0)) ?>">
    </label>
    <?php endif; ?>
    <label>Stock mínimo (alerta)
        <input type="number" name="stock_minimo" min="0" step="1" value="<?= e((string)($valores['stock_minimo'] ?? 0)) ?>">
    </label>
    <label class="check">
        <input type="checkbox" name="afecto_iva" <?= ($valores['afecto_iva'] ?? 1) ? 'checked' : '' ?>>
        Afecto a IVA
    </label>

    <div class="acciones">
        <button type="submit" class="btn">Guardar</button>
        <a href="/productos/" class="btn btn-secundario">Cancelar</a>
    </div>
</form>
