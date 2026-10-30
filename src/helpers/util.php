<?php
// Escapa texto para HTML (previene XSS)
function e(?string $texto): string
{
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

// Formato de pesos chilenos: $12.990
function clp(int $monto): string
{
    return '$' . number_format($monto, 0, ',', '.');
}

function redirigir(string $url): never
{
    header('Location: ' . $url);
    exit;
}

// Mensajes flash (se muestran una sola vez)
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function flash_obtener(): array
{
    $mensajes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensajes;
}
