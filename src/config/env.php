<?php
// Carga variables desde el archivo .env (raíz del proyecto)
function cargarEnv(string $ruta): void
{
    if (!is_file($ruta)) {
        return;
    }
    foreach (file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $linea = trim($linea);
        if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
            continue;
        }
        [$clave, $valor] = explode('=', $linea, 2);
        $_ENV[trim($clave)] = trim($valor);
    }
}

function env(string $clave, ?string $defecto = null): ?string
{
    return $_ENV[$clave] ?? $defecto;
}
