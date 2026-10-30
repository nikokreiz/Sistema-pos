<?php $u = usuario_actual(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Sistema POS') ?> · Sistema POS</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
<?php if ($u): ?>
<header class="topbar">
    <strong>Sistema POS</strong>
    <nav>
        <a href="/productos/">Productos</a>
    </nav>
    <div class="usuario">
        <span><?= e($u['nombre']) ?> (<?= e($u['rol']) ?>)</span>
        <form method="post" action="/logout.php">
            <?= csrf_campo() ?>
            <button type="submit" class="btn btn-secundario">Salir</button>
        </form>
    </div>
</header>
<?php endif; ?>
<main class="contenedor">
<?php foreach (flash_obtener() as $f): ?>
    <div class="alerta alerta-<?= e($f['tipo']) ?>"><?= e($f['mensaje']) ?></div>
<?php endforeach; ?>
