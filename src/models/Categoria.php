<?php
class Categoria
{
    public static function listar(): array
    {
        return db()->query('SELECT id, nombre FROM categorias ORDER BY nombre')->fetchAll();
    }
}
