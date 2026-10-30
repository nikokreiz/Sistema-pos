<?php
// ---------- CSRF ----------
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_validar(): void
{
    $enviado = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $enviado)) {
        http_response_code(403);
        exit('Solicitud no válida (CSRF).');
    }
}

// ---------- Sesión y roles ----------
function usuario_actual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function requiere_login(): void
{
    if (!usuario_actual()) {
        redirigir('/login.php');
    }
}

function requiere_rol(string $rol): void
{
    requiere_login();
    if (usuario_actual()['rol'] !== $rol) {
        http_response_code(403);
        exit('No tienes permisos para esta acción.');
    }
}

function es_admin(): bool
{
    return (usuario_actual()['rol'] ?? '') === 'admin';
}
