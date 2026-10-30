# Modelo de datos

Esquema completo en [`database/schema.sql`](../database/schema.sql).

## Entidades
usuarios, categorias, productos, movimientos_inventario, cajas, ventas, detalle_venta, pagos, documentos_tributarios.

## Relaciones principales
- categorias 1—N productos
- productos 1—N movimientos_inventario
- usuarios 1—N cajas / ventas / movimientos_inventario
- cajas 1—N ventas
- ventas 1—N detalle_venta, pagos, documentos_tributarios

## Decisiones
- Montos en CLP como enteros (sin decimales).
- `precio_venta` incluye IVA; `neto` e `iva` se calculan y guardan en la venta (IVA 19%).
- El stock se actualiza vía `movimientos_inventario` para mantener trazabilidad.
