-- Esquema para PostgreSQL
-- Antes de ejecutarlo, crea la base de datos:  CREATE DATABASE sistema_pos;
-- Luego ejecuta este archivo conectado a la base sistema_pos.

CREATE TABLE usuarios (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol VARCHAR(10) NOT NULL DEFAULT 'cajero' CHECK (rol IN ('admin','cajero')),
  activo SMALLINT NOT NULL DEFAULT 1,
  creado_en TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE productos (
  id SERIAL PRIMARY KEY,
  categoria_id INT NULL REFERENCES categorias(id),
  sku VARCHAR(50) NOT NULL UNIQUE,
  codigo_barras VARCHAR(50) NULL UNIQUE,
  nombre VARCHAR(150) NOT NULL,
  costo INT NOT NULL DEFAULT 0 CHECK (costo >= 0),
  precio_venta INT NOT NULL CHECK (precio_venta >= 0),
  afecto_iva SMALLINT NOT NULL DEFAULT 1,
  stock INT NOT NULL DEFAULT 0 CHECK (stock >= 0),
  stock_minimo INT NOT NULL DEFAULT 0 CHECK (stock_minimo >= 0),
  activo SMALLINT NOT NULL DEFAULT 1
);

CREATE TABLE movimientos_inventario (
  id SERIAL PRIMARY KEY,
  producto_id INT NOT NULL REFERENCES productos(id),
  usuario_id INT NOT NULL REFERENCES usuarios(id),
  tipo VARCHAR(15) NOT NULL CHECK (tipo IN ('entrada','ajuste','merma','venta','devolucion')),
  cantidad INT NOT NULL,
  motivo VARCHAR(255) NULL,
  creado_en TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cajas (
  id SERIAL PRIMARY KEY,
  usuario_id INT NOT NULL REFERENCES usuarios(id),
  apertura_en TIMESTAMPTZ NOT NULL,
  cierre_en TIMESTAMPTZ NULL,
  monto_inicial INT NOT NULL DEFAULT 0,
  monto_cierre_declarado INT NULL
);

CREATE TABLE ventas (
  id SERIAL PRIMARY KEY,
  caja_id INT NOT NULL REFERENCES cajas(id),
  usuario_id INT NOT NULL REFERENCES usuarios(id),
  neto INT NOT NULL,
  iva INT NOT NULL,
  total INT NOT NULL,
  estado VARCHAR(12) NOT NULL DEFAULT 'completada' CHECK (estado IN ('completada','anulada')),
  creado_en TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE detalle_venta (
  id SERIAL PRIMARY KEY,
  venta_id INT NOT NULL REFERENCES ventas(id),
  producto_id INT NOT NULL REFERENCES productos(id),
  cantidad INT NOT NULL,
  precio_unitario INT NOT NULL,
  subtotal INT NOT NULL
);

CREATE TABLE pagos (
  id SERIAL PRIMARY KEY,
  venta_id INT NOT NULL REFERENCES ventas(id),
  medio VARCHAR(15) NOT NULL CHECK (medio IN ('efectivo','debito','credito','transferencia')),
  monto INT NOT NULL
);

CREATE TABLE documentos_tributarios (
  id SERIAL PRIMARY KEY,
  venta_id INT NOT NULL REFERENCES ventas(id),
  tipo VARCHAR(15) NOT NULL CHECK (tipo IN ('boleta','factura','nota_credito','nota_debito')),
  folio INT NULL,
  estado_sii VARCHAR(30) NULL,
  url_pdf VARCHAR(255) NULL,
  emitido_en TIMESTAMPTZ NULL
);
