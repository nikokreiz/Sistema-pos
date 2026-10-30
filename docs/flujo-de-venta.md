# Flujo de venta

Define cómo se realiza una venta de principio a fin en el Sistema POS, y cómo se relaciona con el modelo de datos (`database/schema.sql`) y la normativa chilena (`docs/normativa-chilena.md`).

## 1. Diagrama general

```mermaid
flowchart TD
    A[Inicio de sesión] --> B{¿Caja abierta?}
    B -- No --> C[Abrir caja: monto inicial]
    B -- Sí --> D[Pantalla de venta]
    C --> D
    D --> E[Agregar productos: escáner o búsqueda por nombre/SKU]
    E --> F{¿Hay stock suficiente?}
    F -- No --> G[Aviso: stock insuficiente] --> E
    F -- Sí --> H[Actualizar carrito y totales]
    H --> I{¿Agregar más?}
    I -- Sí --> E
    I -- No --> J[Seleccionar medio(s) de pago]
    J --> K{¿Pago completo?}
    K -- No --> J
    K -- Sí --> L[Confirmar venta: transacción en base de datos]
    L --> M[Emitir boleta electrónica]
    M --> N{¿Emisión exitosa?}
    N -- Sí --> O[Imprimir / enviar comprobante]
    N -- No --> P[Venta queda pendiente de emisión]
    O --> D
    P --> D
    D --> Q[Cerrar caja y arqueo]
```

## 2. Pasos detallados

### 2.1 Apertura de caja
- El cajero no puede vender sin una caja abierta.
- Se registra el monto inicial de efectivo (`cajas.monto_inicial`) y la hora de apertura.
- Un usuario solo puede tener una caja abierta a la vez.

### 2.2 Armado del carrito
- Agregar productos por código de barras, SKU o búsqueda por nombre.
- Cada producto agregado muestra precio unitario (IVA incluido), cantidad y subtotal.
- Se valida el stock al agregar o modificar cantidades. Si no alcanza, se bloquea y se avisa.
- Se puede modificar cantidad o quitar líneas antes de cobrar.

### 2.3 Cálculo de totales
- El total es la suma de los subtotales (precios con IVA incluido).
- Neto = total / 1,19, redondeado; IVA = total − neto.
- Productos no afectos a IVA (`afecto_iva = 0`) se separan como monto exento.
- Todos los montos son enteros en CLP.

### 2.4 Pago
- Medios: efectivo, débito, crédito, transferencia.
- Se permite pago mixto (varias filas en `pagos` cuyo total debe igualar el de la venta).
- En efectivo: se ingresa el monto recibido y el sistema calcula el vuelto.
- Para pagos en efectivo se debe aplicar el redondeo vigente a múltiplos de $10 (validar la regla exacta con la normativa).
- La venta no se puede confirmar si la suma de pagos no cubre el total.

### 2.5 Confirmación (transacción atómica)
En una sola transacción de base de datos:
1. Insertar `ventas` (neto, iva, total, estado `completada`).
2. Insertar `detalle_venta` por cada línea.
3. Insertar `pagos`.
4. Insertar `movimientos_inventario` tipo `venta` (cantidad negativa) y descontar `productos.stock`.

Si cualquier paso falla, se revierte todo (rollback) y la venta no queda registrada.

### 2.6 Documento tributario
- Después de confirmar, se solicita la boleta electrónica al proveedor certificado por el SII.
- Se guarda en `documentos_tributarios`: tipo, folio, estado, URL del PDF y fecha de emisión.
- Si el proveedor no responde, la venta igual queda registrada y su documento queda **pendiente de emisión**, con reintento automático y alerta visible al administrador. No se pierde la venta.

### 2.7 Comprobante
- Imprimir en impresora térmica, mostrar en pantalla o enviar por correo si el cliente lo pide.

### 2.8 Cierre de caja
- El cajero declara el efectivo contado.
- El sistema compara contra: monto inicial + ventas en efectivo − devoluciones en efectivo.
- Se registra la diferencia (sobrante o faltante) y se bloquea la caja.
- Resumen por medio de pago y total de ventas del turno.

## 3. Anulaciones y devoluciones

| Caso | Qué hace el sistema |
|---|---|
| Error antes de cobrar | Se vacía o corrige el carrito, sin registro |
| Venta con documento emitido | Se emite **nota de crédito electrónica**; no se borra la venta |
| Devolución de producto | Movimiento `devolucion` que repone stock, más nota de crédito y reembolso según el medio de pago |

- Solo el rol administrador puede anular ventas ya confirmadas (o un cajero con autorización del administrador).
- Toda anulación guarda usuario, fecha y motivo.
- Las ventas nunca se eliminan; cambian a estado `anulada`.

## 4. Reglas de negocio
- RN-01 No se vende sin caja abierta.
- RN-02 No se vende por encima del stock disponible ni productos sin stock registrado.
- RN-03 La suma de pagos debe ser igual o mayor al total, y el vuelto solo aplica a efectivo.
- RN-04 Toda venta confirmada descuenta stock mediante un movimiento trazable.
- RN-05 Toda venta confirmada genera un documento tributario (o queda pendiente de emisión).
- RN-06 Las ventas y los documentos no se eliminan, solo se anulan.

## 5. Decisiones tomadas
1. **Descuentos:** no se contemplan. El precio de venta es el precio del producto.
2. **Documento tributario del MVP:** solo boleta electrónica. No se piden datos del cliente en la venta.
3. **RUT del cliente:** solo se solicitará cuando se incorpore la factura (segunda fase).
4. **Stock:** no se permite vender sin stock registrado (RN-02 es estricta, no configurable).

## 6. Fuera del MVP (segunda fase)
- Factura electrónica: requiere agregar una tabla de clientes (RUT, razón social, giro, dirección) al modelo de datos y un paso adicional en el flujo para ingresar esos datos antes de emitir.
- Descuentos.
