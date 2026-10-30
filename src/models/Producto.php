<?php
class Producto
{
    public static function listar(string $busqueda = ''): array
    {
        $sql = 'SELECT p.*, c.nombre AS categoria
                FROM productos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE p.activo = 1';
        $params = [];
        if ($busqueda !== '') {
            $sql .= ' AND (p.nombre ILIKE :q1 OR p.sku ILIKE :q2 OR p.codigo_barras ILIKE :q3)';
            $like = '%' . $busqueda . '%';
            $params = ['q1' => $like, 'q2' => $like, 'q3' => $like];
        }
        $sql .= ' ORDER BY p.nombre';
        $st = db()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public static function obtener(int $id): ?array
    {
        $st = db()->prepare('SELECT * FROM productos WHERE id = :id AND activo = 1');
        $st->execute(['id' => $id]);
        return $st->fetch() ?: null;
    }

    // Crea el producto y, si trae stock inicial, registra el movimiento de entrada
    public static function crear(array $d, int $usuarioId): int
    {
        $pdo = db();
        $pdo->beginTransaction();
        try {
            $st = $pdo->prepare(
                'INSERT INTO productos
                 (categoria_id, sku, codigo_barras, nombre, costo, precio_venta, afecto_iva, stock, stock_minimo)
                 VALUES (:categoria_id, :sku, :codigo_barras, :nombre, :costo, :precio_venta, :afecto_iva, :stock, :stock_minimo)
                 RETURNING id'
            );
            $st->execute($d);
            $id = (int)$st->fetchColumn();

            if ($d['stock'] > 0) {
                $mv = $pdo->prepare(
                    "INSERT INTO movimientos_inventario (producto_id, usuario_id, tipo, cantidad, motivo)
                     VALUES (:p, :u, 'entrada', :c, 'Stock inicial')"
                );
                $mv->execute(['p' => $id, 'u' => $usuarioId, 'c' => $d['stock']]);
            }
            $pdo->commit();
            return $id;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // El stock NO se edita aquí: solo cambia mediante movimientos de inventario
    public static function actualizar(int $id, array $d): void
    {
        $st = db()->prepare(
            'UPDATE productos SET categoria_id = :categoria_id, sku = :sku, codigo_barras = :codigo_barras,
             nombre = :nombre, costo = :costo, precio_venta = :precio_venta,
             afecto_iva = :afecto_iva, stock_minimo = :stock_minimo
             WHERE id = :id'
        );
        $st->execute($d + ['id' => $id]);
    }

    // No se elimina: se desactiva para conservar el historial de ventas
    public static function desactivar(int $id): void
    {
        $st = db()->prepare('UPDATE productos SET activo = 0 WHERE id = :id');
        $st->execute(['id' => $id]);
    }

    // Valida y normaliza los datos del formulario. Devuelve [datos, errores]
    public static function validar(array $in, bool $esNuevo): array
    {
        $e = [];
        $sku = trim($in['sku'] ?? '');
        $nombre = trim($in['nombre'] ?? '');
        $barras = trim($in['codigo_barras'] ?? '');

        if ($sku === '') $e[] = 'El SKU es obligatorio.';
        if ($nombre === '') $e[] = 'El nombre es obligatorio.';

        $costo = filter_var($in['costo'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        $precio = filter_var($in['precio_venta'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        $minimo = filter_var($in['stock_minimo'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($costo === false) $e[] = 'El costo debe ser un número entero (CLP).';
        if ($precio === false) $e[] = 'El precio de venta debe ser un número entero (CLP).';
        if ($minimo === false) $e[] = 'El stock mínimo debe ser un número entero.';

        $d = [
            'categoria_id'  => ($in['categoria_id'] ?? '') !== '' ? (int)$in['categoria_id'] : null,
            'sku'           => $sku,
            'codigo_barras' => $barras !== '' ? $barras : null,
            'nombre'        => $nombre,
            'costo'         => $costo === false ? 0 : $costo,
            'precio_venta'  => $precio === false ? 0 : $precio,
            'afecto_iva'    => isset($in['afecto_iva']) ? 1 : 0,
            'stock_minimo'  => $minimo === false ? 0 : $minimo,
        ];

        if ($esNuevo) {
            $stock = filter_var($in['stock'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
            if ($stock === false) $e[] = 'El stock inicial debe ser un número entero.';
            $d['stock'] = $stock === false ? 0 : $stock;
        }
        return [$d, $e];
    }
}
