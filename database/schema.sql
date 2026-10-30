CREATE DATABASE IF NOT EXISTS sistema_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_pos;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('admin','cajero') NOT NULL DEFAULT 'cajero',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT NULL,
  sku VARCHAR(50) NOT NULL UNIQUE,
  codigo_barras VARCHAR(50) NULL UNIQUE,
  nombre VARCHAR(150) NOT NULL,
  costo INT NOT NULL DEFAULT 0,
  precio_venta INT NOT NULL,
  afecto_iva TINYINT(1) NOT NULL DEFAULT 1,
  stock INT NOT NULL DEFAULT 0,
  stock_minimo INT NOT NULL DEFAULT 0,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE movimientos_inventario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  producto_id INT NOT NULL,
  usuario_id INT NOT NULL,
  tipo ENUM('entrada','ajuste','merma','venta','devolucion') NOT NULL,
  cantidad INT NOT NULL,
  motivo VARCHAR(255) NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (producto_id) REFERENCES productos(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE cajas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  apertura_en DATETIME NOT NULL,
  cierre_en DATETIME NULL,
  monto_inicial INT NOT NULL DEFAULT 0,
  monto_cierre_declarado INT NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  caja_id INT NOT NULL,
  usuario_id INT NOT NULL,
  neto INT NOT NULL,
  iva INT NOT NULL,
  total INT NOT NULL,
  estado ENUM('completada','anulada') NOT NULL DEFAULT 'completada',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (caja_id) REFERENCES cajas(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE detalle_venta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venta_id INT NOT NULL,
  producto_id INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario INT NOT NULL,
  subtotal INT NOT NULL,
  FOREIGN KEY (venta_id) REFERENCES ventas(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

CREATE TABLE pagos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venta_id INT NOT NULL,
  medio ENUM('efectivo','debito','credito','transferencia') NOT NULL,
  monto INT NOT NULL,
  FOREIGN KEY (venta_id) REFERENCES ventas(id)
);

CREATE TABLE documentos_tributarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venta_id INT NOT NULL,
  tipo ENUM('boleta','factura','nota_credito','nota_debito') NOT NULL,
  folio INT NULL,
  estado_sii VARCHAR(30) NULL,
  url_pdf VARCHAR(255) NULL,
  emitido_en DATETIME NULL,
  FOREIGN KEY (venta_id) REFERENCES ventas(id)
);
