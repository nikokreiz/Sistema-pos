<?php
class Usuario
{
    public static function buscarPorEmail(string $email): ?array
    {
        $st = db()->prepare('SELECT * FROM usuarios WHERE email = :email AND activo = 1');
        $st->execute(['email' => $email]);
        return $st->fetch() ?: null;
    }

    public static function crear(string $nombre, string $email, string $password, string $rol): int
    {
        $st = db()->prepare(
            'INSERT INTO usuarios (nombre, email, password_hash, rol)
             VALUES (:nombre, :email, :hash, :rol)
             RETURNING id'
        );
        $st->execute([
            'nombre' => $nombre,
            'email'  => $email,
            'hash'   => password_hash($password, PASSWORD_DEFAULT),
            'rol'    => $rol,
        ]);
        return (int)$st->fetchColumn();
    }
}
