# Requisitos

## Alcance MVP
### Inventario
- RF-01 CRUD de productos (nombre, SKU, código de barras, categoría, precio de venta con IVA incluido, costo).
- RF-02 Control de stock y stock mínimo con alerta.
- RF-03 Movimientos de inventario: entrada, ajuste, merma, venta, devolución.
### Ventas
- RF-04 Carrito de venta con búsqueda por nombre/SKU/código de barras.
- RF-05 Medios de pago: efectivo, débito, crédito, transferencia.
- RF-06 Apertura y cierre de caja (arqueo).
- RF-07 Anulación y devolución de ventas (con nota de crédito cuando corresponda).
- RF-08 Emisión de boleta electrónica (integración con proveedor certificado SII).
### Usuarios
- RF-09 Roles: administrador y cajero. Autenticación segura.
### Reportes
- RF-10 Ventas por día/período, productos más vendidos, stock crítico.
- RF-11 Exportación del libro de ventas (CSV/Excel) para el contador.

## No funcionales
- RNF-01 Montos en pesos chilenos (CLP) como enteros, sin decimales.
- RNF-02 Contraseñas con `password_hash`, consultas preparadas (PDO), protección CSRF y XSS.
- RNF-03 Respaldo de base de datos y registro de auditoría de acciones críticas.
- RNF-04 Interfaz usable en tablet y computador.
